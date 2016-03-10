<?php
namespace Api\wx\tpl;
use Api\wx\Template;
class TplBuySuccess extends Template{
	protected function init(){
		$this->template_id='3MvJrfQQ52tqPFoQIZ0o66Fz8vXvLZQM_g12Subx8XE';
		$this->url='http://oneonebao.com';
		$this->dataFields=array('productType','name','number','expDate','remark');
	}
	
/*
您好，您已购买成功。
{{productType.DATA}}：{{name.DATA}}
购买数量：{{number.DATA}}
有效期：{{expDate.DATA}}
{{remark.DATA}}
-----------------------
您好，您已购买成功。
业务名：微信金融某业务
购买数量：1份
有效期：永久有效
备注：如有疑问，请拨打咨询热线123323
*/
	/**
	 * need field :expiredTime; 
	 * @see \Api\wx\Template::needToAll()
	 */
	protected function needToAll($need){
		return array(
			'productType'=>'业务名',
			'name'=>'壹壹保车险',
			'number'=>'1份',
			'expDate'=>$need['expiredTime'],
			'remark'=>'备注：如有疑问，请拨打咨询热线4008888888'
		);
	}
	public function createNeedData($expiredTime){
		return Array(
			'expiredTime'=>$expiredTime
		);
	}
}