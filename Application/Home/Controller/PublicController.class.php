<?php
namespace  Home\Controller;
use Common\Controller\CommonController;
class PublicController extends CommonController{
	private function webHooks(){
		$filename='webHook';
	
		$event = json_decode(file_get_contents("php://input"));
	
		// 对异步通知做处理
		if (!isset($event->type)) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
			exit("fail");
		}
	
		ee('webhooks input:'.getoutstr($event),$filename);
	
		switch ($event->type) {
			case "charge.succeeded":
				// 开发者在此处加入对支付异步通知的处理代码
				header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
				break;
			case "refund.succeeded":
				// 开发者在此处加入对退款异步通知的处理代码
				header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
				break;
			default:
				header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
				break;
		}
	}
}