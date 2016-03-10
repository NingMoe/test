<?php
namespace Home\Controller;
use Common\Config\Driving;
use Think\Controller;
use Api\pay\PingPay;
use Common\Model\OrderModel;
use Common\Lib\Golbal;
use Api\Sms\SmsManager;
use Api\qr\Qr;
class PayController extends MainController{					//Controller{
	private $_pay;
	private $_orderModel;
	private $_orderId;
	private $_number;
	private $_orderInfo;
	private $_redUsable;
	private $_orderAmount;
	private $_mobile;
	public function _initialize(){
		parent::_initialize();

// 		$this->_redUsable=$this->getRedPayUsable();
		$this->_orderModel=new OrderModel();
		
		$orderId=I('get.orderid');
		if($orderId){
			$this->_orderId=$orderId;
			$this->_redUsable=$this->getRedPayUsable();
		}
		
		$number=I('get.ordernum');
		if($number){
			$this->_number=$number;
			$this->_redUsable=$this->getRedPayUsable();
		}
	}
	private function getChargeOrderNum($rawData){
		if(!$rawData || ""==$rawData){
			return false;
		}
		$input_data = json_decode($rawData, true);
		$orderNum=$input_data['ordernum'];
		return $orderNum;
	}
	private function doChargeBefore(){
		ee('charge post:'.getoutstr($_POST),'temp');
		ee('charge get:'.getoutstr($_GET),'temp');
		$rawData=file_get_contents('php://input');
		ee('charge $rawData:'.getoutstr($rawData),'temp');
		$orderNum=$this->getChargeOrderNum($rawData);
		ee('charge ordernum:'.$orderNum,'temp');
		if($orderNum){
			$this->_number=$orderNum;
		}else{
			$this->outJson(array('payStatus'=>'订单号不正确'),1);
		}
		$this->freshToPay($orderNum);
	}
	/**
	 * 支付成功
	 */
	public function paySuc(){
		//todo update order
		
		//show success and address
		
		//send sms
		$this->sucMsg();
	}
	private function sucMsg(){
		$sms=SmsManager::getobj();
		
	}
	/**
	 * 支付也
	 */
	public function pay(){
		$orderInfo=$this->getOrderInfo();
// 		ct($orderInfo);
		$result=array(
			'ordernum'=>$orderInfo['number'],
			'amount'=>$orderInfo['amount'],
			'redUsable'=>$this->_redUsable,
			'mobile'=>$this->_mobile
		);
		ee('pay info:'.getoutstr($result),'temp');
		$this->outJson($result);
	}
	/**
	 * 更新订单信息及报价单，套餐包状态
	 */
	private function freshOrder_Price_package(){
// 		$data['id']=$this->_orderId;
		$data['number']=$this->_number;
		$data['paytime']=NOW_TIME;
		$data['red_amount']=$this->_redUsable;
		$data['pay_amount']=$this->_orderAmount-$this->_redUsable;
		
	}
	/**
	 * 取得订单可顶用红包金额
	 * @return
	 */
	private function getRedPayUsable(){
		if(!$this->_orderInfo){
			$this->_orderInfo=$this->getOrderInfo();
		}
		$userinfo=$this->_client->getBaseInfo();
		ee('userinfo:'.getoutstr($userinfo),'temp');
		$this->_mobile=$userinfo['mobile'];
		$redUseAll=$userinfo['red_usable'];
	
// 		$this->_orderAmount=$this->_orderInfo['amount'];
// 		$rateConfig=Golbal::getRedRate();
// 		$orderRed=$rateConfig * $this->_orderAmount;
		$orderRed=$this->_orderInfo['redamount'];
		ee('redorder:'.$orderRed.'--reduseall:'.$redUseAll);
		$result=$redUseAll;
		if ($orderRed<$redUseAll){
			$result=$orderRed;
		}
		if(null==$result){
			$result=0;
		}
		return $result;
	}
	/**
	 * 取得订单信息
	 */
	private function getOrderInfo(){
// 		$result=$this->_orderModel->getOrderInfoById($this->_orderId);
		$result=$this->_orderModel->getOrderInfoByNumber($this->_number);
		if(!$result){
			
			return false;
		}
		return $result;
	}
	
	public function test(){
		$this->display('pay/views/webview');
	}
	public function charge(){
		$this->doChargeBefore();
		if(!$this->_number){
			$this->outJson(array('payStatus'=>'订单号不正确'),1);
		}

		ee('test charge');
		$this->_pay=new PingPay();
		
		$payReturn=$this->_pay->createCharge()->__toJSON();
		
		$arr=json_decode($payReturn,1);
		$wxqr=trim($arr['credential']['wx_pub_qr']);
		
		ee('charge $payReturn $wxqr:'.$wxqr);
		ee('charge $payReturn arr:'.getoutstr($arr));
		
		if($wxqr){
			$path='Uploads/qr/';
			$name='qr'.$this.$this->_number.'.png';
			$full=$path.$name;
			$qr=new Qr();
			$ret=$qr->createQr($wxqr,$full);
			if(is_file($full)){
				$imgPath=$full;
			}else{
				$imgPath=-1;
			}
			$result=array('img'=>$imgPath);
			$this->outJson($result,1);
		}
		$this->payAjaxReturn($payReturn);
	}

	/**
	 * 发起支付请求，更新操作
	 * @param $orderNum
	 */
	private function freshToPay($orderNum){
//		$orderInfo=$this->getOrderInfo();
		//update orderinfo
		$this->_orderModel->addFresh(array('number'=>$this->_number,'paytime'=>time()));
		//update driving info;
		$drivingId=$this->_orderModel->getDrivingIdByOrderNum($this->_number);
		if(!$drivingId){
			$this->outJson(array('topay:'=>'未找到订单的车辆'),1);
		}
		Driving::changeStatus($drivingId,Driving::$createPay);
	}
	public function refunds(){
		$this->_pay=new PingPay();
		
		$id='ch_80WzTO4iPW9K58CCSOXff54G';
		$amount=1;
		$description='协商退款';
		$data=array(
			'amount'=>$amount,
			'description'=>$description	
		);
		$ch=$this->_pay->retrieveOrder($id);
		$ch->refunds->create($data);
	}
	public function queryOrder(){
		$this->_pay=new PingPay();
		
		$id='ch_80WzTO4iPW9K58CCSOXff54G';
		$ret=$this->_pay->retrieveOrder($id);
		$this->show('query:'.$ret->__toString());
	}
	
	public function receive(){
		$this->webHooks();
	}
	
	private function payAjaxReturn($json){
		header('Content-Type:application/json; charset=utf-8');
		exit($json);
	}
}