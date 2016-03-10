<?php
namespace Home\Controller;
use Common\Controller\CommonController;
use Common\Config\Driving;
use Common\Model\ClientModel;
use Common\Model\CompanyModel;
use Api\wx\JsTicket;
use Api\wx\TplLogin;
use Common\Lib\Tools;
use Common\Model\OrderModel;
use Api\wx\tpl\TplBuySuccess;
class TeController extends CommonController{
	public function index(){
		
		$this->display();
	}
	public function testStatus(){
		Driving::changeStatus(1, 2);
	}
	public function test(){
		$m=new CompanyModel();
		$ret=$m->getList();
		ct($ret);
	}
	public function js(){
		$js=new JsTicket();
		echo date('y-m-d H:i:s',1454066444);
		ct($js->getSignPackage(),'package:');
	}
	public function add(){
		$data=array (
			'openid' => 'oZH5_wkpNtR7ysHSsfBNg4VpbX9U',
			'promotion_id' => 20,
			'origin' => 1,
		);
		$mc=new ClientModel();
		$r=$mc->addUser($data);
		ct($r);echo "<br>";
		ct($mc->getError());
	}
	public function notify(){
		$user=new ClientModel();
		$ret=$user->where(array('id'=>55))->find();
		$this->sendLoginNotify($ret);
	}
	private function sendLoginNotify($user){
		$notify=new \Api\wx\tpl\TplLogin($user['openid']);
		$needData=$notify->createNeedData($user['mobile'],$user['nickname'],Tools::timeFormatS($user['last_login_time']));
		$notify->sendNotify($needData);
	}
	public function notify2(){
		$openid='oZH5_wkpNtR7ysHSsfBNg4VpbX9U';
		$ordnm='660218985480';
		$om=new OrderModel();
		$order=$om->getOrderInfoByNumber($ordnm);
		$this->sucNotify($order,$openid);
	}
	private function sucNotify($order,$openid){
		$content=Tools::parseSqlStore($order['content'],1);
		$jqccendtime=$content['jqccendtime'];
		$notify=new TplBuySuccess($openid);
		$needData=$notify->createNeedData($jqccendtime);
		$notify->sendNotify($needData);
	}
}