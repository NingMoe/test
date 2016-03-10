<?php
namespace Common\Object;
use Api\Sms\Code;
class SessionManage{
	private static $_usersName='user_sign';
	private static $_adminName='admin_yy';
	private static $_phoneCode='phone_code';
	private static $_loginThridTempData='third_data';
	private static $_tempType='simple_type';
	private static $_addCar='add_car';
	
	public static function setAdd(){
		session(self::$_addCar,1);
	}
	public static function getAdd(){
		$addId=session(self::$_addCar);
		self::setEmpty(self::$_addCar);
		if(!$addId){
			return false;
		}
		return TRUE;
	}
	/**
	 * 临时状态标识
	 * @param int $type
	 */
	public static function setTempType(int $type){
		session(self::$_tempType,$type);
	}
	public static function getTempType(){//temp  取完清空
		$type=session(self::$_tempType);
		if (!$type){
			$type=1;   //没设置时默认1diy类型
		}
		self::emptyTempType();
		return $type;
	}
	public static function emptyTempType(){
		self::setEmpty(self::$_tempType);
	}
// 	private static $_phoneCodeExpire=60;//code有效期，秒
	public static  function getUser(){
		$user_sign=session(self::$_usersName);
		//session 检验，待完善
		if(is_numeric($user_sign['id'])  && is_numeric($user_sign['last_login_time']) && is_numeric($user_sign['mobile']) )			//   微信未绑定手机
			return $user_sign;
		return null;
	}
	public static function setUser($users){
		$user_sign=array(
				'id'             => $users['id'],
				'mobile'        => $users['mobile'],
				'last_login_time' => $users['last_login_time'],
		);
		session(self::$_usersName, $user_sign);
	}
	
	public static function EmptyUser(){
		self::setEmpty(self::$_usersName);
	}
	public static function setEmpty($name){
		session($name, null);
	}
	public static function setCode($code,$time){
		session(self::$_phoneCode,array('num'=>$code,'ctime'=>$time));
		return $code;
	}
	public static function getCode(){
		$code=session(self::$_phoneCode);
		if($code['num'] && ($code['time']+Code::$_expire< time())){
			return $code['num'];
		}
		return null;
	}
	public static function setAdmin($admin){
		$adminInfo=array(
				'id'             => $admin['id'],
				'account'        => $admin['account'],
				'groupId'		 => $admin['groupId']	
// 				'last_login_time' => $users['last_login_time'],
		);
		session(self::$_adminName, $adminInfo);
	}
	public static function getAdmin(){
		$admin=session(self::$_adminName);
		if($admin){
			return $admin;
		}
		return false;
	}
	public static function emptyAdmin(){
		self::setEmpty(self::$_adminName);
	}
	/**
	 * 保存第三方授权数据
	 * @param unknown $thirdData
	 */
	public static function setThirdData($thirdData){
		session(self::$_loginThridTempData, $thirdData);
	}
	public static function getThirdData(){
		$thirdData=session(self::$_loginThridTempData);
		if($thirdData){
			return $thirdData;
		}
		return false;
	}
	public static function emptyThirdData(){
		self::setEmpty(self::$_loginThridTempData);
	}
}