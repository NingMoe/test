<?php
namespace Common\Object;
class Level{
	static public $dsfzr=array(
			1=>5,2=>10,3=>15,4=>20,5=>30,6=>50,7=>100,8=>150,9=>200
	);
	static public $sjzwzr=array(
			1=>1,2=>2,3=>3,4=>5,5=>10,6=>20
	);
	static public $ckzwzr=array(
			1=>1,2=>2,3=>3,4=>5,5=>10,6=>20
	);
	static public $hh=array(
			1=>2000,2=>5000,3=>10000,4=>20000
	);
// 	static public $clss=array(        //旧的
// 			0=>array(0=>0,1=>2,2=>4,6=>6),//上0,1,2,6年
// 			1=>array(0=>0,2=>2,3=>4,4=>6)
// 	);
	static public $clss=array(
			0=>0,1=>2,4=>4,6=>6,8=>8              ////上0,1,2,6年
	);
	static public function get($name,$level_package){
		switch ($name) {
			case 'dsfzr' :
				return self::$dsfzr [$level_package];
			case 'sjzwzr' :
				return self::$sjzwzr [$level_package];
			case 'ckzwzr' :
				return self::$ckzwzr [$level_package];
			case 'hh' :
				return self::$hh [$level_package];
			default:
				return '';
		}
	}
}