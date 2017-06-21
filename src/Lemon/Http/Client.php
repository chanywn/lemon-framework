<?php
/**
 * @Author    chanywn
 * @DateTime  2017-06-15
 * @license   MIT
 * @version   0.0.1
 */
namespace Lemon\Http;

class Client {
    
    /**
     * 发送地址
     * @var String
     */
    protected $_url;
    
    /**
     * 发送方式
     * @var String
     */
    protected $_type;

    /**
     * Request Header
     * @var Array
     */
    protected $_header;

    /**
     * Form data
     * @var Array
     */
    protected $_data;

    public function __construct(){
    	$this->_data = '';
    }
    
    /**
     * @param  String
     * @return Void
     */
    public function url($_url){
    	$this->_url = $url;
    }

    /**
     * @param  String
     * @return Void
     */
    public function type($_type){
    	$this->_type = strtolower($_type);
    }

    /**
     * @param  Array
     * @return Void
     */
    public function header($_header)
    {
    	$this->_header = $_header;
    }

    /**
     * @param  Array
     * @return Void
     */
    public function data($_data)
    {
    	$this->_data = $_data;
    }

    /**
     * @return String
     */
    public function send()
    {
    	if(empty($this->_url)){
    		return;
    	}

    	$ch = curl_init();
		
		$this->setopt();
		
		$output = curl_exec($ch);
		curl_close($ch);

		return $output;
    }

    protected function setopt()
    {
    	curl_setopt($ch, CURLOPT_URL, $this->_url);
		if(!empty($this->_header))
			curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_header);
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);
		if(!empty($this->_type) && $this->_type === 'post'){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_data);
		}
    }

}