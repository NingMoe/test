<?php
namespace Home\Controller;
use Api\ocr\bdocr;
use Common\Controller\CommonController;
use Common\Lib\Golbal;
use Common\Lib\Tools;
use wx\Method;
use Common\Model\ClientModel;
use Common\Config\Client;
use Common\Config\Service;
use Common\Object\WorkStatus;
use Common\Object\Dispatch;
use Api\wx\Ticket;
class TController extends CommonController{
	public function index(){
// 		$ret=Golbal::getConfig('red_scale');
// 		$sid='160102515010294';
// 		$client=new ClientModel();
// 		$ret=$client->getUserByMap(array('share_string'=>$sid));
// 		var_dump($ret);
		echo 11;
// 		$obj=new WorkStatus();
// 		$ret=$obj->getUnsolved();
// 		var_dump($ret);

		$dispatch=Dispatch::getInstace();
		$q=$dispatch->getQueueInfo();
		var_dump($q);
		echo "<hr>";
		echo $dispatch->getAssignServiceId();
		echo "<hr>";
		$expression=$dispatch->getQueueInfo();
		var_dump($expression);
	}
	public function scene(){
		$ticket=new Ticket();
		$ret=$ticket->getScene();
		ct($ret);
	}
	public function oc(){
//		$arr_option = getopt("d:");
//		$file = $arr_option['f'];
//		ct($arr_option);
//		exit;
		if(IS_POST){
			$file=$_FILES['first']['tmp_name'];
			ct($file);
			$deText=bdocr::getOcrText($file);
			$deText=json_decode($deText,1);
			ct($deText);
		}else{
			$this->display();
		}
	}
}