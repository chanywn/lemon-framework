<?php
namespace razor;

/**
* 
*/
class response
{
	
	function __construct()
	{
		# code...
	}

	public function redirect($path)
	{
		header('Location: http://localhost:3000'.$path);
		return $this;
	}
	
	public function back()
	{
		header('Location: ' . $_SERVER['HTTP_REFERER']);
		return $this;
	}

	public function write($words)
	{
		echo $words;
	}

	public function statusCode($code)
    {
    	if($code === '404' || $code === 404){
    		header("HTTP/1.1 404 Not Found");  
			header("Status: 404 Not Found");  
			return include __DIR__ . '/../../views/error/err_404.html';
    	}
    }

	public function view($location,$data = [])
	{
		
		include(__DIR__ . '/../../views/'.$location.'.html');
		return;
	}
}