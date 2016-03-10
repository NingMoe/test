<?php
namespace Home\Controller;
use Common\Object\PriceSheet;
use Common\Model\PackageModel;
use Common\Object\Package;
use Common\Object\CarRateInput;
use Common\Model\PricesheetModel;
use Common\Model\DrivinglicenseModel;
use Common\Model\CompanyModel;
use Common\Lib\Tools;
use Common\Object\Name\Name;
use Common\Model\OrderModel;
use Common\Lib\Golbal;
use Common\Config\Driving;
class PricesheetController extends MainController{
	private $_drivingIds;
	private $_rawPriceSheet;//原始报价单列表
	private $_company;
	private $_drivings;//所有行驶证信息
// 	private $_jqAndCc;
	protected function _initialize(){
		parent::_initialize();
		ee('pricesheets:','temp');
		$this->_drivingIds=$this->getDrivingIds();
		ee('_drivingIds all:'.getoutstr($this->_drivingIds),'temp');
		$this->_drivings=$this->getDrivingInfos();
		$this->_rawPriceSheet=$this->getRawPriceSheet();
		$this->_company=$this->getCompany();
// 		var_dump($this->_rawPriceSheet);echo "<hr>".date(':i:s',170);;
		$carid=I('get.carid');
		if(!$carid){
			$carid=I('post.carid');
		}
		if($carid){
			$this->_drivingIds=array($carid);
		}
		ee('_drivingIds init last:'.getoutstr($this->_drivingIds),'temp');
	}
	public function index(){
		$result=array();
		ee('drivingids:'.getoutstr($this->_drivingIds),'temp');
		ee('pricesheets:'.getoutstr($this->_rawPriceSheet),'temp');
		foreach ($this->_drivingIds as $carId){//echo $carId;echo "<hr>";
			$priceSheet=$this->_rawPriceSheet[$carId]; //ct($priceSheet);
			
			ee('one PriceSheet:'.getoutstr($priceSheet),'temp');
			
			$carInfo=$this->_drivings[$carId];
			$actionStatus=$carInfo['action_status'];
			
			if($actionStatus<5 || $actionStatus>9){//未提交套餐//付款成功
// 				$this->outJson(array('pricestatus'=>'未提交套餐包或付款成功',1),1);
				continue;
			}
			
			if($actionStatus<7){//未完成报价
				$priceSheet=null;
			}
			
			$this->_company=null;
			$priceSheetId=$priceSheet['pricesheet_id'];
			$sortId=$priceSheet['sort_id'];
			if($sortId && $priceSheetId){		
				$this->_company=getSortData($sortId);
			}else{
			}
			
// 			ct($this->_company,'thiscom:');
// 			var_dump($carInfo);
			$temp=array();
			$temp['carid']=$carInfo['id'];
			$temp['platenumber']=$carInfo['license_number'];
			$temp['insurancestyle']=getInsureStyleText($carInfo['package_style']);
			$temp['city']='厦门';//以后修改
			
			$tmpStatus='右上数据暂无';
			$righttext='右中数据暂无';
			
			if(!$priceSheet){//报价单不存在

				ee('!$priceSheet','temp');
// 				ee('$this company:'.getoutstr($this->_company),'temp');
				
				$packageId=$carInfo['package_id'];
				if (!$packageId){ //没有套餐 时不显示 或进行不到
					return -1;		//套餐类型有，必须有
				}
				$packageCreateTime=$this->getPackageCreateTime($packageId);
				
				
				$temp['lefttime']=Tools::timeFormat($packageCreateTime);//左侧时间    无报价单是为创建套餐时间
				
				
				if($actionStatus==Driving::$commitPackage){//提交套餐
					$tmpStatus='正在努力为你报价';
					$righttext=$this->getPriceTime($packageCreateTime);
				}elseif ($actionStatus==Driving::$drivingInvalid){
					$tmpStatus='报价失败';
					$righttext='行驶证无效，请重新上传';
				}elseif ($actionStatus==Driving::$packageInvalid){
					$tmpStatus='报价失败';
					$righttext='投保套餐无效，请重新选择';
				}
				$temp['rightstatus']=$tmpStatus;//右侧上状态
				$temp['righttext']=$righttext;//右侧说明字符串
// 				$result[]['companynames']=null;
				$temp['companynames']=null;
				
			}else {//有报价单
				ee('!$priceSheet else','temp');
// 				ee('$this company:'.getoutstr($this->_company),'temp');
				
				$minPrice=-1;
				foreach ($this->_company as $com){
					$price=$com['discount']*$priceSheet['pricesheet_count']+$priceSheet['jq_price']+$priceSheet['cc_price'];
					$price=round($price,2);
					if(-1==$minPrice){//初始
						$minPrice=$price;
					}
					if($price<$minPrice){
						$minPrice=$price;
					}
					$comData[]=array(
							'name'=>$com['name'],
							'prize'=>$price,
							'quoteid'=>$com['id']   //公司id
					);
				}
					
				$tmpStatus='已报价';
				$righttext=$minPrice;
				
				$temp['lefttime']=Tools::timeFormat($priceSheet['updatetime']);//左侧时间    有报价，报价更新时间
				$temp['rightstatus']=$tmpStatus;//右侧上状态
				$temp['righttext']=$righttext;//右侧说明字符串
				$temp['companynames']=$comData;
				unset($comData);
			}
// 			ee('result one:'.getoutstr($temp),'temp');
			$result[]=$temp;
			unset($temp);
		}
// 		ct($result);
		ee('pricesheet index result:'.getoutstr($result),'temp');
		$this->contentName='priceSheet';
		$this->outJson($result,1);
		$this->assign('data',$result);
		$this->display();
	}
	
	public function indexOld(){
		$result;
		$k1='Pricesheet';
		foreach ($this->_rawPriceSheet as $k=>$carSheet){
			if($carSheet['deadtime']>NOW_TIME){
				$result[$k1]['timestate']=-1;//失效  //失效后重新按此套餐询价或 重新选择套餐  //留个重选套餐入口
			}else {
				$result[$k1]['timestate']=1;
			}
			$result[$k1]['id']=$carSheet['id'];
			$result[$k1]['platenumber']=$carSheet['license_number'];
			$result[$k1]['insurancestyle']=$carSheet['package_style'];
			$result[$k1]['quoteovertime']=Tools::timeFormat($carSheet['updatetime']);
			$result[$k1]['state']=$carSheet['status'];
			foreach ($this->_company as $com){
				$data[]=array(
						'name'=>$com['name'],
						'prize'=>$com['discount']*$carSheet['all_count'],
						'quoteid'=>$com['id']
				);
			}
			$result[$k1]['companynames']=$data;
		}
		// 		var_dump($result);
		//$this->contentName='Pricesheet';
		$this->assign('data',$result);
		$this->outJson($result,1);
// 				$this->display();
	}
	/**
	 * 订单或报价单失效后
	 * 重新请求报价
	 */
	public function reQuote(){
		$drivingId=I('post.carid'); //根据车id重新请求报价
		Driving::changeStatus($drivingId,Driving::$commitPackage);
		//fresh package time  no need  pricetime 20minuts
		freshPackageTime($drivingId);
	}
	
	/**
	 * 得到报价倒计时
	 * @param unknown $time
	 */
	private function getPriceTime($packageCreateTime){
		$priceTime=$packageCreateTime+20*60-NOW_TIME;
		if($priceTime<0){
			$priceTime=0;
		}
		$priceTime='00'.date(':i:s',$priceTime);
		return $priceTime;
	}
	/**
	 * 传入公司id,行驶证id,查看具体报价
	 */
	public function detail(){
		$companyId=I('get.companyid');
		$drivingId=I('get.drivingid');
		if(!$companyId || !$drivingId){
			$this->ko('公司或车辆信息有误');
		}
		$result=$this->getDetail($companyId, $drivingId);
		
		//报价明细更新数据库 订单信息
		$data['id']=$result['orderId'];
		unset($result['orderId']);
		$data['status']=1;//已有内容
		$data['content']=Tools::getSqlStore($result);
		$data['updatetime']=NOW_TIME;
		$data['amount']=$result['allPriceDis'];
		$data['deadtime']=$this->_rawPriceSheet[$drivingId]['deadtime'];//失效时间
		$data['company_id']=$result['orderId'];
		$data['company_name']=$result['company'];
		$data['redamount']=$result['redamount'];
		$order=new OrderModel();
		$orderRet=$order->addFresh($data);
		if(!$orderRet){
			ee('更新订单详情失败');
		}
		
		//更新状态
		Driving::changeStatus($drivingId, Driving::$createOrderContent);
		
ee('price sheet result:'.getoutstr($result),'temp');
		$this->contentName='Pricedetail';
		$this->assign('detail',$result);
		$this->outJson($result,1);
		$this->display();
	}
	private function getDetail($companyId,$drivingId){
		$sheetInfo=$this->_rawPriceSheet[$drivingId];
		ee('price sheet sortid:'.getoutstr($sheetInfo['sort_id']),'temp');
		$sortId=$sheetInfo['sort_id'];
		if($sortId){
			$this->_company=convertIdKey(getSortData($sortId));
		}
		$comInfo=$this->_company[$companyId];
		ee('getDetail $comInfo all:'.getoutstr($this->_company),'temp');
		ee('getDetail $comInfo:'.getoutstr($comInfo),'temp');
		$result=array();
		//车辆保险人信息  
		$result['carId']=$sheetInfo['id'];
		$result['number']=$sheetInfo['number'];
		$result['license_number']=$sheetInfo['license_number'];//Tools::timeFormat($sheetInfo['deadtime']);//失效时间
		$result['owner']=$sheetInfo['owner'];
		$result['car_model']=$sheetInfo['car_model'];
		$result['vin']=$sheetInfo['vin'];
		$result['engine_no']=$sheetInfo['engine_no'];
		$result['register_time']=Tools::timeFormat2($sheetInfo['register_time']);
		
		if($sheetInfo['deadtime']>NOW_TIME){//过期
			$result['condition']=-1;//过期
		}else {
			$result['condition']=1;//没过期
		}
		$result['deadtime']=Tools::timeFormat($sheetInfo['deadtime']);//失效时间     //$sheetInfo['deadtime'];//
		$result['orderId']=$sheetInfo['order_id'];
		$result['companyId']=$companyId;
		$result['company']=$comInfo['name'];
		$result['catePrice']=$sheetInfo['pricesheet_count'];
		$result['catePriceDis']=round($sheetInfo['pricesheet_count']*$comInfo['discount'],2);
		$result['allPrice']=$sheetInfo['all_count'];
		$result['allPriceDis']=round($result['catePriceDis']+$sheetInfo['jq_price']+$sheetInfo['cc_price'],2);
		
		$result['jq_price']=$sheetInfo['jq_price'];
		$result['cc_price']=$sheetInfo['cc_price'];
		
		ee('deadtime:'.$sheetInfo['deadtime'],'temp');
		
		$result['jqccstarttime']=Tools::timeFormat($sheetInfo['deadtime']);
		$result['jqccendtime']=Tools::timeFormatYearLater($sheetInfo['deadtime']);
		
		foreach ($sheetInfo['original_sheet'] as $key=>$item){
			$len=strlen($item['level']);
			$suf='万';
			if ($len>3){
				$suf='';
			}
			if($item['level']){
				$item['level']=$item['level'].$suf;
			}
			$data[$key]=array(
				'name'=>Name::$cateName[$key],
				'level'=>$item['level'],
				'price'=>$item['price'],
				'disPrice'=>round($item['price']*$comInfo['discount'],2),
				'noPayPrice'=>$item['noPayPrice'],
				'disNoPayPrice'=>round($item['noPayPrice']*$comInfo['discount'],2)

			);
		}
		$result['cateList']=$data;
		$result['redamount']=round($sheetInfo['pricesheet_count'] * Golbal::getRedRate());//该单可用红包
		
		
		return $result;
	}
	/**
	 * 取得行驶证信息
	 */
	private function getDrivingInfos(){
		return Tools::ConvertArrToAssoc($this->_client->getAllDrivings(),'id');
	}
	private function getPackageCreateTime($packageId){
		$pm=new PackageModel();
		$ret=$pm->getPackageInfoById($packageId);
		if(!$ret['createtime']){
			return false;
		}
		return $ret['createtime'];
	}
	private function getDrivingIds(){
		$drivingIds=$this->_client->getDrivingIdAll();
		if(!$drivingIds){
			$this->error('没有有效行驶证信息，请上传行驶证',U('Upload/upDriving'),1);
		}
		return $drivingIds;
	}
	private function getRawPriceSheet(){
		$mPriceSheet=new PricesheetModel();
		$ret=$mPriceSheet->getAllPriceSheet($this->_drivingIds);
		
		if (!$ret){//没报价也显示
// 			$this->error('没有报价信息,请等待客服审核或提交套餐信息'.$mPriceSheet->getError(),U('package/index'),1);
		}
		
		return $ret;
	}
	private function getCompany(){
		$mCompany=new CompanyModel();
		$ret=$mCompany->getList();
		if(!$ret){
			$this->ko('保险公司信息出错');
		}
		foreach ($ret as $value){
			$result[$value['id']]=$value;
		}
		return $result;
	}
	
// 	private function getJqAdnCc(){
// 		$mDriving=new DrivinglicenseModel();
// 	}

	
}