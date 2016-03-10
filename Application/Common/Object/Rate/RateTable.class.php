<?php
namespace Common\Object\Rate;
class RateTable{
	//费率数据类
	//excel 表起止位置num，
	private  $_ratePos=array(
			'dsfzr' =>array('sx'=>4,'sy'=>6,'ex'=>10,'ey'=>54),//第三方责任险
			'clss'   =>array('sx'=>11,'sy'=>6,'ex'=>20,'ey'=>54),//车损险	车辆损失险	【基础保费】--【费率】
			'qcdq'   =>array('sx'=>23,'sy'=>6,'ex'=>24,'ey'=>54),//盗抢险	全车盗抢险	【基础保费】--【费率】
			'sjzwzr'=>array('sx'=>21,'sy'=>6,'ex'=>21,'ey'=>54),//司机座位责任           【基础保费】--【费率】
			'ckzwzr'=>array('sx'=>22,'sy'=>6,'ex'=>22,'ey'=>54),//乘客座位责任	【基础保费】--【费率】
			'bl'    =>array('sx'=>25,'sy'=>6,'ex'=>26,'ey'=>47),//玻璃险			【国产玻璃】--【进口玻璃】
																//del// 	const SS//涉水险   //必须购买车损险；车损险保费X5%					
			'hh'	=>array('sx'=>17,'sy'=>60,'ex'=>20,'ey'=>65),//划痕险
			
			'zrjy' =>array('sx'=>1,'sy'=>60,'ex'=>7,'ey'=>63),//自燃险 家庭自用车 		1年以下	1-2年	2-6年	6年以上
// 			'zrqt'	=>array('sx'=>9,'sy'=>58,'ex'=>12,'ey'=>61),//自然险 其他车		2年以下	2-3年	3-4年	4年以上
			
			'cszj' 	=>array('sx'=>12,'sy'=>71,'ex'=>12,'ey'=>73),//车身折旧费率
			
			'cxzkxs_lxwcx'=>array('sx'=>16,'sy'=>71,'ex'=>20,'ey'=>75),//出险折扣系数,连续未出险
			'cxzkxs_snycx'=>array('sx'=>21,'sy'=>71,'ex'=>28,'ey'=>75),//出险折扣系数,上年有出险
			'cxzkxs_xchyd'=>array('sx'=>29,'sy'=>71,'ex'=>29,'ey'=>75),//出险折扣系数,新车或异地转入车辆
			
			'jqxjcflb'=>array('sx'=>27,'sy'=>6,'ex'=>27,'ey'=>54)//交强险基础费率表
	);
	private $_rateCateNamePos=array(//费率的分类名称数据
			'tycx_1j'=>array('sx'=>0,'sy'=>6,'ex'=>0,'ey'=>50),//通用车型分类  1级
			'tycx_2j'=>array('sx'=>1,'sy'=>6,'ex'=>1,'ey'=>50),//通用车型分类	2级
			'tycx_3j'=>array('sx'=>3,'sy'=>6,'ex'=>3,'ey'=>50),//通用车型分类	3级
			
			//--------------------
//del 			'jqxclfl'=>array('sx'=>32,'sy'=>16,'ex'=>34,'ey'=>57),//交强险车辆分类。          车辆大类，序号 ，分类明细

	);
	private $_rageMap=array();//费率数据表
	private $_rateCateNameData=array();//分类名称数据
	
	/*
	 * 用excel数据构造
	 */
	function __construct($excel=null){
		if($excel){
			$this->importDataFromExcel($excel);
			$this->backMapData();
			$this->backCateNameData();
		}
// 		ct($this->getRateMapData());
// 		echo "=============";
// 		ct($this->getCateNameData());
	}
	/*
	 * 从excel 导入数据
	 */
	public function importDataFromExcel($excel){
		if($excel){
			foreach ($this->_ratePos as $key=>$value){
				$this->_rageMap[$key]=$this->getArrFromExcel($excel, $value);
			}
			
			foreach ($this->_rateCateNamePos as $key=>$value){
				$this->_rateCateNameData[$key]=$this->getArrFromExcel($excel, $value);
			}
		}
	}
	/*
	 * 从excel里得到数组
	 * @excel对象
	 * @posArr 起止位置数组
	 * @return 起止位置里面的数据，索引数组,以行为索引
	 */
	private function getArrFromExcel($excel,$posArr){
		$result=array();
		$index=0;
		for($i=$posArr['sy'];$i<=$posArr['ey'];$i++){
			for ($j=$posArr['sx'];$j<=$posArr['ex'];$j++){
				$result[$index][]=$excel->getCellValue($j,$i);   //去index为一行的数据
			}
			$index++;
		}
		return $result;
	} 
	
	/*
	 * 从数据文件得到分类名称数据,保存到成员变量里面
	 */
	public function getCateNameData(){
		if(!is_file($this->getRateCateNameDataFileName())){
			die('费率分类名称文件不存在，请导入excel');
		}
		return $this->_rateCateNameData=json_decode(include $this->getRateCateNameDataFileName(),true);
	}
	/*
	 * 从数据文件得到 费率map数据,保存到成员变量里面
	 * 
	 */
	public function getRateMapData(){
		if(!is_file($this->getRateDataFileName())){
			die('费率数据文件不存在，请导入excel');
		}
		$mapjson=include $this->getRateDataFileName();
		return $this->_rageMap=json_decode($mapjson,true);
	}
	
	private function getRateDataFileName(){
		return dirname(dirname(dirname(__FILE__))).'/Data/MapData.php';
	}
	private function getRateCateNameDataFileName(){
		return dirname(dirname(dirname(__FILE__))).'/Data/CateNameData.php';
	}
	private function backMapData(){
		$content="<?php\r\n";
		$content.="return '".json_encode($this->_rageMap)."';";
		file_put_contents($this->getRateDataFileName(),$content);
	}
	private function backCateNameData(){
		$content="<?php\r\n";
		$content.="return $".basename($this->getRateCateNameDataFileName(),'php')."='".json_encode($this->_rateCateNameData)."';";
		file_put_contents($this->getRateCateNameDataFileName(),$content);
	}
	
	
	/*
	 * 将数组转化为文件写入格式
	 */
	private function convertArrToFile($data=array()){
		$result="Array(\r\n";
		foreach($data as $k=>$v){
			$result.="[$k]=>Array(";
			foreach ($v as $childk=>$childv){
				$comma= $childk==count($v)-1? '' : ',' ;
				$result.="'$childk'=>$childv$comma\r\n";
			}
			$result.=")\r\n";
		}
		$result.="\r\n)";
		return $result;
	}
	
	/*
	 * 取得要查的索引。
	 */
	function getRateIndex(){
		
	}

}