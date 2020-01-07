<?php
namespace Core;
use \PDO;
use \PDOException;

class DB {

    private static $_instance = null;
    private $_pdo, $_query, $_error = false, $_result, $_count = 0, $_lastInsertID = null;

    private function __construct() {
        try {
            $this->_pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
            //PDO parameters set in config
        }
        catch (PDOException $e) {
            die($e->getMessage());
        }
    }
    //checks if PDO exists and if it doesn't it creates it
    public static function getInstance() {
        if(!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function query($sql, $params = [], $class = false) {
        $this->_error = false;
        if($this->_query = $this->_pdo->prepare($sql)) {
            $x = 1; //counter
            if(count($params)) { // checks if there is something in $params
                foreach($params as $param) {
                    $this->_query->bindValue($x, $param); // binds $param values
                    $x++;
                }
            }

            if($this->_query->execute()) {
                if($class) {
                    $this->_result = $this->_query->fetchAll(PDO::FETCH_CLASS, $class);
                }
                else {
                    $this->_result = $this->_query->fetchALL(PDO::FETCH_OBJ);
                }
                $this->_count = $this->_query->rowCount();
                $this->_lastInsertID = $this->_pdo->lastInsertid();
            }
            else {
                $this->_error = true;
            }
        }
        return $this;
    }

    protected function _read($table, $params = [], $class) {
        $conditionString = '';
        $bind = [];
        $order = '';
        $limit = '';

        // conditions
        if(isset($params['conditions'])) {
            if(is_array($params['conditions'])) {
                foreach($params['conditions'] as $condition) {
                    $conditionString .= ' ' . $condition . ' AND';
                }
                $conditionString = trim($conditionString);
                $conditionString = rtrim($conditionString, ' AND');
            }
            else {
                $conditionString = $params['conditions'];
            }
            if($conditionString != ''){
            $conditionString = ' WHERE ' . $conditionString;
            }
        }
        // binding
        if(array_key_exists('bind', $params)){
            $bind = $params['bind'];
        }
        // order
        if(array_key_exists('order', $params)) {
            $order = ' ORDER BY ' . $params['order'];
        }
        // limit
        if(array_key_exists('limit', $params)) {
            $limit = ' LIMIT ' . $params['limit'];
        }
        $sql = "SELECT * FROM {$table}{$conditionString}{$order}{$limit}";
        if($this->query($sql, $bind, $class)) {
            if(!count($this->_result)) return false;
            return true;
        }
        return false;
    }

    public function find($table, $params = [], $class=false) {
        if($this->_read($table, $params, $class)) {
            return $this->results();
        }
        return false;
    }

    public function findFirst($table, $params = [], $class=false) {
        if($this->_read($table, $params, $class)) {
            return $this->first();
        }
        return false;
    }

    public function insert($table, $fields = []) {
        $fieldString = '';
        $valueString = '';
        $values = [];

        foreach($fields as $field => $value) {
          $fieldString .= '`' . $field . '`,';  //table columns
          $valueString .= '?,'; //data that gets inserted 
          $values[] = $value; //array of values
        }
        $fieldString = rtrim($fieldString, ','); //gets rid of the last ,
        $valueString = rtrim($valueString, ',');
        $sql = "INSERT INTO {$table} ({$fieldString}) VALUES ({$valueString})";

        if(!$this->query($sql, $values)->error()) {
            return true; //if there is no error return true
        }
        return false; //else return false
    }

    public function update($table, $id, $fields = []) { //add $key if you want to use 2nd sql statement
        $fieldString = '';
        $values = [];
        foreach($fields as $field => $value) {
            $fieldString .= ' ' . $field . ' = ?,';
            $values[] = $value;
        }
        $fieldString = trim($fieldString); //takes extra white space at the beginning and end
        $fieldString = rtrim($fieldString, ','); //takes extra comma
        
        $sql = "UPDATE {$table} SET {$fieldString} WHERE id = {$id}";
        //$sql = "UPDATE {$table} SET {$fieldString} WHERE {$key} = {$id}";
        if(!$this->query($sql, $values)->error()) {
            return true;
        }
        return false;
    }

    public function delete($table, $id) {
        $sql = "DELETE FROM {$table} WHERE id = {$id}";
        if(!$this->query($sql)->error()) {
            return true;
        }
        return false;
    }
    //getter functions 
    public function results() {
        return $this->_result; //gets all the results, it was set with fetchALL
    }

    public function first() {
        return (!empty($this->_result)) ? $this->_result[0] : []; //gets only the first 
    }

    public function count() {
        return $this->_count;
    }

    public function lastID() {
        return $this->_lastInsertID;
    }

    public function get_columns($table) {
        return $this->query("SHOW COLUMNS FROM {$table}")->results();
    }

    public function error() {
        return $this->_error;
    }
}