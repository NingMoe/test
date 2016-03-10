<?php
namespace Common\Model;
use Think\Model;
use Common\Lib\OrderFactory;
class OrderModel extends Model{
	//订单模型类
	/**
	 * data 空时 添加订单号，含id或单号时更新数据
	 * @param string $data
	 */
	public function addFresh($data=null){
		if(null===$data){
			$data['number']=OrderFactory::createOrder($orderId);
			return $this->add($data);
		}
		if($data['id']){
			return $this->where('id='.$data['id'])->save($data);
		}
		if($data['number']){
			ee('number'.$data['number'],'temp');
			return $this->where(array('number'=>$data['number']))->save($data);   //"'number='".$data['number']."'"
		}
	}
	/**
	 * 通过订单号得到订单信息
	 * @param unknown $number 订单号
	 * @return boolean|\Think\mixed
	 */
	public function getOrderInfoByNumber($number){
		if(!$number){
			$this->error='订单id空';
			return false;
		}
		$this->where("number={$number}");
		$result=$this->find();
		return $result;
	}
	/**
	 * 通过订单id得到订单信息
	 * @param unknown $orderId
	 * @return boolean|\Think\mixed
	 */
	public function getOrderInfoById($orderId){
		if(!$orderId){
			$this->error='订单id空';
			return false;
		}
		$this->where("id={$orderId}");
		$result=$this->find();
		return $result;
	}
	/**
	 * 根据用户所有行驶id得到所欲订单信息
	 * @param unknown $drivingI
	 */
	public function getClientOrderByAllDrivings($drivingIds){
		$map['yy_pricesheet.driving_id']=array('in',$drivingIds);
		
		$this->field('driving_id,license_number,action_status,
				yy_order.id,number,yy_order.content,yy_order.status,yy_order.amount,pay_amount,red_amount,
				package_style,yy_order.updatetime,yy_order.deadtime,paytime,company_name');
		$this->join('yy_pricesheet on yy_pricesheet.order_id=yy_order.id');
		$this->join('yy_drivinglicense on yy_drivinglicense.id=yy_pricesheet.driving_id');
		$this->where($map);
		$result=$this->select();
		return $result;
	}

	/**
	 * 通过订单号得到车id 信息
	 * @param $orderNum
	 * @return bool
	 */
	public  function getDrivingIdByOrderNum($orderNum){
		if(!$orderNum){
			$this->error='ordernum error';
			return false;
		}
		$this->field('driving_id');
		$this->join('yy_pricesheet on yy_pricesheet.order_id=yy_order.id');
		$this->where(array('number'=>$orderNum));
		$ret=$this->find();
		return $ret['driving_id'];
	}
}