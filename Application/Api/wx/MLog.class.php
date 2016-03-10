<?php
namespace Api\wx;
class MLog{
	private static $_instance;//单例
	private $_path;//日志目录
	private $_pid;//进程id
	private $_handler;//文件fd
	/**
	*构造函数
	*@param $path 日志对象对应的日志目录
	*/
	function __construct($path){
		$this->_path=$path;
		$this->_pid=getmypid();
	}
	private function _clone(){}
	//单例函数
	public static function instance($path='/temp/'){
		if(!(self::$_instance instanceof self)){
			self::$_instance=new self($path);
		}
		return self::$_instance;
	}
	/**
	*根据文件名获取文件fd
	*@param $fileName 文件名
	*@param 文件fd
	*/
	private function  getHandler($fileName){
		if(@$this->_handler){
			return $this->_handler;
		}
		date_default_timezone_set('PRC');

		$handle=fopen($this->_path . $fileName,'a');
		$this->_handler=$handle;
		return $handle;
	}
	/**
	*向文件中写日志
	*@param $fileName 文件名
	*@param $message 消息
	*/
	public function log($fileName,$message){
		$handle=$this->getHandler($fileName);
		//根据当前时间并进行格式化
		$nowTime=time();
		$logPreffix=date('Y-m-d H:i:s',$nowTime);
		//写文件
		fwrite($handle,"[$logPreffix][$this->_pid]$message\n");
		return true;
	}
		// 析构函数，关闭所有fd
	function __destruct() {
		if ($this->_handler) {
			fclose ( $this->_handler );
		}
	}
}
?>
