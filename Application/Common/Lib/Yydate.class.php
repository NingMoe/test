<?php
namespace Common\Lib;
class Yydate{
	static private $_yydate;
	private function __construct(){
		
	}
	public static function getInstance(){
		if(!self::$_yydate instanceof self){
			self::$_yydate=new self();
		}
		return self::$_yydate;
	}
	/*
	 * 得到间隔一个工作日的下个工作日晚10点,的时间
	 */
	public function getWorkDayY($start=null){
		if($start==null)
			$start=time();
		$y=date('Y',$start);
		$m=date('m',$start);
		$week=date('w',$start);
		$day=date('j',$start);
		$add=0;
		switch ($week){
			case 4:
				$add=4;
				break;
			case 5:
				$add=4;
				break;
			case 6:
				$add=3;
				break;
			default:
				$add=2;
		}
		$day=$day+$add;
		return mktime(22,0,0,$m,$day,$y);
	}
	/*
	 * @start 起始时间
	 * @interval 间隔工作日
	 * @return 间隔后的时间
	 */
	public function getWorkDay($start=null,$interval=2){
		if($start==null)
			$start=time();
		$week=date('w',$start);
		$add=0;
		switch ($week){
			case 4:
				$add=2;
				break;
			case 5:
				$add=2;
				break;
			case 6:
				break;
			case 7:
				break;
			default:
				
		}
		
		$end=$start+($interval+$add)*86400;
		$str=date('Y-m-d H:i:s',$end);
		return $str;
	}
	
	/*
	 * @yy 计算车注册月数
	 * @start end unix time 格式
	 * @返回start 到 end  间的总月数
	 */
	public function getMonths($start=null,$end=null){
		$start===null?$start=time():null;
		$end===null && $end=time();
		if($start>$end){
			return false;
		}
		$sy=date('Y',$start);
		$ey=date('Y',$end);
		$sm=date('m',$start);
		$em=date('m',$end);
		
		$months=1;
		$ny=$ey-$sy;
		$nm=$em-$sm;
// 		if(0>$nm){
// 			//小于0，$ny必大于0;
// 			$ny-=1;
// 			$nm+=12;
// 		}
	
		$months+=$nm+$ny*12;
		
		return $months-1;
	}
}

















