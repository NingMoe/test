<?php
namespace Common\Object;
use Admin\Model\ServiceactionModel;
class ServiceAction extends CommonAction{
	protected $loginOut=2;//登出
	protected $editCarInfo=3;//审核编辑车辆信息
	protected $createPriceSheet=4;//生成报价单
	//客服行为类
	public function __construct($userId){
		parent::__construct($userId);
		$this->m=new ServiceactionModel();
		$this->data['admin_id']=$this->userId;
	}
	/**
	 * 登出
	 */
	public function loginOut(){
		$this->data['actiontype']=$this->loginOut;
		$this->fresh();
	}
	
	/**
	 * 编辑审核车辆信息
	 */
	public function editCarInfo(){
		$this->data['actiontype']=$this->editCarInfo;
		$this->fresh();
	}
	
	/**
	 * 生成报价单
	 */
	public function createPriceSheet(){
		$this->data['actiontype']=$this->createPriceSheet;
		$this->fresh();
	}
}