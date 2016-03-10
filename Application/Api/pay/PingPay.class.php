<?php
namespace Api\pay;
require_once('/../pingpp/init.php');
class PingPay{
	private $_app;
	private $_extra;
	private $_orderNo;
	private $_amount;
	private $_channel;
//sk_test_izDmbLzrHS8OHCOW58CeXnH8
//sk_live_KaH8iP0eTy10G4afzLWrLirL
	public function __construct($apiKey='sk_live_KaH8iP0eTy10G4afzLWrLirL',$appId='app_TyzbDS8S48aPGaXT'){
		\Pingpp\Pingpp::setApiKey($apiKey);
		$this->_app=$appId;
	}
	public function createCharge(){
		//初始化数据 
		$this->initFromInput();
		$this->setExtra();
		$subject='壹壹保';
		$body='内容';
		
		$data=array(
			'subject'   => $subject,
            'body'      => $body,
            'amount'    => $this->_amount,
            'order_no'  => $this->_orderNo,
            'currency'  => 'cny',
            'extra'     => $this->_extra,
            'channel'   => $this->_channel,
            'client_ip' => $_SERVER['REMOTE_ADDR'],
            'app'       => array('id' => $this->_app),
		);
		ee('createCharge data:'.getoutstr($data),'temp');
		try {
			$result=\Pingpp\Charge::create($data);
			ee('createCharge return:'.getoutstr($result),'temp');
			return $result;
		}catch (\Pingpp\Error\Base $e){
			
			ee('createCharge catch error e:'.getoutstr($e),'temp');
			
			header('Status: '.$e->getHttpStatus());
			echo ($e->getHttpBody());
		}
	}
	/**
	 * 查询单笔订单
	 */
	public function retrieveOrder($id){
		return \Pingpp\Charge::retrieve($id);
	}
	/**
	 * 查询指定的 event 对象
	 */
	public function retrieve($evtId=''){
		return \Pingpp\Event::retrieve($evtId);
	}

	private function setExtra(){
		$extra = array();
		switch ($this->_channel) {
			case 'alipay_wap':
				$this->_extra = array(
				'success_url' => 'http://www.yourdomain.com/success',
				'cancel_url' => 'http://www.yourdomain.com/cancel'
						);
						break;
			case 'upmp_wap':
				$this->_extra= array(
				'result_url' => 'http://www.yourdomain.com/result?code='
						);
						break;
			case 'bfb_wap':
				$this->_extra = array(
				'result_url' => 'http://www.yourdomain.com/result?code=',
				'bfb_login' => true
				);
				break;
			case 'upacp_wap':
				$this->_extra = array(
				'result_url' => 'http://www.yourdomain.com/result'
						);
						break;
			case 'wx_pub':
				$this->_extra = array(
				'open_id' => 'on8FRwiHscMnQqmNqjjE5c_MSAFA'  //oZH5_wkpNtR7ysHSsfBNg4VpbX9U
						);
						break;
			case 'wx_pub_qr':
				$this->_extra = array(
				'product_id' => 'Productid'
						);
						break;
			case 'yeepay_wap':
				$this->_extra = array(
				'product_category' => '1',
				'identity_id'=> 'your identity_id',
				'identity_type' => 1,
				'terminal_type' => 1,
				'terminal_id'=>'your terminal_id',
				'user_ua'=>'your user_ua',
				'result_url'=>'http://www.yourdomain.com/result'
						);
						break;
			case 'jdpay_wap':
				$this->_extra = array(
				'success_url' => 'http://www.yourdomain.com',
				'fail_url'=> 'http://www.yourdomain.com',
				'token' => 'dsafadsfasdfadsjuyhfnhujkijunhaf'
						);
						break;
		}
	
	}
	private function initFromInput(){
		$rawData=file_get_contents('php://input');
		$input_data = json_decode($rawData, true);
		
		ee('charge pingpay init $rawData:'.getoutstr($rawData),'temp');
		
		if (empty($input_data['channel']) || empty($input_data['amount'])) {
			echo 'channel or amount is empty';
			exit();
		}
		
		$this->_orderNo=$input_data['ordernum'];
		
		//测试用
		if(!$this->_orderNo || empty($this->_orderNo)){
			$this->_orderNo=substr(md5(time()), 0, 12);
		}
		
		$this->_channel = strtolower($input_data['channel']);
		$this->_amount = $input_data['amount'] * 100;
 		$this->_amount=1;  //是0.01
		
		ee('input_data:'.getoutstr($input_data));
	}
	
}