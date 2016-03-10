<?php
namespace Common\Lib;
use Common\Model\ConfigModel;
use Common\Model\StatusModel;
use Common\Model\PictureModel;
class Golbal{
	static function getPicturePath($id){
		$id=(int)$id;
		$mp=new PictureModel();
		return $mp->getImgById($id);
	}
	/**
	 * 获取配置里keyname的值
	 * $keyName
	 */
	static function getConfig($keyName){
		$config=new ConfigModel();
		$ret=$config->getConfig();
		$result=Tools::ConvertArrToAssoc($ret,'key','value');
		return $result[$keyName];
	}
	static function getRedRate(){
		return self::getConfig('red_scale');
	}
	static function setRedRate($rate){
		$config=new ConfigModel();
		return $config->freshKey('red_scale', $rate);
	}
	
	static function getStatus($keyName){
		$status=new StatusModel();
		$ret=$status->getStatus();
		$result=Tools::ConvertArrToAssoc($ret,'key','value');
		return $result[$keyName];
	}
	static function getCurrentServiceId(){
		return self::getStatus('current_service_id');
	}
	/**
	 * 设置当前客服id
	 * @param  $id 要设置的id
	 */
	static function setCurrentServiceId($id){
		$status=new StatusModel();
		return $status->freshKey('current_service_id', $id);
	}
	static function getDomain(){
		return 'http://oneonebao.com';
	}
}