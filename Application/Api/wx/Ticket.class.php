<?php
namespace Api\wx;
class Ticket{
//https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=TOKEN
//临时二维码请求 {"expire_seconds": 604800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": 123}}}
//永久{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 123}}}
// 或者也可以使用以下POST数据创建字符串形式的二维码参数：
// {"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "123"}}}
// 	private $baseUrl;
	private $interface;
	private $ticket;
	private $qrUrl;
	public function __construct(){
// 		$this->baseUrl='https://api.weixin.qq.com/cgi-bin/qrcode/create';
		$this->interface='qrcode/create';
		$this->qrUrl='https://mp.weixin.qq.com/cgi-bin/showqrcode';
	}
	public function getScene($scene_id){
		if(!$scene_id){
			die('$scene_id empty');
		}
		
		$ticketInfo=$this->queryLimitScene($scene_id);
		Method::wl('$scene_id:'.$scene_id.'--ticketInfo:'.getoutstr($ticketInfo));
		if($ticketInfo['ticket']){
			$this->ticket=$ticketInfo['ticket'];
		}else{
			Method::wl('getScene ticket error');
			return false;
		}
		return $this->showQrcodeUrl();
		
	}
	public function queryLimitScene($scene_id){
		$data=array(
			'action_name'=>'QR_LIMIT_SCENE',
			'action_info'=>array(
				'scene'=>array('scene_id'=>$scene_id)
			)
		);
		$ret=RequireInterface::reqInterface($this->interface,$data);
		Method::wl(getoutstr($ret));
		return $ret;
	}
	private function showQrcodeUrl(){
		//https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=TICKET
		if(!$this->ticket){
			Method::wl('getScene ticket error');
			return false;
		}
// 		$param['ticket']=$this->ticket;
// 		$ret=Method::doCurlGetRequest($this->qrUrl,$param);
		$url=$this->qrUrl.'?ticket='.urlencode($this->ticket);
		return $url;
	}
	public function getTicket(){
		return $this->ticket;
	}
}