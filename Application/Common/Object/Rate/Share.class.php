<?php
namespace Common\Object\Rate;
use Common\Lib\Tools;
class Share{
	private $_shmId;
	private $_shmSize;//实际大小
	private $_shmKey;
	private static $_shareList;	//共享对象列表
	private static $_shmKeyList=array(
			'serviceInfo'=>array(1024,100),
			'commonRate'=>array(1025,100)
	);
	private function __construct($shareName){
		$this->_shmKey=self::$_shmKeyList[$shareName][0];
		$this->_shmSize=self::$_shmKeyList[$shareName][1];
		$this->_shmId=shmop_open($this->_shmKey, 'c', 0644, $this->_shmSize);
	}
	
	public static function getInstance($shareName){
		//只能从configlist里构造固定的对象
		if(!Tools::isValid(self::$_shmKeyList[$shareName][0])){
			return false;
		}
		if(!(self::$_sharelist[$shareName] instanceof self)){
			self::$_sharelist[$shareName]=new self($shareName);
		}
		return self::$_sharelist[$shareName];
	}
	//读取对象，size读取大小
	public function getData($size=0){
// 		if(!Tools::isValid(self::$_shmKeyList[$shareName][0])){
// 			return false;
// 		}
		if(0==$size){
			$size=$this->_shmSize;
		}
		$data=shmop_read($this->_shmId, 0, $size);
		return $data;
	}
	public function setData($data,$startPos=0){
		if(is_array($data) && Tools::isValid($data)){
			$data=serialize($data);
		}
		$retSize=shmop_write($shm_id,$data,$startPos);
	}
	public function getSerializeData($size=0){
		return unserialize($this->getData($size));
	}
	public function deleteData(){
		return shmop_delete($this->_shmId);
	}
	private function ftok($filename = "", $proj = "")
	{
		if( empty($filename) || !file_exists($filename) )
		{
			return -1;
		}
		else
		{
			$filename = $filename . (string) $proj;
			for($key = array(); sizeof($key) < strlen($filename); $key[] = ord(substr($filename, sizeof($key), 1)));
			return dechex(array_sum($key));
		}
	}
}