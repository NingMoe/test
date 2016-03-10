<?php
namespace Common\Lib;
class Tools{
	/**
	 * 过滤空或无效数据 0不虑
	 * @param unknown $arr
	 */
	public static function filterInvalidData($arr){
		if(!$arr || !is_array($arr)){
			die('filterInvalidData param error');
		}
		foreach ($arr as $k=>$item){  
			if(!$item || ''==$item ){  //0不虑
				unset($arr[$k]);
			}
		}
		return $arr;
	}
	/**
	 * 保存&获取分布的表单数据
	 * @param unknown $data
	 * @return array
	 */
	static function getFormData($data=null){
		if(is_array($data)){
			if (null==session('formdata')){
				session('formdata',json_encode($data));
			}else{
				$formdata=array_merge(json_decode(session('formdata'),true),$data);
				session('formdata',json_encode($formdata));
			}
		}
		return json_decode(session('formdata'),1);
	}
	/**
	 * 生成推广id
	 */
	static function getShareId(){
		return date('ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).rand(0, 9);
	}
	/**
	 * 得到随机的分享红包金额
	 */
	static function getRedAmount(){
		//todo 控制随机金额 概率
		$levelRange=array(array(10,20),array(21,30),array(31,50),array(51,70),array(71,100));
		$level=self::getRandLevel();
		if($level>4){
			$level=4;
		}
		return mt_rand($levelRange[$level][0], $levelRange[$level][1]);
	}
	
	static function getRandByPrecent($num,$total){
		$num=(int)$num;
		if(!$num || $num>$total){
			die('数据不正确');
		}
		if(rand(1, $total)>$num){
			return false;
		}else{
			return true;
		}
	}
	/**
	 * 得到随机数
	 * @num 几分之1，
	 * @ return levle 几层
	 */
	//1层5-10，2层10-20，3层20-30，4层30-50，5层50-90
	static function getRandLevel($num=3){
		$level=0;
		while (1==rand(1, $num)){//num之1概率进入下一层
			$level++;
		}
		return $level;
	}
	/**
	 * 转换索引数组为关联数组
	 * @arr 索引数组
	 * @keyFieldName 关联键名
	 * @valueFieldName 有值得话，key-value 没有的话每项也是数组
	 */
	static function ConvertArrToAssoc($arr,$keyFieldName,$valueFieldName=null){
		if(empty($arr) || empty($keyFieldName)){
			return false;
		}
		$result=array();
		foreach($arr as $item){
			if(null===$valueFieldName){
				$result[$item[$keyFieldName]]=$item;
			}else{
				$result[$item[$keyFieldName]]=$item[$valueFieldName];
			}
		}
		return $result;
	}
	/**
	 * 计算数组value的总和
	 * @param  $arr 一维数组
	 */
	static function sumValue($arr){
		$result=0;
		foreach ($arr as $value){
			$result+=$value;
		}
		return $result;
	}
	static function timeFormatS($time){
		return date('Y-m-d H:i:s',$time);
	}
	/**
	 * 默认格式化日期
	 * 'Y-m-d H:i',
	 */
	static function timeFormat($time){
		return date('Y-m-d H:i',$time);
	}
	/**
	 * 默认格式化日期
	 *'Y-m-d'
	 */
	static function timeFormat2($time){
		return date('Y-m-d',$time);
	}
	static function timeFormatYearLater($time){
		$y=date('Y',$time);
		$y++;
		return $y.date('-m-d H:i',$time);
	}
	static function getSqlStore($obj){
		return json_encode($obj);
	}
	/**
	 * 
	 * @param unknown $str
	 * @param number $type true 时返回数组
	 * @return mixed
	 */
	static function parseSqlStore($str,$type=0){//typeTRUE 时，将返回 array 而非 object 。
		$result=json_decode($str,$type);
		if(!$result){
			die('数据解析出错');
		}
		return $result;
	}
	/*
	 * 检查变量是否有效
	 */
	static function isValid($var){
		if(!isset($var) || empty($var) )
			return null;
		return true;
	}
	/**
	 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
	 * @param  string $str  要分割的字符串
	 * @param  string $glue 分割符
	 * @return array
	 */
	static function str2arr($str, $glue = ','){
		return explode($glue, $str);
	}
	
	/**
	 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
	 * @param  array  $arr  要连接的数组
	 * @param  string $glue 分割符
	 * @return string
	 */
	static function arr2str($arr, $glue = ','){
		return implode($glue, $arr);
	}
	
	/**
	 * 字符串截取，支持中文和其他编码
	 * @static
	 * @access public
	 * @param string $str 需要转换的字符串
	 * @param string $start 开始位置
	 * @param string $length 截取长度
	 * @param string $charset 编码格式
	 * @param string $suffix 截断显示字符
	 * @return string
	 */
	static function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
		if(function_exists("mb_substr"))
			$slice = mb_substr($str, $start, $length, $charset);
		elseif(function_exists('iconv_substr')) {
			$slice = iconv_substr($str,$start,$length,$charset);
			if(false === $slice) {
				$slice = '';
			}
		}else{
			$re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
			$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
			$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
			$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
			preg_match_all($re[$charset], $str, $match);
			$slice = join("",array_slice($match[0], $start, $length));
		}
		return $suffix ? $slice.'...' : $slice;
	}
	static function sortCmp($a,$b){
		if ($a == $b) {
			return 0;
		}
		return ($a < $b) ? -1 : 1;
	}
	
}