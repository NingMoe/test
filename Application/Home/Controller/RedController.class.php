<?php
namespace Home\Controller;
use Common\Controller\CommonController;
use Common\Model\ClientModel;
use Common\Model\RedshareModel;
use Api\wx\Method;
use Api\wx\Auth;
use Common\Lib\Tools;
use Api\wx\JsTicket;
class RedController extends CommonController{
	/**
	 * 公共分享红包控制器
	 */
	private $_user;
	private $_list;
	private $auth;
	protected function _initialize(){
		parent::_initialize();

		Method::wl('access url:'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		Method::wl('get:'.getoutstr($_GET));
		
		$this->_user=$this->getClientByUrl();
		
		$this->auth=new Auth();
		$userinfo=$this->getUser();
		if($userinfo){
			//todo 更新用户信息
			 $this->addUserInfo($userinfo);
		}
		$this->_list=$this->getList();
		ee('red index list:'.getoutstr($this->_list),'temp');
	}

	/**
	 * 授权地址
	*/
	public function getShareUrl(){
  		$url='http://oneonebao.com/home/red/cut.html';
//		$url='http://www.oneonebao.com/#out/sharecenter/';http://www.oneonebao.com/#out/sharecenter/160216101975716
		$sid=$this->_user['share_string'];
		if(!$sid){
// 			$sid='160102515010294';
			die('sid error');
		}
 		$redirect_uri=$url.'?sid='.$sid;
//		$redirect_uri=$url.$sid;
    	Method::wl('redirect_uri:'.$redirect_uri);
		$url=$this->auth->getAccessUrl($redirect_uri);
		Method::wl('sq url:'.$url,'temp');
		$result=array('shareUrl'=>$url);
		$this->outJson($result);
	}
	public function cut(){
		$url='http://oneonebao.com/#out/sharecenter/';//www.
		$sid=$this->_user['share_string'];
		$redirect_uri=$url.$sid;
		Method::wl('tourl redirect_uri:'.$redirect_uri);
		$this->toUrl($redirect_uri);
	}
	/**
	 * 点击帮他砍价
	 */
	public function shareCut(){
		if($_GET){

		}else{


		}
		$url='http://oneonebao.com/home/red/shareCut.html';
		$sid=$this->_user['share_string'];
		$sid='160102515010294';
		Method::wl('redirect_uri:'.$url.'?sid='.$sid);
		$url=$this->auth->getAccessUrl($url.'?sid='.$sid);
		
		$this->assign('url',$url);
		$this->display();
	}
	
	//redirect_uri/?code=CODE&state=STATE
	/**
	 * 得到授权后的返回code
	 */
	public function getUser(){

		$code=I('get.code');
		if($code){
			$this->auth->setCode($code);
			$userInfo=$this->auth->getUSerInfo();
			return $userInfo;
		}
		return false;
	}
	
	private function addUserInfo($userinfo){
		Method::wl('addUserInfo in');
		
// 		$client=new ClientModel();//////
// 		$ret=$client->getUserByMap(array('openid'=>$userinfo['openid']));////
// 		if($ret){
// // 			return false;  //用户已存在
// 		}
//$this->_user['id']+openid判断是否砍过
		$userinfo['refresh_token']=$this->auth->getRefreshToken();
		$userinfo['client_id']=$this->_user['id'];
		$userinfo['amount']=Tools::getRedAmount();
		$userinfo['createtime']=NOW_TIME;
		Method::wl('add shareuser info:'.getoutstr($userinfo));
		//todo 判断是否符合砍价
		
		$m=New RedshareModel();
		$ret=$m->addFresh($userinfo);
		if(!$ret){
			die('添加砍价信息失败');
		}
		$this->freshUserRed($userinfo['client_id'], $userinfo['amount']);
	}
	private function freshUserRed($clientId,$redAmount){
		if(!$clientId || !$redAmount){
			die('用户id或红包金额错误');
		}
		$mc=new ClientModel();
		$userInfo=$mc->getUserByMap(array('id'=>$clientId));
		$data['red_usable']=$userInfo['red_usable']+$redAmount;
		$data['red_total']=$userInfo['red_total']+$redAmount;
		$data['id']=$clientId;
		$mc->updateUserInfo($data);
	}
	public function index(){
		ee('red index:','temp');
		$this->contentName='redindex';
		$this->outJson($this->_list,1);
	}
	
	/**
	 * 解析地址得到用户，取得用户信息
	 */
	private  function getClientByUrl(){
		$sid=I('get.sid');
		
		if(empty($sid)){
// 			$sid='160102515010294';  //测试用
			die('地址错误');
		}
		ee('getshare sid:'.$sid,'temp');
		$client=new ClientModel();
		$ret=$client->getUserByMap(array('share_string'=>$sid));

		if(!$ret){
// 			$this->error('用户信息出错',null,1);
die($client->getError());
		}
		return $ret;
	}
	private function getList(){
		$m=new RedshareModel();
		return $m->getShared($this->_user['id']);
	}

}