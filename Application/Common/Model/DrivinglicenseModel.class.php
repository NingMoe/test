<?php
namespace Common\Model;
use Think\Model;
use Common\Lib\Tools;
use Common\Lib\Yydate;
use Common\Object\CarRateInput;
use Common\Object\Package;
use Common\Object\PriceSheet;
use Common\Config\Driving;
class DrivinglicenseModel extends Model{
	/**
	 * 得到制定行驶证id的当前订单信息 
	 * @param unknown $drivingId
	 */
	public function getOrderInfoFromDrivingId($drivingId){
		if(!$drivingId){
			$this->error='传人id不正确';
			return -5;//id不正确
		}
		$this->field('number,yy_order.updatetime,yy_order.status,pay_type,pay_amount,red_amount,amount,content');
		$this->join('yy_pricesheet on yy_pricesheet.id=yy_drivinglicense.pricesheet_id');
		$this->join('yy_order on yy_order.id=yy_pricesheet.order_id');
		$this->where('yy_drivinglicense.id='.$drivingId);
		$ret=$this->find();
		
		return $ret;
	}
	/**
	 * 得到指定用户的所有行驶证
	 */
	public function getDrivings($userId){
		$ret=$this->field(true)->where("client_id=$userId")->select();
		if(Tools::isValid($ret)){
			return $ret;
		}else{
			return null;
		}
	}
	public function getjqAdnCc($drivingId){
		$ret=$this->getDrivingInfoById($drivingId);
		if(!$ret['jq_price'] || !$ret['jq_price']){
			$this->error='交强或车船税未设置或不正确';
			return false;
		}
		$result=array(
			'jq'=>$ret['jq_price'],
			'cc'=>$ret['cc_price'],
		);
		return $result;
	}
	/**
	 *从drivingid得到行驶证所有信息
	 */
	public function getDrivingInfoById($drivingId){
		$this->field($this->myFields());
		$this->join('yy_client on yy_client.id=yy_drivinglicense.client_id');
		
		return $this->where("yy_drivinglicense.id=$drivingId")->find();
	}
	/**
	 * 得到id车辆的车牌号
	 * @param unknown $drivingId
	 */
	public  function getCarNum($drivingId){
		$ret=$this->getDrivingInfoById($drivingId);
		
		return $ret['license_number'];
	}
	/**
	 * 根据车id得到电话
	 */
	public function getMobileByDrivingId($drivingId){
		$this->join('yy_client on yy_drivinglicense.client_id=yy_client.id');
		$this->where('yy_drivinglicense.id='.$drivingId);
		$result=$this->find();
		return $result['mobile'];
	}
	/**
	 * 得到联合客户信息
	 * @param unknown $drivingId
	 * @return \Think\mixed
	 */
	public function getUnionClientInfoById($drivingId){
		$this->join('yy_client on yy_drivinglicense.client_id=yy_client.id');
		$this->where('yy_drivinglicense.id='.$drivingId);
		$result=$this->find();
		return $result;
	}
	/**
	 * 得到后台编辑的车辆信息
	 * @param unknown $drivingId
	 */
	public function getEditDirvingInfo($drivingId){
		if(!$drivingId){
			die('车辆id有误');
		}
		$this->field($this->myFields());
		$this->join('yy_client on yy_client.id=yy_drivinglicense.client_id');
// 		$this->join('yy_remark on yy_remark.id=yy_drivinglicense.remark_id');  //！！！！空时没数据
		$this->where('yy_drivinglicense.id='.$drivingId);
		$result=$this->find();
		
		$result['reg_time']=Tools::timeFormat2($result['reg_time']);
		$result['register_time']=Tools::timeFormat2($result['register_time']);
		if($result['remark_id']){
			$result['remark_content']=$this->getRemarkContentById($result['remark_id']);
		}

		return $result;
	}
	public function getRemarkContentById($remarkId){
		if (!$remarkId){
			die('留言id有误');
		}
		$rm=new RemarkModel();
		$content=$rm->getContentById($remarkId);
		return $content;
	}
	
	/**
	 * service
	 * 得到 客服管理 的所有行驶证信息
	 */
	public function getDrivingList($admin,$page,$limit,$map){
		if(!$admin['id']){
			die('管理信息错误');
		}
		$this->field($this->myFields());
		$this->join('yy_client on yy_client.id=yy_drivinglicense.client_id');
		if($admin['groupId']!=1){ //管理员可看所有    //!== 不行， 类型不符？
			$map['service_id']=$admin['id'];
		}
		

		$this->where($map);
		$this->order('yy_drivinglicense.update_time desc');
		$this->page($page,$limit);
		$result=$this->select();

		foreach ($result as $key=>$value){
			$result[$key]['reg_time']=Tools::timeFormat2($value['reg_time']);
			$result[$key]['register_time']=Tools::timeFormat2($value['register_time']);
		}
		
		$this->join('yy_client on yy_client.id=yy_drivinglicense.client_id');
		$allNum=$this->where($map)->count();  //后面，否则字段减少 why
		return Array('num'=>$allNum,'list'=>$result);
		
	}
	/**
	 * service
	 * 得到 客服管理 的所有行驶证信息
	 */
	public function getClientDrivings($admin){
		if(!$admin['id']){
			die('管理信息错误');
		}
		$this->field($this->myFields());
		$this->join('yy_client on yy_client.id=yy_drivinglicense.client_id');
		
		if($admin['groupId']!=1){ //管理员可看所有    //!== 不行， 类型不符？
			$this->where("yy_client.service_id={$admin['id']}");
		}
		
		$this->order('id desc');
		
		$result=$this->select();
		foreach ($result as $key=>$value){
			$result[$key]['reg_time']=Tools::timeFormat2($value['reg_time']);
			$result[$key]['register_time']=Tools::timeFormat2($value['register_time']);
		}
		return $result;
	}
	/**
	 * 添加或更新车辆信息
	 * @param unknown $data
	 * @param string $clientId
	 * @return Ambigous <boolean, unknown>|boolean
	 */
	public function addFreshDriving($data,$clientId=null){
		
		ee('addFreshDriving:'.getoutstr($data).'--clientid:'.$clientId,'temp');
		
		$data['update_time']=NOW_TIME;
		if($data['id']){//id存在为更新  行驶证
			$ret=$this->fresh($data);
			if($ret){//更新成功   改变状态
				//行驶证已上传或更新
// 				$this->changeUpDrivingStatus($data['id']);	//  不用了，放数据里
			}
			return $ret;
		}
		if(null===$clientId){
			$this->error='用户id为空不能添加行驶证信息';
			return false;
		}
		$data['client_id']=$clientId;
		$data['license_number']='待询价';
		//添加行驶证
		$retDrivingId=$this->add($data);
		if(!$retDrivingId){
			$this->error='添加行驶证信息失败';
			return false;
		}else{
			//添加行驶证成功  更新状态
// 			$this->changeUpDrivingStatus($retDrivingId);   //  不用了，放数据里
		}
		//更新用户信息
		$clientData['driving_id']=$retDrivingId;
		$clientData['id']=$clientId;
		if(!$this->freshUserInfo($clientData)){
			return false;
		}
		return $retDrivingId;
	}
	/**  
	 * !!只上传一张
	 * 更改已上传状态
	 * @param unknown $drivingId
	 * @return \Common\Model\Ambigous
	 */
	private function changeUpDrivingStatus($drivingId){
		return Driving::changeStatus($drivingId, Driving::$upDriving);
	}
	private function freshUserInfo($data){
		$mc=new ClientModel();
		$ret=$mc->updateUserInfo($data);
		if(!$ret){
			$this->error='用户信息更新失败,'.$mc->getError();
		}
		return $ret;
	}
	/**
	 * edit 提交
	 * @data 要更新的数据，需要带id
	 */
	public function fresh($data){
		ee('fresh iniii:','temp');
	
		if(!$data['id']){
			$this->error="更新id不存在";
			return false;
		}
		
		if(isset($data['register_time'])&& 0!=$data['register_time']){
// 			$datetime=new \DateTime($data['register_time']);  //   timedate 格式  
			$data['register_time']=strtotime($data['register_time']);//年用4位
			ee('date:'.date('y-m-d',$data['register_time']),'temp');
		}
		$data['update_time']=time();
		ee('1111id:'.$data['id'],'temp');
		ee('11 create price save car data:'.getoutstr($data),'temp');
		$ret=$this->where(array('id'=>$data['id']))->save($data);//'id='.$data['id'];
		ee('11 create price ret:'.getoutstr($ret),'temp');
		ee('11 create price ret geterror:'.$this->getError(),'temp');
		if(!$ret){
			$this->error="更新数据失败fresh112";
			//return true;//
		}else {
			
		}

		return $ret;
	}
	
	private function myFields(){
		return "username,mobile,yy_drivinglicense.id,client_id,cartype_id,license_number,owner,car_model,vin,engine_no,
				register_time,bar_code,first_id,second_id,package_id,last_package_id,pricesheet_id,car_year,
				car_price,discount,jq_price,cc_price,zd_price,passenger_num,service_id,nickname,wid,province,city,reg_time,
				remark_id,insure_companyid,expire,action_status,firstcard_id,secondcard_id,sort_id,promotion_id";		
	}
	
	/**
	 * 生成报价单
	 */
	public function createPrice($drivingId){
		
		$info=$this->getDrivingInfoById($drivingId);
	
		$carRateInfo=$this->getCarRateInfo($drivingId,$info);
		if(!$carRateInfo){
			return false;
		}

		//生成新的报价类目单
		$priceData=$this->createNewPriceCate($drivingId,$carRateInfo);
		if(!$priceData){
			return false;
		}
		
		ee('pricesheetid:'.$info['pricesheet_id']);
		$ps=new PricesheetModel();
		if($info['pricesheet_id']){
			//已有报价单，则更新报价单
			$update=$ps->addSheet(Tools::getSqlStore($priceData['priceCate']),$info['pricesheet_id']);
			if(!$update){
				$this->error="报价单更新失败 suberror:".$ps->getError();
				return false;
			}
		}else{//增加报价单
			$retId=$ps->addSheet(Tools::getSqlStore($priceData['priceCate']),null,$drivingId);
			if(!$retId){
				$this->error="数据表添加报价单失败";
				return false;
			}
			$data['pricesheet_id']=$retId;
		}
		//更新行驶证表，报价单或总价信息
		$data['id']=$drivingId;
		//报价单总价
		$data['pricesheet_count']=$priceData['priceCount'];

		return $this->fresh($data);
	}
	/**
	 * 生成新的报价单
	 * 返回分类价钱和总价
	 */
	private function createNewPriceCate($drivingId,$carRateInfo){
		//取得套餐包存储数据;
		$mPackage=new PackageModel();
		$ret=$mPackage->getCurrentPackage($drivingId);
		if(!$ret){
			$this->error='套餐包数据有误';
			return false;
		}
		//构造套餐包对象
		$package=new Package($ret);
		
		$priceSheet=new PriceSheet($carRateInfo,$package);
		$priceCate=$priceSheet->getPriceSheet();
		$priceCount=$priceSheet->getPriceCount();
		ee('pricecate:'.$priceCate.'--pricecount:'.$priceCount,'temp');
		if(!$priceCate || !$priceCount){
//			$this->error='生成报价或总价出错';
//			return false;
		}
		return array('priceCate'=>$priceCate,'priceCount'=>$priceCount);
	}
	
	private function getCarRateInfo($drivingId,$info=null){
		if(null===$info){
			$info=$this->getDrivingInfoById($drivingId);
		}
		if(!$info){
			$this->error='行驶证信息不存在';
			return false;
		}//!$info['car_year'] ||  //
		if(!$info['cartype_id']||!$info['car_price']||!$info['register_time']||!$info['discount']||!$info['passenger_num']){
			$this->error='请补充信息:车型 ,车龄,车身价 ,注册日期,出险折扣,乘客座位数';
			return false;
		}
		ee('----------passenger_num:'.$info['passenger_num']);
		$registeredMonth=Yydate::getInstance()->getMonths($info['register_time']);//已经注册月份
		
		$carRateInfo=new CarRateInput($info['cartype_id'], $info['car_price'], $info['car_year'], $registeredMonth,(float)$info['discount'],$info['passenger_num']);
		if(!$carRateInfo){
			$this->error='车型 ,车龄,车身价 ,注册日期,出险折扣 ,乘客座位 信息有误';
			return false;
		}
		return $carRateInfo;
	}
	
	/**
	 * 得到行驶证状态分组
	 *
	 */
	public function getDrivingStatusGroup($ids,$statuss){//但字符串条件则只支持一次
// 		var_dump($ids);var_dump($statuss);
		$map['action_status']=array('in',$statuss);
		$map['service_id']=array('in',$ids);
		$this->field('action_status,yy_drivinglicense.id,yy_client.service_id,count(yy_client.service_id) as servicenum');//field字段不区分大小写
		$this->group('yy_client.service_id');
		$this->join('yy_client on yy_client.driving_id=yy_drivinglicense.id');
		$this->where($map);
		$result=$this->select();
		
		return $result;
	}
	/**
	 * 搜索 用户信息
	 * 通过手机号，订单号，
	 */
	public function searchClientDrivingInfo($searchStr=null){
		$searchStr=trim(I('post.search'));
		$len=strlen($searchStr);
		if(11==$len){//手机号
			$where['mobile']=$searchStr;
			$this->join('yy_client on yy_client.id=yy_drivinglicense.client_id');
		}elseif (12==$len){//订单号
			$where['yy_order.number']=$searchStr;
			$this->join('yy_pricesheet on yy_pricesheet.driving_id=yy_drivinglicense.id');
			$this->join('yy_order on yy_order.id=yy_pricesheet.order_id');
		}
		$this->field($this->myFields());
		$this->where($where);
		$result=$this->select();
		if(!$result){
			$this->error='信息有误';
		}
		return $result;
	}
	public function changeCurrentDrivingId($drivingId){
		$drivingInfo=$this->getDrivingInfoById($drivingId);
		$clientId=$drivingInfo['client_id'];
		if(!$clientId){
			die('用户或车辆信息有误');
		}
		$cm=new ClientModel();
		$cm->addFresh(array('id'=>$clientId,'driving_id'=>$drivingId));
	}
	
}

