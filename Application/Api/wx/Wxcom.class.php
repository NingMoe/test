<?php
namespace Api\wx;
use Org\Util\String;
class Wxcom{
	protected $_postObject;
	protected $_fromUserName;
	protected $_toUserName;
	protected $_createTime;
	protected $_msgType;
	protected $_msgId;
	protected $_time;
	
	protected $_event;
	protected $_eventKey;
	protected $_Latitude;//地理位置纬度
	protected $_Longitude;//地理位置经度
	protected $_Precision;//地理位置精度
	
	protected $_ticket;
	
	public function getToUserName(){
		return $this->_toUserName;
	}
	//组装提示信息，HINT_TPL在GlobalDefine.php中定义
	protected function makeHint($hint){
		$resultStr=sprintf(C('HINT_TPL'),$this->_fromUserName,
			$this->_toUserName,$this->_time,'text',$hint);
		return $resultStr;
	}
	/*
	 * 返回菜单数据
	 */
	protected function setUpMenuData($menuId=""){
		$url="http://www.163.com";
		$resultStr=sprintf(C('MENU_TO_URL'),$this->_fromUserName,
			$this->_toUserName,$this->_time,$url);
		return $resultStr;
	}
	/*
	 * $itemArr 图文项的二维数组
	 */
	protected function makeNews($itemArr){
		$num=count($itemArr);
		$newsItems="";
		foreach ($itemArr as $item){
			if(is_array($item)){
				$newsItems.=sprintf(Method::$newsItem,$item['title'],$item['description'],$item['picUrl'],$item['url']);
			}else{
				return false;
			}
		}
		$news=sprintf(Method::$news,$this->_fromUserName,$this->_toUserName,
				$this->_time,'news',$num,$newsItems);
		return $news;
	}
	protected function makeItem($title="",$description="",$picUrl="",$url=""){
		$item=array();
		$item['title']=$title;
		$item['description']=$description;
		$item['picUrl']=$picUrl;
		$item['url']=$url;
		return $item;
	}
	
	public function init($postObj){
		//获取参数
		$this->_postObject=$postObj;
		if($this->_postObject==false){
			return false;
		}
		$this->_fromUserName=(string)trim($this->_postObject->FromUserName);
		$this->_toUserName=(string)trim($this->_postObject->ToUserName);
		$this->_msgType=(string)trim($this->_postObject->MsgType);
		$this->_createTime=(int)trim($this->_postObject->CreateTime);
		$this->_msgId=(int)trim($this->_postObject->MsgId);
		$this->_time=time();
		if($this->_msgType=='event'){
			$this->_event=(string)$this->_postObject->Event;
			$this->_eventKey=$this->_postObject->EventKey;
			if('LOCATION'==$this->_event){
				$this->_Latitude=(string)trim($this->_postObject->Latitude);
				$this->_Longitude=(string)trim($this->_postObject->Longitude);
				$this->_Precision=(string)trim($this->_postObject->Precision);
			}
			if('SCAN'==$this->_event || 'subscribe'==$this->_event){
				$this->_ticket=(string)trim($this->_postObject->Ticket);
			}
		}
		
		if(!($this->_fromUserName && $this->_toUserName && $this->_msgType)){
			return false;
		}
		return true;
	}
	public function process(){
		return $this->makeHint("message not define.");
	}
	
}



