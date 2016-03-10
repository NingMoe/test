<?php
namespace Admin\Controller;

use Common\Object\Package;
use Common\Model\PackageModel;

class PackageController extends AdminController{
	private $_drivingId;
	protected function _initialize(){
		parent::_initialize();

		$this->pm=new PackageModel();

		$carId=I('get.carid');
		if(!$carId){
			$carId=I('post.carid');
		}
		if(!$carId){
			die('carid error');
		}
		$this->_drivingId=$carId;
	}
	

	
	/*
	 * 自定义套餐
	 */
	public function diy(){
		ee("ceshi get:".getoutstr($_GET),'temp');
		ee("ceshi post:".getoutstr($_POST),'temp');
// 		SessionManage::setTempType(1);
		if(IS_POST){
			if($this->submitPackage()){
				$this->success('保存套餐成功');
			}else{
				echo 'error:'.$this->pm->getError();
			}
			
		}
	}
	
	/**
	 * 提交套餐数据
	 */
	public function submitPackage(){
		$package=new Package();
		$result=$this->pm->addPackage($this->_drivingId,$package->getStoreString());
		return $result;		
	}
	

	
}





