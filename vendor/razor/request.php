<?php
namespace razor;

class request {
    
    public $path;
    
    public $method;

    public function __construct()
    {
        $this->path = explode('?',$_SERVER['REQUEST_URI'])[0];
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    public function get($param = FALSE)
    {
        if($param === FALSE) {
            return $_GET; 
        } else {
            return htmlentities($_GET[$param], ENT_QUOTES, 'UTF-8');
        }
    }

    public function post($param = FALSE)
    {
        if($param === FALSE) {
            return $_POST; 
        } else {
            return htmlentities($_POST[$param], ENT_QUOTES, 'UTF-8');
        }
    }

    public function isPost()
    {
        return $this->method === 'POST' ? true : false;
    }

    public function isGet()
    {
        return $this->method === 'Get' ? true : false;
    }
}