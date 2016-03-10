<?php
namespace Common\Object;
use Common\Lib\Golbal;
use Common\Lib\Tools;
class Dispatch{
	private $_keyname='current_service_id';
	private $serviceQueue;//客服对列
	private $_cs_id;//已分配，分配到的客服id
	private $_assign_id;//当前要分配的客服id
	private $workStatus;//客服工作状态
	private $averageNum;//未解决里平均数
	private $servicePool;//客服池子数量
	//客服分配类
	private static $_disObj;
	public function __construct(){
		//顺序
		$this->workStatus=new WorkStatus();
		if(!$this->workStatus->allId){
// 			die('没有在线客服');   //返回当前保存的id
		}
		$this->averageNum=(int)($this->workStatus->unsolvedSum/$this->workStatus->serviceNum);
		$this->servicePool=$this->workStatus->unsolved;
		$this->_cs_id=$this->getCurrentId();
		$this->serviceQueue=$this->getQueue();
		ee('averageNum:'.$this->averageNum);
		
	}

	public static function getInstace(){
		if(self::$_disObj instanceof self){
		}else{
			//先尝试从内存构造
			
			
			
			self::$_disObj=new self();
		}
		return self::$_disObj;
	} 
	/**
	 * 取到当前分配的id
	 */
	public function getAssignServiceId(){
		$this->assignService();
		return $this->_assign_id;
	}
	public function getQueueInfo(){
		return $this->serviceQueue;
	}
	private static function getServiceInfoFromMemory(){
		
	}
	private static function setServiceInfoToMemory(){
	
	}
	/**
	 * 得到客服对列
	 * @return \SplQueue
	 */
	private function getQueue(){
// 		$this->workStatus->allId=array(21,22,36,87,15,21,5,62,12,14,18,26);
// 		$this->_cs_id=5;
		$q=new \SplQueue();
		$isLater=false;//在后
		foreach ($this->workStatus->allId as $id){
			if($this->_cs_id==$id){
				$isLater=true;
			}else{//不分配当前值，当前值入尾部
				if($isLater){
					// 				$q->push($id);
					$q->enqueue($id);
				}
			}

		}
		
		$isPrevious=true;//在前
		foreach ($this->workStatus->allId as $id){
			if($this->_cs_id==$id){
				$isPrevious=false;//分配当前值
				$q->push($id);
			}
			
			if($isPrevious){
				$q->push($id);
			}
		}
		return $q;
	}
	/**
	 * 分配客服
	 */
	private function assignService(){
		//头部是要分配的
		if($this->averageNum<11){//少于10个时，全部平均分配
			$this->assignOne();
			return ;
		}
		while ($this->getAssignServiceId() && $this->getIdRate($this->_assign_id)){
			
		}
		
	}
	private function getIdRate($serviceId){
		$num=$this->servicePool[$serviceId];
		$noAssign=$this->averageNum * 0.5;//超出不分配
		$distance=$num-$this->averageNum;
		if($distance<1){//小于等于平均数
			return true;
		}else{
			if($distance<$noAssign){
				return Tools::getRandByPrecent($this->averageNum-$distance, $this->averageNum);
			}
		}
		return false;
	}
	/**
	 * 分配移动一次
	 */
	private function assignOne(){
		$this->_assign_id=$this->serviceQueue->dequeue();
		$this->serviceQueue->enqueue($this->_assign_id);
	}
	private function getCurrentId(){
		$id=Golbal::getCurrentServiceId();
		if(!$id || !in_array($id, $this->workStatus->allId)){
			$id=$this->workStatus->allId[0];  //当前不存在，初始化为第一个
		}
		return $id;
	}

	
//=----------------------------------------------
	public function __constructold(){
		//得到挡墙客服id，和客服队列，保存内存
		$m=M('status');
		$ret=$m->where("keyname='{$this->_keyname}'")->getField('value');
		if($ret){
			$this->_cs_id=$ret;
		}else{
				
		}
	}
}