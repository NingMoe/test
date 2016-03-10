<?php
namespace Common\Model;
use Think\Model;
class CtokenModel extends Model{
	public function getInfoByAppId($appId){
		if(!$appId){
			$this->error='appid error';
			return false;
		}
		$this->where(array('appId'=>$appId));
		return $this->find();
	}
}