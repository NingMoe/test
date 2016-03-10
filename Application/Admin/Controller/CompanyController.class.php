<?php
namespace Admin\Controller;
use Common\Model\CompanyModel;
class CompanyController extends AdminController{
	//保险公司管理控制器
	
	protected function _initialize(){
		parent::_initialize();
		$this->_m=new CompanyModel();
		$fields=array('id'=>'编号','name'=>'公司名称','discount'=>'折扣','updatetime'=>'更新时间','操作');
		$this->assign('fields',$fields);
	}
	public function index(){
    	$list=$this->_m->getList();
		$this->assign('list',$list);
		$this->display();
	}
	public function edit(){
		if(IS_POST){
			if($this->_m->fresh(I('post.'))){
				$this->success('更新成功',U('index'),1);
			}else{
				$this->error('更新失败:'.$this->_m->getError());
			}
			
		}elseif(IS_GET){
			$id=I('get.id');
			if(!$id){
				$this->error('id无效');
				exit;
			}
			
			$ret=$this->_m->getInfo($id);
			$this->assign('info',$ret);
			$this->display();
		}
	}
	public function add(){
		if(IS_POST){
			if($this->_m->addCompany(I('post.'))){
				$this->error('增加成功',U('index'),1);
			}else{
				$this->error('增加失败：'.$this->_m->getError());
			}
		}else{
			$this->display();
		}
	}
	public function del(){
		$msg=$this->_m->del(I('get.id'));
		if($msg){
			$this->success('删除成功：'.$msg);
		}else{
			$this->error('删除失败');
		}
	}
}