<?php
namespace Common\Model;
use Think\Model;
class SortModel extends Model{
	public function getSortInfoById($sortId){
		if(!$sortId){
			die('sortid error');
		}
		$this->where('id='.$sortId);
		
		return $this->find();
	}
	
	public function getSortContentById($sortId){
		$ret=$this->getSortInfoById($sortId);
		return $ret['content'];
	}
	/**
	 * 添加或更新数据局
	 * @param unknown $data
	 */
	public function addFresh($data){
		if(!$data){
			die('empty data');
		}
		if($data['id']){
			return $this->where('id='.$data['id'])->save($data);
		}
		
		return $this->add($data);
	}
}