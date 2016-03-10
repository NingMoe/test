<?php
namespace Common\Model;
use Think\Model;
use Common\Lib\Tools;
class CompanyModel extends Model{
	public function getTest(){
		echo 'test11';
	}
	/**
	 * 得到公司列表
	 * @return \Think\mixed
	 */
	public function getList(){
		$this->field('id,name,discount,updatetime');
// 		$this->where('status!=-1');
		$result=$this->select();
		usort($result,array('Common\Model\CompanyModel','sortCmp'));
		return $result;
	}
	public function del($id){
		$where['id']=$id;
		return $this->where($where)->delete();
	}
	public function getInfo($id){
		$result=$this->where("id=$id")->find();
		return $result;
	}
	public function fresh($data){
		$data['updatetime']=NOW_TIME;
		return $this->where('id='.$data['id'])->save($data);
	}
	public function addCompany($data){
		$data['updatetime']=NOW_TIME;
		return $this->add($data);
	}
	static public function sortCmp($a,$b){
		return Tools::sortCmp($a['discount'], $b['discount']);
	}
}



