<?php
namespace Common\Object;

class PriceSheet{
	//报价单类;
	/*
	 * 单项保险价数组，把有的动态添加
	 */
	private  $_priceCates;
	private  $_priceCount=0;
	public $cc;//车船险
	public $jq;//交强险
	
	public function __construct(CarRateInput $carInfo,Package $package=null){
			$priceMath=new PriceMath($carInfo);
			
			$this->computer($package,$priceMath);
	}
	/*
	 * 根据套餐，计算生成报价单
	 */
	public function computer(Package $package,PriceMath $priceMath){
// 		if(!is_array($package) && !is_object($package)){
// 			echo '套餐数据空或者有误';
// 			return false;
// 		}
		if($package instanceof Package){
			//保证车损在前  todo
			
			foreach ($package->_cate->getFields() as $fieldName){
				if($package->_cate->$fieldName->_isSelected){
				//得到价钱和保额
					$price=$priceMath->$fieldName($package->_cate->$fieldName);
					$level_package=$package->_cate->$fieldName->_level;
					if($price){
						$this->_priceCount+=$price['price'];
						if($price['noPayPrice']){
							$this->_priceCount+=$price['noPayPrice'];
						}
					}
					if($level_package){
						$level=Level::get($fieldName, $level_package);
					}else{
						$level='';
					}
					
					$this->_priceCates[$fieldName]=array(
						'price'=>round($price['price'],2),
						'noPayPrice'=>round($price['noPayPrice'],2),
						'level'=>$level
					);
				}
			}
		}else{
			die("套餐信息不正确");
		}
		
	}
	public function getPriceSheet(){
		return $this->_priceCates;
	}
	public function getPriceCount(){
		return $this->_priceCount;
	}
}