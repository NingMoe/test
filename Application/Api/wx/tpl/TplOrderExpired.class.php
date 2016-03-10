<?php
namespace Api\wx\tpl;
use Api\wx\Template;
class TplOrderExpired extends Template{
	protected function init(){
		$this->template_id='4g7xMruVOHQvbhY6P58lDv-K4-BaEXd4ZBm6arGTGhY';
		$this->url='http://oneonebao.com';
		$this->dataFields=array('first','keyword1','keyword2','keyword3','remark');
	}
/*
{{first.DATA}}
车牌号：{{keyword1.DATA}}
订单号：{{keyword2.DATA}}
失效原因：{{keyword3.DATA}}
{{remark.DATA}}
----------------------
您好，您的订单已失效，请点击失效订单重新提交询价。
车牌号：沪D88C25
订单号：234567890
失效原因：订单超过48小时未支付
感谢您的支持，有任何疑问请致电95555
 */
	/**
	 * need field :carNumber;
	 * @see \Api\wx\Template::needToAll()
	 */
	protected function needToAll($need){
		return array(
				'first'=>'您好，您的订单已失效，请点击失效订单重新提交询价。',
				'keyword1'=>$need['carNumber'],
				'keyword2'=>$need['orderNum'],
				'keyword3'=>'订单超过48小时未支付',
				'remark'=>'尊敬的客户：你的车险投保咨询已报价，价格仅供参考，以实际出单为准。'
		);
	}
	public function createNeedData($carNumber,$orderNum){
		return Array(
				'carNumber'=>$carNumber,
				'orderNum'=>$orderNum
		);
	}
}