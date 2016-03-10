<?php
namespace Home\Controller;
use Common\Object\Package;
use Common\Model\PackageModel;
use Common\Lib\Tools;
use Common\Config\Interaction;
use Common\Model\DrivinglicenseModel;
use Common\Object\SessionManage;
class PackageController extends MainController{
	private $_drivingId;
	protected function _initialize(){
		parent::_initialize();
		if(!$this->_client->getCurrentDrivingId()){
			$this->error('请先上传行驶证',U('Upload/upDriving'),1);
			exit;
		}
		$this->m=new PackageModel();
		
		$this->_drivingId=$this->_client->getCurrentDrivingId();
		$carId=I('get.carid');
		if($carId){
			$this->_drivingId=$carId;
		}
	}
	
	public function index(){
		$this->display();
	}
	
	/*
	 * 和去年一样
	 */
	public function lastYear(){
		SessionManage::setTempType(2);
		$ret=$this->m->getLastYearPackage($this->_client->getCurrentDrivingId());
		ee('lastYear ret:'.getoutstr($ret),'temp');
		if(!$ret || !is_string($ret)){
			$this->outJson(array('lastyear'=>'去年数据不存在'),1);
		}
		$packageStr=Tools::parseSqlStore($ret);
		$this->m->addPackage($this->_drivingId, $packageStr);
	}
	
	/*
	 * 自定义套餐
	 */
	public function diy(){
		ee("ceshi get:".getoutstr($_GET),'temp');
		ee("ceshi post:".getoutstr($_POST),'temp');
// 		SessionManage::setTempType(1);
		if(IS_POST){
			if($this->submitPackage()){
				$this->success('保存套餐成功,客服将尽快审核报价',U('index/index'));
			}else{
				echo 'error:'.$mPackage->getError();
			}
			
		}else{
			$this->display('selectPolicy');
		}
	}
	
	/**
	 * 提交套餐数据
	 */
	public function submitPackage(){
		$package=new Package();
		$result=$this->m->addPackage($this->_drivingId,$package->getStoreString());
		return $result;		
	}
	
	/*
	 * 通过问题搭配
	 */
	public function helpMe(){
// 		$result=$this->createPackageByHelpData($data);
// 		ee('result1:'.getoutstr($result),'temp');
		
// 		$result=$this->convertPackageDataToFront($retdata);
// 		ee('result2:'.getoutstr($result),'temp');
		
		SessionManage::setTempType(3);
		ee('getPost:'.getoutstr($_POST),'temp');
		if(IS_POST){
			if($_POST['step']){
				//answer1,答案1,2,3
				Tools::getFormData($_POST);
			}else{
				$data=Tools::getFormData($_POST);
				//最后一步处理数据，返回套餐包，修改套餐形式
				$result=array();
				unset($data['step']);
				ee('$data:'.getoutstr($data),'temp');
				
				$result=$this->createPackageByHelpData($data);
				ee('result1:'.getoutstr($result),'temp');
				
// 				$result=$this->convertPackageDataToFront($result);
// 				ee('result2:'.getoutstr($result),'temp');
				
				$this->contentName='packageInfo';
				$this->outJson($result);
			}
		
		}else{
// 			$this->display();
		}
	}

   private function convertPackageDataToFront($data){
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
   
   private function convertPackageDataToFront2($data){
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
	 * 取得车辆的当前套餐包id
	 * @param unknown $drivingId
	 */
	private function getPackageIdByDrivingId($drivingId){
		$md=new DrivinglicenseModel();
		$carInfo=$md->getDrivingInfoById($drivingId);
		return $carInfo['package_id'];
	}
	/**
	 * 从help数据包生成套餐包
	 */
	public function createPackageByHelpData($data=NULL){
		$data=array(
				'answer1'=>1,'answer2'=>2,'answer3'=>3,'answer4'=>1,'answer5'=>2,'answer6'=>3,'answer7'=>1,
		);
		
// 		$convertPackagePostData=$this->convertToPackagePostDataFromHelpData($data);
		
// 		ct($convertPackagePostData);
		$convertPackagePostData=$this->convertToPackageStyleFromHelpDataNew($data);
		return $convertPackagePostData;
		
		
// 		$package=new Package($convertPackagePostData);
// 		$str=$package->getStoreString();
// 		$arr=Tools::parseSqlStore($str,1);
// 		return $arr;
	}
	private function convertToPackageStyleFromHelpDataNew($data){
		$pa=array(1=>'dsfzr',2=>'sjzwzr',3=>'hh',4=>'ckzwzr',5=>'bl');
		$result=array();
		foreach ($pa as $k=>$v){
			$field='answer'.$k;
			$sel=$data[$field];
			$temp=null;
			if($sel){
				$temp['_isSelected']=true;
				$temp['_level']=$this->getAnswer($k,$sel);
			}
			if(5==$k){
				$temp['_isSelected']=true;
				$v='zd';
			}
			if($temp!=null){
				$temp['_noPay']=false;
				$result[$v]=$temp;
			}
		}
	
		$sel6=$data['answer6'];
		$sel7=$data['answer7'];
		if($sel6 && $sel7){
			//todo update database
	
		}
		return $result;
	
	
	}
	private function convertToPackageStyleFromHelpData($data){
		$pa=array(1=>'dsfzr',2=>'sjzwzr',3=>'hh',4=>'ckzwzr',5=>'bl');
		$result=array();
		foreach ($pa as $k=>$v){
			$field='answer'.$k;
			$sel=$data[$field];
			$temp=null;
			if($sel){
				$temp[$v]=true;
				$temp[$v.'_level']=$this->getAnswer($k,$sel);
			}
			if(5==$k){
				$temp['zd']=true;
				$v='zd';
			}
			if($temp!=null){
				$result[$v]=$temp;
			}
		}
		
		$sel6=$data['answer6'];
		$sel7=$data['answer7'];
		if($sel6 && $sel7){
			//todo update database
				
		}
		return $temp;
		
		
	}
	private function convertToPackagePostDataFromHelpData($data){
		$pa=array(1=>'dsfzr',2=>'sjzwzr',3=>'hh',4=>'ckzwzr',5=>'bl');
		$result=array();
		foreach ($pa as $k=>$v){
			$field='answer'.$k;
			$sel=$data[$field];
			if($sel){
				$result[$v]='on';
				$result[$v.'_level']=$this->getAnswer($k,$sel);
			}
			if(5==$k){
				$result['zd']='on'; //制定专修勾选
			}
		}
		$sel6=$data['answer6'];
		$sel7=$data['answer7'];
		if($sel6 && $sel7){
			//todo update database
			
		}
		return $result;
	}
	/**
	 * 得到问题对应答案
	 * @param  $type  问题
	 * $sel 选择
	 */
	private function getAnswer($type=1,$sel){
		if(!is_numeric($sel)){
			die('选项不正确');
		}
		$result=null;
		switch ($type){
			case 1:
				if($sel==3){
					$result=4;//id
				}else{
					$result=8-$sel;
				}
				break;
			case 2:
				if(3==$sel){
					$result=2;
				}else{
					$result=6-$sel;
				}
				break;
			case 3:
				$result=4-$sel;
				break;
			case 4:
				if(3==$sel){
					$result=2;
				}else{
					$result=6-$sel;
				}
				break;
			case 5:
				$result=3-$sel;
				break;
			default:	
				$result=$sel;//默认返回原值，6,7项
				break;
		}
		
		return $result;
	}
	/**
	 * 得到当前套餐包数据
	 * 用户失效后重选套餐
	 */
	//Home/package/getcurrentpackage/carid/2
	public function getCurrentPackage(){
		$drivingId=I('post.carid');
		ee('getCurrentPackage $drivingId:'.$drivingId,'temp');
		$this->_client->changeCurrentDrivingId($drivingId);
// 		$drivingId=$this->_client->getCurrentDrivingId();   //测试用
		if(!$drivingId){
			$this->outStatusByJson(Interaction::$invalidId);//id无效  车辆
			return ;
		}
		// 		$packageId=$this->getPackageIdByDrivingId($drivingId);
	
		$packageInfo=$this->m->getCurrentPackage($drivingId); 		//var_dump($packageInfo);
		// 		$this->ajaxEcho($packageInfo);
	ee('getCurrentPackage $$packageInfosss:'.$packageInfo,'temp');
		$packageInfo=Tools::parseSqlStore($packageInfo,1);
		
		$result=$this->convertPackageDataToFront($packageInfo);
		ee('getCurrentPackage $$packageInfo:'.getoutstr($packageInfo),'temp');
		ee('getCurrentPackage $$packageInfo $result:'.getoutstr($result),'temp');
		$this->contentName='packageInfo';
		$this->outJson($packageInfo);
	}
	
	
	
}





