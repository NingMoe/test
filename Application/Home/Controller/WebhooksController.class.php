<?php
namespace Home\Controller;
use Api\Sms\SmsManager;
use Common\Model\DrivinglicenseModel;
use Think\Controller;
use Common\Model\OrderModel;
use Common\Lib\Tools;
use Common\Config\Driving;
use Api\pay\PingPay;
use Common\Model\ClientModel;
use Api\wx\tpl\TplBuySuccess;
class WebhooksController extends Controller{
	private $filename;
	private $event;
	private $openid=NULL;
	
	protected function _initialize(){
		$this->filename='webHook';
//		$this->test();exit;
		ee('pay callback init start',$this->filename);
		
		$rawdata=file_get_contents("php://input");
		$this->event=json_decode($rawdata);
		
		ee('webhooks input rawdata:'.getoutstr($rawdata),$this->filename);
		$rawdata2=file_get_contents("php://input");
		ee('webhooks input rawdata2:'.getoutstr($rawdata2),$this->filename);
		ee('webhooks input event:'.getoutstr($this->event),$this->filename);
		
		// 对异步通知做处理
		if (!isset($this->event->type)) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
			exit("fail");
		}
		
	}
	//receivePaysuccessNotify
	public function rpn(){
		switch ($this->event->type) {
			case "charge.succeeded":
				// 开发者在此处加入对支付异步通知的处理代码
				$this->el('webhooks charge.succeeded:',$this->filename);
				$this->paySuccess($this->event->data->object);
				header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
				break;
			case "refund.succeeded":
				// 开发者在此处加入对退款异步通知的处理代码
				$this->el('webhooks refund.succeeded:',$this->filename);
				$this->refundSuccess($this->config->data['object']);
				header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
				break;
			default:
				header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
				break;
		}
	}
	//receiveRefundSuccessNotify
	public function rrn(){
		
	}
	//receiveOtherNotify
	public function ron(){
		
	}
	private function paySuccess($object){
		$this->el('paySuccess object:'.getoutstr($object));
		$sucInfo['ping_ch_id']=$object->id;
		$sucInfo['channel']=$object->channel;
		$sucInfo['order_no']=$object->order_no;
		$sucInfo['pay_amount']=$object->amount/100;
		$sucInfo['client_ip']=$object->client_ip;
		$sucInfo['paysuc_time']=time();
		$sucInfo['status']=2;//订单状态支付成功
		
		if('wx_pub_qr'==$sucInfo['channel']){
			$sucInfo['open_id']=$object->extra->open_id;
			$sucInfo['bank_type']=$object->extra->bank_type;
			$sucInfo['product_id']=$object->extra->product_id;
		}
		
		$this->el('1 sucinfo:'.getoutstr($sucInfo));
		$this->freshOrder($sucInfo);
		
	}
	private function refundSuccess($object){
		$this->el('paySuccess object:'.getoutstr($object));
		
	}
	private function freshOrder($paySucOrderInfo){
		$this->el('freshorder start');
		$num=$paySucOrderInfo['order_no'];
		if(!$num){
			$this->el('error num:'.$num);
			return false;
		}
		
		if(!$this->checkPingxxCharge($paySucOrderInfo['ping_ch_id'])){
			$this->el('checkPingxxCharge error num:'.$num);
			return false;
		}
		
		$om=new OrderModel();
		$sqlOrder=$om->getOrderInfoByNumber($num);
		if(!$this->checkOrder($sqlOrder, $paySucOrderInfo)){
			$this->el('order check fail:');
			return false;
		}
		
		
		$this->el('webhook fresh order info:'.getoutstr($paySucOrderInfo));
		
		$ret=$om->where(array('number'=>$num))->save($paySucOrderInfo);
		if(!$ret){
			$this->el('fresh order fail num:'.$num);
		}

		//更新车辆信息
// 		$content=Tools::parseSqlStore($sqlOrder['content'],1);
		$drivingId=$om->getDrivingIdByOrderNum($num);
		$this->el('carid:'.$drivingId);
		Driving::changeStatus($drivingId, Driving::$paySuccess);

		//更新用户可用红包
		$datar=array('carId'=>$drivingId,'redamount'=>$sqlOrder['redamount']);
		$this->cutRedUsed($datar);

		//短信通知
		$this->sendPaySuccessMsg($drivingId);
		//模板消息
// 		$this->el('to notify sqlorder:'.getoutstr($sqlOrder));
		if(null!=$this->openid){
			$this->sucNotify($sqlOrder);
		}

	}
	private function sendPaySuccessMsg($drivingId){
		if(!isset($drivingId)){
			die('car id error');
		}
		$md=new DrivinglicenseModel();
		$md->join('yy_client on yy_client.id=yy_drivinglicense.client_id');
		$md->field('license_number,mobile,openid');
		$carSql=$md->where(array('yy_drivinglicense.id'=>$drivingId))->find();
		$this->el('sendsms carsql:'.getoutstr($carSql));
		
		if(isset($carSql['openid'])){
			$this->openid=$carSql['openid'];
// 			$this->el('this openid:'.$this->openid);
		}
		
		$sms=SmsManager::getobj();
		$content=$sms->paySuccessMessage($carSql['license_number']);
		$sms->sendSms($carSql['mobile'],$content);

	}
	private function sucNotify($order){
		$content=Tools::parseSqlStore($order['content'],1);
		$jqccendtime=$content['jqccendtime'];
		$notify=new TplBuySuccess($this->openid);
		$needData=$notify->createNeedData($jqccendtime);
		$notify->sendNotify($needData);
	}
	public function test(){
		$car=86;
		$this->sendPaySuccessMsg($car);
	}
	private function cutRedUsed($datar){
		$mc=new ClientModel();
		$mc->freshUserRed($datar);
	}
	private function checkOrder($sqlOrder,$payOrder){
		//todo
		
		return true;
	}
	private function checkPingxxCharge($ch_id){
		$pay=new PingPay();
		
		$ret=$pay->retrieveOrder($ch_id);
		$this->el('checkPingxxCharge retrieveOrder ret:'.getoutstr($ret));
		
		return true;
		
	}
	private function el($msg){
		ee($msg,$this->filename);
	}
}


















