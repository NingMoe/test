<?php
namespace Api\Controller;
use Api\wx\Method;
use Api\wx\wk;
class InterfaceController{
    public function index(){	
    	
    	$postStr=file_get_contents("php://input");
    	Method::wl("---request start----------");
    	Method::wl('request raw data:' . getoutstr($postStr));
    	Method::wl('get:' . var_export($_GET,true));
    	Method::wl('post:' . var_export($_POST,true));
    	
    	//接入配置
    	if(isset($_GET ["echostr"])){
    		if (Method::checkSignature()){
    				echo $_GET ["echostr"];
    		}else {
    			//获取来源ip，并写日志
    			$ip = Method::getip ();
    			Method::wl("checksig error,ip:".$ip);
    		}
    		exit ( 0 );
    	}
		
		if(empty($postStr)){
			Method::wl("empty postStr error input!");
			$this->exitErrorInput();
		}
    	//获取参数
    	$postObj=simplexml_load_string($postStr,'SimpleXMLElement',LIBXML_NOCDATA);
    	Method::wl('raw to obj:'.getoutstr($postObj));
    	$toUserName=(string)trim($postObj->ToUserName);
    	if(!$toUserName){
    		Method::wl("error input!toUserName is empty");
    		exitErrorInput();
    	}else{
			$myobj=new wk();
    	}
    	//初始化
    	if(!$myobj->init($postObj)){
    		Method::wl("robj empty or input error!");
    		$this->exitErrorInput();
    	}
    	$ret=$myobj->process();
    	Method::wl("response:".$ret);
    	echo $ret;
    	
       	Method::wl("**** ####interface request end suc**********" );
    }

    function exitErrorInput(){
    	Method::wl("**** exitErrorInput request end**********");
    	exit(0);//
    }
}