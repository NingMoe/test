<?php
namespace Common\Object;
use Admin\Model\AdminModel;
use Common\Model\DrivinglicenseModel;
use Common\Lib\Tools;
class WorkStatus{
	//客服工作状态   考核分配用
	public $allId;//所有在线id,该类针对的对象
	public $unsolved;//每客服未解决数组数组
	public $solved;//每客服已解决数量数组
	public $unsolvedSum;//未解决客户总数
	public $solvedSum;//已解决客户总数
	public $serviceNum;//客服数量
	public function __construct(){
		$mAdmin=new AdminModel();
		$this->allId=$mAdmin->getOnlineServiceIds();
		$this->unsolved=$this->getUnsolved();
		$this->solved=$this->solved;
		$this->unsolvedSum=Tools::sumValue($this->unsolved);
		$this->solvedSum=Tools::sumValue($this->solved);
		$this->serviceNum=count($this->allId);
		ee('serviceNUm:'.$this->serviceNum.'----unsolved num:'.$this->unsolvedSum);
	}
	/**
	 * 得到未解决数量
	 */
	private function getUnsolved(){
		$statuss=array(0,1,2,3,4,5,6);
		
		return $this->getAllServiceNum($statuss);
	}
	
	private function getResolved(){
		$statuss=array(7,8,9,10,11);
		
		return $this->getAllServiceNum($statuss);
	}
	/**
	 * 取到 allid 的服务状态在 statuss的数量
	 * @param unknown $statuss
	 */
	private function getAllServiceNum($statuss){
		$mDriving=new DrivinglicenseModel();
		$ret=$mDriving->getDrivingStatusGroup($this->allId, $statuss);
		return Tools::ConvertArrToAssoc($ret,'service_id','servicenum');
	}

}