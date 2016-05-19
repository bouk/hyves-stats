<?php
class DB {
    public $connection;
    private static $instance;
    
    private function __construct() {
        $this->connection = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    }
    
    public static function getInstance() {
        if(!isset(self::$instance)) {
            self::$instance = new DB();
        }
        return self::$instance;
    }
    
    public static function getConnection() {
        return self::getInstance()->connection;
    }
    
    public static function field($query) {
        $row = self::row($query);
        return $row[0];
    }
    
    public static function row($query) {
        if($result = self::getConnection()->query($query)) {
            return $result->fetch_array();
        } else {
            return false;
        }
    }
    
    public static function set($query) {
        if($result = self::getConnection()->query($query)) {
            $set = array();
            while($row = $result->fetch_array()) {
            	$set[] = $row;
            }
            return $set;
        } else {
            return false;
        }
    }
    
    public static function query($query) {
        return self::getConnection()->query($query);
    }
    
    public static function prepare($query) {
        return self::getConnection()->prepare($query);
    }
    
    public static function escape($string) {
        return self::getConnection()->real_escape_string($string);
    }
}
