<?php
namespace Common\Model;
use Think\Model;
class PositionModel extends Model{
	public function addLocationDataFromOpenid($location,$openid){
		ee('addLocationDataFromOpenid openid:'.$openid);
		ee('addlocationdata:'.getoutstr($location));
	
		if (!$openid || !$location){
			$this->error='location data error';
			return false;
		}
		$cm=new ClientModel();
		$userId=$cm->getIdByOpenid($openid);ee('ttttttt3');
		ee('addlocationdata $userId:'.getoutstr($userId));
		
		if(!$userId){
			$this->error='没有该用户id';
			ee(' have no user openid:'.$openid);
			return false;
		}
		
		$location['client_id']=$userId;
		$location['createtime']=NOW_TIME;
		ee('addlocationdata:'.getoutstr($location));
		$ret=$this->add($location);
		return $ret;
	}
	public function getClientAllPositions($clientId){
		$this->field('latitude,longitude,createtime');
		$this->where('client_id='.$clientId);
		$ret=$this->select();
		return $ret;
	}
}