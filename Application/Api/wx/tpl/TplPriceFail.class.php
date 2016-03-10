<?php
namespace Api\wx\tpl;
use Api\wx\Template;
class TplPriceFail extends Template{
	protected function init(){
		$this->template_id='swX8sox9QhtFK9GnBhFiL4gPaHGwGzhnm0puldAsuMA';
		$this->url='http://oneonebao.com';
		$this->dataFields=array('first','keyword1','keyword2','keyword3','remark');
	}

	/*
{{first.DATA}}
车牌号：{{keyword1.DATA}}
失败原因：{{keyword2.DATA}}
时间：{{keyword3.DATA}}
{{remark.DATA}}
	------------------------------------------
您好，我们暂时无法给您报价。
车牌号：沪D88C25
失败原因：行驶证不清晰
时间：2016年1月1日 18：36
有任何疑问请致电：95555
	*/
	/**
	 * need field :carNumber;
	 * @see \Api\wx\Template::needToAll()
	 */
	protected function needToAll($need){
		return array(
				'first'=>'您好，我们暂时无法给您报价。',
				'keyword1'=>$need['carNumber'],
				'keyword2'=>$need['reason'],//不太适合
				'keyword3'=>$need['time'],//
				'remark'=>'有任何疑问请致电：95555'
		);
	}
	public function createNeedData($carNumber,$reason,$time){
		return Array(
				'carNumber'=>$carNumber,
				'reason'=>$reason,
				'time'=>$time,
		);
	}
}