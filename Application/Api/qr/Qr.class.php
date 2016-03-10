<?php
namespace Api\qr;
require_once '/../phpqrcode/qrlib.php';
class Qr{
	public function test(){
// 		\QRcode::png('http://baidu.com','test1.png', QR_ECLEVEL_L, 5, 0);
		\QRcode::png('http://baidu.com',false, QR_ECLEVEL_L, 9, 0);
	}
	public function createQr($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4, $saveandprint=false){
		return \QRcode::png($text,$outfile,$level,5,$margin,$saveandprint);
	}
}