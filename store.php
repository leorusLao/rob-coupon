<?php

/*
** 库存入栈
*/

require 'DAOPDO.class.php';
$pdo = DAOPDO::getInstance('127.0.0.1', 'trial', 'trial&2018', 'trial', 'utf8');

$code = 'CO5976982252';
$where = "SELECT * FROM `coupon` WHERE `code`='$code' ";
$result = $pdo->query($where);
$stock = $result[0]['total'];

if($stock>0){
	$redis = new Redis();
	$redis->connect('127.0.0.1',6379);

	for($i=0;$i<$stock;$i++)
	   $redis->LPUSH('coupon_store:'.$code,1);
}


?>