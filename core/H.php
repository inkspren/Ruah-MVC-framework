<?php
namespace Core;

class H {
       
    //dump and die for debugging
   public static function dnd($data){
        echo '<pre>';
        var_dump($data);
        echo'</pre>';
        die();
    }
  
    public static function currentPage() {
        $currentPage = $_SERVER['REQUEST_URI'];
        if($currentPage == PROOT || $currentPage == PROOT.'home/index') {
            $currentPage = PROOT . 'home';
        }
        return $currentPage;
    }

    public static function getObjectProperties($obj) {
        return get_object_vars($obj);
    }
}