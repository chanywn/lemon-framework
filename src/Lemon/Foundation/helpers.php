<?php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;


date_default_timezone_set('Asia/Shanghai');


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


/**
 * 简单对称加密算法之加密
 * @param String $string 需要加密的字串
 * @param String $skey 加密EKY
 * @return String
 */
function passEncode($string = '', $skey = 'TyXo7l20c') {
    $strArr = str_split(base64_encode($string));
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key < $strCount && $strArr[$key].=$value;
    return str_replace(array('=', '+', '/'), array('AqC', 'xxD', 'e3R'), join('', $strArr));
}


/**
 * 简单对称加密算法之解密
 * @param String $string 需要解密的字串
 * @param String $skey 解密KEY
 * @return String
 */
function passDecode($string = '', $skey = 'TyXo7l20c') {
    $strArr = str_split(str_replace(array('AqC', 'xxD', 'e3R'), array('=', '+', '/'), $string), 2);
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key <= $strCount  && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
    return base64_decode(join('', $strArr));
}