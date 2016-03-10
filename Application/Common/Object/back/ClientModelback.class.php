<?php
namespace Common\Model;
use Think\Model;
use User\Lib\UserC;
use Common\Object\SessionManage;
use Common\Lib\Tools;
use Common\Object\ClientAction;
use Common\Object\Drivinglicense;
use Common\Object\Dispatch;
class ClientModel22 extends Model{
	private $_userInfo;
	
// 	/* 用户模型自动验证 */
// 	protected $_validate = array(
// 			/* 验证用户名 */
// 			array('username', '1,30', -1, self::EXISTS_VALIDATE, 'length'), //用户名长度不合法
// 			array('username', 'checkDenyMember', -2, self::EXISTS_VALIDATE, 'callback'), //用户名禁止注册
// 			array('username', '', -3, self::EXISTS_VALIDATE, 'unique'), //用户名被占用
	
// 			/* 验证密码 */
// 			array('password', '6,30', -4, self::EXISTS_VALIDATE, 'length'), //密码长度不合法
	
// 			/* 验证邮箱 */
// 			array('email', 'email', -5, self::EXISTS_VALIDATE), //邮箱格式不正确
// 			array('email', '1,32', -6, self::EXISTS_VALIDATE, 'length'), //邮箱长度不合法
// 			array('email', 'checkDenyEmail', -7, self::EXISTS_VALIDATE, 'callback'), //邮箱禁止注册
// 			array('email', '', -8, self::EXISTS_VALIDATE, 'unique'), //邮箱被占用
	
// 			/* 验证手机号码 */
// 			array('mobile', '//', -9, self::EXISTS_VALIDATE), //手机格式不正确 TODO:
// 			array('mobile', 'checkDenyMobile', -10, self::EXISTS_VALIDATE, 'callback'), //手机禁止注册
// 			array('mobile', '', -11, self::EXISTS_VALIDATE, 'unique'), //手机号被占用
// 	);
	
// 	/* 用户模型自动完成 */
// 	protected $_auto = array(
// 			array('password', 'think_ucenter_md5', self::MODEL_BOTH, 'function', UC_AUTH_KEY),
// 			array('reg_time', NOW_TIME, self::MODEL_INSERT),
// 			array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
// 			array('update_time', NOW_TIME),
// 			array('status', 'getStatus', self::MODEL_BOTH, 'callback'),
// 	);

	/**
	 * 通过字段名   和值，取得用户信息
	 * map信息
	 */
	public function getUserByMap($map){
		$result=$this->where($map)->find();
		return $result;
	}
	/**
	 * 得到当前用户信息
	 * @return array 当前用户信息
	 */
	public function getCurUser(){
		$users=$this->getUserSession();
		if (null===$users){
			return null;
		}
		$userInfo=$this->getUserInfoByS($users);
		if(empty($userInfo) || !is_array($userInfo) || !isset($userInfo)){
			return null;
		}
		$md=new DrivinglicenseModel();
		$userInfo['drivings']=$md->getDrivings($userInfo['id']);
// 		var_dump(Tools::isValid($userInfo['driving']));
		return $userInfo;
	}
	
	private function getUserInfoByS($data){
		return $this->where($data)->find();
	}
	
	/**
	 * 注销当前用户
	 * @return void
	 */
	public function logout(){
		$this->nullUserSession();
	}
		
	/**
	 * 注册一个新用户
	 * @param  string $username 用户名
	 * @param  string $password 用户密码
	 * @param  string $email    用户邮箱
	 * @param  string $mobile   用户手机号码
	 * @return integer          注册成功-用户信息，注册失败-错误编号
	 */
	public function register(){
		$data = array(
				'mobile'   => I('post.phone'),
				'password' =>  UserC::pwMd5(I('post.password')),
				'last_login_time' => NOW_TIME,
				'last_login_ip'   => get_client_ip(1)
		);
		$uid=I('post.id');
		if($uid){
			//微信绑定
			$user=$this->getUserByMap(array('id'=>$uid));
		}else{
			$user=$this->findUserByMobile($data['mobile']);
		}
		//用户已经存在
		if($user){
			$this->setUserSession($user);
			if(!$user['service_id']){
				$data['service_id']=$this->getServiceId();
			}
			$thirdData=$this->getThirdSession();
			if($thirdData){
				
			}
			return $this->freshUser($data);
		}

		/* 添加用户 */ //新用户增加注册时间
		$data['reg_time']=$data['last_login_time'];
		//分配客服
		
		$data['service_id']=$this->getServiceId();
		if($this->create($data)){
			$uid = $this->add();
			$this->setUserSession($this->findUserByMobile($data['mobile']));
			return $uid ? $uid : 0; //0-未知错误，大于0-注册成功
		} else {
			return $this->getError(); //错误详情见自动验证注释
		}
	}
	
	private function getServiceId(){
		$dispatch=Dispatch::getInstace();
		$result=$dispatch->getAssignServiceId();
		return $result;
	}
	/**
	 * 登录指定用户
	 * @param  integer $uid 用户ID
	 * @return boolean      ture-登录成功，false-登录失败
	 */
	public function login(){
		$data = array(
				'mobile'   => I('post.phone'),
				'password' => UserC::pwMd5(I('post.password')),
				'last_login_time' => NOW_TIME,
				'last_login_ip'   => get_client_ip(1)
		);

		if($data['password']==$this->getPwByMobile($data['mobile'])){
			$this->freshUser($data);
			$user=$this->findUserByMobile($data['mobile']);
			
			//记录登录行为
			$action=new ClientAction($user['id']);
			$action->login();
			
// 			if(1 != $userInfo['status']){
// 				$this->error = '用户未激活或已禁用！';
// 				return false;
// 			}
			$this->setUserSession($user);
		}else{
			$this->error = '用户不存在或密码错误！'; 
			return false;
		}

		return true;
	}
	/**
	 * 
	 * @param array $userdata 第三方信息数据
	 * @param number $type 登录类型
	 * @return boolean
	 */
	public function loginUserByThird($userdata,$type=1){
		if(!$userdata){
			$result=0;//授权数据信息不存在
			return $result;
		}
		if(1===$type){
			if(!$userdata['openid']){
				$result=-1;//微信授权数据信息有误
				return $result;
			}
			$map=array('openid'=>$userdata['openid']);
		}
		$userInfo=null;
		if($map){
			$userInfo=$this->getUserByMap($map);
		}
		//绑定完手机才增加用户信息
		if(!$userInfo){
			//用户信息不存在，然后下一步绑定手机
			$this->setThirdSession($userdata);
			$result=1;//绑定手机
			return $result;
		}
		
		$userInfo['last_login_time']= NOW_TIME;
		$userInfo['last_login_ip']=get_client_ip(1);
		
		$this->freshUser($userInfo);

		//记录登录行为
		$action=new ClientAction($userInfo['id']);
		$action->login();

		$this->setUserSession($userInfo);
	
		$result=3;  //正常登录成功
		if(!$userInfo['mobile']){
			//手机未绑定
			$userInfo['mobile']=-5; //兼容session
			$result=2;//登录成功，手机未绑定--放哪？
		}
		
		return $result;
	}
	/**
	 * 绑定手机
	 */
	public function bindMobile(){
		
	}
	/**
	 * 更新用户数据
	 * @return boolean
	 */
	public function updateUserInfo($data){
		if(!$data['id'] && $data['mobile']){
			$this->error='用户id或手机号为空不能更新';
			return false;
		}
// 		if($data['mobile']){
// 			return $this->freshUser($data);
// 		}
		$ret=$this->where('id='.$data['id'])->save($data);
		if(!$ret){
			$this->error='更新用户信息失败';
		}
		return $ret;
	}
	private function setThirdSession($thirdData){
		SessionManage::setThirdData($thirdData);
	}
	private function getThirdSession(){
		return SessionManage::getThirdData();
	}
	private function getUserSession(){
		return SessionManage::getUser();
	}
	private function nullUserSession(){
		SessionManage::EmptyUser();
	}
	private function setUserSession($user){
		SessionManage::setUser($user);
		ee("session-user:".var_export(SessionManage::getUser(),1),'temp');
	}
	//通过手机号查找用户
	private function findUserByMobile($mobile){
		return $this->where("mobile=$mobile")->find();
	}
	//通过手机号查找用户密码
	private function getPwByMobile($mobile){
		return $this->where("mobile=$mobile")->getField('password');
	}
	/**
	 * 根据数据刷新用户，
	 * data需含mobile
	 */
	private function freshUser($data){
		return $this->where('mobile='.$data['mobile'])->save($data);
	}
	//----------------------------------

	/**
	 * 自动登录用户
	 * @param  integer $user 用户信息数组
	 */
	private function autoLogin($user){
		/* 更新登录信息 */
		$data = array(
				'uid'             => $user['uid'],
				'login'           => array('exp', '`login`+1'),
				'last_login_time' => NOW_TIME,
				'last_login_ip'   => get_client_ip(1),
		);
		$this->save($data);
	
		/* 记录登录SESSION和COOKIES */
		$auth = array(
				'uid'             => $user['uid'],
				'username'        => get_username($user['uid']),
				'last_login_time' => $user['last_login_time'],
		);
	
		session('user_auth', $auth);
		session('user_auth_sign', data_auth_sign($auth));
	
	}
	
	/**
	 * 根据配置指定用户状态
	 * @return integer 用户状态
	 */
	protected function getStatus(){
		return true; //TODO: 暂不限制，下一个版本完善
	}
	

	
// 	/**
// 	 * 用户登录认证
// 	 * @param  string  $username 用户名
// 	 * @param  string  $password 用户密码
// 	 * @param  integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
// 	 * @return integer           登录成功-用户ID，登录失败-错误编号
// 	 */
// 	public function login($username, $password, $type = 1){
// 		$map = array();
// 		switch ($type) {
// 			case 1:
// 				$map['username'] = $username;
// 				break;
// 			case 2:
// 				$map['email'] = $username;
// 				break;
// 			case 3:
// 				$map['mobile'] = $username;
// 				break;
// 			case 4:
// 				$map['id'] = $username;
// 				break;
// 			default:
// 				return 0; //参数错误
// 		}
	
// 		/* 获取用户数据 */
// 		$user = $this->where($map)->find();
// 		if(is_array($user) && $user['status']){
// 			/* 验证用户密码 */
// 			if(think_ucenter_md5($password, UC_AUTH_KEY) === $user['password']){
// 				$this->updateLogin($user['id']); //更新用户登录信息
// 				return $user['id']; //登录成功，返回用户ID
// 			} else {
// 				return -2; //密码错误
// 			}
// 		} else {
// 			return -1; //用户不存在或被禁用
// 		}
// 	}
	
	/**
	 * 获取用户信息
	 * @param  string  $uid         用户ID或用户名
	 * @param  boolean $is_username 是否使用用户名查询
	 * @return array                用户信息
	 */
	public function info($uid, $is_username = false){
		$map = array();
		if($is_username){ //通过用户名获取
			$map['username'] = $uid;
		} else {
			$map['id'] = $uid;
		}
	
		$user = $this->where($map)->field('id,username,email,mobile,status')->find();
		if(is_array($user) && $user['status'] = 1){
			return array($user['id'], $user['username'], $user['email'], $user['mobile']);
		} else {
			return -1; //用户不存在或被禁用
		}
	}
	
	
	/**
	 * 更新用户登录信息
	 * @param  integer $uid 用户ID
	 */
	protected function updateLogin($uid){
		$data = array(
				'id'              => $uid,
				'last_login_time' => NOW_TIME,
				'last_login_ip'   => get_client_ip(1),
		);
		$this->save($data);
	}
	
	/**
	 * 更新用户信息
	 * @param int $uid 用户id
	 * @param string $password 密码，用来验证
	 * @param array $data 修改的字段数组
	 * @return true 修改成功，false 修改失败
	 */
	public function updateUserFields($uid, $password, $data){
		if(empty($uid) || empty($password) || empty($data)){
			$this->error = '参数错误！';
			return false;
		}
	
		//更新前检查用户密码
		if(!$this->verifyUser($uid, $password)){
			$this->error = '验证出错：密码不正确！';
			return false;
		}
	
		//更新用户信息
		$data = $this->create($data);
		if($data){
			return $this->where(array('id'=>$uid))->save($data);
		}
		return false;
	}
	/**
	 * 验证用户密码
	 * @param int $uid 用户id
	 * @param string $password_in 密码
	 * @return true 验证成功，false 验证失败
	 */
	protected function verifyUser($uid, $password_in){
		$password = $this->getFieldById($uid, 'password');
		if(UserC::pwMd5($password_in) === $password){
			return true;
		}
		return false;
	}

	
}