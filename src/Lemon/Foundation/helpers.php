<?php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

function error_display()
{
	$whoops = new \Whoops\Run;
	$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
	$whoops->register();	
}

function error($value)
{
	$log = new Logger('name');
	$log->pushHandler(new StreamHandler('../logs/'.formatDay().'error.log', Logger::WARNING));
	$log->error($value);
}

function warning($value)
{
	$log = new Logger('name');
	$log->pushHandler(new StreamHandler('../logs/'.formatDay().'warning.log', Logger::WARNING));
	$log->warning($value);
}


function debug($type)
{	
	try {
		if(is_array($type)){
			print_r($type);
		}else if(is_string($type) || is_numeric($type)){
			echo $type;
		}else{
			var_dump($type);
		}
	}catch(Exception $e) {
		echo $e->getMessage();
	}
	die();
}

function now()
{
	return strtotime(date('Y-m-d H:i:s'));
}

function formatDay()
{
	return date('Y_m_d_');
}