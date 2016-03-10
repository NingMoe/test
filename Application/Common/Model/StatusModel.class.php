<?php
namespace Common\Model;
use Think\Model;
class StatusModel extends Model{
	public function getStatus(){
		return $this->select();
	}
	/**
	 * 刷新keyname的value值
	 * @param unknown $keyName
	 * @param unknown $value
	 */
	public function freshKey($keyName,$value){
		return $this->where("key=$keyName")->save(array('value'=>$value));
	}
}