<?php
namespace Common\Lib;
class Yupload extends Upload{
	protected  $maxSize=2190000;//允许的最大值
	protected $_allowExt=array('jpeg','jpg','png','gif');
	protected $_savePath='Uploads/Picture/';
	
	/**
	 * 上传文件，返回文件信息
	 * @see \Common\Lib\Upload::uploadFile()
	 */
	public function uploadFile(){

		$ret=parent::uploadFile();
		//todu upfile
// 		ini_set('upload_max_filesize','3M');//没用
		ee('ini:'.ini_get('upload_max_filesize'),'temp');
		if($ret<0 || $ret==0){//检验没通过，返回信息
			ee('yup,ret err no:'.$ret.'--error:'.$this->_error,'temp');
			return false;
		}
		return $this->moveFile();
	}

	/*
	 * 检查文件合法性
	 */
	protected function isValid(){
		foreach ($this->_fileInfos as $fileInfo){
			//判断上传文件的大小
			if($fileInfo['size']>$this->maxSize){
				ee('filesize:'.$fileInfo['size'],'temp');
				$this->_error='上传文件过大';
				return false;
			}
			if(!in_array($this->getExt($fileInfo),$this->_allowExt)){
				ee('filetype:'.$this->getExt($fileInfo),'temp');
				$this->_error='非法文件类型';
				return false;
			}
			//判断文件是否是通过HTTP POST方式上传来的
			if(!is_uploaded_file($fileInfo['tmp_name'])){
				$this->_error='文件不是通过HTTP POST方式上传来的';
				return false;
			}
			
			$error=$fileInfo['error'];
			//2.判断下错误号，只有为0或者是UPLOAD_ERR_OK，没有错误发生，上传成功
			if($error==UPLOAD_ERR_OK){
				
			}else{
				//匹配错误信息
				switch($error){
					case 1:
						$this->_error='上传文件超过了PHP配置文件中upload_max_filesize选项的值';
						return false;;
					case 2:
						$this->_error='超过了表单MAX_FILE_SIZE限制的大小';
						return false;;
					case 3:
						$this->_error='文件部分被上传';
						return false;;
					case 4:
						$this->_error='没有选择上传文件';
						return false;;
					case 6:
						$this->_error='没有找到临时目录';
						return false;;
					case 7:
					case 8:
						$this->_error= '系统错误';
						return false;;
				}
			}//end else
			
		}//end foreach 

		return true;
	}
}