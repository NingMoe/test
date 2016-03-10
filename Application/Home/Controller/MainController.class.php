<?php
namespace Home\Controller;
use Common\Controller\CommonController;
use Common\Object\Client;
use User\Lib\UserC;
use Common\Object\ClientAction;
use Common\Object\SessionManage;
class MainController extends CommonController{

	
	protected $m;
	
	/**
	 * 用户行为对象
	 */
	protected $_client;//用户对象
// 	private $_allowList=array('public','error');

	protected function _initialize(){
		parent::_initialize();
		$this->_client=Client::getInstance();
		if(null==$this->_client){
			SessionManage::EmptyUser();
			$this->error('您还没有登录，请先登录！', U('User/login'),1);
			exit;
		}
		
		$this->userAction=new ClientAction($this->_client->getClientId());
		

	}
	
	public function index(){
		
	}
	
	
	public function selectPolicy(){
		$this->display();
	}
	
	public function changeCurrentDrivingId($drivingId){
		
	}

}