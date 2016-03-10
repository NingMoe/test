<?php
namespace Api\wx;
class TokenStub{
	/*
	 * getToken 鍙栧緱鍏紬璐﹀彿鐨刟ccess_token
	 */
	public static function getToken($force=FALSE){
		$appid=Method::$appID;
		if(!($appid && Method::$appsecret)){
			Method::wl("appId or appSecret not exists");
			return false;
		}
		try {
			$ctoken=M('ctoken');

			$haveret=$ctoken->where("appId='".$appid."'")->find();
// 			cout($havesave);cout($ctoken->data());//涓や釜涓�鏍枫��
// 			$havesave=count($ret);//鏁扮粍鐨勬暟閲忥紝閲岄潰4椤广��
			//force 涓虹湡鍒欎笉妫�鏌ユ暟鎹簱閲屾槸鍚︽湁鍙敤token锛� 鍚﹀垯鍏堟鏌ユ暟鎹簱
			if(false==$force){
				if($haveret){
					//妫�鏌token琛ㄤ腑鐨刟ccess_token鏄惁鍙敤锛岃缃彁鍓�90绉掕繃鏈熷噺灏戞帴鍙ｈ皟鐢ㄨ繑鍥瀉ccess_token杩囨湡鐨勬満浼�
// 					echo "have<br>";
					$row=$ctoken->data();
					$token=$row['access_token'];
					$expire=$row['expires_in'];
					$addTimestamp=$row['addtimestamp'];
// 					cout($row);
					$currentTime=time();
// 					echo $token.",".$expire.",".$addTimestamp.",".$currentTime;
					if($addTimestamp+$expire-90>$currentTime){

						Method::wl("getToken--token 娌¤繃鏈�");
						return $token;
					}
				}//鏁版嵁搴撻噷鑳界敤鍒欒繑鍥烇紝涓嶈兘鐢ㄥ垯鍙栨柊鏁版嵁銆�
			}
			//缁勮鑾峰彇access_token鐨勮姹傚弬鏁帮紝
			$para=array(
					"grant_type"=>"client_credential",
					"appid"=>Method::$appID,
					"secret"=>Method::$appsecret
			);
			//鎷艰url
			$url=Method::$wxApiUrl . "token";
			Method::wl("URL:".$url."  req data:". json_encode($para));
			//鍙戦�乬et璇锋眰
			$ret=Method::doCurlGetRequest($url, $para);
			Method::wl("response data:" . $ret);

			$retData=json_decode($ret,true);
			if(!$retData || @$retData['errorcode']){
				Method::wl("request wx to get token error,errorcode:".$retData['errorcode'].",errmsg:".$retData['errmsg']);
				return false;
			}
			// 浠庤繑鍥炴暟鎹腑鑾峰彇鍒扮殑access_token鍜屼粬鐨勮繃鏈熸椂闂达紝鏇存柊ctoken琛�
			$token=$retData['access_token'];
			$ctoken->create($retData);
			$ctoken->addtimestamp=time();   ///////addTimestamp,T瀛楁鍙堜笉瀵瑰簲浜嗐�� tp閲岀殑闂锛燂紵 涓嶆槸锛屽湪涓嬮潰锛侊紒锛侊紒锛佽繖閲屽ぇ灏忓潎鍙�
			if($haveret){//宸茬粡瀛樺偍杩囧垯鏇存柊锛屽惁鍒欐坊鍔�
				$rett=$ctoken->where("appId='".$appid."'")->save();
				Method::wl('save token');
			}else{
				$ctoken->appId=$appid;
				$ctoken->add();
			}
			
			return $token;
		} catch (DB_Exception $e) {
			Method::wl("operate ctoken error! msg:" . $e->getMessage());
			return false;
		}
	}
}

?>

















