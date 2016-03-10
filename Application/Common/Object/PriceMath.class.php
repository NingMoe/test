<?php
namespace Common\Object;
use Common\Object\Rate\RateMath;
class PriceMath{
	//保单价计算类
	private $_carTypeId;//车型id
	private $_carYear;//车龄
	private $_carPrice;//车身价
	private $_registeredMonths;//已注册月数
	private $_dicountFactor;//出险折扣系数
	private $_ckNum;//乘客座位数
	
	private $_csPrice;//车损险未加,免赔和出险折扣的   价钱
	
	private $_CarDepreciationPrice;//车身折旧价
	
	private $_oneRate;//单项费率
	
	private $_catePrice;//险种分类价
	
	private $_carTypeGroup=0;//0为家庭自用 大类， 1为其他 营业类等
	
	private $_error;
	
	private $_zrLevelIndex;//自燃险等级索引
	
	public function __construct(CarRateInput $carInfo){
		$this->_carTypeId=$carInfo->_typeId;
		$this->_carYear=$carInfo->_carYear+1;
		$this->_carPrice=$carInfo->_carPrice;
		$this->_registeredMonths=$carInfo->_registeredMonths;
		$this->_dicountFactor=$carInfo->_dicountFactor;
		$this->_ckNum=$carInfo->_ckNum;
		ee('math  -----nun:'.$this->_ckNum);
		
		unset($carInfo);
		$this->setCarTypeGrounp();
		
		ee('carType:'.$this->_carTypeId,'newtemp');
		
// 		$rateMath=new RateMath($this->_carTypeId-1);
		$rateMath=RateMath::getInstance($this->_carTypeId);
		$this->_oneRate=$rateMath->getOneMap();
		
		ee('CARras:'.getoutstr($this->_oneRate),'newtemp');
		ee('month:'.$this->_registeredMonths,'newtemp');
			
		$this->_catePrice=new Cate();
		
	}
	/*
	 * 取得车身折扣价
	 */
	private function getCarDepreciationPrice(){ee('cardisrate:'.getoutstr($this->getCarDepreciationRate()),'newtemp');
// 		车身折旧价=车身价*（1-折旧率*折旧自然月数）
		if(!$this->_CarDepreciationPrice){
			$this->_CarDepreciationPrice=$this->_carPrice * (1-$this->getCarDepreciationRate() * $this->_registeredMonths);
		}
		return $this->_CarDepreciationPrice;
	}
	/*
	 * 取得车身折旧费率
	 */
	private function getCarDepreciationRate(){
		$rate;
		$arr1=array(1,2,4,5,8,9);
		$arr2=array(12,17,18,19,20,21,30,38);
		if(in_array($this->_carTypeId, $arr1)){
			$rate=0.006;
		}elseif (in_array($this->_carTypeId, $arr2)){
			$rate=0.012;
		}else{
			$rate=0.009;
		}
		return $rate;
	}
	
	private function setCarTypeGrounp(){
		if($this->_carTypeId>17){           /////------------------------------wwwwwwwwwwwwwwwwwwwwww
			$this->_carTypeGroup=1;
		}
	}
	//得到列索引
	private function get_convert_clss_index(){
		$index=-1;
		if(0==$this->_carTypeGroup){//!!!!!!!!!!!!!_carTypeGroup为1时///0
			if($this->_carYear==1){
				$index=0;
			}elseif($this->_carYear==2){
				$index=1;
			}elseif($this->_carYear<7){
				$index=2;
			}elseif ($this->_carYear>6){
				$index=6;
			}
		}else{//_carTypeGroup为0时
			if($this->_carYear<3){
				$index=0;
			}elseif($this->_carYear==3){
				$index=2;
			}elseif($this->_carYear==4){
				$index=3;
			}elseif ($this->_carYear>4){
				$index=4;
			}
		}
		return $index;
	}
	//得到列索引
	private function get_convert_clss_index_new(){
		$index=-1;
		
		if($this->_carYear==1){
			$index=0;
		}elseif($this->_carYear>8){
			$index=8;
		}elseif($this->_carYear>6){
			$index=6;
		}elseif ($this->_carYear>4){
			$index=4;
		}else{
			$index=2;
		}
		
		return $index;
	}
	//第三方
	public function dsfzr(Insure $insure){
		$price=0;
		$level_map_index=$insure->_level-1;
		if($level_map_index<7){
			$price=$this->_oneRate->dsfzr[$level_map_index];
		}elseif($level_map_index==7 || $level_map_index==8){
			$a=$this->_oneRate->dsfzr[6];
			$b=$this->_oneRate->dsfzr[5];
			$n=$level_map_index==7?1:2;
			$price=$a+0.9*$n*($a-$b);
		}
		
		ee("dsf  rate:$price",'newtemp');
		
		$noPayPrice=0;
		if($insure->_noPay){
			$noPayPrice=$price*0.15;
		}
		
		ee("dsf  _noPay:$price",'newtemp');
		ee("dsf  dis:".$this->_dicountFactor,'newtemp');
		

		
		return $this->_catePrice->dsfzr=array('price'=>$price*$this->_dicountFactor,'noPayPrice'=>$noPayPrice*$this->_dicountFactor);
	}
	//车损
	public function clss(Insure $insure){
		$price=0;
// 		$level_map_index=Level::$clss[$this->_carTypeGroup][$this->get_convert_clss_index()];   ////old
		$level_map_index=$this->get_convert_clss_index_new();
		//基本保费+精友原始车身价X费率
		$basePrice=$this->_oneRate->clss[$level_map_index];
		$rate=$this->_oneRate->clss[$level_map_index+1];
		$price=$basePrice+$rate*$this->_carPrice;
		$this->_csPrice=$price;
		
		$noPayPrice=0;
		if($insure->_noPay){
			$noPayPrice=$price*0.15;
		}
		
		ee($this->_csPrice.'--$basePrice|get_convert_clss_index-new-'.$this->get_convert_clss_index_new(),'newtemp');
		ee($level_map_index.'--ccccccs$basePrice:'.$basePrice.'--rate:'.$rate,'newtemp');
		
		return $this->_catePrice->clss=array('price'=>$price*$this->_dicountFactor,'noPayPrice'=>$noPayPrice*$this->_dicountFactor);
	}
	//取到车损原始价
	private function getCsPrice(){
		if($this->_csPrice){
			return $this->_csPrice;
		}else{
			//计算车损    
			//保证车损在前
		}
	}
	//盗抢
	public function qcdq(Insure $insure){
ee($this->_oneRate->qcdq[0].'-ra0,------qcdq------ra1-'.$this->_oneRate->qcdq[1].'--cardisp:'.$this->getCarDepreciationPrice(),'newtemp');
// 		基本保费+车身折旧价X费率

		$price=$this->_oneRate->qcdq[0]+$this->_oneRate->qcdq[1] * $this->getCarDepreciationPrice();
		
		$noPayPrice=0;
		if($insure->_noPay){
			$noPayPrice=$price*0.20;
		}
		
		return $this->_catePrice->qcdq=array('price'=>$price*$this->_dicountFactor,'noPayPrice'=>$noPayPrice*$this->_dicountFactor);
	}
	//司机
	public function sjzwzr(Insure $insure){
		$price=0;
		$levelPrice=Level::$sjzwzr[$insure->_level] * 10000;//单位是万
		$price=$levelPrice*$this->_oneRate->sjzwzr[0]; 
		
		$noPayPrice=0;
		if($insure->_noPay){
			$noPayPrice=$price*0.15;
		}
		
		return $this->_catePrice->sjzwzr=array('price'=>$price*$this->_dicountFactor,'noPayPrice'=>$noPayPrice*$this->_dicountFactor);
	}
	//乘客
	public function ckzwzr(Insure $insure){
		$price=0;
		$levelPrice=Level::$ckzwzr[$insure->_level] * 10000;//单位是万
		$price=$this->_ckNum * $levelPrice * $this->_oneRate->ckzwzr[0]; 
		ee($this->_ckNum.'--'.$levelPrice.'--'.$this->_oneRate->ckzwzr[0].'---price:'.$price);
		$noPayPrice=0;
		if($insure->_noPay){
			$noPayPrice=$price*0.15;
		}
		return $this->_catePrice->ckzwzr=array('price'=>$price*$this->_dicountFactor,'noPayPrice'=>$noPayPrice*$this->_dicountFactor);
	}
	//玻璃      选择档次  1，国产   2，进口
	public function bl(Insure $insure){
		$price=0;
		$level_map_index=$insure->_level-1;
		$rate=$this->_oneRate->bl[$level_map_index];
		//必须购买车损险；精友原始车身价X费率
		$price=$rate * $this->_carPrice;
		
		return $this->_catePrice->bl=array('price'=>$price*$this->_dicountFactor);
	}
	//涉水
	public function ss(Insure $insure){
		if($this->_carYear>3){
			$this->_error='超过3年不保  涉水险';
			return -1;
		}
// 		必须购买车损险；车损险保费X5%
		$price=$this->_csPrice*0.005;
		
		$noPayPrice=0;
		if($insure->_noPay){
			$noPayPrice=$price*0.15;
		}
		
		return $this->_catePrice->ss=array('price'=>$price*$this->_dicountFactor,'noPayPrice'=>$noPayPrice*$this->_dicountFactor);
	}
	//划痕
	public function hh(Insure $insure){
		if($this->_carYear>3){
			$this->_error='超过3年不保  划痕险';
			return -1;
		}
		$level_map_index=$insure->_level-1;
		$price=$this->_oneRate->hh[$this->getHhRowIndex()][$level_map_index];
		ee($this->getHhRowIndex().'--row--hhrate:'.$price.'--mapindex:'.$level_map_index,'newtemp');	

		$noPayPrice=0;
		if($insure->_noPay){
			$noPayPrice=$price*0.15;
		}
	
		return $this->_catePrice->hh=array('price'=>$price*$this->_dicountFactor,'noPayPrice'=>$noPayPrice*$this->_dicountFactor);
	}
	//自燃
	public function zr(Insure $insure){
// 		$row=$this->getZrRow();  ///old
		$row=$this->getZrRowNew();
		if(!$row){
			$this->_error='该类车不能买自燃险，或信息有误 未知错我';
			return 0;
		}
		
		
		$price=$this->getCarDepreciationPrice() * $row[$this->getZrLevelIndexNew()];
		
		ee($price.':pricezrrrrrrrrrrrrrrrrrow:'.getoutstr($row).'----index:'.$this->getZrLevelIndexNew(),'newtemp');
		
		return $this->_catePrice->zr=array('price'=>$price*$this->_dicountFactor,'noPayPrice'=>0);
	}
	//指定
	public function zd(Insure $insure){
		//指定专修， 客服填写  todo
		
		return 0;
	}
	
	private function getHhRowIndex(){
		$index;
		if($this->_carPrice<300000){//小于30万   含起点，不含终点
			$index=0;
		}elseif($this->_carPrice>499999){
			$index=2;
		}else{
			$index=1;
		}
		if($this->_carYear>2){  /////////-------------------------
			$index+=3;
		}
		return $index;
	}
	
	//得到自燃险的行
	private function getZrRowNew(){
		$arrqt=array(
				array(1,2,3),
				array(22,23,24,25),//非营业特种车
				array(26,27,28,29,30,31,32,33,34,35,36,37,38),//出租、租赁车、城市公交车、公路客运车
				array(44,45,46,47)			//营业货车、营业特种车
		);
		$ci=-1;
		foreach ($arrqt as $key=>$value){
			if(in_array($this->_carTypeId, $value)){
				$ci=$key;
// 				$this->setZrLevle_qt();
			}
		}
		if(-1==$ci){
			$this->_error='该车型不能保自燃险';
			return false;
		}
		return $this->_oneRate->zrjy[$ci];
	}
	
	//得到自燃险的行
	private function getZrRow(){
		$arr=array(1,2,3);
		if(in_array($this->_carTypeId, $arr)){
			$this->setZrLevel_jy();
			return $this->_oneRate->zrjy[0];
		}
		$arrqt=array(
				array(17,18,19,20,21),
				array(22,23,24,25),
				array(26,27,28,29),
				array(30,31,32,33,34)
		);
		$ci=-1;
		foreach ($arrqt as $key=>$value){
			if(in_array($this->_carTypeId, $value)){
				$ci=$key;
				$this->setZrLevle_qt();
			}
		}
		if(-1==$ci){
			$this->_error='该车型不能保自燃险';
			return false;
		}
		return $this->_oneRate->zrqt[$ci];
	}
	/**
	 * 自燃new
	 */
	private function getZrLevelIndexNew(){
		if($this->_carYear>6){
			$level=6;
		}else{
			$level=$this->_carYear-1;
		}

		return $level;
	}
	
	private function getZrLevelIndex(){
		return $this->_zrLevelIndex;
	}
	private function setZrLevel_jy(){
		$level=-1;
		if($this->_carYear==1){
			$level=0;
		}elseif($this->_carYear==2){
			$level=1;
		}elseif($this->_carYear>6){
			$level=3;
		}else{
			$level=2;
		}
		$this->_zrLevelIndex=$level;
	}
	private function setZrLevle_qt(){
		$level=-1;
		if($this->_carYear>4){
			$level=3;
		}elseif($this->_carYear==4){
			$level=2;
		}elseif($this->_carYear==3){
			$level=1;
		}else{
			$level=0;
		}
		$this->_zrLevelIndex=$level;
	}
	
	
}







