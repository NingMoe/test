<?php
namespace Api\Sms;
class Code{
	private $_min=1000;
	private $_max=9999;
	public static  $_expire=30;//有效期，单位秒
	private $_num;
	private $_birthTime;
	public function get(){
		return $this->_num;
	}
	public function __construct(){
		$this->_num=rand($this->_min,$this->_max);
		$this->_birthTime=time();
	}
	public function getExpire(){
		return $this->_expire;
	}
	public function getBirth(){
		return $this->_birthTime;
	}
}