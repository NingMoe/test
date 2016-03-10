<?php
namespace Home\Controller;
use Common\Model\DrivinglicenseModel;
use Common\Model\OrderModel;
use Think\Controller;
use Common\Model\PictureModel;
use Common\Config\Driving;
use Common\Object\SessionManage;
class UploadController extends MainController{
	protected function _initialize(){
		parent::_initialize();
		$this->m=new PictureModel();
	}
	public function index(){
		$this->upDriving();
	}
	public function setAddCar(){
		SessionManage::setAdd();
		$this->ajaxEcho('{success:true}');
	}
	
	/**
	 * 上传行驶证
	 */
	public function upDriving(){
		if(IS_POST){
// 			$pm=new PictureModel();
			if(!$this->m->upfile($this->_client)){  //_drivingCurrentId  还不在   //if(!$pm->upfile($this->_client)){ 
				$this->ajaxEcho('{success:false}');
				$this->ko('上传错误:'.$this->m->getError(),3);
			}else{
				
				$this->success('上传成功',U('package/index'),1);
			}
		}else{
			$this->display('updriving');
		}
	}
	/**
	 * 上传身份证
	 */
	public function upIdentityCard(){
		if(IS_POST){
			if(!$this->m->upIdentityCard($this->_client)){  
				$this->ko('上传错误:'.$this->m->getError(),3);
			}else{
				$this->success('上传成功',null,1);
			}
		}else{
			$this->display('upidentitycard');
		}
	}
	//得到上传身份证信息，切换车辆
	public function toUpCard(){
//		$drivingId=I('get.carid');
//		if(!$drivingId){
			$orderNum=I('get.orderNum');
			$m0=new OrderModel();
			$drivingId=$m0->getDrivingIdByOrderNum($orderNum);
//		}
		if(!$drivingId){
			die('car id error');
		}
		//切换车辆id
		$this->_client->changeCurrentDrivingId($drivingId);
	}
}