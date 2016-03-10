<?php
namespace Common\Config;
class Interaction{
	//交互状态
	static $invalidId=-5;//id无效
	static $emptyData=-6;//数据空或
	static $errorData=-7;//数据有误
	
	static $dbAddError=-8;//数据添加失败
	static $dbUpdatError=-9;//数据更新失败
	static $dbSuccess=1;//数据操作成功
}