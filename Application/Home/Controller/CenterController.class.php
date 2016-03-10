<?php
namespace Home\Controller;
use Common\Model\DrivinglicenseModel;
use Common\Model\AddressModel;
use Common\Model\SuggestModel;
use Common\Config\Interaction;
class CenterController extends MainController{
	private $redUsable;//可用红包金额
	private $quotedpriceNum;//新的报价
	private $addressNum;//保单地址
	public function _initialize(){
		parent::_initialize();
		$baseInfo=$this->_client->getBaseInfo();
		$this->redUsable=$baseInfo['red_usable'];
		
	}
	/**
	 * 用户中心页控制 ，车牌信息
	 */
	public function index(){
		$dataFormat = array (
				'platenumber' => '待提交 ',
				'username' => '神秘人 ',
				'carinfo' => '待提交 ',
				'chejianumber' => '待提交 ',
				'fadongjinumber' => '待提交 ',
				'regtime' => '待提交 ',
				'cankaoprize' => '待提交 ',
				'cateexpire'=>'猜不到',
				'jqexpire'=>'猜不到'
		);
		
		$this->contentName='chepailist';
		$page=I('get.page');
		$limit=I('get.limit');
		ee('center cplist post:'.getoutstr($_GET));
		$start=($page-1)*$limit;
		if(!$start){
			$start=0;
		}
		if(!$limit){
			$limit=2;
		}
		$drivings=$this->_client->getAllDrivingsView();
		$result=array();
		foreach ($drivings as $v){
			$tempData=$dataFormat;
			$tempData['drivingid']=$v['drivingid'];
			$tempData['platenumber']=$v['platenumber'];
			foreach ($dataFormat as $dk=>$dv){
				if ($v[$dk] && $v[$dk]!=''){
					$tempData[$dk]=$v[$dk];
				}
			}
			
			$result[]=$tempData;
		}
		
ee('car s :'.getoutstr($result)); 
ee('car s$start :'.$start.'--count:'.count($result)); 		
		if($start<count($result) && $result){
		ee('car 11s$start :'.$start.'--count:'.count($result)); 
			$result=$this->getPageData($start, $limit,$result);
		}else{
			$result=null;
		}
ee('car list ret :'.getoutstr($result));

		$this->outJson($result,1);
	}
	private function getPageData($start,$limit,$data){
		$result=array();
		while ($limit && $start<count($data) ){
			$result[]=$data[$start];
			$start++;
			$limit--;
		}
		return $result;
	}
	private function getDrivings(){
		
		
	}
	
	/**
	 * 取得用户行驶证处理状态
	 */
	public function getStatus(){
		$drivingId=I('get.carid');//取得车id参数，没有用当前默认
		ee('getstatus carid:'.$drivingId);
		if(!$drivingId){
			$drivingId=$this->_client->getCurrentDrivingId();
		}
		if(!$drivingId){
			
			$this->outJson(array('actionstatus'=>-1,'errorMsg'=>'没有行驶证信息'),1);
			
		}
		$md=new DrivinglicenseModel();
		$ret=$md->getDrivingInfoById($drivingId);
		
		ee('getstatus $ret action_status:'.$ret['action_status']);
		$this->outJson(array('actionstatus'=>$ret['action_status']));
	}
	/**
	 * 
	 */
	public function frontInfo(){
		$userinfo=$this->_client->getBaseInfo();
		$countPrice=1;
		$redAmount=$userinfo['red_usable'];
		$countAddress=$this->getAddressCount($this->_client->getClientId());
		$suggest='';
		$data=array(
				array('iconname'=>'baojia','title'=>'新的报价','rightname'=>$countPrice.'家'),
				array('iconname'=>'kanjiahongbao','title'=>'砍价红包','rightname'=>$redAmount.'元'),
				array('iconname'=>'dizhi','title'=>'保单收取地址管理','rightname'=>$countAddress.'个'),
				array('iconname'=>'yijian','title'=>'意见反馈','rightname'=>$suggest)
		);
		
		$this->contentName='melistbottom';
		$this->outJson($data,1);
	}
	private function getAddressCount($clientId){
		$ma=new AddressModel();
		$ret=$ma->getAllAddress($clientId);
		if (!$ret){
			$result=0;
		}else{
			$result=count($ret);
		}
		return $result;
	}
	public function suggest(){
		if(IS_POST){
			$content=I('post.content');
			if(!$content || trim($content)==""){
				$this->outStatusByJson(Interaction::$emptyData);
			}
			$data=array(
				'client_id'=>$this->_client->getClientId(),
				'content'=>$content
			);
			$ms=new SuggestModel();
			$ret=$ms->add($data);
			if($ret){
				$this->outStatusByJson(Interaction::$dbSuccess);
			}else{
				$this->outStatusByJson(Interaction::$dbAddError);
			}
		}
	}
}