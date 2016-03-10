<?php
namespace Api\Sms;
use Common\Object\SessionManage;
class SmsManager{
	private $_clSmsObj;
	private $_productId=349312826;
	private $_msg;
	static private $obj;
	public function __construct(){//兼容暂不关
		$this->_clSmsObj=new ClSmsApi();
	}
	public static function getobj(){
		if(!(self::$obj instanceof self)){
			self::$obj=new self();
		}
		return self::$obj;
	}
	/**
	 * 发送验证码
	 * @param unknown $mobile
	 * @return boolean
	 */
	public function sendCode($mobile){
		return $this->sendSms($mobile, $this->codeMsg());
	}
	/**
	 * 发送短信
	 * @param unknown $mobile
	 * @param unknown $content
	 * @param string $productId
	 * @return boolean
	 */
	public function sendSms($mobile,$content=NULL,$productId=null){
		if(null===$productId){
			$productId=$this->_productId;
		}
		if(null==$content){
			$content=$this->_msg;
		}
		$result=$this->_clSmsObj->sendSMS($mobile,$content,true,$this->_productId);
		$result=$this->execResult($result);
		ee('smsmanager re:'.var_export($result,true),temp);
		if(0==$result[1]){
			$this->_msg='发送成功';
			return true;
		}else{
			$this->_msg="发送失败{$result[1]}";
			return false;
		}
	}
	public function queryBalance(){
		return $this->_clSmsObj->queryBalance();
	}
	private function codeMsg(){
		$code=new Code();
		SessionManage::setCode($code->get(),$code->getBirth());
// 		return '您的验证码是:'.$code->get().',有效期'.$code::$_expire.'秒,请尽快使用，只能用于注册，勿转发他人。';
		return '您正在进行身份认证操作，验证码为：'.$code->get().'，请勿告诉他人。如非本人操作请联系客服4008888888';
	}
	/**
	 * 处理返回值
	 *
	 */
	public function execResult($result){
		$result=preg_split("/[,\r\n]/",$result);
		return $result;
	}
	/**
	 * 生成报价单消息
	 * @param unknown $carNumber 车牌号
	 */
	public function createPriceMessage($carNumber){
		$this->_msg='您的爱车：{'.$carNumber.'}有新的报价，请用本机号登陆壹壹保浏览详情。如有任何疑问请联系客服4008888888';
		return $this->_msg;
	}
	/**
	 * 支付成功消息
	 * @param unknown $carNumber 车牌号
	 */
	public function paySuccessMessage($carNumber){
		$this->_msg='您的爱车：{'.$carNumber.'}的车辆保险订单已经支付成功，我们会尽快为您邮寄保单。如有任何疑问请联系客服4008888888';
		return $this->_msg;
	}
	
	/**
	 * 保单生效通知消息
	 * @param unknown $carNumber  车牌号
	 * @param int $effectTime  生效时间
	 * @param int $deadTime 失效时间
	 * @return string
	 */
	public function insureEffect($carNumber,$effectTime,$deadTime){
		$this->_msg='您的爱车：{.$carNumber.}的车辆保险单已经生效。生效时间：'.$this->formatTime($effectTime)
		.'到期日期：'.$this->formatTime($deadTime).'。如有任何疑问请联系客服4008888888';
		return $this->_msg;
	}
	private function formatTime($time){
		return date('Y年m月d日',$time);
	}
/*
 *  【壹壹保提醒您】您正在进行身份认证操作，验证码为：1234，请勿告诉他人。如非本人操作请联系客服4008888888
1、您的爱车：{闽D88C25}有新的报价，请用本机号登陆壹壹保浏览详情。如有任何疑问请联系客服4008888888   
2、您的爱车：{闽D88C25}的车辆保险订单已经支付成功，我们会尽快为您邮寄保单。如有任何疑问请联系客服4008888888
3、您的爱车：{闽D88C25}的车辆保险单已经生效。生效时间：xxxx年x月x日到期日期：xxxx年x月x日。如有任何疑问请联系客服4008888888 
 */	
	
}