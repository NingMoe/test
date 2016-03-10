<?php
namespace Admin\Controller;
use Think\Controller;
use Admin\Model\PromotionModel;
use Common\Model\PictureModel;
class PromotionController extends AdminController{
	private $id;
	protected function _initialize(){
		parent::_initialize();
		$this->_m=new PromotionModel();
		$fields=array('id'=>'编号','name'=>'姓名','scan_num'=>'扫码次数','scan_users'=>'关注人数','ticket_url'=>'二维码'
				,'reply_imgid'=>'回复图','title'=>'标题','description'=>'描述');
		$this->assign('fields',$fields);
	}
	public function Index(){
		$list=$this->getList();
		
		
		$this->assign('list',$list);
		$this->display();
	}
	public function addUser(){//echo "用户名称不能空";exit;
		$name=I('post.name');
		if(empty($name)){
			$this->ko('用户名称不能空');
		}
		$data['name']=$name;
		$ret=$this->_m->addUserEx($data);
		if($ret){
			$msg='添加成功';
		}else{
			$msg="添加失败";
		}
		$this->ok($msg,U('Promotion/Index'));
		
	}
	public function addReply(){
		if(IS_POST){
			ee('addreply post:'.getoutstr($_POST),'temp');
			ee('addreply file:'.getoutstr($_FILES),'temp');
			
			$id=I('post.id');
			$url=I('post.reply_url');
			$title=I('post.title');
			$description=I('post.description');
			if(!$id || !$url){
				$this->ko('id or url error');
				exit;
			}

			$pm=new PictureModel();
			$imgId=$pm->upSimpleOneImg();
			if(!$imgId){
				$this->ko('图片上传失败');
				exit;
			}
			$data=array('id'=>$id,'reply_url'=>$url,'reply_imgid'=>$imgId,'title'=>$title,'description'=>$description);
			if($this->_m->freshInfo($data)){
				$this->ok('添加成功',U('Promotion/Index'));
			}else{
				$this->ko('添加失败');
			}
			
		}else{
			$id=I('get.id');//链接进来
			$info=$this->_m->getInfo($id);
			$this->assign('info',$info);
			$this->display('edit');
		}
		
	}
	private function getList(){
		$ret=$this->_m->getAll();
		return $ret;
	}
	public function editInfo(){
		$this->setId();
		if(IS_POST){
			if($this->_m->freshInfo($_POST)){
				$this->success('更新成功',U('index'));
			}else{
				$this->ko('更新失败'.$this->_m->getError());
			}
		}else{
			$editFields=array('name'=>'姓名','title'=>'回复标题	','description'=>'回复描述');
			$this->assign('editFields',$editFields);
			$info=$this->_m->getInfo($this->id);
			$this->assign('info',$info);
			$this->display();
		}
	}
	public function del(){
		$this->setId();
		if($this->_m->delById($this->id)){
			$this->ok('删除成功');
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