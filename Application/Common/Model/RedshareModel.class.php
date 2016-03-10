<?php
namespace Common\Model;
use Think\Model;
use Common\Lib\Tools;
class RedshareModel extends Model{
	public function addFresh($data){
		if($data['id']){
			//todo update
			
		}else{

			$result=$this->add($data);
		}
		return $result;
	}
	/**
	 * 根据用户id得到红包 已分享的用户列表 的信息。
	 */
	public function getShared($clientId){
		if(!is_numeric($clientId)){
			$this->error='用户id不正确';
			return false;
		}
// 		$this->join('yy_client on yy_client.id=yy_redshare.client_id');
		$this->where('client_id='.$clientId);
		$this->field('amount,nickname,headimgurl,createtime as time');
		$this->order('createtime desc');
		$result=$this->select();
		if(!$result){
			$this->error='没有分享数据';
		}
		foreach ($result as $k=>$value){
			$result[$k]['time']=Tools::timeFormat($value['time']);
		}
		return $result;
	}
	
	public function getTopList($start=0,$len=10){
		$this->field('sum(amount) as sharetotal,client_id,yy_client.headimgurl,yy_client.nickname,max(createtime) as time');
		$this->join('yy_client on yy_client.id=yy_redshare.client_id');
		$this->group('yy_client.id');
		$this->order('sharetotal desc');
		$result=$this->limit($start,$len)->select();
		return $result;
	}
	
}