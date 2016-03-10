<?php
namespace Admin\Model;
use Think\Model;
use Api\wx\Ticket;
use Common\Model\PictureModel;
class PromotionModel extends Model{
	
	public function getAll($map=NULL){
		if(null!=$map){
			$this->where($map);
		}
		$this->order('id asc');
		$result=$this->select();
		return $result;
	}
	
	public function addUserEx($data){
		$userId=$this->add($data);
		if(!$userId){
			$this->error='添加用户失败';
			return false;
		}
		$ticket=new Ticket();
		$scene=$ticket->getScene($userId);
		$ticketSql=$ticket->getTicket();
		$save=array(
			'ticket_url'=>$scene,
			'ticket'=>$ticketSql
		);
		ee('$userId:'.$userId);
		$ret=$this->where(array('id'=>$userId))->save($save);
		return $ret;
	}
	public function getInfo($id){
		if(!$id){
			$this->error='id empty';
			return false;
		}
		$ret=$this->where(array('id'=>$id))->find();
		
		return $ret;
	}
	
	public function freshInfo($data){
		if(!$data['id']){
			$this->error='fresh id error';
			return false;
		}
		$ret=$this->where(array('id'=>$data['id']))->save($data);
		return $ret;
	}
	public function getInfoByMap($map){
		if(!$map){
			return false;
		}
		return $this->where($map)->find();
	}
	public function getWxReplyInfo($map){
		$result=$this->getInfoByMap($map);
		if(!$result){
			return false;
		}
		$result['reply_imgid']=$this->getWxImgPath($result['reply_imgid']);
		ee('wximgurl:'.$result['reply_imgid'],'temp');
		return $result;
	}
	private function getWxImgPath($imgId){
		$baseUrl='http://oneonebao.com/';
		$mp=new PictureModel();
		$imgPath=$mp->getImgById($imgId);
		return $baseUrl.$imgPath;
	}
	public function delById($id){
		if(empty($id)){
			die('id error');
		}
		return $this->where(array('id'=>$id))->delete();
	}
}








