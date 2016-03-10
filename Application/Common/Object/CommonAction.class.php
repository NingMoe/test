<?php
namespace Common\Object;
class CommonAction{
	//行为公共类
	protected $m;//对应数据模型类,取数据
	protected $userId;//标识对应用户
	protected $data;//要更新的数据
	
	protected $login=1;//登录

	public function __construct($userId){
		if(!$userId){
			die('用户id不存在');
		}
		
		$this->userId=$userId;
		$this->data['createtime']=NOW_TIME;
	}
	
	public function login(){
		$this->data['actiontype']=$this->login;
		$this->fresh();
	}
	/**
	 * 更新数据
	 */
	protected function fresh(){
		$this->m->addFresh($this->data);
	}
}