<?php
namespace Home\Controller;
use Common\Model\OrderModel;
use Common\Lib\Tools;
use Common\Config\Driving;
use Api\wx\tpl\TplOrderExpired;
class OrderController extends MainController{
	//订单控制器
	private $orders;
	private $ordersUncomplete=array();
	private $ordersComplete=array();
	private $drivingIds;
	private $isOne;
	protected function _initialize(){
		parent::_initialize();
		
		$drivingId=I('get.carid');
		if(!$drivingId){
			$drivingId=I('post.carid');
		}
		ee('dirvingId get and post:'.$drivingId,'temp');
		
		ee('Order get:'.getoutstr($_GET),'temp');
		ee('Order post:'.getoutstr($_POST),'temp');
		
		if ($drivingId){
			$this->drivingIds=array($drivingId);					//$drivingId;
			$this->isOne=true;
		}else{
			$this->drivingIds=$this->_client->getDrivingIdAll();
			ee('dirvingid all'.getoutstr($this->_client->getDrivingIdAll()),'temp');
		}
		
		ee('this dirvingid:'.getoutstr($this->drivingIds),'temp');
		
// 		$this->drivings=$this->getAllDrings($this->drivingId);

		$this->m=new OrderModel();
		$this->orders=$this->m->getClientOrderByAllDrivings($this->drivingIds);
		ee('this orders:'.getoutstr($this->orders),'temp');
		//ct($this->orders,'orders:');
		$this->orders=Tools::ConvertArrToAssoc($this->orders, 'number');
		
		ee('this orders convert number key:'.getoutstr($this->orders),'temp');
		
		foreach ($this->orders as $order){
			if(2==$order['status'] || Driving::$paySuccess==$order['action_status']){//2为已完成
				$this->ordersComplete[]=$order;
			}elseif(1==$order['status'] && (Driving::$createOrderContent==$order['action_status']||Driving::$createPay==$order['action_status'])){//生成内容未支付
				$this->ordersUncomplete[]=$order;
			}
		}
		ee('orders completed:'.getoutstr($this->ordersComplete),'temp');

	}
	


	public function  getUncompleted(){
		$result=$this->getSetupData($this->ordersUncomplete);
		$this->contentName='ordergetUncompleted';
		$this->outJson($result,1);
	}
	
	public function getComplete(){
		$result=$this->getSetupData($this->ordersComplete);
		$this->contentName='ordergetComplete';

		$this->outJson($result,1);
	}
	public function getAll(){
		$allOrder=array_merge($this->ordersUncomplete,$this->ordersComplete);
		$result=$this->getSetupData($allOrder);
		$this->contentName='allorder';
		
		//ct($result);
		$this->outJson($result,1);
// 		ct($result);
	}
	
	/**
	 * 得到车牌与订单的组装数据
	 */
	private function getSetupData($orders){
		if(empty($orders) || null==$orders){
			return false;
		}
		
// 		$result=array();
// 		foreach ($this->drivings as $k=>$value){
// 			ct($value);
// 			$did=$value['drivingid'];
// 			$tempdata['drivingid']=$value['id'];
// 			$tempdata['platenumber']=$value['license_number'];
// 			$tempdata['insurancestyle']=$value['package_style'];

			
// 			$subtempdata=array();
// 			foreach ($orders as $ok=>$item){
// 				if($item['driving_id']==$value['id']){
// 					$subtempdata[]=$item;
// 					// 	driving_id,yy_order.id,number,yy_order.content,yy_order.status,yy_order.amount,pay_amount,red_amount,
// 					// 	paytime,company_name
					
// // 					$subtempdata[$ok]['content']=Tools::parseSqlStore($subtempdata[$ok]['content'],1);
// 					unset($subtempdata[$ok]['content']);
// 				}
// 			}
// 			$tempdata['orders']=$subtempdata;
			
// 			if($this->isOne){
// 			//单辆车时增加信息
					
// 			}
// 			$result[]=$tempdata;
// 		}
		$result=$orders;
		foreach ($orders as $k=>$v){
			

			$result[$k]['platenumber']=$v['license_number'];
			$result[$k]['insurancestyle']=getInsureStyleText($carInfo['package_style']);
			$result[$k]['city']='厦门';//以后修改
			
			$result[$k]['deadtime']=Tools::timeFormat($v['deadtime']);
			if($v['deadtime']<NOW_TIME){
				//在之前已过期
				//tod更新状态
				
				//todo发送模板消息
				$user=$this->_client->getBaseInfo();
				$openid=$user['openid'];
// 				$orderNum=$v['number'];
				$notify=new TplOrderExpired($openid);
				$needData=$notify->createNeedData($v['license_number'],$v['number']);
				$notify->sendNotify($needData);
			}
			$result[$k]['lefttime']=Tools::timeFormat($v['updatetime']);
			$result[$k]['rightstatus']=$this->getstatusText($v['status']);//右侧上状态
// 			$temp['righttext']=$righttext;//右侧说明字符串

// 			$result[$k]=$temp;
			unset($result[$k]['content']);
		}
		
// 		ct($result,'re:');
		return $result;
	}
	private function getstatusText($status){
		$textArr=array(1=>'未付款',2=>'已付款');
		
		return $textArr[$status];
	}
	public function isHave(){

		$orderNum=count($this->ordersComplete)+count($this->ordersUncomplete);
		$result['orderhave']=-1;
		if($orderNum>0){
			$result['orderhave']=1;
		}
		
		$this->outJson($result);
	}
	public function detail(){
		$num=I('get.number');
// 		$num='160105569849';
		if(!$num){
			die('订单号不正确');
		}
		
		$data=$this->orders[$num];//['content'];
		$content=$data['content'];
		$content=Tools::parseSqlStore($content,1);
		$content['status']=$data['status'];
		//ct($content,'detail:');
		$this->outJson($content,1);
	}
	public function getAllDrings($drivingId=NULL){
		$ret=$this->_client->getAllDrivings();
		
		if($drivingId){
			foreach ($ret as $k=>$value){
				if($value['id']==$drivingId){
					$result[]=$value;
				}
			}
			return $result;
		}
		return  $ret;
	}
	public function orderStatus(){
		$num=I('get.number');
		if(!$num){
			$num=I('post.number');
		}
		if(!$num){
			die('订单号不正确');
		}
		
		$data=$this->orders[$num];//['content'];
		$status=$data['status'];
		$carid=$this->m->getDrivingIdByOrderNum($num);
		$this->contentName='orderPay';
		$this->outJson(array('orderStatus'=>$status,'carid'=>$carid,'orderNum'=>$num));
	}
}






















