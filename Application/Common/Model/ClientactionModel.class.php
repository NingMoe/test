<?php
namespace Common\Model;
use Think\Model;
class ClientactionModel extends Model{
	/**
	 * 添加或更新数据
	 * @param unknown $data
	 */
	public function addFresh($data){
		if(!$data){
			$this->error='传入数据空';
			return false;
		}
		if($data['id']){
			return $this->where('id='.$data['id'])->save($data);
		}else{
			return $this->add($data);
		}
	}
}