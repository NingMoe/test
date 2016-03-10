<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Think\Controller;
use Admin\Model\AdminModel;
/**
 * 后台首页控制器
 */
class PublicController extends Controller{
	private $_admin;
	protected function _initialize(){
		$this->_admin=new AdminModel();
	}

    /**
     * 后台用户登录
     */
    public function login($username = null, $password = null, $verify = null){
        if(IS_POST){
            /* 检测验证码 TODO: */
//             if(!check_verify($verify)){
//                 $this->error('验证码输入错误！');
//             }
			if($this->_admin->login()){
				$this->success('登录成功',U('index/index'),1);
			}else{
				$this->error('密码或用户名错误',null,1);
			}
        }else {
        	$this->display();
        }
        
    }

    /* 退出登录 */
    public function logout(){
    	$this->_admin->logintOut();
    	$this->redirect('login',null,1);
//         if(is_login()){
//             D('Member')->logout();
//             session('[destroy]');
//             $this->success('退出成功！', U('login'));
//         } else {
//             $this->redirect('login');
//         }
    }

    public function verify(){
        $verify = new \Think\Verify();
        $verify->__set('length',2);
        $verify->entry(1);
    }

}
