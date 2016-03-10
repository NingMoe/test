<?php
namespace Common\Lib;
class Yfile{
	
	
	
// 	private $_img;
	public function __construct(){
// 		$this->_img=new \Think\Image();
	}
	
	public function getImgInfo($fileName){
		return getimagesize($filename);
	}
	
	public function upLoadFile(){
		

		
	}
	
	

	

	/**
	 * 得到文件扩展名
	 * @param string $filename
	 * @return string
	 */
	function getExt($filename){
		return strtolower(pathinfo($filename,PATHINFO_EXTENSION));
	}
	
	/**
	 * 产生唯一字符串
	 * @return string
	 */
	function getUniName(){
		return md5(uniqid(microtime(true),true));
	}
	
	function getFrontFormValidHtml(){
		return $str=<<<EOF
			<form action="doAction3.php" method="post" enctype="multipart/form-data">
			<!-- <input type="hidden" name="MAX_FILE_SIZE" value='176942' /> -->
			请选择您要上传的文件：<input type="file" name='myFile' />
			<!-- <input type="file" name="myFile"  accept="image/jpeg,image/gif,image/png"/><br /> -->
			<input type="submit" value="上传文件" />
			</form>
EOF;
		
	}

// 	array_filter
//  array_value
}