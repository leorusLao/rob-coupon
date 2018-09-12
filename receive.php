<?php

/*
**	抢优惠券 
**
*/

$redis = new Redis();
$redis->connect('127.0.0.1',6379);

$code = 'CO5976982252';

//判断库存
$store = $redis->LPOP('coupon_store:'.$code);

if(!$store){
	echo '已经抢光了';
	return;
}
	
/*抢购成功流程*/

require 'DAOPDO.class.php';
$pdo = DAOPDO::getInstance('127.0.0.1', 'trial', 'trial&2018', 'trial', 'utf8');

$where = "SELECT * FROM user  AS t1  JOIN (SELECT ROUND(RAND() * ((SELECT MAX(id) FROM `user`)-(SELECT MIN(id) FROM user))+(SELECT MIN(id) FROM user)) AS id) AS t2 WHERE t1.id >= t2.id ORDER BY t1.id LIMIT 1";
$result = $pdo->query($where);

$uid = $result[0]['uid'];

//日志
$log = 'coupon.log';

$content = '我是:'.$uid.',我读到:'.$store.PHP_EOL;
$f = file_put_contents($log, $content, FILE_APPEND);

$content = '我是:'.$uid.'我开始加入卡券'.PHP_EOL;
$f = file_put_contents($log, $content, FILE_APPEND);

//加入用户卡券包
$now = date('Y-m-d H:i:s');
$data_arr = array(
	'uid' => $uid,
	'pcode' => 'CO5976982252',
	'created_at' => $now,
	'updated_at' => $now
);
$r = $pdo->insert('user_coupon', $data_arr);

$content = '我是:'.$uid.'我开始减库存'.PHP_EOL;
$f = file_put_contents($log, $content, FILE_APPEND);

//减库存
$result = $pdo->exec("UPDATE `coupon` SET `total` = total - 1 WHERE `total`>0 AND code = '$code'");




?>