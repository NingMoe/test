<?php
namespace Common\Object\Rate;
class RateCateIndex{
	//分类与费率表索引的对应
	/*
	 * 根据carTypeId得到费率索引值
	 */
	static function getCarTypeIndex($carTypeId){
		
		return $carTypeId;
	}
	/*
	 * 根据CorverageLevelId得到费率索引值
	 */
	static function getCorverageLevelIndex($corverageLevelId){
	
		return $corverageLevelId;
	}
}