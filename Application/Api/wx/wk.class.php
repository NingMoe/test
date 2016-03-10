<?php
namespace Api\wx;
use Common\Model\ClientModel;
use Common\Model\PositionModel;
use Org\Util\String;
use Admin\Model\PromotionModel;
use Common\Object\Client;
class wk extends Wxcom{
	public function index(){

	}
	public function getUserInfoAndSave(){
// 		$userInfo=new UserinfoController();
// 		$ret=$userInfo->getUserInfo($this->_fromUserName);
// 		$str=var_export($ret,true);
// 		wl("getUserInfo ret:".$str);
// 		if(!$ret){
// 			return false;
// 		}
// 		$data['openid']=$ret['openid'];
// 		$data['nickname']=$ret['nickname'];
// 		$data['sex']=$ret['sex'];
// 		$data['language']=$ret['language'];
// 		$data['city']=$ret['city'];
// 		$data['province']=$ret['province'];
// 		$data['country']=$ret['country'];
// 		$data['headimgurl']=$ret['headimgurl'];
// 		$data['subscribe_time']=$ret['subscribe_time'];
// 		$mUser=M('user');
// 		$have=$mUser->where("openid='".$data['openid']."'")->select();

// 		if($have){
// 			wl("getUserInfoAndSave have:".count($have));
// 			return;//
// 		}
// 		$rs=$mUser->add($data);//有多余字段create不成功?create,add不能连写///fetchSql(true)->
// 		wl("ret: ".$rs);
// 		if($rs){
// 			wl("add userinfo 成功 rs:$rs");
// 		}else{
// 			wl("add userinfo 失败  rs:$rs");
// 		}
	}
	private function deleteUser(){
		$mUser=M('user');
		$mUser->where("openid='".$this->_fromUserName."'")->delete();
	}
	private function getLocation(){
		$openid=$this->_fromUserName;
		if(!$openid){
			return -1;
		}

		$data=array(
			'latitude'=>(String)$this->_Latitude,
			'longitude'=>(String)$this->_Longitude,
			'precision'=>(String)$this->_Precision,
		);
		Method::wl('pos:'.getoutstr($data));
		
		$mp=new PositionModel();
		$mp->addLocationDataFromOpenid($data, $openid);
	}
	
	private function freshPromotion($promotionInfo,$newUser=NULL){
		Method::wl('12');
		Method::wl('new user:'.$newUser);
		$result['id']=$promotionInfo['id'];
		$result['scan_num']=$promotionInfo['scan_num']+1;
// 		unset($promotionInfo['reply_imgid']);
		if(null!=$newUser && $newUser){
			$result['scan_users']=$promotionInfo['scan_users']+1;
		}
		$mp=new PromotionModel();
		$mp->freshInfo($result);
	}
	/**
	 * 扫码推广，返回对应图文信息
	 * @return boolean|Ambigous <boolean, string>
	 */
	private function promotion(){
		$result="";//返回对应推广者的图文消息
		//todo 更新推广数据
		if(!$this->_ticket){
// 			return false;//没有推广新信息
			return $this->makeHint('欢迎关注');
		}
		$promotionInfo=$this->getPromotionInfo($this->_ticket);
		
		Method::wl('$promotionInfo:'.getoutstr($promotionInfo));
		if($promotionInfo){  //有推广信息时才处理
			//todo add user
			
			$map['openid']=$this->_fromUserName;
			Method::wl('todo add user:'.$map['openid']);
			$mc=new ClientModel();
			$ret=$mc->where($map)->find();
			$newUserId=null;
			if(!$ret){//用户不存在
				$map['promotion_id']=$promotionInfo['id'];
				$map['origin']=1;
				Method::wl('1user not exist:'.getoutstr($map));
				Method::wl('1user not exist2:');
//				$mc=new ClientModel();
				$newUserId=$mc->addUser($map);
				Method::wl('9 $newUserId:'.getoutstr($newUserId));
				if(!$newUserId){
					Method::wl('promotionid:'.$promotionInfo['id'].'--add scan user error');
				}
				Method::wl('10:');
			}
			Method::wl('11:');
			$this->freshPromotion($promotionInfo,$newUserId);
			$result=$this->getPromotionReplyNews($promotionInfo);
			Method::wl('promotion reply $result:'.getoutstr($result));
			return $result;
		}
		//get reply news;
		
	}
	private function getPromotionReplyNews($promotionInfo){
		$arr=$this->makeItem($promotionInfo['title'],$promotionInfo['description'],
				$promotionInfo['reply_imgid'],$promotionInfo['reply_url']);
		$items=array($arr);
		return 	$this->makeNews($items);
	}
	private function getPromotionInfo($ticket){
		if(!$ticket){
			return false;
		}
		$mp=new PromotionModel();
		return $mp->getWxReplyInfo(array('ticket'=>$ticket));
	}
	private function subscribe(){
		Method::wl('subscribe fromuser:'.$this->_fromUserName);
		Method::wl('subscribe fromticket:'.$this->_ticket);
		
		return $this->promotion();
// 		return $this->makeHint("欢迎关注");
	}
	public function process(){
		Method::wl("process in");
		Method::wl('msgType:' . $this->_msgType);
		if($this->_msgType=='event'){
			$event=$this->_event;
			Method::wl('$event:' . $event);
			switch ($event){
				case 'subscribe':
					return $this->subscribe();
				case 'unsubscribe':
					$this->deleteUser();
					return $this->makeHint("欢迎再来!");
				case 'CLICK':
					return $this->makeHint($this->_eventKey."菜单开发中。。。");
				case 'LOCATION':
					return $this->getLocation();
				case 'SCAN':
					return $this->promotion();
				default:
					return $this->makeHint("该事件暂不处理!");
			}
		}
		$userContent=$this->_postObject->Content;
		//返回个人资料
		if($userContent=="myinfo"){
			$userInfo=new Userinfo();
			$userContent=var_export($userInfo->getUserInfo($this->_fromUserName),true);
			return $this->makeHint("你的info 是:".$userContent);
		}
		if($userContent=="notify"){
			Method::wl("notify--");
			$notify=new Notify();
			$ret=$notify->test();
			if(!$ret){
				$userContent=var_export($ret,true);
				return $this->makeHint("你的notify ret是:".$userContent);
			}else{
				Method::wl("process --notify success-*************");
				return $this->makeHint("通知成功，感谢关注");
			}
			
		}
		if($userContent=="news"){
			$arr=$this->makeItem("title标题","description感谢订阅，请绑定手机号!",
							"http://f.hiphotos.baidu.com/image/pic/item/4b90f603738da9771c9d6cc9b351f8198718e3a6.jpg","http://www.baidu.com");
			$items=array($arr,$arr,$arr);
			return 	$this->makeNews($items);
		}
		if($userContent=="new"){
			$arr=$this->makeItem("title标题","description感谢订阅，请绑定手机号!",
					"http://f.hiphotos.baidu.com/image/pic/item/4b90f603738da9771c9d6cc9b351f8198718e3a6.jpg","http://www.baidu.com");
			$items=array($arr);
			return 	$this->makeNews($items);
		}
		//返回发送内容
		Method::wl('this->_postObject->Content:' . $userContent);
		return $this->makeHint("你发送的是:".$userContent);
	}
}