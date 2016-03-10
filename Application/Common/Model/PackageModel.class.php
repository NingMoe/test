<?php
namespace Common\Model;
use Think\Model;
use Common\Config\Driving;
use Common\Object\SessionManage;
class PackageModel extends Model{
	/*
	 * 保存套餐数据
	 * return boolean
	 * 
	 */	
	public function addPackage($drivingId,$packageStr){
		if(!$drivingId || !$packageStr){
			$this->error="行驶证id或套餐数据错误";
			return false;
		}
		$packageType=SessionManage::getTempType();
		$packageId=$this->getPackageIdByDrivingId($drivingId);
		$packageOldType=$this->getPackageTypeByDrivingId($drivingId);
		if($packageId){
			//套餐已存，则转到更新操作
			if($this->updatePackage($packageId,$packageStr)){
// 				if($packageType!=$packageOldType){
					$mDriving=new DrivinglicenseModel();
					$dataDriving=array(
						'id'=>$drivingId,
						'update_time'=>time(),
						'action_status'=>Driving::$commitPackage,
						'package_style'=>$packageType);//套餐提交
					if(!$mDriving->addFreshDriving($dataDriving)){
						$this->error='update driving data fail';
					}
// 				}else {
					
// 				}
				return true;
			}else{
				return false;
			}
		}
		$data=array(
			'driving_id'=>$drivingId,
			'content'=>$packageStr,
			'createtime'=>time()
		);
		$retPackageId=$this->add($data);
		$mDriving=new DrivinglicenseModel();
		$dataDriving=array('id'=>$drivingId,
			'package_id'=>$retPackageId,
			'update_time'=>time(),
			'action_status'=>Driving::$commitPackage,
			'package_style'=>$packageType);//套餐提交
		if($mDriving->addFreshDriving($dataDriving)){
			return true;
		}else{
			return false;
		}
	}
	//更新保险套餐信息
	public function updatePackage($packageId,$packageStr){
		$data=array(
// 				'action_status'=>Driving::$commitPackage,
				'content'=>$packageStr,
				'createtime'=>time()
		);
		return $this->where("id=$packageId")->save($data);//save设置where;//成功返回int(1);
	}
	/**
	 * 
	 * @param unknown $packageId
	 * @return \Think\mixed
	 */
	public function getPackageInfoById($packageId){
		$ret=$this->where('id='.$packageId)->find();
		return $ret;
	}
	/**
	 * 根据行驶证id得到套餐内容
	 */
	public function getCurrentPackage($drivingId){
	ee('getCurrentPackage $$packageInfo2:','temp');
		$packageId=$this->getPackageIdByDrivingId($drivingId);
		ee('model getcp packageid:'.$packageId,'temp');
		if(!$packageId){
			$this->error='该行驶证套餐包数据有误或不存在';
			return false;
		}
		$map['id']=$packageId;
		$mPackage=$this->where($map)->find();
		return $mPackage['content'];
	}
	private function getPackageIdByDrivingId($drivingId){
		$mDriving=new DrivinglicenseModel();
		$driving=$mDriving->getDrivingInfoById($drivingId);
		return $driving['package_id'];
	}
	private function getPackageTypeByDrivingId($drivingId){
		$mDriving=new DrivinglicenseModel();
		$driving=$mDriving->getDrivingInfoById($drivingId);
		return $driving['package_style'];
	}
	/**
	 * 更具车id取到上一次保的套餐
	 */
	public function getLastYearPackage($drivingId){
		$this->join('yy_drivinglicense on yy_drivinglicense.last_package_id=yy_package.id');
		$this->where("yy_drivinglicense.id={$drivingId}");
		$result=$this->select();
		if(!$result['content']){
			$this->error='去年内容没有或出错';
			return false;
		}
		return $result;
	}
	/**
	 * 刷新套餐包时间
	 * @param unknown $packageId
	 * @return unknown
	 */
	public function freshTime($packageId){
		$this->where('id='.$packageId);
		$ret=$this->save(array('createtime'=>NOW_TIME));
		
		return $ret;
	}
}










