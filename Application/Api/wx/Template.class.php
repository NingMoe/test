<?php
namespace Api\wx;
class Template{
	protected  $toUser='';
	protected  $url='';
	protected  $template_id='';
	
	protected $dataFields=array();
	protected $color='#173177';
	public function __construct($toUser){
		if(!is_string($toUser) || ''==$toUser){
			die('to user error');
		}
		$this->toUser=$toUser;
		
		$this->init();
	}
	protected function init(){
	
	}
	
	public function sendNotify($needData){
		ee('needdata:'.getoutstr($needData),'temp');
		$allData=$this->needToAll($needData);
		ee('$allData:'.getoutstr($allData),'temp');
		$post_send=$this->getTemplate($allData);
		ee('$post_send:'.getoutstr($post_send),'temp');
		Notify::sendNotify($post_send);
	}
	protected function createNeedData(){
		
	}
	protected function needToAll($needData){
		return $needData;
	}
	public function getTemplate($data){
		return 
		array(
			'touser'=>$this->toUser,
			'template_id'=>$this->template_id,
			'url'=>$this->url,
			'data'=>$this->getData($data)
		);
	}
	private function setColor($intColor){
		$this->color='#'.$intColor;
	}
	protected function getData($data){
		$result=array();
		foreach ($this->dataFields as $keyWord){
			$result[$keyWord]=array(
					'value'=>$data[$keyWord],
					'color'=>$this->color
			);
		}
		return $result;
	}
}