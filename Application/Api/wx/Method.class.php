<?php
namespace Api\wx;
class Method{
	//方法类
	static public $appID='wx19a978f8557d81a3';
	static public $appsecret='25e42ccf5063d355ef6cda2917ba362e';
	static public $token='WEIXIN5TOKEN11';
	static public $EncodingAESKey='UGncTcOGeHWCMILN6ArlctsviGqSaleZsZTzyvZrzrE';
	//微信接口地址
	static public $wxApiUrl='https://api.weixin.qq.com/cgi-bin/';
	//普通消息模板
	static public $hintTpl ="<xml>
				  <ToUserName><![CDATA[%s]]></ToUserName>
				  <FromUserName><![CDATA[%s]]></FromUserName>
				  <CreateTime>%s</CreateTime>
				  <MsgType><![CDATA[%s]]></MsgType>
				  <Content><![CDATA[%s]]></Content>
				  <FuncFlag>0</FuncFlag>
				  </xml>";
	
    //图文消息模板
	static public $news="<xml>
	<ToUserName><![CDATA[%s]]></ToUserName>
	<FromUserName><![CDATA[%s]]></FromUserName>
	<CreateTime>%s</CreateTime>
	<MsgType><![CDATA[%s]]></MsgType>
	<ArticleCount>%d</ArticleCount>
	<Articles>%s</Articles>
	</xml>";
		//图文item
	static public $newsItem="<item>
	<Title><![CDATA[%s]]></Title>
	<Description><![CDATA[%s]]></Description>
	<PicUrl><![CDATA[%s]]></PicUrl>
	<Url><![CDATA[%s]]></Url>
	</item>";
	//菜单跳转链接
	static public $menuToUrl="<xml>
	<ToUserName><![CDATA[%s]]></ToUserName>
	<FromUserName><![CDATA[%s]]></FromUserName>
	<CreateTime>%s</CreateTime>
	<MsgType><![CDATA[event]]></MsgType>
	<Event><![CDATA[VIEW]]></Event>
	<EventKey><![CDATA[%s]]></EventKey>
	</xml>";
		
	static function checkSignature(){
		@$signature=$_GET["signature"];
		@$timestamp=$_GET["timestamp"];
		@$nonce=$_GET["nonce"];
		
		Method::wl('$signature:'.$signature.'--$timestamp:'.$timestamp.'--$nonce:'.$nonce);
		
		$token=self::$token;
		$tmpArr=array($token,$timestamp,$nonce);
	
		sort($tmpArr);
		$tmpStr=implode($tmpArr);
		$tmpStr=sha1($tmpStr);
	
		Method::wl('tmpStr:'.$tmpStr.'--token:'.$token);
		
		if($tmpStr==$signature){
			return true;
		}else{
			return false;
		}
	}
	/**
	 * @desc 封装curl调用接口，get的请求方式
	 */
	static function doCurlGetRequest($url,$data,$timeout=5){
		if($url==""||$timeout<=0){
			return false;
		}
		$url=$url . '?' . http_build_query($data);

		$ch=curl_init($url);
		curl_setopt($ch,CURLOPT_HEADER,false);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)$timeout);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	
		return curl_exec($ch);
	}
	/*
	 * @desc 封装curl的调用接口，post的请求方式
	*
	*/
	static function doCurlPostRequest($url,$requestString,$timeout=5){
		if($url==""||$timeout<=0){       /////!!!!!!$url=""!!!!!!!!!!
			die('url empty');
			return false;
		}
	
		$ch=curl_init((string)$url);
		curl_setopt($ch, CURLOPT_HEADER,false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $requestString);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)$timeout);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//这两行没有还是去不到https数据
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	
		return curl_exec($ch);
	}
	
	/**
	 * 对数组和标量进行urlencode处理
	 * 通常调用wphp_json_encode
	 * 处理json_encode中文显示的问题
	 * @param array $data
	 * @return string
	 */
	static function wphp_urlencode($data){
		if(is_array($data)||is_object($data))
		{
			foreach ($data as $k=>$v){
				if(is_scalar($v)){
					if(is_array($data)){
						$data[$k]=urlencode($v);
					}elseif (is_object($data)){
						$data->$k=urlencode($v);
					}
				}elseif (is_array($v)){
					$data[$k]=self::wphp_urlencode($v);//递归调用该函数
				}elseif (is_object($v)){
					$data->$k=self::wphp_urlencode($v);
				}
			}
		}
		return $data;
	}
	/*
	 * json编码
	* 解决中文经过json_encode()处理后显示不直观情况
	* 如默认会将“中文”变成"\u4e2d\u6587"，不直观
	* 如无特殊需求，并不建议使用该函数，直接使用json_encode更好，省资源
	* json_encode()的参数编码格式为utf—8时方可正常工作
	*
	* @param array|object $data
	* @return array|object
	*/
	static function ch_json_encode($data){
		$ret=self::wphp_urlencode($data);
		$ret=json_encode($ret);
		return urldecode($ret);
	}
	//获取来源ip
	static function getIp() {
		if (isset ( $_SERVER )) {
			if (isset ( $_SERVER ["HTTP_X_FORWARDED_FOR"] )) {
				$realip = $_SERVER ["HTTP_X_FORWARDED_FOR"];
			} else if (isset ( $_SERVER ["HTTP_CLIENT_IP"] )) {
				$realip = $_SERVER ["HTTP_CLIENT_IP"];
			} else {
				$realip = $_SERVER ["REMOTE_ADDR"];
			}
		} else {
			if(getenv("HTTP_X_FORWARDED_FOR")){
				$realip=getenv("HTTP_X_FORWARDED_FOT");
			}elseif (getenv("HTTP_CLIENT_IP")){
				$realip=getenv("HTTP_CLIENT_IP");
			}else{
				$realip=getenv("REMOTE_ADDR");
			}
		}
		return $realip;
	}
	
	static function getLogPath(){
		$path=dirname(__FILE__).'/log/';
		if(!is_dir($path)){
			mkdir($path);
		}
		return $path;
	}
	static function getLogName(){
		return 'wl'.date('md').'.log';
	}
	//实际用到的日志函数
	static function wl($logMessage="no msg"){
		self::w_log(self::getLogName(),$logMessage);
	}
	
    static function w_log($confName,$logMessage="no msg"){
		$st=debug_backTrace();
		$function='';//调用w_log的函数名
		$file='';//调用w_log的文件名
		$line='';//调用w_log的行号
		foreach($st as $item){
			if($file){
				$function=$item['function'];
				break;
			}
			if($item['function']== 'wl'){
				$file=$item['file'];
				$line=$item['line'];
			}
		}
		$function=$function ? $function : 'main';
		//为缩短日志的输出，file只取最后一部分文件名
		$file=explode("\\",rtrim($file,'/'));
		$file=$file[count($file)-1];
		//组装日志的头部
		$prefix="[$file][$function][$line]";
		$logFileName=$confName;
		$logobj=MLog::instance(self::getLogPath());
		$logobj->log($logFileName,$prefix . $logMessage);
	}
} 