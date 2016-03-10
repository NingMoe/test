<?php
namespace Admin\Controller;
use Admin\Model\AdminModel;
use Common\Config\Driving;
use Common\Model\ClientModel;
use Common\Object\Package;
use Common\Model\PackageModel;
use Common\Lib\Tools;
class PortController extends AdminController{
	//返回最后次请求至今的时间
	public function newServiceNumber(){
		$am=new AdminModel();
		$aInfo=$am->getCurrentAdminAllInfo();
		if(!$aInfo){
			die('客服信息error');
			return false;
		}
		$lastGetTime=$aInfo['lastget_time'];
		if(!$lastGetTime){
			$lastGetTime=0;
		}
		
		$mc=new ClientModel();
		$newGetTime=NOW_TIME;
		$map['yy_drivinglicense.update_time']=array(array('egt',$lastGetTime),array('lt',$newGetTime),'and');
		$map['service_id']=$aInfo['id'];
		$map['action_status']=Driving::$commitPackage;
//		ee('lasttime:'.$lastGetTime.'-new time:'.$newGetTime,'temp');
		$mc->join('yy_drivinglicense on yy_drivinglicense.client_id=yy_client.id');
		$ret=$mc->where($map)->select();
//		$am->freshLastGetTime($newGetTime);
		if(!$ret){
			$result=0;
		}else{
			$result=count($ret);
		}

		$this->outJson(array('num'=>$result));
	}
	
	public function getLastGetTime($id=null){
		
	}
	/**
	 * 提交套餐数据
	 */
	public function submitPackage($dirvingId){
		$package=new Package();
		$pm=new PackageModel();
		$result=$pm->addPackage($dirvingId,$package->getStoreString());
		return $result;
	}
	/*
	 * 自定义套餐
	*/
	public function diy(){
		ee("port diy ceshi get:".getoutstr($_GET),'temp');
		ee("port diy ceshi post:".getoutstr($_POST),'temp');
		// 		SessionManage::setTempType(1);
		if(IS_POST){
			$drivingId=I('post.carid');
			if(!$drivingId){
				$this->ko('车id错误');
			}
			if($this->submitPackage($drivingId)){
				$this->success('保存套餐成功,客服将尽快审核报价',U('Driving/edit',array('id'=>$drivingId)),1);
			}else{
				$this->ko('提交套餐失败');
			}
				
		}else{
			$this->ko('please post data');
		}
	}
	public static function convertPackageDataToFront($data){
		$result=array();
		foreach ($data as $k=>$v){
			$result[$k]=0;
			$result[$k.'_noPay']=0;
			if ($v['_isSelected']){
				$result[$k]=1;
			}
			if($v['_noPay']){
				$result[$k.'_noPay']=1;
			}
			$result[$k.'_level']=$v['_level'];
		}
		return $result;
	}
	/**
	 * 得到当前套餐包数据
	 * 用户失效后重选套餐
	 */
	//Home/package/getcurrentpackage/carid/2
	public static function getCurrentPackage($drivingId){
		if(!$drivingId){

		}
		ee('port getCurrentPackage $drivingId:'.$drivingId,'temp');
		$mp=new PackageModel();
		$packageInfo=$mp->getCurrentPackage($drivingId); 		//var_dump($packageInfo);

		ee('port getCurrentPackage $$packageInfosss:'.$packageInfo,'temp');
		if($packageInfo){
			$packageInfo=Tools::parseSqlStore($packageInfo,1);
			$result=self::convertPackageDataToFront($packageInfo);
		}else {
			$result=null;
		}
		
// 		ee('port getCurrentPackage $$packageInfo:'.getoutstr($packageInfo),'temp');
// 		ee('port getCurrentPackage $$packageInfo $result:'.getoutstr($result),'temp');
// 		$this->contentName='packageInfo';
// 		$this->outJson($packageInfo);
		
		return $result;
	}
}