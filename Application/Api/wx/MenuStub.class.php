<?php
namespace Api\wx;
class MenuStub{
	public static function create($data){
		$ret=RequireInterface::reqInterface("menu/create", $data);
		if(false===$ret){
			return  false;
		}
		return true;
	}
	public static function get(){
		$ret=RequireInterface::reqInterface("menu/get", array());
		if(false===$ret){
			return false;
		}
		return $ret;
	}
	public static function delete(){
		$ret=RequireInterface::reqInterface("menu/delete", array());
		if(false===$ret){
			return false;
		}
		return true;
	}
}











	










