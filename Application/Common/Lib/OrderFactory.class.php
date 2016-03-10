<?php
namespace Common\Lib;
class OrderFactory{
	//订单生产类
	static public function createOrder(){  //$orderId
		$result;
		$rand=rand(10, 99);
		$str=substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 4);
		$result=date('y',time()).date('m',time()).date('d',time()).$str.$rand;		//sprintf("%04d",$orderId)
		return $result;
	}
}