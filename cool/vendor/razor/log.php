<?php
//错误信息
ini_set('display_errors',1);  
//php启动错误信息 
ini_set('display_startup_errors',1); 
//打印出所有的 错误信息     
error_reporting(-1);                     
ini_set('error_log', dirname(__FILE__) . '/../../logs/'.date("Y-m-d").'_error_log.txt');

session_start();