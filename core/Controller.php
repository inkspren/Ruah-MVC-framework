<?php
namespace Core;
use Core\Application;

class Controller extends Application {

    protected $_controller, $_action;
    public $view, $request;

    public function __construct($_controller, $_action) {
        parent::__construct();
        $this->_controller = $_controller;
        $this->_action = $_action;
        $this->request = new Input();
        $this->view = new View();
    }

    protected function load_model($model) {
        $modelPath = 'App\Models\\' . $model;
        if(class_exists($modelPath)) {
            $this->{$model.'Model'} = new $modelPath();
        }
    } 
    
    public function jsonResponse($resp) {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        http_response_code(200);
        echo json_encode($resp);
        exit;
    }
    
}