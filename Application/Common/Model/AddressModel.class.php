<?php
namespace Common\Model;
use Think\Model;
class AddressModel extends Model{
	//用户地址模型
	/**
	 * 得到用户所有地址信息
	 * @param unknown $clientId
	 * @return \Think\mixed
	 */
	public function getAllAddress($clientId){
		$this->where(array('client_id'=>$clientId));
		$ret=$this->select();
		if(!$ret){
			$this->error='没有地址信息';
		}
		return $ret;
	}
	/**
	 * 添加或更新地址信息
	 * 
	 */
	public function addFresh($data){
		if($data['id']){
			//todo update
			return $this->save($data);
		}else{
			return $this->add($data);
		}
	}
}