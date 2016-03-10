<?php
namespace Common\Model;
use Think\Model;
class RemarkModel extends Model{
	public function addFresh($data){
		$result=false;
		if($data['id']){
			$map=array('id'=>$data['id']);
			return $result=$this->where($map)->save($data);
		}
		//没有新增
		$drivingId=$data['carid'];
		$content=$data['remarkContent'];
		
		if(!$drivingId){
			$this->error='对应车辆id不存在';
			return false;
		}
		
		$retId=$this->add(array('driving_id'=>$drivingId,'content'=>$content,'createtime'=>time()));
		if(!$retId){
			$this->error='新增留言出错';
			return false;
		}
		
		return $retId;
// 		$addData=array(
// 			'id'=>$drivingId,
// 			'remark_id'=>$retId,
// 			'update_time'=>NOW_TIME
// 		);
// 		$md=new DrivinglicenseModel();
// 		$dret=$md->addFreshDriving($addData);
// 		return $dret;
	}
	public function getContentById($remarkId){
		$info=$this->getInfoById($remarkId);
		return $info['content'];
	}
	public function getInfoById($remarkId){
		$ret=$this->where(array('id'=>$remarkId))->find();
		return $ret;
	}
	public function getList($drivingId){
		if (!$drivingId){
			$this->error='remark getlist drivingid error';
			return false;
		}
		$this->where(array('driving_id'=>$drivingId));
		$ret=$this->select();
		return $ret;
	}
}