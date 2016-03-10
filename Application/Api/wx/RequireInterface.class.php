<?php
namespace Api\wx;
class RequireInterface{
	static public function reqInterface($interface,$data=array(),$method="post"){
		$token=TokenStub::getToken();
		//retry 3 times
		$retry=3;
		while($retry){
			$retry--;
			if(false===$token){
				Method::wl("get token error!");
				return false;
			}
			$url=Method::$wxApiUrl . $interface;      //?";get多个问好
			if("post"==$method){
				$url.="?access_token=" . $token;
				$chjsondata=Method::ch_json_encode($data);
				Method::wl("post req url:".$url." post require data:" . var_export($chjsondata,1));
				$ret=Method::doCurlPostRequest($url,$chjsondata);
			}else if("get"==$method){
 				$data['access_token']=$token;  ///顺序不要搞错
				Method::wl("get req url:".$url." get req data:" . $data);
				$ret=Method::doCurlGetRequest($url, $data);
			}
			Method::wl("response:".$ret);
				
			$retData=json_decode($ret,true);//当该参数为 TRUE 时，将返回 array 而非 object 。
			if(!$retData||$retData['errcode']){
				Method::wl("req reqInterface  error");
				if($retData['errcode']==40014 || $retData['errcode']==40001){  //token 过期  ///40001自己加的invalid credential, access_token is invalid or not latest
					$token=tokenStub::getToken(true);
				}
			}else{
				return $retData;
			}
		}
		return false;
	}
}