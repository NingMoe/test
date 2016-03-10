<?php
namespace Api\wx;
use Common\Model\CtokenModel;
class JsTicket{
	private $appId;
 	private $appSecret;
 	private $saveTicketInfo;//数据库保存的信息
 	public function __construct($appId=NULL, $appSecret=NULL) {
 		$this->appId = null===$appId ? Method::$appID : $appId;
 		$this->appSecret = null===$appSecret ? Method::$appsecret : $appSecret;
 		if(!$this->appId || !$this->appSecret){
 			die('id or secret error');
 		}
 	}
 	
 	public function getSignPackage($url=NULL) {
 		$jsapiTicket = $this->getJsApiTicket();
 	
 		if(null==$url){
 			$url=self::getUrl();
 		}
 	
 		$timestamp = time();
 		$nonceStr = $this->createNonceStr();
 	
 		// 这里参数的顺序要按照 key 值 ASCII 码升序排序
 		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
 	
 		$signature = sha1($string);
 	
 		$signPackage = array(
 				"appId"     => $this->appId,
 				"nonceStr"  => $nonceStr,
 				"timestamp" => $timestamp,
 				"url"       => $url,
 				"signature" => $signature,
 				"rawString" => $string
 		);
 		return $signPackage;
 	}
 	static function getUrl(){
 		// 注意 URL 一定要动态获取，不能 hardcode.
 		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
 		$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
 		return $url;
 	}
 	private function createNonceStr($length = 16) {
 		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
 		$str = "";
 		for ($i = 0; $i < $length; $i++) {
 			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
 		}
 		return $str;
 	}
 	private function getJsApiTicket() {
 		// jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
 		$data = $this->getTicketInfo();
 		if (!$data['jsapi_ticket'] || ($data['js_addtimestamp']+7000) < time() ) {
 			$accessToken = $this->getAccessToken();
 			ee(' access_token:'.$accessToken,'temp');
 			// 如果是企业号用以下 URL 获取 ticket
 			// $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
//  			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
 			$urlBase="https://api.weixin.qq.com/cgi-bin/ticket/getticket";
 			$params=array('type'=>'jsapi','access_token'=>$accessToken);
//  			$res = json_decode($this->httpGet($url));
 			$res=Method::doCurlGetRequest($urlBase, $params);
 			$res=json_decode($res);
 			ee(' getticket ret:'.getoutstr($res),'temp');
 			$ticket = $res->ticket;
 			if ($ticket) {
 				$saveNew['js_addtimestamp'] = time();// + 7000;
 				$saveNew['jsapi_ticket'] = $ticket;
 				$saveNew['js_expires'] = $res->expires_in;
 				$this->saveTicketInfo($saveNew);
 			}else{
 				die('httpGet ticket error:');
 			}
 		} else {
 			$ticket = $data['jsapi_ticket'];
 		}
 		if(!$ticket){
 			die('ticket error:');
 		}
 		return $ticket;
 	}
 	
 	private function getTicketInfo(){
 		if(!$this->saveTicketInfo){
 			$mt=new CtokenModel();
 			$this->saveTicketInfo=$mt->getInfoByAppId($this->appId);
 		}
		ee('token data get saved:'.getoutstr($this->saveTicketInfo),'temp');
 		return $this->saveTicketInfo;
 	}
 	private function saveTicketInfo($data){
 		ee('token data save:'.getoutstr($data),'temp');
 		$mt=new CtokenModel();
 		$mt->where(array('appId'=>$this->appId))->save($data);
 	}
 	
 	private function getAccessToken(){
 		$data=$this->getTicketInfo();

 		if(!$data['access_token'] || ($data['addtimestamp']+7000)<time() ){
 			ee('token expired','temp');
 			$access_token=TokenStub::getToken();
 		}else{
 			$access_token=$data['access_token'];
 		}	
 		return $access_token;
 	}
 	
 	
 	private function getAccessTokenOld() {
 		// access_token 应该全局存储与更新，以下代码以写入到文件中做示例
 		$data = json_decode($this->get_php_file("access_token.php"));
 		if ($data->expire_time < time()) {
 			// 如果是企业号用以下URL获取access_token
 			// $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
 			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
 			$res = json_decode($this->httpGet($url));
 			$access_token = $res->access_token;
 			if ($access_token) {
 				$data->expire_time = time() + 7000;
 				$data->access_token = $access_token;
 				$this->set_php_file("access_token.php", json_encode($data));
 			}
 		} else {
 			$access_token = $data->access_token;
 		}
 		return $access_token;
 	}
 	
 	private function httpGet($url) {
 		$curl = curl_init();
 		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
 		curl_setopt($curl, CURLOPT_TIMEOUT, 500);
 		// 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
 		// 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
 		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
 		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
 		curl_setopt($curl, CURLOPT_URL, $url);
 	
 		$res = curl_exec($curl);
 		curl_close($curl);

 		return $res;
 	}
}