<?php
namespace Admin\Controller;

use Admin\Model\AdminModel;
class ServiceController extends AdminController{
	private $roleId;
	private $id;
	protected function _initialize(){
		parent::_initialize();
		$this->_m=new AdminModel();

		$roleId=I('get.roleId');
		if(!$roleId){
			$roleId=I('post.roleId');
		}
		if(!$roleId){
			die('request roleId error');
		}
		$this->roleId=$roleId;
		$this->assign('roleId',$roleId);
		$fields=array('id'=>'编号','account'=>'用户名','password'=>'密码','name'=>'姓名'
//			'id'=>'编号','id'=>'编号',
		);
		$this->assign('fields',$fields);

		if(IS_POST){
			if(isset($_POST['roleId'])){
				$_POST['role']=$_POST['roleId'];
			}
		}
	}
	public function index(){
		$list=$this->_m->getRoleList($this->roleId);
		$this->assign('list',$list);


		$this->display();
	}
	public function edit(){
		$this->setId();
		if(IS_POST){
			if($this->_m->freshInfoById($this->id,$_POST)){
				$url=U('index',array('roleId'=>$this->roleId));
//				echo $url;//exit;
				$this->success('更新成功',$url,1);
			}else{
				$this->ko('更新失败'.$this->_m->getError());
			}
		}
		else{
			$info=$this->_m->getInfoById($this->id);
			$this->assign('info',$info);
			$this->display();
		}

	}
	public function del(){
		$this->setId();
		if($this->_m->delById($this->id)){
			$this->ok('删除成功',U('index'));
		}else{
			$this->ko('删除失败'.$this->_m->getError());
		}
	}
	private function setId(){
		$id=I('get.id');
		if(!$id){
			$id=I('post.id');
		}
		if(!$id){
			die('request id error');
		}
		$this->id=$id;
	}
}