<?php
namespace Common\Object;
class CarRateInput{
	public $_typeId;
	public $_carPrice;
	public $_carYear;
	public $_registeredMonths;
	public $_dicountFactor;//出险折扣系数
	public $_ckNum;//乘客座位数
	public function __construct($typeId,$carPrice,$carYear,$registeredMonths,$dicountFactor,$ckNum=1){
		if( !is_numeric($typeId) || !is_numeric($carPrice)|| !is_numeric($carYear) || !is_numeric($registeredMonths) | !is_float($dicountFactor)){
			die('车型号或车龄或车价或注册月数或折扣系数不正确');
		}
		$this->_typeId=$typeId;
		$this->_carPrice=$carPrice;
		$this->_carYear=$carYear;
		$this->_registeredMonths=$registeredMonths;
		$this->_dicountFactor=$dicountFactor;
		$this->_ckNum=$ckNum;
	}
}