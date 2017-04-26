<?php

use razor\request;
use razor\response;

class route {
    private static $request;
    private static $response;

    private static $parameter;
    //注册的处理函数
    private static $callbacks = [];
    //路由匹配次数
    private static $MatchTimes = 0;
    //路由回调索引
    private static $MatchRouteIndex = -1;

    public static function init()
    {
        require 'request.php';
        require 'response.php';
        self::$request = new request();
        self::$response =new response();
        self::$parameter = [];

        array_push(self::$parameter, self::$request, self::$response);

        self::$MatchTimes = 0;
        self::$MatchRouteIndex = -1;
    }

    public static function get($path, $callback)
    {
        self::map('GET', $path, $callback);
    }

    public static function post($path, $callback)
    {
        self::map('POST',$path, $callback);
    }

    public static function any($path, $callback)
    {
        self::map('GET|POST',$path, $callback);
    }
    
    private static function map($method, $path, $callback)
    {
        array_push(self::$callbacks, ['method'=> $method, 'path' => $path, 'callback' => $callback]);
    }
    private static function match($method, $path) {
        $requestPathArr = explode('/', self::$request->path);
        $callbackPathArr = explode('/',  $path);
        if(self::$request->method !== $method 
            && $method !== 'GET|POST') { 
            return false; 
        }
        if(count($requestPathArr) !== count($callbackPathArr)) { return false; }
        $ErrMatchNum = 0;
        for($i=0; $i<count($requestPathArr); $i++) 
        {
            if($requestPathArr[$i] !== $callbackPathArr[$i]) 
            {
                if($callbackPathArr[$i] === '(:any)') {

                    if(preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/",$requestPathArr[$i])) { 
                       $ErrMatchNum++;
                    } else {
                        array_push(self::$parameter, $requestPathArr[$i]);
                        continue;
                    }
                    
                } elseif($callbackPathArr[$i] === '(:num)'){
                   if(is_numeric($requestPathArr[$i])) {
                       array_push(self::$parameter, $requestPathArr[$i]);
                       continue;
                   } else {
                       $ErrMatchNum++;
                   }
                } else {
                    $ErrMatchNum++;
                }
            }
        }
        if($ErrMatchNum === 0) {
            return true;
        } else {
            return false;
        }
    }

    private static function preprocessing()
    {
        for($i = 0; $i < count(self::$callbacks); $i++) 
        {
            if(self::match(self::$callbacks[$i]['method'], self::$callbacks[$i]['path'])) 
            {
                self::$MatchRouteIndex = $i;
                self::$MatchTimes++;
            }
        }
    }

    public static function run()
    {
        self::init();
        self::preprocessing();
        if( self::$MatchTimes !== 1 || self::$MatchRouteIndex === -1) {
            self::$response->statusCode(404);
        } else {
            call_user_func_array(self::$callbacks[self::$MatchRouteIndex]['callback'], self::$parameter);
        }
    }
}