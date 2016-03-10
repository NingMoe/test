<?php
namespace Common\Object;
class Package{
	//默认保单套餐类
	public $_cate;
	
// 	public $cc;//车船险
// 	public $jq;//交强险

	public function __construct($data=null){
		$this->_cate=new Cate();
		$this->_cate->setInsure();
		
		$this->setUpData($data);
	}
	
	private function setUpData($data=null){
		if(is_string($data)){
			$data=$this->deString($data);
		}
		if(null==$data){
			if(IS_POST){
				$data=I('post.');
				unset($_POST);
			}else{
				echo 'error 数据空';
				exit;
			}
		}
		ee('data:'.var_export($data,true),'temp');
		$fields=$this->_cate->getFields();
		//交强险处理
		//交强车船不设，后面强制设置
		// 		unset($field['jq']);
		// 		unset($field['cc']);
			
		if(is_array($data)){
			$this->setArrayData($fields, $data);
		}elseif (is_object($data)){//对象时处理
			$this->setObjectData($fields, $data);
		}else{
			echo '数据错误';
			exit;
		}
		
// 		$this->setMustJqCc();
	}
	
	private function setArrayData($fields,$data){
		foreach ($fields as $fieldName){ ///ee($fieldName.'---for','temp');  
			if(isset($data[$fieldName])){ /// ee('fieldname:'.$fieldName.'='.$this->isChecked($data[$fieldName]),'temp');
				$this->_cate->$fieldName->_isSelected=$this->isChecked($data[$fieldName]);//            $this->isChecked($data[$fieldName]);
				$this->_cate->$fieldName->_level=$this->selectedValue($data[$fieldName.'_level']);
				$this->_cate->$fieldName->_noPay=$this->isChecked($data[$fieldName.'_nopay']);
			}
		}
		ee('cate:'.var_export($this->_cate,1),'temp');
	}
	/*
	 * 从保存的对象构造
	 */
	private function setObjectData($fields,$data){ 
		foreach ($fields as $fieldName){
			if(isset($data->$fieldName)){
				$this->_cate->$fieldName->_isSelected=$data->$fieldName->_isSelected;
				$this->_cate->$fieldName->_level=$data->$fieldName->_level;
				$this->_cate->$fieldName->_noPay=$data->$fieldName->_noPay;
			}
		}
	}
	public function getCate(){
		return $this->_cate;
	}
	/*
	 * 解析存储字符串
	 */
	public function ParseFromStore($str){
		return $this->deString($str);
	}
	/*
	 * 得到存储字符串
	 *///存储cate内容，用cate内容构造   ,不存this了
	public function getStoreString(){
		return $this->enString($this->_cate);
	}
	
	private function enString($obj){
		return json_encode($obj);
// 		return serialize($obj);
	}
	private function deString($str){
		return json_decode($str);
// 		return unserialize($str);
	}

	private function isChecked($name){
		return 'on'==$name?true:false;
// 		if('on'==$name){
// 			return true;
// 		}
// 		return false;
	}
	private function selectedValue($name){
// 		return isset($name)?$name:0;//没有保额，对应0     //isset为true， 也可以

		return $name;//设置过有值，没设置null   //return  $name all null

	}
	
	/*
	 * 设置交强险和车船税  因为必选强制设置
	 */
// 	private function setMustJqCc(){
// 		$this->jq=new Insure('jq',true);
// 		$this->cc=new Insure('cc',true);
// 	}
}