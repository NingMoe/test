<?php
namespace Common\Object;
use Common\Model\ClientactionModel;
class ClientAction extends CommonAction{
	protected $position=2;//位置
	//客户行为类
	public function __construct($userId){
		parent::__construct($userId);
		$this->m=new ClientactionModel();
		$this->data['client_id']=$this->userId;
	}
	
	public function position(){
		$this->data['actiontype']=$this->position;
		$this->data['value']='';
		$this->fresh();
	}
	
	
}