<?php
namespace App\Controllers;
use Core\Controller;

class HomeController extends Controller {

    public function __construct($controller, $action) { //passed from the router, constructs an object with controller and action and passes it to the parent
        parent::__construct($controller, $action); //parent is Controller.php
    }

    public function indexAction() {//queryParams are getting passed here
        $this->view->render('home/index'); //path from the view folder
        //we already have view  because it's set up in the parent, render is from view
    }

    public function testAjaxAction() {
        $resp = ['success'=>true, 'data'=>['id'=>23,'name'=>'Curtis','favorite_food'=>'bread']];
        $this->jsonResponse($resp);
    }
}