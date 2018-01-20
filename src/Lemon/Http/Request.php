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

    public function get($param = FALSE, $default = FALSE)
    {
        if($param === FALSE && $default === FALSE) {
            return $_GET; 
        } elseif($param !== FALSE && $default === FALSE) {
            return isset($_GET[$param]) ? $_GET[$param] : NULL;
        } else if($param !== FALSE && $default !== FALSE) {
            return isset($_GET[$param]) ? $_GET[$param] : $default;
        } else {
            return NULL;
        }
    }

    public function post($param = FALSE, $default = FALSE)
    {
        if($param === FALSE && $default === FALSE) {
            return $_POST;
        } elseif($param !== FALSE && $default === FALSE) {
            return isset($_POST[$param]) ? $_POST[$param] : NULL;
        } else if($param !== FALSE && $default !== FALSE) {
            return isset($_POST[$param]) ? $_POST[$param] : $default;
        } else {
            return NULL;
        }
    }

    public function input($param = FALSE, $default = FALSE)
    {
        if($this->isPost()){
            if($param === FALSE && $default === FALSE) {
                return $_POST;
            } elseif($param !== FALSE && $default === FALSE) {
                return isset($_POST[$param]) ? $_POST[$param] : NULL;
            } else if($param !== FALSE && $default !== FALSE) {
                return isset($_POST[$param]) ? $_POST[$param] : $default;
            } else {
                return NULL;
            }
        } elseif($this->isGet()) {
            if($param === FALSE && $default === FALSE) {
                return $_GET; 
            } elseif($param !== FALSE && $default === FALSE) {
                return isset($_GET[$param]) ? $_GET[$param] : NULL;
            } else if($param !== FALSE && $default !== FALSE) {
                return isset($_GET[$param]) ? $_GET[$param] : $default;
            } else {
                return NULL;
            }
        } else {
            return NULL;
        }
    }

    public function file($param = FALSE)
    {
        if($param === FALSE) {
            return false; 
        } else {
            return isset($_FILES[$param]) ? $_FILES[$param] : false;
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

    // public function isPut()
    // {
    //     return $this->method === 'PUT' ? true : false;
    // }

    // public function isDelete()
    // {
    //     return $this->method === 'DELETE' ? true : false;
    // }
}