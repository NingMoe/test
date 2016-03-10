<?php
namespace Common\Model;
use Think\Model;
use Common\Lib\Tools;
use Common\Lib\OrderFactory;
use Common\Lib\Yydate;
class PricesheetModel extends Model{
	/**
	 * 添加或更新原始报价单数据
	 * @original_sheet string 报价单数据
	 * @redAmount 红包金额
	 * @id 更新的id
	 */
	public function addSheet($original_sheet,$id=null,$drivingId=null){   //,$redAmount参数位置变了。
		if(!is_string($original_sheet)){
			$this->error='报价单数据有误';
			return false;
		}
		$data['original_sheet']=$original_sheet;
		$data['updatetime']=NOW_TIME;
		//过期时间
		$data['deadtime']=Yydate::getInstance()->getWorkDayY();
		ee('addSheet deadtime add sheet'.date('y-m-d H:i:s',$data['deadtime']),'temp');
		if(null===$id){
			if(null===$drivingId){
				$this->error='行驶证id空';
				return false;
			}
			//第一次添加时，生成订单号，客户查看时更新订单内容
			//生成空订单，订单号写入
			$order=new OrderModel();
			$retid=$order->addFresh();
			if(!$retid){
				$this->error='生成订单失败';
				return false;
			}
			//订单号
			$data['order_id']=$retid;
			//行驶id
			$data['driving_id']=$drivingId;
			return $this->add($data);
		}else{
			return $this->where("id=$id")->save($data);
		}
	}
	/**
	 * 得到用户指定行驶证的报价单信息
	 */
	public function getPriceSheet($drivingId){
		$map['yy_drivinglicense.id']=$drivingId;
	
		$this->field('owner,car_model,vin,engine_no,register_time,yy_drivinglicense.id,license_number,package_style,jq_price,cc_price,pricesheet_id,pricesheet_count,original_sheet,
				yy_pricesheet.updatetime,yy_pricesheet.order_id,yy_pricesheet.status,yy_pricesheet.deadtime');
	
		$this->join('yy_drivinglicense on yy_drivinglicense.pricesheet_id=yy_pricesheet.id');
		//状态0，正在投保中状态, //全部显示
		// 		$this->where('yy_drivinglicense.status=0');
	
		$this->where($map);
		$ret=$this->find();
	
		if(!$ret){
			$this->error='车辆报价单信息出错';
			return false;
		}
		
		$result;

		if($ret['original_sheet']){
			$result['original_sheet']=Tools::parseSqlStore($ret['original_sheet'],1);
		}
		//计算加交强和车船总价
		$result['all_count']=$ret['jq_price']+$ret['cc_price']+$ret['pricesheet_count'];
		//转换结构
		return $result;
	}
	/**
	 * 得到用户当前的所有报价单信息
	 */
	public function getAllPriceSheet($drivingIds){
		$map['yy_drivinglicense.id']=array('in',$drivingIds);
		
		$this->field('owner,car_model,vin,engine_no,register_time,yy_drivinglicense.id,license_number,package_style,jq_price,cc_price,pricesheet_id,pricesheet_count,original_sheet,
				yy_pricesheet.updatetime,yy_pricesheet.order_id,yy_pricesheet.status,yy_pricesheet.deadtime,sort_id,number');
		
		$this->join('yy_drivinglicense on yy_drivinglicense.pricesheet_id=yy_pricesheet.id');
		$this->join('yy_order on yy_pricesheet.order_id=yy_order.id');
		//状态0，正在投保中状态, //全部显示
// 		$this->where('yy_drivinglicense.status=0');
		
		$this->where($map);
		$ret=$this->select();
		
		if(!$ret){
			$this->error='车辆报价单信息出错';
			return false;
		}
		$result;
		//得到是所有车辆数组         
		foreach ($ret as $key=>$value){
			////有没有报价的，不显示报价单
// 			if(!$ret['original_sheet'])
// 				$this->error='原始报价单信息出错，请联系客服';
  			//转化报价单为数组
  			if($value['original_sheet']){
  				$ret[$key]['original_sheet']=Tools::parseSqlStore($value['original_sheet'],1);
  			}
  			//计算加交强和车船总价
  			$ret[$key]['all_count']=$value['jq_price']+$value['cc_price']+$value['pricesheet_count'];
			//转换结构
			$result[$value['id']]=$ret[$key];
		}
		return $result;
	}
}