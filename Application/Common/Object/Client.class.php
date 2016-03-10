<?php
namespace Common\Object;
use Common\Model\ClientModel;
use Common\Config\Driving;
class Client{
	//客户类
	private $_drivingCurrentId;//当前形势证id
	private $_drivingAll;		//所有行驶证信息
	private $_baseInfo;			//用户基本信息
	public $_m;					//数据模型
	static private $_ClientObj; //实例对象
	private function __construct($userInfo,ClientModel $model){//构造返回无效    return null;
		$this->_drivingCurrentId=$userInfo['driving_id'];
		$this->_drivingAll=$userInfo['drivings'];
		$this->_baseInfo=array(
				'id'=>$userInfo['id'],
				'username'=>$userInfo['username'],
				'mobile'=>$userInfo['mobile'],
				'last_login_time'=>$userInfo['last_login_time'],
				'share_string'=>$userInfo['share_string'],
				'red_usable'=>$userInfo['red_usable'],
				'service_id'=>$userInfo['service_id'],
				'status'=>$userInfo['status'],
				'openid'=>$userInfo['openid']
		);
		$this->_m=$model;
	}
	public static function getInstance($param=null){
		if(!(self::$_ClientObj instanceof self)){
			$cm=new ClientModel();
			$userInfo=$cm->getCurUser();
// 			ee('clientinfo:'.getoutstr($userInfo));
			if($userInfo){
				self::$_ClientObj=new self($userInfo,$cm);
			}else{
				return null;//当前用户不存
			}
		}
		return self::$_ClientObj;
	}
	public function getModel(){
		return $this->_m;
	}
	public function getDrivingIdAll(){
		$result;
		foreach ($this->_drivingAll as $value){
			$result[]=$value['id'];
		}
		return $result;
	}
	/**
	 *  更新客户行驶证保单 客服处理进程状态
	 * @param Driving $status
	 */
	public function updateClientDrivingsStatus(Driving $status){
		$data['id']=$this->getClientId();
		$data['action_status']=$status;
		$this->_m->updateUserInfo($data);
	}
	public function getClientId(){
		return $this->_baseInfo['id'];
	}
	public function getCurrentDrivingId(){
		return $this->_drivingCurrentId;
	}
	public function getBaseInfo(){
		return $this->_baseInfo;
	}
	/**
	 * 得到所有行驶证信息
	 */
	public function getAllDrivings(){
		return $this->_drivingAll;
	}
	/**
	 * 得到行驶证显示信息
	 */
	public function getAllDrivingsView($drivingId=null){
		$result=array();
		foreach ($this->_drivingAll as $driving){
			$data['drivingid']=$driving['id'];
			$data['platenumber']=$driving['license_number'];
			$data['username']=$driving['owner'];
			$data['carinfo']=$driving['car_model'];
			$data['chejianumber']=$driving['vin'];
			$data['fadongjinumber']=$driving['engine_no'];
			$data['regtime']=$driving['register_time'];
// 			$data['bar_code']=$driving['bar_code'];
			$data['cankaoprize']=$driving['car_price'];
			$result[$data['drivingid']]=$data;  //注意
		}
// 		return array('chepailist'=>$result);
		return $result;
	}
	
	public function changeCurrentDrivingId($drivingId){
		if($drivingId==$this->getCurrentDrivingId()){
			//不用处理
			return true;
		}
		if($drivingId && in_array($drivingId, $this->getDrivingIdAll())){
			$data=array(
				'id'=>$this->getClientId(),
				'driving_id'=>$drivingId
			);
			ee('changeCurrentDrivingId data:'.getoutstr($data),'temp');
			$this->_m->addFresh($data);
		}
	}
	
	private function getClientModel(){
		echo 'getclient';
	}
	private function __set($propertyName,$value){
		$this->$propertyName=$value;
	}
	private function __get($propertyName){
		if(isset($this->$propertyName)){
			return $this->$propertyName;
		}else{
			return null;
		}
	}
	
}