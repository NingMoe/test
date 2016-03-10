<?php
namespace Common\Model;
use Think\Model;
class RedModel extends Model{
	/**
	 * 添加或更新红包信息
	 */
	public function addFresh($data){
		if($data['id']){
			$this->where('id='.$data[id]);
			//todo 红包数据
			
			
			
			return $this->save($data);
		}else{
			return $this->add($data);
		}
	}
	/**
	 * 通过报价单号id得到红包信息
	 */
	public function getRedByPriceSheetId($priceSheetId){
		if(!$priceSheetId){
			$this->error='报价单id空或不存在';
			return false;
		}
		$this->field('yy_red.id,size,cut_down,yy_red.status');
		$this->join('yy_pricesheet on yy_pricesheet.red_id=yy_red.id');
		$this->where("yy_pricesheet.id=$priceSheetId");
		$result=$this->select();
		if(!$result){
			$this->error='该报价单红包信息不存在';
			return false;
		}
		return $result;
	}
}