<?php
namespace Home\Controller;
use Think\Controller;
use Common\Object\SessionManage;
use Common\Controller\CommonController;
use Common\Model\ClientModel;
use Api\wx\Auth;
use Api\wx\Method;
use Common\Lib\Golbal;
use Api\wx\JsTicket;
/**
 * 用户登录及注册
 */
class CopyofUserController extends CommonController{
	private $_clientM;

	public function getJsTicketPackage(){
		ee('getJsTicketPackage start:','temp');
		$url=JsTicket::getUrl();
	
		ee('getjs url:'.$url,'temp');
		// 		echo "url:".$url;
		$url='http://www.oneonebao.com/';
		$js=new JsTicket();
		$ret=$js->getSignPackage($url);
		ee('getjs getSignPackage:'.getoutstr($ret),'temp');
		// 		ct($ret);
		$this->outJson($ret);
	}
	
	public function _initialize(){
		parent::_initialize();
		$this->_clientM=new ClientModel();
	}
	/* 退出登录 */
	public function logout(){
		$this->_clientM->logout();
// 		$this->redirect('User/login');
	}
	
	public function isLogin($name='isLogin'){
		$user=SessionManage::getUser();
// 		$user=$this->_clientM->getCurUser();
		ee('isLogin usersession:'.getoutstr($user));
		if(!$user){
			$guest=array('userid'=>'-1');
		}else{
			$guest=array('userid'=>$user['id']);
		}
		$result=array($name=>$guest);
		$this->outJson($result);
	}
	
	/* 注册页面 */
	public function register(){
		SessionManage::EmptyUser();
		if(IS_POST){ //注册用户
			// 			/* 检测验证码 */
			ee('s--getcode:'.SessionManage::getCode().'--srcode:'.I('post.phoneCode'),'temp');
			if(SessionManage::getCode()!=I('post.phoneCode')){
				$this->ko('验证码输入错误！');
			}
			ee('$user rr','temp');
			//注册用户
			if($this->_clientM->register()){
				//注册成功，转到首页
// 				$this->success('登录成功！',U('index/index'));
				
				
			}else{
				//注册失败，显示错误信息
// 				$this->error($this->_clientM->getError());
			}
			$this->isLogin('register');
		
		} else { //显示注册表单
			$this->assign('action',$this->_urlAction);
			$this->display('login');
		}
	}
	/* 登录页面 */
	public function login(){
		
		SessionManage::EmptyUser();
		if(IS_POST){ 
			//注册用户
			//有新登录，empty session
			if($this->_clientM->login()){
				//登录成功，转到首页
// 				$this->success('登录成功！',U('te'),1);
				
			
			}else{
				//登录失败，显示错误信息
// 				$this->error($this->_clientM->getError());

			}
			
			$this->isLogin('login');
			
// 			} else { //登录失败
// 				switch($uid) {
// 					case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
// 					case -2: $error = '密码错误！'; break;
// 					default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
		} else { //显示登录表单
			$this->assign('action',$this->_urlAction);
			$this->display();
			
		}
	}
	public function loginByWx(){
		$auth=new Auth();
		$userInfo=$auth->getUSerInfo();
		$loginInfo=$this->_clientM->loginUserByThird($userInfo);
		$url='';
		if(0===$loginInfo || -1===$loginInfo){//授权失败   授权信息不存在或授权数据信息有误
//返回之前页
			$url='http://oneonebao.com/#mimalogin';
		}elseif (3===$loginInfo){
//存在登录成功 返回主页
			$url='http://oneonebao.com/#home';
		}elseif (1===$loginInfo){  //2===$loginInfo || 
//授权成功，用户信息不存在 ，绑定账户
			$url='http://oneonebao.com/#yanzhenglogin';
// 			$this->error('绑定手机账户',U('user/Login'),1);
		}
		
		$this->toUrl($url);
// 		$this->isLogin($name);
	}
	public function test(){
		$url='http://oneonebao.com/#home';
// 		$this->redirect($url,null,3);
		header("location:{$url}");
	}
	public function bindMobile(){
		$uid=I('post.id');
		$phone=I('post.phone');
		if(!$uid || !$phone){
			//id或电话不存在
		}
	}
	/**
	 * 取到授权地址
	 * @return string
	 */
	public function getAuthUrl(){
		$relative=U('user/loginByWx');
// 		$redirectUri='http://'.$_SERVER['HTTP_HOST'].$relative;
		$redirectUri=Golbal::getDomain().$relative;
		Method::wl('login redirect_uri:'.$redirectUri);
		$auth=new Auth();
		echo $auth->getAccessUrl($redirectUri);
	}
//发送手机验证码
	public function getCode(){
		ee('gecode'.var_export($_POST,true),'temp');
		$sms=new \Api\Sms\SmsManager();
		$result=$sms->sendCode($_POST['phoneNum']);
		ee('result:'.var_export($result,true),'temp');
		$this->outJson(array('getCode'=>$result));
	}
	/**
	 * 未登陆车牌信息
	 */
	public function getCars(){

		$index=0;
	
		$result[$index]['id']=-1;
		$result[$index]['carnumber']='一键询价';
		$result[$index]['action']='yijianxunjia';
	

		$index++;
		$result[$index]['id']=-2;
		$result[$index]['carnumber']='添加车辆';
		$result[$index]['action']='addcar';
	
		$this->contentName='homeinfo';
		$this->outJson($result,1);
	}
//-----------------------------	
	/**
	 * 获取用户注册错误信息
	 * @param  integer $code 错误编码
	 * @return string        错误信息
	 */
	private function showRegError($code = 0){
		switch ($code) {
			case -1:  $error = '用户名长度必须在16个字符以内！'; break;
			case -2:  $error = '用户名被禁止注册！'; break;
			case -3:  $error = '用户名被占用！'; break;
			case -4:  $error = '密码长度必须在6-30个字符之间！'; break;
			case -5:  $error = '邮箱格式不正确！'; break;
			case -6:  $error = '邮箱长度必须在1-32个字符之间！'; break;
			case -7:  $error = '邮箱被禁止注册！'; break;
			case -8:  $error = '邮箱被占用！'; break;
			case -9:  $error = '手机格式不正确！'; break;
			case -10: $error = '手机被禁止注册！'; break;
			case -11: $error = '手机号被占用！'; break;
			default:  $error = '未知错误';
		}
		return $error;
	}
}
