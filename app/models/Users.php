<?php
namespace App\Models;
use Core\Model;
use Core\Session;
use Core\Cookie;
use Core\Validators\RequiredValidator;
use Core\Validators\EmailValidator;
use Core\Validators\MaxValidator;
use Core\Validators\MinValidator;
use Core\Validators\UniqueValidator;
use Core\Validators\MatchesValidator;
use App\Models\UserSessions;

class Users extends Model {

    private $_isLoggedIn, $_sessionName, $_cookieName, $_confirm;
    public static $currentLoggedInUser = null;
    public $id, $username, $email, $password, $fname, $lname, $acl, $deleted = 0;

    public function __construct($user='') {
        $table = 'users';
        parent::__construct($table);
        $this->_sessionName = CURRENT_USER_SESSION_NAME;
        $this->_cookieName = REMEMBER_ME_COOKIE_NAME;
        $this->_softDelete = true;
        if($user != '') {
            if(is_int($user)) {
                $u = $this->_db->findFirst('users', ['conditions'=>'id = ?', 'bind'=>[$user]], 'App\Models\Users'); //Users is class
            }
            else {
                $u = $this->_db->findFirst('users', ['conditions'=>'username = ?', 'bind'=>[$user]], 'App\Models\Users'); 
            }
            if($u) {
                foreach($u as $key => $val) {
                    $this->$key = $val;
                }
            }
        }
    }

    public function validator() {
        $this->runValidation(new RequiredValidator($this, ['field'=>'fname', 'msg'=>'First Name is required.']));
        $this->runValidation(new RequiredValidator($this, ['field'=>'lname', 'msg'=>'Last Name is required.']));

        $this->runValidation(new RequiredValidator($this, ['field'=>'email', 'msg'=>'Email is required.']));
        $this->runValidation(new EmailValidator($this, ['field'=>'email', 'msg'=>'Email must be a valid email address.']));
        $this->runValidation(new MaxValidator($this, ['field'=>'email', 'rule'=>150, 'msg'=>'Email must be at less than 150 characters.']));

        $this->runValidation(new MinValidator($this, ['field'=>'username', 'rule'=>6, 'msg'=>'Username must be at least 6 characters.']));
        $this->runValidation(new MaxValidator($this, ['field'=>'username', 'rule'=>150, 'msg'=>'Username must be at less than 150 characters.']));
        $this->runValidation(new UniqueValidator($this, ['field'=>'username', 'msg'=>'Username already exists.']));

        $this->runValidation(new RequiredValidator($this, ['field'=>'password', 'rule'=>6, 'msg'=>'Password is required.']));
        $this->runValidation(new MinValidator($this, ['field'=>'password', 'msg'=>'Password must be at least 6 characters.']));
        if($this->isNew()) {
            $this->runValidation(new MatchesValidator($this, ['field'=>'password', 'rule'=>$this->_confirm, 'msg'=>'Passwords do not match.']));
        }
    }

    public function beforeSave() {
        if($this->isNew()) {
            $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        }
    }

    public function findByUsername($username) {
        return $this->findFirst(['conditions'=>"username = ?", 'bind'=>[$username]]);
    }

    public static function currentUser() {
        if(!isset(self::$currentLoggedInUser) && Session::exists(CURRENT_USER_SESSION_NAME)) {
                $u = new Users((int)Session::get(CURRENT_USER_SESSION_NAME));
                self::$currentLoggedInUser = $u;
        }
        return self::$currentLoggedInUser;
    }

    public function login($rememberMe=false) {
        Session::set($this->_sessionName, $this->id); //set session
        if($rememberMe) { //if remember me is set
            $hash = md5(uniqid() + rand(0, 100)); //create hash
            $user_agent = Session::uagent_no_version(); //grab user agent
            Cookie::set($this->_cookieName, $hash, REMEMBER_ME_COOKIE_EXPIRY); //set cookie
            $fields = ['session'=>$hash, 'user_agent'=>$user_agent, 'user_id'=>$this->id]; //look in the database
            $this->_db->query("DELETE FROM user_sessions WHERE user_id = ? AND user_agent = ?", [$this->id, $user_agent]); //delete old sessions
            $this->_db->insert('user_sessions', $fields); //insert new session info
        }
    }

    public static function loginUserFromCookie() {
        $userSession = UserSessions::getFromCookie();
        if($userSession && $userSession->user_id != '') {
            $user = new self((int)$userSession->user_id);
            if($user) {
                $user->login();
            }
            return $user;
        }
        return;
    }

    public function logout(){
        $userSession = UserSessions::getFromCookie();
        if($userSession) $userSession->delete();
        Session::delete(CURRENT_USER_SESSION_NAME);
        if(Cookie::exists(REMEMBER_ME_COOKIE_NAME)) {
            Cookie::delete(REMEMBER_ME_COOKIE_NAME);
        }
        self::$currentLoggedInUser = null;
        return true;
    }

    public function acls() {
        if(empty($this->acl)) return []; //getting acl from the db
        return json_decode($this->acl, true);
    }

    public function setConfirm($value) {
        $this->_confirm = $value;
    }

    public function getConfirm() {
        return $this->_confirm;
    }
}