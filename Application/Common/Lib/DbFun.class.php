<?php
namespace Common\Lib;
use Common\Config\Driving;
use Common\Model\DrivinglicenseModel;
class DbFun{
	//数据库通用操作函数
	/**
	 * 更新行驶证处理状态
	 * @param unknown $drivingId
	 * @param Driving $actionStatus
	 * @return Ambigous <boolean, unknown>
	 */
	static function updateDrivingActionStatus($drivingId,Driving $actionStatus){
		$m=new DrivinglicenseModel();
		$data['id']=$drivingId;
		$data['action_status']=$actionStatus;
		return $m->addFreshDriving($data);
	}
	
	
}