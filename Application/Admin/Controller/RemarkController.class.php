<?php 
namespace Admin\Controller;
use Common\Lib\Tools;
use Common\Model\RemarkModel;
class RemarkController extends AdminController{
	private function logRequest(){
		ee('remarkController post:'.getoutstr($_POST),'temp');
		ee('remarkController get:'.getoutstr($_GET),'temp');
	}
	public function getRemarks(){
		
	}
	public function saveRemark(){
		$this->logRequest();
		$data=I('post.');
// 		if ($data['carid']){
// 			$data['driving_id']=$data['carid'];
// 		}
		if(!$data['carid']){
// 			$this->ko('carid or content error');
			$this->outJson(array('remarks:'=>-1));
		}
		if($data['remarkContent'] &&  ""!=$data['remarkContent']){
//			$data['createtime']=time();
			$mr=new RemarkModel();
			$ret=$mr->addFresh($data);
		}
		
		$result=self::getList($data['carid']);
		ee('return:'.getoutstr($result),'temp');
		$this->outJson(array('remarks'=>$result));
		
	}
	
	public static function getList($carId){
		$mr=new RemarkModel();
		$ret=$mr->getList($carId);
// 		$retstr="";
		$result=array();
		foreach ($ret as $k=>$v){
// 			$retstr.=$v['content'].'\r\n';
			$result[]=array(
				'content'=>$v['content'],
				'time'=>date('Y-m-d H:i',$v['createtime'])
			);
		}
		return $result;
// 		$content=nl2br(htmlspecialchars($rawdata));
		return $retstr;
	}
	public function emptyRemark(){
		$carid=I('post.carid');
		if(!$carid){
			die('车辆id错误');
		}
		$mr=new RemarkModel();
		$mr->where(array('driving_id'=>$carid))->delete();
		
		$result=self::getList($carid);
		$this->outJson(array('remarks:'=>$result));
	}
}




?>