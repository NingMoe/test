<?php
namespace Common\Config;
use Common\Model\DrivinglicenseModel;
class Driving{
	//行驶证 保单状态  按步骤，进行
	//对应driving action_status 
	
// 	static $init=1;
// 	static $upDriving=2;//行驶证上传1
// 	static $drivingInvalid=3;//报价失败,行驶证无效，

	static $drivingInvalid=1;//报价失败,行驶证无效，
	static $upDriving=2;//行驶证上传1
	static $auditInfomation=3;  //报价信息已审核
	static $packageInvalid=4;//报价失败,套餐无效
	static $commitPackage=5;//提交套餐1
	static $priceOrOrderFailure=6;//报价或订单 失效
	static $createPrice=7;//报价完成1
	static $createOrderContent=8;//生成未支付订单1		报价,查看详情后订单有数据
	static $createPay=9;//订单发起支付请求
	static $paySuccess=10;//订单已支付成功
	static $insureEffect=11;//保单生效
	static $pastDue=12;//保单过期
	
	/**
	 * 更改车辆流畅状态
	 */
	static function changeStatus($drivingId,$status){
		if(!is_numeric($status)){
			die('状态信息有误');
		}
		if(!$drivingId){
			die('车辆id有误');
		}
		$data=array(
			'id'=>$drivingId,
			'action_status'=>$status,
			'update_time'=>time()
		);

		$md=new DrivinglicenseModel();
		$ret=$md->addFreshDriving($data);
		
		return $ret;
	}
	
}