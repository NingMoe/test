<?php
namespace Api\wx\tpl;
use Api\wx\Template;
class TplLogin extends Template{
	protected function init(){
		$this->template_id='DaCwkwKa3QsrIdFIdsSMsVlJ5lMLXWuh2JUYI8BdBGs';
		$this->url='http://oneonebao.com';
		$this->dataFields=array('first','keyword1','keyword2','keyword3','remark');
	}

	/*
	{{first.DATA}}
账号：{{keyword1.DATA}}
微信号：{{keyword2.DATA}}
时间：{{keyword3.DATA}}
{{remark.DATA}}
	------------------------------------------
	你好，你的账号在网站登录
账号：昆昆
微信号：ettt
时间：2014年8月28日16:02:33
若非本人操作，请尽快修改密码 
	*/
	/**
	 * need field :mobile;nickname;loginTime
	 * @see \Api\wx\Template::needToAll()
	 */
	protected function needToAll($need){
		return array(
				'first'=>'您好，您的账号在壹壹保网登录',
				'keyword1'=>$need['mobile'],
				'keyword2'=>$need['nickname'],
				'keyword3'=>$need['loginTime'],
				'remark'=>'若非本人操作，请尽快修改密码 '
		);
	}
	public function createNeedData($mobile,$nickname,$loginTime){
		return array(
			'mobile'=>$mobile,
			'nickname'=>$nickname,
			'loginTime'=>$loginTime
		);
	}
}