<?php
use Common\Model\PictureModel;
use Common\Model\DrivinglicenseModel;
use Common\Model\PackageModel;
use Common\Model\CompanyModel;
use Common\Model\SortModel;
use Common\Lib\Tools;
function ftime($time){
	return date('y-m-d H:i',$time);
}
/**
 * @param $id
 * @return mixed
 */
function getPromotionName($id){
	$mp=new \Admin\Model\PromotionModel();
	$info=$mp->getInfo($id);
	return $info['name'];
}


/**
 * 
 * 得到投保套餐类型字符串
 */
function getInsureStyleText($insureStyleId){
	$text=array(1=>'自定义套餐',2=>'按去年投保',3=>'帮我选择');
	return $text[$insureStyleId];
}


//输出测试
function ct($var,$tip=""){
	echo "<pre>";
	if($tip!="")
		echo $tip.":<br>";
	var_dump($var);
	echo "</pre>";
}

/**
 * 得到公司排序数据 ,通过现有的公司筛选
 */
function getSortData($sortId){
	$ms=new SortModel();
	$content=$ms->getSortContentById($sortId);
	$content=Tools::parseSqlStore($content,1);
	$comlist=getCompanyList();
// 	$comlist=Tools::ConvertArrToAssoc($comlist, $keyFieldName)
	$result=array();//ct($content,'con1:');ct($comlist,'con2:');
	foreach ($content as $k=>$com){
		$id=$com['id'];
		if($comlist[$id]){  //不需要根据现有公司，去掉这个
			$result[$k]=$com;
			$result[$k]['name']=$comlist[$id]['name'];
		}
	}
	//ct($result,'con:');
// 	ee('sortinfo result:'.getoutstr($result));
	return $result;
}
function convertIdKey($data){
	if(!is_array($data)){
		die('convertIdKey data not array');
	}
	$result=array();
	foreach($data as $key=>$value){
		$result[$value['id']]=$value;
	}
	return $result;
}
/**
 * 
 */
function getSortInfo($sortId){
	$ms=new SortModel();
	$content=$ms->getSortContentById($sortId);
	$content=tools::parseSqlStore($content,1);
	$comlist=$this->getCompanyList();
	$comlist=Tools::ConvertArrToAssoc($comlist,'id');
	$result=array();
	foreach ($content as $k=>$com){
		$id=$com['id'];
		if($comlist[$id]){
			$result[$k]=$com;
			$result[$k]['name']=$comlist[$id]['name'];
		}

	}
	ee('sortinfo result:'.getoutstr($result));
	return $result;
}
/**
 * 得到通用保险公司数据
 * @return unknown
 */
function getCompanyList(){
	$mCompany=new CompanyModel();
	$ret=$mCompany->getList();
	if(!$ret){
		$this->ko('保险公司信息出错');
	}
	foreach ($ret as $value){
		$result[$value['id']]=$value;
	}
	return $result;
}
/*
 * trace
 */
function getDebugTrace(){
	$st=debug_backtrace();
	return var_export($st,true);
}
/*
 * 缺德文件的写入次数
 * @param $name文件名
 */
function getIndex($name=""){
	if(""===$name){
		return false;
	}
	static $nn=array();
	if(isset($nn[$name])){
		$nn[$name]++;
	}else{
		$nn[$name]=0;
	}
	return $nn[$name];
}
/*
 * 检查log是否在期限内，只要最新一次写的。
 * 过期返回false
 * 没过期返回true
 */
function isLogInExpire(){
	$expire=20;//过期时间 秒
	if(null===session('logtime') && null===cookie('logtime')){
		session('logtime',time());
		cookie('logtime',time());
	}else{
		$oldtime=(null!=session('logtime'))?session('logtime'):cookie('logtime');
		if((time()-$oldtime)<$expire){
			return true;
		}
	}
	return false;
}
/*
 * 检查log是否超大小
 * 超过返回true
 * 没过返回false
 */
function isLogOutSize($destination){
	$log_max_size=37152;
	if(is_file($destination) && floor($log_max_size)<filesize($destination)){
		return true;
	}
	return false;
}
/*
 * 日志记录
 * @param $log 日志内容
 * @param $destinationName 日志文件名
 * @clear 为true时写入时清空源文件
 */
function ee($log,$destinationName=''){
	''==$destinationName && $destinationName=date('M_d');
	$destinationName='nl-'.$destinationName;
	$destination=dirname(C('DATA_CACHE_PATH')).'/Logs/tpl/'.$destinationName.".log"; 
	if(!is_dir(dirname($destination))){
		mkdir(dirname($destination));
	}
	
	$prelog="";
	$n=getIndex($destinationName);
	//开始写时检查是否清空文件
	if($n==0){
		if(!isLogInExpire() || isLogOutSize($destination)){
			file_put_contents($destination,"");
		}
		$prelog="\r\n\r\n*********** new start************\r\n";
	}
	
	$prelog.=date('m-d.H:i:s')." [n:{$n} [uri:".$_SERVER['REQUEST_URI']." [pos:".getPos()."\r\n";
	session("logtime",time());
	cookie('logtime',time());
	error_log($prelog."{$log}\r\n",3,$destination);
}
/*
 * 日志只记录一次
 * one log 
 */
function ol($log,$destinationName=''){
	ee($log,$destinationName);
}
//取得位置
function getPos(){
	return MODULE_NAME."/".CONTROLLER_NAME."/".ACTION_NAME;
}
function getImg($id){
	if(!$id){
		return false;
	}
	$m=new PictureModel();
	return '/'.$m->getImgById($id);
}
function getoutstr($ret){
	return var_export($ret,1);
}
/**
 * 刷新车辆的 当前套餐包时间
 * @param unknown $drivingId
 */
function freshPackageTime($drivingId){
	$md=new DrivinglicenseModel();
	$carInfo=$md->getDrivingInfoById($drivingId);
	$packageId=$carInfo['package_id'];
	$mp=new PackageModel();
	return $mp->freshTime($packageId);
}













