<?php
$mysqli=new mysqli('rds0x45803576iw99t31.mysql.rds.aliyuncs.com', 'yyb_product', 'ONEonebao2015', 'yyb_product','3306');
if ($mysqli->connect_error) {
	die('Connect Error (' . $mysqli->connect_errno . ') '
			. $mysqli->connect_error);
}
var_dump($mysqli);