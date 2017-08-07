<?php

namespace Lemon;

class Validation
{
	private $rules;
	private $data;
	public $success = true;
	public $errors = [];
	private $reasons = [];

	public function __construct($data, $rules){

		$this->data = $data;
    	$this->rules = $rules;
    	$this->reasons = $this->str();
    	$this->inspect();
	}


	private function inspect()
	{
		foreach ($this->rules as $attribute => $rule)
		{
			$anotherName = $attr = $attribute;
			$attributes = explode(':', $attribute);
			if(count($attributes) > 1) {
				$anotherName = $attributes[1];
				$attr = $attributes[0];
			}
			
			foreach (explode('|', $rule) as $key) {
				$colon = explode(':', $key);
				if(count($colon) > 1) {
					$action = (string)$colon[0];
					if(!isset($this->data[$attr])){
						$this->errors[] = str_replace(':attribute', $anotherName, $this->reasons['required']);
						break;
					} else {
						$reason = $this->$action($this->data[$attr], $colon[1]);
					}
					
				} else {
					
					if(!isset($this->data[$attr])){
						$this->errors[] = str_replace(':attribute', $anotherName, $this->reasons['required']);
						break;

					} else {
						$reason = $this->$key($this->data[$attr]);
					}
				}

				if ( $reason !== true ) {
		        	$this->errors[] = str_replace(':attribute', $anotherName, $reason);
		        	break;
		        }
			}
		}

		if(count($this->errors)) {
	    	$this->success = false;
	    }
	}

	//['required','unique','min:6','max:9','alpha','alpha_numeric','numeric','integer','email']
	
	private function str()
	{
		return [
			'email'     => ':attribute 格式不可用',
			'min'       => ':attribute 长度必须大于或等于 :min',
			'max'       => ':attribute 长度必须小于 :max.',
			'required'  => ':attribute 是必填项',
			'numeric'   => ':attribute 必须为数字',
			'integer'   => ':attribute 必须为整数',
			'alpha'     => ':attribute 必须仅包含字母字符', 
			'alpha_dash'=> ':attribute 必须仅包含字母、数字、破折号',
			'alpha_num' => ':attribute 必须仅包含字母、数字',
			'url'       => ':attribute 格式不可用',
			'ip'        => ':attribute 格式不可用'
		];
	}

	/* 基本验证*/
	protected function required($value)
	{
		if($value == 0 || $value == '0') {
			return true;
		}
		return !$value ? $this->reasons['required'] : true;
	}

	protected function min($value, $min)
	{
		return mb_strlen($value, 'UTF-8') >= $min ? true : str_replace(':min', $min, $this->reasons['min']);
	}

	/*  
	* 字符转长度 
	* @prarm $value string
	* @prarm $max   int
	*/
	protected function max($value, $max)
	{
		return mb_strlen($value, 'UTF-8') <= $max ? true : str_replace(':max', $max, $this->reasons['max']);
	}

	/* 数字和数字字符串 包括整数、负数、小数
	* @prarm $value string
	*/
	protected function numeric($value)
	{
		return is_numeric($value) ? true : $this->reasons['numeric'];
	}
	/* 整型 包括正整数、负整数*/
	protected function integer($value)
	{
		return filter_var($value, FILTER_VALIDATE_INT) !== false ? true : $this->reasons['integer'];
	}

	/* 邮箱 */
	protected function email($value)
	{
		return filter_var($value, FILTER_VALIDATE_EMAIL) ? true : $this->reasons['email'];
	}

	/* url */
	protected function url($value)
	{
		return filter_var($value, FILTER_VALIDATE_URL) ? true : $this->reasons['url'];
	}

	/* ip */
	protected function ip($value)
	{
		return filter_var($value, FILTER_VALIDATE_IP) ? true : $this->reasons['ip'];
	}

	/*验证字段值是否仅包含字母字符。*/
	protected function alpha($value)
	{
		//匹配由26个英文字母组成的字符串 
		return preg_match("/^[A-Za-z]+$/", $value) === 1 ? true : $this->reasons['alpha_num'];
	}
	/* 验证字段值是否仅包含字母、数字*/
	protected function alpha_num($value)
	{
		//匹配由数字和26个英文字母组成的字符串 
		return preg_match("/^[A-Za-z0-9]+$/", $value) === 1 ? true : $this->reasons['alpha_num'];
	}
	
	/* 验证字段值是否仅包含字母、数字、破折号（ - ）*/
	protected function alpha_dash($value)
	{
		return preg_match("/^[-A-Za-z0-9]+$/", $value) === 1 ? true : $this->reasons['alpha_dash'];
	}
	
	public function __call($method, $parameters)
	{
		throw new \UnexpectedValueException("Validate rule [$method] does not exist!");
	}
}