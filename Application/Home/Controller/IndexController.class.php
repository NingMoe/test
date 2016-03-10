<?php
namespace Home\Controller;
use Think\Controller;
use Common\Object\Dispatch;
use Common\Object\Policy;
use Common\Object\Package;
use Common\Lib\Yydate;
use Common\Object\SessionManage;
class IndexController extends MainController{
	//首页
    public function index(){
//     	new Package();
//     	$this->redirect('Package/diy');
// 		$this->display();

// $ydate=Yydate::getInstance();
// $start=mktime(0,0,0,11,30,2014);
// $end=mktime(0,0,0,1,20,2015);
// echo $ydate->getMonths();

    	$this->display();
    }
	public function getcs(){
		var_dump(Dispatch::getInstace());
	}
	/**
	 * 取所有车牌
	 */
	public function getCars(){
		$cars=$this->_client->getAllDrivings();
		$index=0;
		
// 		$result[$index]['id']=-1;
// 		$result[$index]['carnumber']='一键询价';
// 		$result[$index]['action']='yijianxunjia';
		
		foreach ($cars as $k=>$car){
			if($car['license_number'] && $car['license_number']!=""){
				
				$result[$index]['id']=$car['id'];
				$result[$index]['carnumber']=$car['license_number'];
				//add for front
				$result[$index]['action']='carinfo';
				
				$index++;
			}
			
		}
		
// 		$index++;
		$result[$index]['id']=-2;
		$result[$index]['carnumber']='添加车辆';
		$result[$index]['action']='addcar';
		ee('index carlist:'.getoutstr($result),'temp');
		$this->contentName='homeinfo';
		$this->outJson($result,1);
	}
	
	
	
}