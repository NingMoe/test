<?php
namespace Admin\Controller;
use Admin\Model\AdminModel;
use Common\Model\DrivinglicenseModel;
use Api\Sms\SmsManager;
use Common\Lib\Golbal;
use Common\Config\Driving;
use Common\Config\Interaction;
use Common\Object\Rate\RateMath;
use Common\Model\CompanyModel;
use Common\Lib\Tools;
use Common\Model\SortModel;
use Common\Model\PositionModel;
use Common\Model\OrderModel;
use Common\Model\ClientModel;
use Common\Model\RemarkModel;
use Api\wx\tpl\TplPriceFail;
class DrivingController extends AdminController{
	//行驶证信息控制器
	private $companyList;
	protected function _initialize(){
		parent::_initialize();
		$this->_m=new DrivinglicenseModel();

		$starr=$this->getStatusArr();
		$this->assign('statusFields',$starr);

		ee('DrivingController post:'.getoutstr($_POST),'temp');
		ee('DrivingController get:'.getoutstr($_GET),'temp');
	}
	
	/**
	 * 所有分配用户行驶证列表
	 */
	public function allAllot(){
		$allDriving=$this->_m->getClientDrivings($this->_admin);
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
	private function getTimeField($mapData){
		if(isset($mapData['regdata'])){
			$result='register_time';
		}
		if(isset($mapData['status'])){
			$result='';
		}
		if(isset($mapData['mobile'])){
			$result='';
		}
		if(isset($mapData['platenumber'])){
			$result='';
		}
		if(isset($mapData['froms'])){
			$result='register_time';
		}
		return $result;
	}
	private function getSqlField($mapData){
		$result='';
		if(isset($mapData['regdata'])){
			$result='register_time';
		}
		if(isset($mapData['status'])){
			$result='action_status';
		}
		if(isset($mapData['mobile'])){
			$result='mobile';
		}
		if(isset($mapData['platenumber'])){
			$result='license_number';
		}
		if(isset($mapData['froms'])){
			$result='origin';
		}
		return $result;
	}
	private function getMap($mapData){
		$map=array();
		ee('mapdata:'.getoutstr($mapData),'temp');
		//regdata    status   mobile platenumber    froms
		//fromdata    todata
		$timeField=$this->getTimeField($mapData);
		$sqlField=$this->getSqlField($mapData);
		ee('$timeField:'.$timeField.'--$sqlField'.$sqlField,'temp');
		if(''!=$mapData['fromdata'] && ''!=$mapData['todata']){
			$start=strtotime($mapData['fromdata']);
			$end=strtotime($mapData['todata']);
			ee('$start:'.$start.'--$end'.$end,'temp');
			if(''!=$timeField){
				$map[$timeField]  = array(array('egt',$start),array('elt',$end),'and');
			}
			
		}
		if(isset($mapData['texts']) && ''!=$mapData['texts'] && ''!=$sqlField){
			$map[$sqlField]=$mapData['texts'];
		}
		
		ee('search map:'.getoutstr($map),'temp');
		return $map;
	}
	private function doIndexList(){
		$mapData=I('post.');
		$page=I('get.p');
		$limit=I('get.limit');
		if(!$page){
			$page=1;
		}
		if(!$limit){
			$limit=10;
		}
		$map=$this->getMap($mapData);

// 		$allDriving=$this->_m->getClientDrivings($this->_admin);
		$allDriving=$this->_m->getDrivingList($this->_admin,$page,$limit,$map);
		$this->assign('alld',$allDriving['list']);
// 		ct($allDriving);
		$pageObj=new \Think\Page($allDriving['num'],$limit);
		foreach ($map as $k=>$v){
			$pageObj->parameter[$k]=urlencode($v);
		}
		$pageObj->setConfig('header','共 条记录');
		$pageObj->setConfig('prev','上一页');
		$pageObj->setConfig('next','下一页');
		$pageObj->setConfig('header','个行驶证');
		$show=$pageObj->show();
		$this->assign('page',$show);
	}
	public function index(){
		$this->doIndexList();
		$fields=array(
				'reg_time'=>'注册时间 ','mobile'=>'电话','nickname'=>'微信昵称','wid'=>'微信ID','owner'=>'车主','license_number'=>'车牌号',
				'register_time'=>'行驶证注册日期	','expire'=>'商业险到期日','action_status'=>'状态','province'=>'省份',
				'city'=>'城市','insure_companyid'=>'保险公司','service_id'=>'归属人','remark_id'=>'备注','promotion_id'=>'推广员'
		);
		$am=new AdminModel();
		$am->freshLastGetTime();
		$this->assign('fields',$fields);
		$this->display();
	}
// 	$fields=array(
// 			'username'=>'客户名','mobile'=>'电话','id'=>'行驶证编号','license_number'=>'行驶证号',
// 			'owner'=>'车主','car_model'=>'品牌型号','vin'=>'vin识别号','engine_no'=>'发动机号','register_time'=>'注册日期',
// 			'bar_code'=>'副页条码','package_id'=>'套餐单号','pricesheet_id'=>'报价单号',
// 			'cartype_id'=>'车辆类型','car_year'=>'车龄','passenger_num'=>'乘客座位数','car_price'=>'车身价','discount'=>'出险折扣',
// 			'jq_price'=>'交强险价','cc_price'=>'车船税价'
// 	);
	public function edit(){
		if(IS_POST){//提交更新

			$ret=$this->doPostData();

			if($ret){
				$this->success('更新成功',null,1);
			}else{
				$this->error('更新失败,错误信息:'.$this->_m->getError(),null,1);
			}
		}else{//url get 
			
			$drivingId=I('get.id');
			if(!$drivingId){
				$this->error('行驶证编号不正确');
			}
			$drivingInfo=$this->_m->getEditDirvingInfo($drivingId);
			ee('dirvingInfo:'.getoutstr($drivingInfo),'temp');
			//四张图
			$fourImg=array(
					$this->getImg($drivingInfo['first_id']),
					$this->getImg($drivingInfo['second_id']),
					$this->getImg($drivingInfo['firstcard_id']),
					$this->getImg($drivingInfo['secondcard_id'])
			);
			
			$priceInfoFields=array('license_number'=>'车牌号','passenger_num'=>'乘客座位数','car_price'=>'车身价','car_year'=>'车龄',
					'register_time'=>'行驶证注册日期','jq_price_dis'=>'交强险折扣','cc_price'=>'车船税价','zd_price'=>'指定专修价');
			$carInfoFields=array('reg_time'=>'客户注册日期','owner'=>'车主','license_number'=>'车牌号','vin'=>'vin识别号',
					'car_model'=>'品牌型号','engine_no'=>'发动机号','service_id'=>'客服id','mobile'=>'电话','nickname'=>'微信昵称',
					'wid'=>'微信ID','action_status'=>'状态'
			);
			$orderFields=array(
					'updatetime'=>'订购日期','number'=>'订单号','status'=>'是否付款成功','pay_type'=>'付款方式',
					'pay_amount'=>'付款金额','red_amount'=>'红包金额','amount'=>'订单原价'
			);

			$orderInfo=$this->getCurrentOrderByDrivingId($drivingId);
			ee(__LINE__.'-line-orderinfo:'.getoutstr($orderInfo),'temp');
			$orderContent=$this->getOrderfList($orderInfo);
			ee('order content:'.getoutstr($orderContent),'temp');
			
			if($drivingInfo['sort_id']){
				$comInfo=$this->getSortInfo($drivingInfo['sort_id']);
			}else {
				$comInfo=$this->getCompanyList();
			}
			ee('reg time1:'.$drivingInfo['register_time'],'temp');
			ee('reg time2:'.$drivingInfo['register_time'],'temp');
			
			$packageInfo=PortController::getCurrentPackage($drivingId);
			$remark=RemarkController::getList($drivingId);
//ct($packageInfo);

			$this->assign('package',$packageInfo);
			$this->assign('remark',$remark);
			$this->assign('orderFields',$orderFields);
			$this->assign('orderInfo',$orderInfo);
			$this->assign('ordercontent',$orderContent);
			$this->assign('comInfo',$comInfo);//公司信息
			$this->assign('info',$drivingInfo);
			$this->assign('priceInfoFields',$priceInfoFields);
			$this->assign('carInfoFields',$carInfoFields);
			$this->assign('fourImg',$fourImg);
			$this->display('demo');
		}
	}
	private function getStatusArr(){
		return array(
				Driving::$drivingInvalid=>'行驶证无效',
				Driving::$upDriving=>'行驶证上传',
				Driving::$auditInfomation=>'报价信息已审核',
				Driving::$packageInvalid=>'套餐无效',
				Driving::$commitPackage=>'提交套餐',
				Driving::$priceOrOrderFailure=>'报价或订单 失效',
				Driving::$createPrice=>'报价完成',
				Driving::$createOrderContent=>'生成未支付订单',
				Driving::$paySuccess=>'订单发起支付请求',
				Driving::$paySuccess=>'订单已支付成功',
				Driving::$insureEffect=>'保单生效',
				Driving::$pastDue=>'保单过期'
		);
	}
	private function getOrderfList($orderInfo){
		if(!$orderInfo || !$orderInfo['content']){
			return null;
		}
		$orderContent=Tools::parseSqlStore($orderInfo['content'],1);
		if(2==$orderInfo['status']){
			$amount=$orderInfo['pay_amount'];
		}else{
			$amount=$orderInfo['amount'];
		}
		$temp=$amount-$orderContent['jq_price']-$orderContent['cc_price'];
		$rate=$temp / $orderContent['catePrice'];
		$orderContent['rate']=round($rate,3);
		$orderContent['finalAmount']=$amount;
		foreach ($orderContent['cateList'] as $key=>$item){
			$orderContent['cateList'][$key]['name']=$item['name'];
			$orderContent['cateList'][$key]['disPrice']=round($item['price']*$rate,2);
			$orderContent['cateList'][$key]['disNoPayPrice']=round($item['noPayPrice']*$rate,2);
		}
		return $orderContent;
	}
	public function createPrice($id=NULL){
		
		$drivingId=I('post.id');
		if(!$drivingId && $id!=null){
			$drivingId=$id;
		}
		if(!$drivingId){
			$this->ko('error空id');
		}else{
			$drivingInfo=$this->_m->getDrivingInfoById($drivingId);
			ee('createPrice $drivingInfo:'.getoutstr($drivingInfo),'temp');
			if(!$drivingInfo['package_id']){
				$this->ko('报价单生成失败，用户未选择套餐包数据');
				exit;
			}
			if($this->_m->createPrice($drivingId)){
			ee('$create suceccccccc:','temp');
				//更新状态
				Driving::changeStatus($drivingId, Driving::$createPrice);
				$sms=SmsManager::getobj();
				$sms->createPriceMessage($this->_m->getCarNum($drivingId));
				$sms->sendSms($this->_m->getMobileByDrivingId($drivingId));
				$this->ok('报价单生成成功！');
			}else{
				$this->ko('报价单生成失败,错误信息:'.$this->_m->getError(),3);
			}
		}
	}
	private function doPostData(){
		$data=I('post.');
		if(!$data){
			$this->outStatusByJson(Interaction::$emptyData);
		}
		if(!$data['carid']){
			$this->outStatusByJson(Interaction::$invalidId);
		}
		if(!$data['type']){
			$this->outStatusByJson(Interaction::$errorData);
		}
		if($data['type']==1){
			$ret=$this->getPostPriceData($data);
			$result=$this->_m->fresh($ret);
			$this->createPrice($data['carid']);
		}elseif ($data['type']==2){
			$ret=$this->getPostDrivingData($data);
			ee('getPostDrivingData $ret:'.getoutstr($ret),'temp');
			$result=$this->freshDrivingAndClient($ret);
		}elseif ($data['type']==3){
			$ret=$this->getPostOrderData($data);
			ee('getPostOrderData $ret:'.getoutstr($ret),'temp');
			$result=$this->freshOrder($ret);
		}elseif ($data['type']==4){
			$ret=$this->doPostRemark($data);
			
		}
		return $result;
	}
	public function doPostRemark($data){
		$drivingId=$data['carid'];
		$remarkId=$data['remark_id'];
		$content=$data['remarkContent'];

		$mdata=array(
				'id'=>$data['remark_id'],
				'carId'=>$data['carid'],
				'remarkContent'=>$data['remarkContent']
		);
		$result=array();
		$rm=new RemarkModel();
		$result=$rm->addFresh($mdata);
		return $result;
	}
	
	private function freshDrivingAndClient($data){
		if(!$data){
			die('data error');
		}
		$mc=new ClientModel();
		$ret1=$mc->addFresh($data);
		ee('freshuser ret:'.getoutstr($ret1).'--error:'.$mc->getError(),'temp');
		$data['id']=$data['carid'];
		$result=$this->_m->fresh($data);
		if($result){//更新车辆信息成功
			//报价失败 模板提醒
			if(isset($data['action_status'])){
				$msg='';
				if (Driving::$drivingInvalid==$data['action_status']){
					$msg='行驶证无效';
				}elseif (Driving::$packageInvalid==$data['action_status']){
					$msg='套餐无效';
				}
				if(''!=$msg){
					//每次提交都有当前的车牌信息
					$number=$data['license_number'];
					$openid=$mc->getOpenIdByMobile($data['mobile']);
					if($openid){
						$notify=new TplPriceFail($openid);
						$needData=$notify->createNeedData($number, $msg, Tools::timeFormatS(NOW_TIME));
						$notify->sendNotify($needData);
					}	
				}
			}
		}
		return $result;
	}
	private function freshOrder($data){
		//todo fresh orderinfo
		if(!$data){
			die('data error');
		}
		$mo=new OrderModel();
		return $mo->addFresh($data);
	}
	/**
	 * 取到订单数据
	 */
	private function getPostOrderData($data){
		$result=$data;
// 		$result['id']=$data['carid'];
		return $result;
	}
	/**
	 * 取到报价数据
	 * data 包含carid need
	 */
	private function getPostPriceData($data){
		$result=array();
		$result['id']=$data['carid'];
		$result['license_number']=$data['license_number'];
		$result['passenger_num']=$data['passenger_num'];
		$result['car_price']=$data['car_price'];
		$result['car_year']=$data['car_year'];
		$result['register_time']=$data['register_time'];  //更新数据是处理
		$result['cartype_id']=$data['areacarid'];
		if($data['jq_price_dis']){
			$result['jq_price']=$this->getJqPrice($data['jq_price_dis'],$result['cartype_id']);
		}
		$result['cc_price']=$data['cc_price'];
		$result['zd_price']=$data['zd_price'];
		$dis=$this->getCxzkRate($data['topIndex'], $data['verticalIndex'],$data['lineIndex'] );//出险折扣
		if($dis){
			$result['discount']=$dis;
		}
		
		$sortId=$this->getSortIdByDrivingId($result['id']);
		$sortString=$this->getSortString($data);
		$sortdata['content']=$sortString;
		$ms=new SortModel();
		if(!$sortId){//不存在时需要id
			$sortId=$ms->addFresh($sortdata);
		}else{
			$sortdata['id']=$sortId;
			$ms->addFresh($sortdata);
		}
		
		$result['sort_id']=$sortId;
		
		return Tools::filterInvalidData($result);
	}
	/**
	 * 得到车辆编辑的，post数据
	 * @return multitype:
	 */
	private function getPostDrivingData($data){
		$result=$data;
		
		return $result;
	}
	private function getImg($id){
		return Golbal::getPicturePath($id);
	}
	private function getSortIdByDrivingId($drivingId){
		$dirvingInfo=$this->_m->getEditDirvingInfo($drivingId);
		return $dirvingInfo['sort_id'];
	}
	private function getSortInfo($sortId){
		$ms=new SortModel();
		$content=$ms->getSortContentById($sortId);
		$content=tools::parseSqlStore($content,1);
		$comlist=$this->getCompanyList();
		$comlist=Tools::ConvertArrToAssoc($comlist,'id');
		$result=array();
		foreach ($content as $k=>$com){
			$id=$com['id'];
			if($comlist[$id]){
				$result[$k]=$com;
				$result[$k]['name']=$comlist[$id]['name'];
			}
		}
		ee('sortinfo result:'.getoutstr($result));
		return $result;
	}
	/**
	 * 得到公司排序字符串
	 * @param  $data 包含公司排序费率的，数据   com_id   rate_id
	 * @return Ambigous <string, multitype:unknown >
	 */
	private function getSortString($data){
		
		$result;
		//com_id   rate_id

		foreach ($this->getCompanyList() as $com){
			$id=$com['id'];
			$com_id='com_'.$id;
			$rate_id='rate_'.$id;
			if (isset($data[$com_id]) && isset($data[$rate_id])){
				$result[$data[$com_id]]=array(
					'id'=>$com['id'],
					'discount'=>$data[$rate_id]
				);
			}
		}
		
		ksort($result);
		return Tools::getSqlStore($result);
	}

	/**
	 * @param unknown $jqdis
	 * @param unknown $carTypeId
	 */
	private  function getJqPrice($jqdis,$carTypeId){
		$jqPrice=RateMath::getInstance($carTypeId)->getJqPrice();
		
		ee('jqprice'.getoutstr($jqPrice),'temp');
		if(!$jqdis){
			$jqdis=1;
		}
		$result=$jqdis * $jqPrice;
		
		return $result;
	}
	/**
	 * 
	 * @param unknown $topIndex  连续/上年/新车
	 * @param unknown $verticalIndex  几年几次       //新车只有1行传0
	 * @param unknown $lineIndex	赔付率    （赔款金额/上年签单保费）
	 * @return unknown
	 */
	private function getCxzkRate($topIndex,$verticalIndex,$lineIndex){
		$rate=RateMath::getInstance($carTypeId)->getCxzkTable();
		if($topIndex==2){
			$verticalIndex=0;
			$lineIndex=0;
		}
		if (!$verticalIndex){
			$verticalIndex=0;
		}
		if(!$lineIndex){
			$lineIndex=0;
		}
		$result=$rate[$topIndex][$lineIndex][$verticalIndex];
		ee('cxzr rate:'.$result,'temp');
		return $result;
	}
	/**
	 * 得到该车辆上面的当前操作订单
	 * @param unknown $drivingId
	 */
	private function getCurrentOrderByDrivingId($drivingId){
		$ret=$this->_m->getOrderInfoFromDrivingId($drivingId);
		return $ret;
	}
	/**
	 * 得到公司列表
	 */
	private function getCompanyList(){
		if(!$this->companyList){
			$mc=new CompanyModel();
			$ret=$mc->getList();;
			
			$this->companyList=$ret;
		}
		return $this->companyList;
	}
	
	public function getPositions(){
		$clientId=I('get.userid');
		if(!$clientId){
			die('id error');
		}
		$mposs=new PositionModel();
		$result=$mposs->getClientAllPositions($clientId);
		$this->assign('pos',$result);
	}
	

	
}




