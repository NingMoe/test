<?php
namespace Api\wx\tpl;
use Api\wx\Template;
class TplInsureExpired extends Template{
	protected function init(){
		$this->template_id='ZJlewQwV6Vf_CWDQiZDNXtNiqX-yM5fC9U_6GZm_Xdo';
		$this->url='http://oneonebao.com';
		$this->dataFields=array('first','keyword1','keyword2','keyword3','keyword4','keyword5','remark');
	}

	/*
{{first.DATA}}
车型名称：{{keyword1.DATA}}
车牌号码：{{keyword2.DATA}}
到期险种：{{keyword3.DATA}}
保单号码：{{keyword4.DATA}}
到期时间：{{keyword5.DATA}}
{{remark.DATA}}
	------------------------------------------
您好，您在我公司投保的车险快到期了
车型名称：福特
车牌号码：通A88888
到期险种：商业车险
保单号码：POI123455
到期时间：2015年9月9日
为避免脱保，请速来续保
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