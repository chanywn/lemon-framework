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
			$file = __DIR__ . '/../../../../../../views/error/_404.php';
			if(!file_exists($file)){
	       		return $this->write('404 Not Found'); 
	    	}
			return $this->view('error/_404');
			
    	}
    }

	public function view($location,$model = false)
	{
		$file = __DIR__ . '/../../../../../../views/'.$location.'.php';
		if(!file_exists($file)){
	       throw new \Exception("File does not exist($file)");
	    }
	    if($model){
			foreach($model as $key => $value){
				$$key = $value;
			}
		}
		return include(__DIR__ . '/../../../../../../views/'.$location.'.php');
	}
}