<?php
namespace Home\Controller;
use Common\Model\AddressModel;
use Common\Model\OrderModel;

class AddressController extends MainController{
	//地址管理控制器
	protected function _initialize(){
		parent::_initialize();
		$this->m=new AddressModel();
	}
	/**
	 * 支付成功页
	 */
	public function paySuccess(){
		$ret=$this->m->getAllAddress($this->_client->getClientId());
	}
	/**
	 * 得到当前 用户的地址
	 */
	public function getCurrentAddress(){
		
		$ret=$this->m->getAllAddress($this->_client->getClientId());
		ee('getCurrentAddress ret:'.getoutstr($ret),'temp');
		$this->contentName='addresslist';
		$this->outJson($ret,1);
	}
	/**
	 *添加地址信息 
	 */
	public function add(){
		$data=I('post.');
		if(!$data){
			$this->ko('数据为空');
		}

		$data['client_id']=$this->_client->getClientId();
		$ret=$this->m->addFresh($data);
// 		if(!$ret){
// 			$this->ko('添加失败：'.$this->m->getError());
// 		}else{
// 			$this->ok('添加成功');
// 		}
		if(isset($data['orderNum']) && !isset($data['id']) && $ret){
			//更新订单地址id
			$data=array(
				'number'=>$data['orderNum'],
				'address_id'=>$ret
			);
			$mo=new OrderModel();
			$mo->addFresh($data);
		}

		$result=array('addressResult'=>$ret);
		$this->outJson($result);
	}
	
	/**
	 * 编辑制定id的地址
	 */
	public function edit(){
		$data=I('post.');
		if(!$data['id']){
			$this->ko('没有指定id');
		}
		$ret=$this->m->addFresh($data);
// 		if(!$ret){
// 			$this->ko('更新失败：'.$this->m->getError());
// 		}else{
// 			$this->ok('更新成功');
// 		}

		$result=array('addressResult'=>$ret);
		$this->outJson($result);
	}
}
