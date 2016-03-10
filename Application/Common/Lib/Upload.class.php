<?php
namespace Common\Lib;
class Upload{
// 	protected $_isMulti=false;//标识是否多文件//本身都是数组
	protected $_fileInfos;
	protected $_savePath='Uploads/';
	protected $_error;
	protected $_errNoMsg=array(
			1=>"上传文件不存在",2=>"文件不合法"
	);
	public function __construct($savePath=''){//unset($_FILES['first']);var_dump($_FILES);exit;
		if(''!=$savePath){
			$this->_savePath=$savePath;
		}
		 $this->_fileInfos=$_FILES;
		 ee('__construct up file info:'.getoutstr($this->_fileInfos),'temp');
		 unset($_FILES);
// 		 //是否多文件数组
// 		 if(!$this->_fileInfo['name']){
// 		 	$this->_isMulti=true;
// 		 }
	}
	
	protected function uploadFile(){
		//空时empty-true，isset-true,if($_)-false
		if(!$this->isUploadFilesExist()){
			$this->_error="上传文件不存在";
			return -1;
		}
		//文件合法检查
		if(!$this->isValid()){		
// 			$this->_error="文件不合法";   //覆盖掉内部详细信息了。
			return -2;
		}
		
		//后面需要没return 也不行
		return true;
	}
	public function isUploadFilesExist(){
		ee('isUploadFilesExist up file info:'.getoutstr($this->_fileInfos),'temp');
		if(empty($this->_fileInfos) || !$this->_fileInfos){ ee('isUploadFilesExist上传传文件不存在 ','temp');
			return false;
		}
		return true;
	}
	public function unsetUpFile($keyName){
		unset($this->_fileInfos[$keyName]);
	}
	/**
	 *@ 得到文件名数组的md5和sha1值，以文件名分组
	 */
	public function getMd5AndSha1(){
		$result=array();
		foreach ($this->_fileInfos as $key=>$fileInfo){
			$result[$key]['md5']=md5_file($fileInfo['tmp_name']);
			$result[$key]['sha1']=sha1_file($fileInfo['tmp_name']);
		}
		return $result;
	}
	/**
	 * 移动上传的文件到指定目录
	 * return 移动的文件地址列表
	 */
	protected function moveFile(){
		$result=array();
		foreach ($this->_fileInfos as $key=>$fileInfo){
			$destination=$this->getUpPath().$this->getUniName().'.'.$this->getExt($fileInfo);
			if(@move_uploaded_file($fileInfo['tmp_name'],$destination)){
				$this->_error="文件上传成功";
				$result[$key]=array(
						'path'=>$destination,
						'name'=>$fileInfo['name'],
						'create_time'=>time(),
						'md5'=>md5_file($destination),
						'sha1'=>sha1_file($destination)
				);
			}else{
				$this->_error='文件上传失败';
				return false;
			}
		}
		return $result;
	}
	protected function getExt($fileInfo){
		return strtolower(pathinfo($fileInfo['name'],PATHINFO_EXTENSION));
	}
	/*
	 * 移动上传的文件到指定目录
	 */
	protected function copyFile(){
		//copy($src,$dst):将文件拷贝到指定目录，拷贝成功返回true,否则返回false
		$result=array();
		foreach ($this->_fileInfos as $key=>$fileInfo){
			$destination=$this->getUpPath().$this->getUniName().'.'.$this->getExt($fileInfo);
			if(copy($fileInfo['tmp_name'],$destination)){
				$this->_error="文件上传成功";
				$result[$key]=array(
						'path'=>$destination,
						'name'=>$fileInfo['name'],
						'create_time'=>time(),
						'md5'=>md5_file($destination),
						'sha1'=>sha1_file($destination)
				);
			}else{
				$this->_error='文件上传失败';
				return false;
			}
		}
		return $result;
	}
	protected function getUpPath(){
		if(!file_exists($this->_savePath)){
			mkdir($path,0777,true);
			chmod($path,0777);
		}
		return $this->_savePath;
	}
	public function getLastError(){
		return $this->_error;
	}
	public function getErrNoMsg(){
		return $this->_errNoMsg;
	}
	//子类根据需要实现
	protected function isValid(){
		return true;
	}
	/**
	 * 产生唯一字符串
	 * @return string
	 */
	protected function getUniName(){
		return md5(uniqid(microtime(true),true));
	}
}