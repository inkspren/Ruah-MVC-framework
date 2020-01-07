<?php
namespace Core;
use Core\Session;
use App\Models\Users;

class Router {

    public static function route($url) {
        
        //controllers (gets controller from the first element of the array, controller must be capitalized)
        $controller = (isset($url[0]) && $url[0] != '') ? ucwords($url[0]).'Controller' : DEFAULT_CONTROLLER.'Controller'; //the first part of the url is the controller
        $controller_name = str_replace('Controller', '', $controller);
        array_shift($url); //takes away the first element of the array
        
        //action (action is now the first element of the array, concatenate the word Action)
        $action = (isset($url[0]) && $url[0] != '') ? $url[0] . 'Action' : 'indexAction'; // methods in controller classes are actions
        $action_name = (isset($url[0]) && $url[0] != '')? $url[0] : 'index';
        array_shift($url);

        //acl check
        $grantAccess = self::hasAccess($controller_name, $action_name);
        
        if(!$grantAccess) {
            $controller = ACCESS_RESTRICTED.'Controller';
            $controller_name = ACCESS_RESTRICTED; //update controller name
            $action = 'indexAction';
        }

        //params (what's left in the url after controller and action)
        $queryParams = $url;
        //get the controller
        $controller = 'App\Controllers\\'. $controller;

        //create new object and instantiate with controller (e.g. Home)
        $dispatch = new $controller($controller_name, $action); //pass in controller name and action

        if(method_exists($controller, $action)){ //if action exists in controller
            call_user_func_array([$dispatch, $action], $queryParams); // calls method(action) on dispatch object and passing queryParams into it, array with object, method
        }
        else{
            die('That method does not exist in the controller \"' . $controller_name . '\"');
        }
    }

    public static function redirect($location) {
        if(!headers_sent()) {
            header('Location: '.PROOT.$location);
            exit();
        }
        else {
            echo '<script type="text/javascript">';
            echo 'window.location.href="'.PROOT.$location.'";';
            echo '</script>';
            echo '<noscript>';
            echo '<meta http-equiv="refresh" content="0;url='.$location.'"/>';
            echo '</noscript>'; exit;
        }
    }

    public static function hasAccess($controller_name, $action_name='index') {
        $acl_file = file_get_contents(ROOT . DS . 'app' . DS . 'acl.json');
        $acl = json_decode($acl_file, true); //turns json string into an array
        $current_user_acls = ["Guest"];
        $grantAccess = false;

        if(Session::exists(CURRENT_USER_SESSION_NAME)) {//if someone is logged in
            $current_user_acls[] = "LoggedIn";
            foreach(Users::currentUser()->acls() as $a){
                $current_user_acls[] = $a;
            }
        }

        foreach($current_user_acls as $level) {
            if(array_key_exists($level, $acl) && array_key_exists($controller_name, $acl[$level])) {//if level and controller name exist in acl, checking in acl.json
                if(in_array($action_name, $acl[$level][$controller_name]) || in_array("*", $acl[$level][$controller_name])) { //action exists
                    $grantAccess = true;
                break;
                }
            } 
        }
        //check for denied
        foreach($current_user_acls as $level) {
            $denied = $acl[$level]['denied'];
            if(!empty($denied) && array_key_exists($controller_name, $denied) && in_array($action_name, $denied[$controller_name])) {
                $grantAccess = false;
            break;
            }
        }
        return $grantAccess;
    }

    //loop through menu json
    public static function getMenu($menu) {
        $menuAry = [];
        $menuFile = file_get_contents(ROOT . DS . 'app' . DS . $menu . '.json');
        $acl = json_decode($menuFile, true);
        foreach($acl as $key => $val) {
            if(is_array($val)) { //if it's an array it's a dropdown menu
                $sub = [];
                foreach($val as $k => $v) {
                    if($k == 'seperator' && !empty($sub)) {
                        $sub[$k] = '';
                        continue;
                    }
                    else if($finalVal = self::get_link($v)) {
                        $sub[$k] = $finalVal;
                    }
                }
                if(!empty($sub)) {
                    $menuAry[$key] = $sub;
                }
            }
            else {
                if($finalVal = self::get_link($val)) {
                    $menuAry[$key] = $finalVal;
                }
            }
        }
        return $menuAry;
    }

    private static function get_link($val) {
        //check if extrnal link
        if(preg_match('/https?:\/\//', $val) ==1) {//? next to s = s is optional
            return $val;
        }
        else {
            $uAry = explode('/', $val);
            $controller_name = ucwords($uAry[0]);
            $action_name = (isset($uAry[1])) ? $uAry[1] : '';
            if(self::hasAccess($controller_name, $action_name)) {//return link if user has access to it
                return PROOT . $val;
            }
            return false;
        }
    }
}