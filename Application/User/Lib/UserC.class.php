<?php
namespace User\Lib;
class UserC{
	/**
	 * 系统非常规MD5加密方法
	 * @param  string $str 要加密的字符串
	 * @return string
	 */
	public static function pwMd5($str, $key = 'yynn'){
		return '' === $str ? '' : md5(sha1($str) . $key);
	}
	public static function adminPwMd5($str, $key = 'wy'){
//		return '' === $str ? '' : md5(sha1($str) . $key);
		return $str;
	}
	/**
	 * 数据签名认证
	 * @param  array  $data 被认证的数据
	 * @return string       签名
	 */
	static function data_auth_sign($data) {
		//数据类型检测
		if(!is_array($data)){
			$data = (array)$data;
		}
		ksort($data); //排序
		$code = http_build_query($data); //url编码并生成query字符串
		$sign = sha1($code); //生成签名
		return $sign;
	}
	/**
	 * 检测用户是否登录
	 * @return integer 0-未登录，大于0-当前登录用户ID
	 */
	static function is_login(){
		$user = session('user_auth');
		if (empty($user)) {
			return 0;
		} else {
			return session('user_auth_sign') == self::data_auth_sign($user) ? $user['uid'] : 0;
		}
	}
}