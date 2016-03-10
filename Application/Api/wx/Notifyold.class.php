<?php
namespace Api\wx;
//模板消息
class NotifyOld{
	private $_touser;
	private $_template_id;
	private $_interface_set;//设置所属行业
	private $_interface_getId;//获得模板id
	private $_interface_send;//发送模板消息
	
	private $_tplId;
	private $_tpl_url;//模板字段
	private $_data_first;//模板数据字段
	private $_data_remark;//模板数据字段
	/*
	 * $touser  touser的openid
	 */
	public function __construct($toUser=""){
		wl("notify __construct new -----------------------------------------------------------");
// 		$this->_touser="o0MFCt-Z1Sz56WIpJ0z8UYBwJ75U";
		$this->_touser="oG2tvuP5bttLg0UbFQi2rZRt5BxE";
		if($toUser!=""){
			$this->_touser=$toUser;
		}
		$this->_touser="o0MFCt-Z1Sz56WIpJ0z8UYBwJ75U";//测试用
		$this->_template_id="tgA3cCt9oVJrl5ZU1fAU-Ky9Zq2ok1ZfkVz9muJPU94";
		$this->_interface_set="template/api_set_industry";
		$this->_interface_getId="template/api_add_template";
		$this->_interface_send="message/template/send";

		$this->_tplId=array(
				"JIXqV5K9fM106Q3mj3_qMU1s4ha2F3Al3JgoyYjbagM",
				"B0MfCn9m7056r5dhgOotKSRx7z7D_5rF4e57NJw1ZmA",
				"xJ85O_YV3g22_US7kNmNZY5roTZJwwqupLuuPXfdo5E",
				"lU-x_xwKKbUGGDwGq2H9LdSHOlBaviD7xbGWNakyp5g"
		);
		$this->_data_first=array(
				"你发布的职位收到了一条职位申请",
				"您好,您申请的职位收到了面试邀请。",
				"你发布的档期被人抢单了",
				"您抢的人才得到了同意"
		);
		$this->_data_remark=array(
				"点击想请查看申请人信息",
				"请尽快反馈。",
				"点击查看抢单人列表选择公司",
				"请尽快联系"
		);
		$this->_tpl_url=array(
				"http://www.163.com/",
				"http://www.163.com/",
				"http://www.163.com/",
				"http://www.163.com/"
		);
	}
	public function index(){
		echo "notify";
	}
	//取得模板消息
	public function getNotify(){
		$post_set=array("industry_id1"=>1,"industry_id2"=>34);
		$post_getId['template_id_short']="igSaWSD0NvXfTRUMMUc_dqHQkusTj39BGXAq7Awa38k";
		$ret=RequireInterface::reqInterface($this->_interface_set,$post_set);
// 		$ret=RequireInterface::reqInterface($interface_getId, $post_getId);
		cout($ret);
	}
	public function test($userOpenid="",$url="http://www.163.com/",$comName="角落科技"){  //面试通知
		wl("interviewNotify----------");
		if(""!=$userOpenid){
			$this->_touser=$userOpenid;
		}
		$post_send=$this->tmpTplData($url,$comName, date("通知时间：y年m月d日--h:m:s",time()));
		$ret=$this->sendNotify($post_send);
		if($ret){
			return $ret;
		}else{
			return false;
		}
	}
	/*
	 * $data 模板data数据，按keyword=>value;
	 * $typeId所用模板id，用0-3表示
	 */
	public function setNotify($typeId,$data){
		if(empty($data)){
			return false;
		}
		$post_send['touser']=$this->_touser;
		$post_send['template_id']=$this->_tplId[$typeId];
		$post_send['url']=$this->_tpl_url[$typeId];
		$post_send['data']['first']=array("value"=>$this->_data_first[$typeId],"color"=>"black");
		$i=0;
		foreach ($data as $keyword=>$value){
			$i++;
			$post_send['data']["keyword$i"]=array("value"=>$value,"color"=>"green");
		}
		$post_send['data']['remark']=array("value"=>$this->_data_remark[$typeId],"color"=>"red");
		$ret=$this->sendNotify($post_send);
		if($ret){
			echo  $ret;
		}else{
			return false;
		}
	}
	
	//-------------------------------------------------
	//发送模板消息
	private function sendNotify($post_send){//cout($post_send);
		$ret=RequireInterface::reqInterface($this->_interface_send, $post_send);
		return $ret;
	}
	//组装具体自己测试模板的具体数据，返回该数据
	private function tmpTplData($typeId,$url,$comName,$time){
		$post_send['touser']=$this->_touser;
		$post_send['template_id']=$this->_template_id;
		$post_send['url']=$url;
		$post_send['data']['comp']=array("value"=>$comName,"color"=>"#0000ff");
		$post_send['data']['time']=array("value"=>$time,"color"=>"green");
		return $post_send;
	}
	

}
