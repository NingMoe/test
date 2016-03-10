<?php
namespace Common\Model;
use Think\Model;
use Common\Lib\Yupload;
use Common\Object\SessionManage;
use Common\Object\Drivinglicense;
use Common\Object\Client;
use Api\Controller\InterfaceController;
use Common\Config\Driving;

class PictureModel extends Model{
	private $_uploader;
	public function _initialize(){
// 		$this->_uploader=new Yupload();
	}
	private function joinError($err){
		$this->error='error:'.$err.'__上传error:'.$this->_uploader->getLastError();
	}
	
	private function setUploader(){
		$this->_uploader=new Yupload();
	}
	/**
	 * 简单上传保存单个文件，返回其id
	 * @return \Think\mixed|boolean
	 */
	public function upSimpleOneImg(){
		$this->setUploader();
		$saveFileInfo=$this->_uploader->uploadFile();
		ee('savefileinfo:'.getoutstr($saveFileInfo),'temp');
		if($saveFileInfo){
			//保存数据库
			$retId=$this->add($saveFileInfo['reply_imgid']);
			return $retId;
		}
		return false;
	}
	public function upfile(Client $client){  //上传成功一半，更新用户信息
		$this->setUploader();
		
		$isadd=SessionManage::getAdd();
		ee('upfile isadd:'.getoutstr($isadd),'temp');
		$mdriving=new DrivinglicenseModel();
		if($isadd){
			$drivingId=null;
		}else {
			$drivingId=$client->getCurrentDrivingId();
		}

		$drivingInfo=null;
		$clientId=$client->getClientId();
		if(!$clientId){
			$this->error='用户id不正确';
			return false;
		}
		if($drivingId){//当前行驶证存在
			ee('dirvingid:'.$drivingId,'temp');
			$fileMS=$this->_uploader->getMd5AndSha1();
			$data['id']=$drivingId;
			//判断md5值，文件存在，直接得到id值，
			foreach ($fileMS as $key=>$value){
				$picIdName=$key.'_id';
				$tempFileId=$this->getIdByMS($value);//得到文件id
				if($tempFileId){
					$data[$picIdName]=$tempFileId;
					$this->_uploader->unsetUpFile($key);
				}
			}
			ee('picturemodel upfile driving exist data:'.getoutstr($data),'temp');
			//如果两页都已经存在，则返回
			$drivingInfo=$mdriving->getDrivingInfoById($drivingId);
			if($drivingInfo['first_id'] && $drivingInfo['second_id']){
				if($drivingInfo['first_id']==$data['first_id'] && $drivingInfo['second_id']==$data['second_id']){
					$this->joinError('该行驶证已存在');
					return false;
				}
			}
		}
	
		//文件还存在，则上传文件///上传文件还在，说明没有处理完
		ee('isUploadFilesExist before:','temp');
		if($this->_uploader->isUploadFilesExist()){
			ee('isUploadFilesExist after:','temp');
			$ret=$this->_uploader->uploadFile();	ee('upthis:'.getoutstr($ret),'temp');
			if($ret){
				foreach ($ret as $k=>$v){ ee('$ret[$k]:'.var_export($ret[$k],true),'temp');
					$picIdName=$k.'_id';
					if(!isset($data[$picIdName])){
						//如果文件存在，则覆盖更新
// 						if ()
						$data[$picIdName]=$this->add($ret[$k]);
					}
				}
			}else{
				$this->joinError('上传出错_uploader11');
				return false;
			}
		}else{
			//没有文件存在，上传完成
//			return true;
		}
		ee('isUploadFilesExist aftereee1111 data:'.getoutstr($data),'temp');
		$data['action_status']=Driving::$upDriving;
		$addcarId=$mdriving->addFreshDriving($data,$clientId);
//		if(!$drivingId){
//			//添加操作 切换当前车辆
//			$dm=new DrivinglicenseModel();
//			ee('new carid:'.$addcarId,'temp');
//			$dm->changeCurrentDrivingId($addcarId);
//		}
		if($addcarId){
			
			return true;
		}else{				//信息没动的话，返回的是false；//没动走不到了
			$this->error='行驶证信息更新出错'.$mdriving->getError();
			return false;
		}
		
	}
		
	public function upIdentityCard(Client $client){  //上传成功一半，更新用户信息
		$this->setUploader();
		
		$mdriving=new DrivinglicenseModel();
		$drivingId=$client->getCurrentDrivingId();
		$clientId=$client->getClientId();
		if(!$clientId){
			$this->error='用户id不正确';
			return false;
		}
		
		if($drivingId){//当前行驶证存在
			ee('upIdentityCard dirvingid:'.$drivingId,'temp');
			$fileMS=$this->_uploader->getMd5AndSha1();
			ee('upIdentityCard $fileMS:'.getoutstr($fileMS),'temp');
			$data['id']=$drivingId;
			//判断md5值，文件存在，直接得到id值，
			foreach ($fileMS as $key=>$value){
				$picIdName=$key.'_id';
				$tempFileId=$this->getIdByMS($value);//得到文件id
				
				ee('filems $picIdName:'.$picIdName,'temp');
				ee('$tempFileId'.getoutstr($tempFileId).'-----value:'.getoutstr($value),'temp');

				if($tempFileId){
					$data[$picIdName]=$tempFileId;
					$this->_uploader->unsetUpFile($key);
				}
			}
			//如果两页都已经存在，则返回
			$drivingInfo=$mdriving->getDrivingInfoById($drivingId);
			
			ee($drivingInfo['firstcard_id'].'-drivinginfo-fcard-data:'.$data['firstcard_id'].'---secondcard data:'.$data['secondcard_id'],'temp');
			
			if($drivingInfo['firstcard_id'] && $drivingInfo['secondcard_id']){
				if($drivingInfo['firstcard_id']==$data['firstcard_id'] && $drivingInfo['secondcard_id']==$data['secondcard_id']){
					$this->joinError('身份证证已存在');
					return false;
				}
			}
		}
	
		//文件不存在，则上传文件///上传文件还在，说明没有处理完
		if($this->_uploader->isUploadFilesExist()){
			$ret=$this->_uploader->uploadFile();	ee('upIdentityCard upthis:'.getoutstr($ret),'temp');
			if($ret){
				foreach ($ret as $k=>$v){ 
					$picIdName=$k.'_id';
					
					ee($picIdName.':$picIdName upIdentityCard $ret[$k]:'.var_export($ret[$k],true),'temp');
					
					if(!isset($data[$picIdName])){
						$data[$picIdName]=$this->add($ret[$k]);
					}
				}
			}else{
				$this->joinError('上传出错_uploader11');
				return false;
			}
		}else{
			//没有文件存在，上传完成
//			return true;
		}
			
	
		if($mdriving->addFreshDriving($data)){
			return true;
		}else{				//信息没动的话，返回的是false；//没动走不到了
			$this->error='身份证信息更新出错'.$mdriving->getError();
			return false;
		}
	
	}
	
	/**
	 * 得到制定id 的图片地址
	 * @param unknown $pictureId
	 * @return \Think\mixed
	 */
	public function getImgById($pictureId){
		$pictureId=(int)$pictureId;
		if(!is_numeric($pictureId)){
			die('图片id不正确');
		}
		$this->where("id={$pictureId}");
		return $this->getField('path');
	}
	public function getIdByMS($data){
		return $this->where($data)->getField('id');
	}
	public function getUpload(){
		return $this->_uploader;
	}
	
}