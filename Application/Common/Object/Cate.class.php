<?php
namespace Common\Object;
class Cate{
	//保险分类名称数据信息类
	public $dsfzr;//第三者责任险
	public $clss;//车辆损失险
	public $qcdq;//全车盗抢险
	public $sjzwzr;//司机乘坐责任险
	public $ckzwzr;//乘客座位责任险
	public $bl;//玻璃单独破碎险
	public $ss;//涉水险
	public $hh;//车身划痕险
	public $zr;//自燃损失险
	
	public $zd;//指定专修，只判断是否勾选
	
	public function __construct(){
	}
	public function setInsure(){
// 		$obj=new Insure();  //指向的同一个对象  ！！！！！！！！！！！！！！！
		$this->dsfzr=new Insure();
		$this->clss=new Insure();
		$this->qcdq=new Insure();
		$this->sjzwzr=new Insure();
		$this->ckzwzr=new Insure();
		$this->bl=new Insure();
		$this->ss=new Insure();
		$this->hh=new Insure();
		
		$this->zd=new Insure();
	}
	/*
	 * 返回属性名数组
	 */
	public function getFields(){
// 		$result=array('','');
		$fieldArr=get_class_vars(get_class($this));
		return array_keys($fieldArr);
	}
}