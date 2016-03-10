<?php
namespace Admin\Model;
use Think\Model;
use Common\Object\SessionManage;
use User\Lib\UserC;
use Common\Lib\Tools;
use Common\Object\ServiceAction;
class AdminModel extends Model{
	private $_ywhere;
	
	public function login(){
		$where = array(
				'account'   => I('post.account'),
				'password' => UserC::adminPwMd5(I('post.password'))
		);
		$data=array(
				'islogin'=>1,   //!!!!!!!!!!设置登录状态  ，在线
				'login_time' => NOW_TIME,
				'login_ip'   => get_client_ip(1)
		);
		$this->_ywhere=$this->options['where']=$where;//为什么不能保存
		$userinfo=$this->find();
		if($userinfo && $this->freshLogin($data)){
			$userinfo['groupId']=$this->getGroupId($userinfo['id']);ee($userinfo['groupId'].':group');
			//记录行为
			$action=new ServiceAction($userinfo['id']);
			$action->login();
			//更改登录状态
			
			//登录更新信息成功
			SessionManage::setAdmin($userinfo);
			return true;
		}
		return false;
	}
	
	/**
	 * 设置退出登录状态   不在线
 	 */
	private function setLoginOutStatus(){
		return $this->where('id='.$this->getId())->save(array('islogin'=>0));
	}
	
	public function getGroupId($userId){
		$m=M('auth_group_access');
		$ret=$m->where("uid=$userId")->getField('group_id');
		if($ret){
			return $ret;
		}else{
			$this->error='获得用户分组出错';
		}
	}
	public function freshLogin($data){
		$this->error='更新登录信息中';
		return $this->where($this->_ywhere)->save($data);
	}
	public function register($data=null){
		if(IS_POST && null===$data){
			$data=I('post.');
		}
		if(!Tools::isValid($data)){
			$this->error='注册数据不正确';
			return false;
		}
		$data['password']=UserC::adminPwMd5($data['password']);
		$data['create_time']=time();
		return $this->add($data);//返回成功id
// 		var_dump($ret);
	}

	public function getGroup(){
		
	}
	/**
	 * 得到客服管理id
	 * @return Ambigous <>
	 */
	public function getId(){
		$adminSession=SessionManage::getAdmin();
		return $adminSession['id'];
	}
	public function getAccount(){
		$adminSession=SessionManage::getAdmin();
		return $adminSession['account'];
	}
	public function getAdminInfo(){
		return SessionManage::getAdmin();
	}
	public function isLogin(){
		if(SessionManage::getAdmin()){
			return true;
		}
		return false;
	}
	public function logintOut(){
		$ret=$this->setLoginOutStatus();
		if(!$ret){
			ee('!!!!!登出，更新状态失败');
		}
		SessionManage::emptyAdmin();
	}
	/**
	 * 得到在线销售客服id,要分配的
	 */
	public function getOnlineServiceIds(){
		$map=array('role'=>3,'islogin'=>1);
		$this->field('id');
		$this->where($map);
		$ret=$this->select();
		if(!$ret){
			ee('获取在线客服信息失败');
// 			die('获取在线客服信息有误');
		}
		$result=array();
		foreach ($ret as $row){
			$result[]=$row['id'];
		}
		return $result;
	}
	public function getCurrentAdminAllInfo(){
		$this->where(array('id'=>$this->getId()));
		return $this->find();
	}
	public function freshLastGetTime($time){
		if(!$time){
//			die('time error');
			$time=time();
		}
		$data['lastget_time']=$time;
		return $this->where(array('id'=>$this->getId()))->save($data);
	}
	public function getRoleList($roleId){
		if(!$roleId){
			die('roleid error');
		}

		$map['role']=$roleId;


		$ret=$this->where($map)->select();

		return $ret;
	}
	public function getInfoById($id){
		if(!$id){
			die('id error');
		}
		return $this->where(array('id'=>$id))->find();
	}
	public function freshInfoById($id,$data){
		if(!$id || !$data){
			die('id or data error');
		}
		$data['password']=UserC::adminPwMd5($data['password']);
		return $this->where(array('id'=>$id))->save($data);
	}
	public function delById($id){
		if(!$id){
			die('id error');
		}
		return $this->where(array('id'=>$id))->delete();
	}

}













