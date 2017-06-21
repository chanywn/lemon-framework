<?php
namespace Lemon\Http;

class Request {
    
    public $path;
    
    public $method;

    public function __construct()
    {
        $this->path = explode('?',$_SERVER['REQUEST_URI'])[0];
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    public function get($param = FALSE,$default = FALSE)
    {
        if($param === FALSE) {
            return $_GET; 
        } else {
            return isset($_GET[$param]) ? $_GET[$param] : $default;
        }
    }

    public function post($param = FALSE)
    {
        if($param === FALSE) {
            return $_POST; 
        } else {
            return $_POST[$param];
        }
    }

    public function file($param = FALSE)
    {
        if($param === FALSE) {
            return false; 
        } else {
            return $_FILES[$param];
        }
    }

    public function isPost()
    {
        return $this->method === 'POST' ? true : false;
    }

    public function isGet()
    {
        return $this->method === 'GET' ? true : false;
    }
}