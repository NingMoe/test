<?php
namespace Api\Controller;
use wx\MenuStub;
use Api\wx\Method;
class MenuController{                       /// \Common\Controller\CommonController{
	private $_menuData;
	function __construct(){         		// _initialize(){
		$this->_menuData=array(
				'button'=>array(
								array(
										'name'=>"关于我们",
										'sub_button'=>array(
												array(
														'type'=>'view',
														'name'=>'yy首页',
														'url'=>'http://oneonebao.com/'			//'THE_WEBSITE'
												)
										)
								),
								array(
										'name'=>"资讯中心",
										'sub_button'=>array(
												array(
														'type'=>'view',
														'name'=>'yy首页',
														'url'=>'http://120.27.139.117/home'
												)
										)
								),
								array(
										'name'=>"测试中心",
										'sub_button'=>array(
												array(
														'type'=>'view',
														'name'=>'测试首页',
														'url'=>'http://120.27.139.117/home/te'
												),
												array(
														'type'=>'view',
														'name'=>'测试',
														'url'=>'http://120.27.139.117/home/te'
												)
										)
								),
					)
			);
		Method::wl("***start menucontroller--__construct***");
	}
	public function index(){echo "menu:";
		$ret=\Api\wx\MenuStub::create($this->_menuData);

// 		$ret=\Api\wx\MenuStub::delete();
		$ret=\Api\wx\MenuStub::get();
		print_r($ret);
		Method::wl("menu action return:".$ret);
		if(false===$ret){
			Method::wl("false===$ret menu fail!");
			echo "menu action fail!\n";
		}else{
			Method::wl("menu action success");
			echo "menu action  success!\n";
		}
		Method::wl("***END MENU****");
	}
	public function __destruct(){
		Method::wl("***into menuController-->>>>>>>>>>__construct***");
		$this->_menuData=null;
	}
}





















