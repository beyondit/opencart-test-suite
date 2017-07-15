<?php
namespace Session;
class Test
{
    public static $static_data = array();
    public static $session_id = 0;
    
    public function & __get($name)
    {
        if ($name === 'data') {
            return self::$static_data;
        }
    }
    
    public function __set($name, $value)
    {
        if ($name === 'data') {
            return self::$static_data[$name] = $value;
        }
    }
    
    public function __construct()
    {
        if (self::$session_id === 0) {
            self::$session_id = bin2hex(openssl_random_pseudo_bytes(16));
        }
    }
    
    public function getId() {
        return self::$session_id;
    }
    
    
    public function read($session_id) {
        return true;
    }
    
    public function write($session_id, $data) { }
}