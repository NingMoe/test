<?php
namespace Api\wx\tpl;
use Api\wx\Template;
class TplInsureEffect extends Template{
	protected function init(){
		$this->template_id='b6WGL31GqByb4uc0zUTHIuczfgqIPThbnlR14IWoTDk';
		$this->url='http://oneonebao.com';
		$this->dataFields=array('first','policyno','product','contno','appdate','remark');
	}

	/*
{{first.DATA}}

保单号：{{policyno.DATA}}
险种：{{product.DATA}}
投保人：{{contno.DATA}}
生效日期：{{appdate.DATA}}
{{remark.DATA}}
	------------------------------------------
您好，您的客户 张三 的保单生效啦！

保单号：201400000009
险种：鸿发年年及其附加险
投保人：张三
生效日期：2014年3月20日
本单FYC：1000.01元

注：具体金额以公司发放为准，FYC金额仅供参考！
如有疑问，请在“更多--小薇客服”里留言~
	*/
	/**
	 * need field :carNumber;
	 * @see \Api\wx\Template::needToAll()
	 */
	protected function needToAll($need){
		return array(
			'first'=>'报价完成',
			'keyword1'=>$need['carNumber'],
			'keyword2'=>$need['company'],//不太适合
			'keyword3'=>$need['owner'],//
			'keyword4'=>$need['minPrice'],//
			'keyword5'=>$need['createTime'],
			'remark'=>'尊敬的客户：你的车险投保咨询已报价，价格仅供参考，以实际出单为准。'
		);
	}
	public function createNeedData($carNumber,$company,$owner,$minPrice,$createTime){
		return Array(
				'carNumber'=>$carNumber,
				'company'=>$company,
				'owner'=>$owner,
				'minPrice'=>$minPrice,
				'createTime'=>$createTime
		);
	}
}