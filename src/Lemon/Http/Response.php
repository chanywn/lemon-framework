<?php
namespace Lemon\Http;

class Response
{
	
	function __construct()
	{
		# code...
	}

	public function redirect($path)
	{
		header('Location: http://'.$_SERVER['HTTP_HOST'].$path);
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

	public function json($words)
	{
		echo json_encode($words);
	}

	public function statusCode($code)
    {
    	if($code === '404' || $code === 404){
    		header("HTTP/1.1 404 Not Found");  
			header("Status: 404 Not Found");
			return $this->view('error/_404');  
    	}
    }

	public function view($location,$model = false)
	{
		return include(__DIR__ . '/../../../../../../views/'.$location.'.php');
	}
}