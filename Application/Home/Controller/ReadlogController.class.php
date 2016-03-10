<?php
namespace Home\Controller;
use Think\Controller;
use Api\wx\Method;
class ReadlogController extends Controller{
	private $_file;
	private $_list;
	protected function _initialize(){
		$destinationName='nl-'.date('M_d');
		$destination=dirname(C('DATA_CACHE_PATH')).'/Logs/tpl/'.$destinationName.".log";
		
		$name='nl-webHook';
		$name2='nl-temp';
		$name4='nl-newtemp';
		$this->_list=array(0=>C('LOG_PATH').'../tpl/'.$name.'.log',
			1=>Method::getLogPath().Method::getLogName(),
			2=>C('LOG_PATH').'../tpl/'.$name2.'.log',
			3=>$destination,
			4=>C('LOG_PATH').'../tpl/'.$name4.'.log'
		);
		
		$this->_file=$this->_list[1];
		
// 		echo $this->_file;
	}
	function index(){
		$emptyurl=U(CONTROLLER_NAME.'/'."emptylog");
		$this->assign('content',$this->getRawData());
		$this->assign('list',$this->_list);
		$this->display();
	}
	function emptylog(){
		echo 'emptylog in.';
		if(trim($_GET['todo'])=='全部清空'){
			foreach ($this->_list as $item){
				$rs=file_put_contents($item,"");  //可以
			}
			echo "清空完成".$rs;
		}
	}
	function selFile(){
		$fileid=$_GET['file'];
		$this->_file=$this->_list[$fileid];
		$rawdata=file_get_contents($this->_file);
		echo nl2br($rawdata);
	}
	function getRawData(){
		// 		$handle=fopen($file, 'r+');
		// 		$content=fread($handle, filesize($file));
		// 		fclose($handle);
		$rawdata=file_get_contents($this->_file);
		$content=nl2br(htmlspecialchars($rawdata));
		return $content;
	}
}