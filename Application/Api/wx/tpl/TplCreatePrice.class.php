<?php
namespace Api\wx\tpl;
use Api\wx\Template;
class TplCreatePrice extends Template{
	protected function init(){
		$this->template_id='9WcdxVuKCWXRxCClzbo60B-IFOCPoqsH4RW7akdBGqs';
		$this->url='http://oneonebao.com';
		$this->dataFields=array('first','keyword1','keyword2','keyword3','keyword4','keyword5','remark');
	}
	
/*
{{first.DATA}}
车牌：{{keyword1.DATA}}
投保公司：{{keyword2.DATA}}
车主：{{keyword3.DATA}}
报价金额：{{keyword4.DATA}}
时间：{{keyword5.DATA}}
{{remark.DATA}}
------------------------------------------
车主投保咨询
车牌：粤L88888
投保公司：人保财险
车主：李三
报价金额：16742.19
时间：2014年7月21日13:26
尊敬的客户：你的车险投保咨询已报价，价格仅供参考，以实际出单为准。
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