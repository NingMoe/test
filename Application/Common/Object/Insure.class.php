<?php
namespace Common\Object;
class Insure{
	//保险类，单个保险,选择情况
// 	public $_name;		 //名称	
	public $_isSelected=null; //是否选择
	public $_level=null; //所保额度
	public $_noPay=null;//免赔，true为有
	public function __construct($isSelected=null,$level=null,$nopay=null){
		$this->_isSelected=$isSelected;
		$this->_level=$level;
		$this->_noPay=$nopay;
	}
}