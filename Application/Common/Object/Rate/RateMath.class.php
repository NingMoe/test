<?php
namespace Common\Object\Rate;
class RateMath{
	//费率计算类,得到具体车型的费率
	private $_oneMapObj;//计算费率单表
	private $_jqPriceRate;//交强险费率表
	private $_jqPrice;//交强险价
	private $_cxzkTable;//出险折扣表
	private static $instance;//保存实例
	//根据车型构造当前费率表
	private function __construct($carTypeId){ 
		
		$carTypeId=$carTypeId-1;
		
		$rowIndex=RateCateIndex::getCarTypeIndex($carTypeId);
		if(!is_numeric($rowIndex)){
			die('cartypeid error');
		}
		$this->_oneMapObj=new OneMap();
		$rateTable=new RateTable();//var_dump($rateTable);
		$this->setSixBaseMap($rateTable,$carTypeId);
		
		$rateMap=$rateTable->getRateMapData();
		$this->_oneMapObj->hh=$rateMap['hh'];
		$this->_oneMapObj->zrjy=$rateMap['zrjy'];
		$this->_oneMapObj->zrqt=$rateMap['zrqt'];
		
		$this->_jqPriceRate=$rateMap['jqxjcflb'];
		$this->_jqPrice=$this->_jqPriceRate[$carTypeId][0]; //后注意加0，该处只有1列，但都有列
		
		$this->_cxzkTable=array($rateMap['cxzkxs_lxwcx'],$rateMap['cxzkxs_snycx'],$rateMap['cxzkxs_xchyd']);
// 		ee('jqtable:'.getoutstr($this->_cxzkTable),'temp');
// 		var_dump($this->_oneMapObj);
	}
	static public function getInstance($carTypeId){
		if (!(self::$instance instanceof self)){
			self::$instance=new self($carTypeId);
		}
		return self::$instance;
	}
	public function getOneMap(){
		return $this->_oneMapObj;
	}
	private function setSixBaseMap($rateTable,$carTypeId){
		if(!is_object($rateTable)){
			die('ratetable is not object');
		}
		$i=0;
		foreach ($rateTable->getRateMapData() as $key=>$value){
			$i++;
			$this->_oneMapObj->$key=$value[$carTypeId];
			if(6===$i)
				break;
		}
	}
	/**
	 * 交强费率表
	 */
	public function getJqRateTable(){
		return $this->_jqPriceRate;
	}
	/**
	 * 交强价
	 */
	public function getJqPrice(){
		return $this->_jqPrice;
	}
	/**
	 * 出险折扣表
	 */
	public function getCxzkTable(){
		return $this->_cxzkTable;
	}
}




