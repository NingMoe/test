<?php
return array(
	    /* 数据库配置 */
    'DB_TYPE'   => 'mysql', // 数据库类型
//     'DB_HOST'   => '121.42.59.64', // 服务器地址
    'DB_HOST'   => '120.27.139.117', // 服务器地址
    'DB_NAME'   => 'yy', // 数据库名

     'DB_USER'   => 'nn', // 用户名
    //'DB_USER'   => 'root', // 用户名
     'DB_PWD'    => 'fire10ncnull06+',  // 密码
   // 'DB_PWD'    => '',  // 密码

    'DB_USER'   => 'nn', // 用户名
//     'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => 'fire10ncnull06+',  // 密码
//     'DB_PWD'    => '',  // 密码

    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'yy_', // 数据库表前缀
    
	'SHOW_PAGE_Trace' =>true,
	'DB_DEBUG'=>true,
	
	/* SESSION 和 COOKIE 配置 */
	'SESSION_PREFIX' => 'yy_ss_', //session前缀
	'COOKIE_PREFIX'  => 'yy_ss_', // Cookie前缀 避免冲突
	
	'BAO_TYPE_NAME' => array (
			'cc'=>'车船险使用税',
			'jq' => '交强险',
			'dsfzr' => '第三者责任险',
			'clss' => '车辆损失险',
			'qcdq' => '全车盗抢险',
			'sjzwzr' => '司机乘坐责任险',
			'ckzwzr' => '乘客座位责任险',
			'bl' => '玻璃单独破碎险',
			'ss' => '涉水险',
			'hh' => '车身划痕险',
			'zr' => '自然损失险' 
	)
);