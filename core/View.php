<?php
namespace Core;

class View {
    protected $_head, $_body, $_siteTitle = SITE_TITLE, $_outputBuffer, $_layout = DEFAULT_LAYOUT;

    public function __construct() {}

    public function render($viewName) { //viewName gets from the controllers
        $viewAry = explode ('/', $viewName); //explodes viewName on /
        $viewString = implode(DS, $viewAry); //uses default directory separator
        if(file_exists(ROOT . DS . 'app' . DS . 'views' . DS . $viewString . '.php')){
            include(ROOT . DS . 'app' . DS . 'views' . DS . $viewString . '.php'); //gets view
            include(ROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . $this->_layout . '.php'); //gets layout
        }
        else {
            die('The view \"'. $viewName . '\"does not exist.');
        }
    }

    public function content($type) {
        if($type == 'head') {
            return $this->_head;
        }
        elseif($type == 'body') {
            return $this->_body;
        }
        return false;
    }

    public function start($type) {
        $this->_outputBuffer = $type; //stores head or body in _outputBuffer
        ob_start();
    }

    public function end() {
        if($this->_outputBuffer == 'head') {
            $this->_head = ob_get_clean();
        }
        elseif($this->_outputBuffer == 'body') {
            $this->_body = ob_get_clean();
        }
        else {
            die('You must first run the start method');
        }
    }

    public function siteTitle() {
        return $this->_siteTitle;
    }

    public function setSiteTitle($title) {
        $this->_siteTitle = $title;
    }

    public function setLayout($path) {
        $this->_layout = $path;
    }
}
