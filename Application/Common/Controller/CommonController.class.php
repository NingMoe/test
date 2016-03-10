<?php
namespace Common\Controller;
use Think\Controller;
use Api\wx\Method;
class CommonController extends Controller{
	protected $callback="";
	protected $contentName="";
	
	protected $_urlAction;
	protected $userAction;

	protected function _initialize(){
		$this->_urlAction=ACTION_NAME;
		if('newServiceNumber'!=$this->_urlAction){
			ee('com get:'.getoutstr($_GET),'temp');
			ee('com post:'.getoutstr($_POST),'temp');
		}
		if(isset($_GET['callback'])){
			$this->callback=I('get.callback');
		}
	}
	protected function ok($msg,$min=1){
		$this->success($msg,null,$min);
	}
	
	protected function ko($msg,$min=1){
		$this->error($msg,null,$min);
	}
	
	protected function ajaxEcho($json){
		header('Content-Type:application/json; charset=utf-8');
		exit($json);
	}
	
	/*
	 * 根据data数组得到要返回的json数据
	 * isCh 为true时解决中文问题
	*/
	protected function getJsonReturn($data,$callback="",$contentName="",$isCh=false){
		if(empty($data)){
			return false;
		}
		if(""!=$contentName){
			$data=array($contentName=>$data);
		}
		
		if($isCh){
			$data=Method::ch_json_encode($data);
		}else{
			$data=json_encode($data);
		}

		if(""!=$callback){
			$data=$callback."(".$data.")";
		}
	
		return $data;
	}
	/**
	 * 
	 * @param unknown $data
	 * @param unknown $isCh 为true时解决中文问题
	 */
	protected function outJson($data,$isCh=false){
		$json=$this->getJsonReturn($data,$this->callback,$this->contentName,$isCh);
		$this->ajaxEcho($json);
	}
	/**
	 * 跳转到制定url
	 * @param unknown $url
	 */
	public function toUrl($url){
		header("location:{$url}");
	}
	/**
	 * 返回前端交互的json状态码
	 * @param unknown $status
	 */
	protected function outStatusByJson($status){
		$ret=array('userActionStatus'=>$status);
		$this->outJson($ret);
	}
}