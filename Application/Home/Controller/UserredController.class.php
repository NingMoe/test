<?php
namespace Home\Controller;
use Common\Model\RedshareModel;
use Common\Lib\Tools;
class UserredController extends MainController{
	//客户红包操作控制
	private $redPacket;
	private $sharedList;
	protected function _initialize(){
		parent::_initialize();
		ee('Userred ST:','temp');
		$this->m=new RedshareModel();
		$this->sharedList=$this->m->getShared($this->_client->getClientId());
		
		
		
	}
	/**
	 * 红包排行榜  改到  redcontroller
	 */
	public function topList(){
		$this->contentName='toplist';
		$result=$this->m->getTopList();
		
		$this->outJson($result,1);
	}
	/**
	 * 分享操作,点击后分享出去，链接里带用户分享id
	 */
	public function share(){
		ee('USERREssssDddddd share:','temp');
		$sid=$this->getSharedId();
		if($sid){
			
		}else{
			$data['id']=$this->_client->getClientId();
			$data['share_string']=Tools::getShareId();
			$ret=$this->_client->_m->updateUserInfo($data);
			if(!$ret){
				$this->ko('更新信息失败:'.$this->_client->_m->getError());
			}
			$sid=$data['share_string'];
		}
		if(!$sid){
			$this->ko('分享失败');
		}
// 		$shareUrl=U('red/index',"sid=$sid",true,true);
		$shareUrl='http://www.oneonebao.com/#out/sharecenter/'.$sid;

		ee('front get share url:'.$shareUrl,'temp');
		$result=array('userShareUrl'=>$shareUrl);
		$this->outJson($result,1);
	}
	/**
	 * 取得是否已分享信息
	 */
	private function getSharedId(){
		$temp=$this->_client->getBaseInfo();
		$sid=$temp['share_string'];
		if(is_string($sid) && !empty($sid)){
			return $sid;
		}
		return false;
	}
	/**
	 * 已分享列表
	 */
	public function sharedList(){
		ee('sharedList ii:','temp');
		ee('sharedList:'.getoutstr($this->sharedList),'temp');
		$this->contentName='userred';
		$this->outJson($this->sharedList,1);
	}
	
	public function index(){
		$this->sharedList();
	}
}