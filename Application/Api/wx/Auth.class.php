<?php
namespace Api\wx;
class Auth{
	//微信服务号，网页授权
	//第一步：用户同意授权，获取code
	//https://open.weixin.qq.com/connect/oauth2/authorize?appid=APPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=STATE#wechat_redirect
	private $redirect_uri;
	private $appId;
	private $appSecret;
	private $baseUrl_token;
	private $baseUrl_access;
	private $baseUrl_userInfo;
	private $access_extra;
	private $code;
	private $access_token;
	private $openId;
	private $refresh_token;
	
	public function __construct($redirect_uri=""){
		$this->redirect_uri=$redirect_uri;
		$this->baseUrl_access='https://open.weixin.qq.com/connect/oauth2/authorize';
		$this->access_extra='#wechat_redirect';
		$this->baseUrl_token='https://api.weixin.qq.com/sns/oauth2/access_token';
		$this->baseUrl_userInfo='https://api.weixin.qq.com/sns/userinfo';
		$this->appId=Method::$appID;
		$this->appSecret=Method::$appsecret;
		
// 		$this->appId='wx28e2d5f1a31d1a2b';
// 		$this->appSecret='563ba7d8c2c7523de3d64239dc98e73a';
		
		$code=I('get.code');
		if($code){
			$this->setCode($code);
		}
	}
	
	public function getAccessUrl($redirect_uri=null){
		if(null!=$redirect_uri){
			$this->redirect_uri=$redirect_uri;
		}
		$param['appid']=$this->appId;
		$param['redirect_uri']=$this->redirect_uri;
		$param['response_type']='code';
		$param['scope']='snsapi_userinfo';   //snsapi_base    //snsapi_userinfo
		$param['state']=123;		//重定向后会带上state参数，开发者可以填写a-zA-Z0-9的参数值，最多128字节
		
		$fullUrl=$this->baseUrl_access . '?' . http_build_query($param) . $this->access_extra;
		Method::wl('getAccessUrl fullurl:'.$fullUrl);
		return $fullUrl;
	}
// 	如果用户同意授权，页面将跳转至 redirect_uri/?code=CODE&state=STATE
/**
 * code从回调页，接手
 */
	public function setCode($code){
		if(!$code){
			die('code不正确');
		}
		$this->code=$code;
	}
	/**
	 * 通过code 取得token 的url
	 *///?appid=APPID&secret=SECRET&code=CODE&grant_type=authorization_code
	public function getToken(){
		if(!$this->code){
			die('code没设置');
		}
		
		$param['appid']=$this->appId;
		$param['secret']=$this->appSecret;
		$param['code']=$this->code;
		$param['grant_type']='authorization_code';
		
		$ret=Method::doCurlGetRequest($this->baseUrl_token, $param);
		
		$result=json_decode($ret,1);
		
		Method::wl('gettoken return:'.getoutstr($result));
		
		if(!$result){
			die('获取token信息出错');
		}
		
		return $result;
	}
	/**
	 * 取得用户信息
	 */
	public function getUSerInfo(){
		//https://api.weixin.qq.com/sns/userinfo?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN
		if(!$this->access_token || !$this->openid){
			$ret=$this->getToken();
			if(!$ret){
				die('获取token出错');
			}
			$this->access_token=$ret['access_token'];
			$this->openId=$ret['openid'];
			$this->refresh_token=$ret['refresh_token'];
		}
		
		ee('auth getuserinfo openid:'.$this->openId,'temp');
		
		$data['access_token']=$this->access_token;
		$data['openid']=$this->openId;
		$data['lang']='zh_CN';
		
		$ret=Method::doCurlGetRequest($this->baseUrl_userInfo, $data);
		$result=json_decode($ret,1);
		
		method::wl('auth getUSerInfo ret:'.getoutstr($result));
		
		if(!$result){
			die('获取用户信息出错');
		}
		
		return $result;
	}
	public function getRefreshToken(){
		return $this->refresh_token;
	}
}