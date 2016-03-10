<?php
namespace Api\wx;
class Userinfo{
	//根据openid得到用户的微信信息。
	public function getUserInfo($openID="om1JBwttuYigeHCbxfioKfM2WI7A"){
		$data['access_token']="";
		$data['openid']=$openID;
		$data['lang']="zh_CN";
		$ret=RequireInterface::reqInterface("user/info", $data,"get");
		if(false===$ret){
			return false;
		}
		return $ret;
	}
}