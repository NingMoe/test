<?php
namespace Admin\Controller;
use Common\Controller\CommonController;
use Common\Object\ServiceAction;
use Admin\Model\AdminModel;
class AdminController extends CommonController{
	//后台父类，权限控制
	protected $_m;
	protected $_admin;
	protected function _initialize(){
// 		$model=CONTROLLER_NAME.'Model';
// 		$this->_m=D(CONTROLLER_NAME);
		$admin=new AdminModel();
		//session不存在时，不允许直接访问
		if(!$admin->isLogin()){
			$this->error('还没有登录，正在跳转到登录页',U('Public/login'),1);
		}
		
		$this->_admin=$admin->getAdminInfo(); 
		
		$this->userAction=new ServiceAction($this->_admin['id']);
		
		$this->assign('adminName',$admin->getAccount());
		//session存在时，不需要验证的权限
		$not_check = array('Index/index','Admin/loginOut');
		 
		//当前操作的请求                 模块名/方法名
		if(in_array(CONTROLLER_NAME.'/'.ACTION_NAME, $not_check)){
			return true;
		}
		
		return true;
		//动态判断权限
		$auth = new \Think\Auth();
		if(!$auth->check(CONTROLLER_NAME.'/'.ACTION_NAME,$admin->getId())){
			echo CONTROLLER_NAME.'/'.ACTION_NAME;
	
			if($admin->isLogin()){//如果已经登录
				$this->error('没有权限请重新操作');//返回前页
			}else{
				$this->error("没有权限",U('Public/login'));
			}
		}	
	}

	public function add(){
		if(IS_POST){
			$ret=D('admin')->register();
			if($ret){
				$this->success('添加成功');
			}else{
				$this->error("添加失败");
			}
		}else{
			$this->display();
		}
	}
}