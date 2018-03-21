<?php
namespace Lemon;

class Route {
    private static $request = null;

    private static $response = null;

    private static $parameter = [];

    private static $callbacks = [];

    private static $anyPreg = "/[\'.,:;*?~`!@#$^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/";


    public static function initialise()
    {   
        self::$request = new \Lemon\Http\Request();
        self::$response =new \Lemon\Http\Response();
        array_push(self::$parameter, self::$request, self::$response);
    }

    public static function get($path, $callback)
    {
        self::map('GET', $path, $callback);
    }

    public static function post($path, $callback)
    {
        self::map('POST',$path, $callback);
    }

    public static function put($path, $callback)
    {
        self::map('PUT',$path, $callback);
    }

    public static function delete($path, $callback)
    {
        self::map('DELETE',$path, $callback);
    }

    public static function options($path, $callback)
    {
        self::map('OPTIONS',$path, $callback);
    }

    public static function any($path, $callback)
    {
        self::map('ANY',$path, $callback);
    }
    
    private static function map($method, $path, $callback)
    {
        array_push(self::$callbacks, ['method'=> $method, 'path' => $path, 'callback' => $callback]);
    }
    
    private static function match($method, $path) 
    {
        $requestPathArr = explode('/', self::$request->path);
        
        $callbackPathArr = explode('/',  $path);

        if($method !== self::$request->method && $method !== 'ANY') return false;

        if(count($requestPathArr) !== count($callbackPathArr))  return false;

        for($i=0; $i<count($requestPathArr); $i++)
        {
            if($requestPathArr[$i] !== $callbackPathArr[$i]) 
            {
                if($callbackPathArr[$i] === "(:any)")
                {
                    if(preg_match(self::$anyPreg, $requestPathArr[$i]))
                        return false;
                    else
                        array_push(self::$parameter, $requestPathArr[$i]);
                } 
                elseif($callbackPathArr[$i] === '(:num)')
                {
                    if(is_numeric($requestPathArr[$i]))
                        array_push(self::$parameter, $requestPathArr[$i]);
                    else 
                        return false;
                }   
                else
                {
                    return false;
                }
            }
        }
        return true;
    }

    private static function preprocessing()
    {
        for($i = 0; $i < count(self::$callbacks); $i++) 
        {
            if(self::match(self::$callbacks[$i]['method'], self::$callbacks[$i]['path'])) 
            {
                return $i;
            }
        }
        return false;
    }

    public static function run()
    {
        header('X-Powered-By:Lemon 1.0');
        self::initialise();
        $_i = self::preprocessing();

        if($_i === false) {
            return self::$response->code(404);
        } else {
            call_user_func_array(self::$callbacks[$_i]['callback'], self::$parameter);
        }
    }
}