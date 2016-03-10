<?php
namespace Api\Controller;
use Api\qr\Qr;
class QrController{
	public function test(){
		$q=new Qr();
		$q->test();
	}
}