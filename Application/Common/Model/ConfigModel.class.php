<?php
namespace Common\Model;
use Think\Model;
class ConfigModel extends Model{
	public function getConfig(){
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
