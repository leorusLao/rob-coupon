<?php

require 'DAOPDO.class.php';
$pdo = DAOPDO::getInstance('127.0.0.1', 'trial', 'trial&2018', 'trial', 'utf8');

$code = 'CO5976982252';

$where = "SELECT * FROM `coupon` WHERE `code`='$code' ";

$result = $pdo->query($where);

$stock = $result[0]['total'];


$where = "SELECT * FROM user  AS t1  JOIN (SELECT ROUND(RAND() * ((SELECT MAX(id) FROM `user`)-(SELECT MIN(id) FROM user))+(SELECT MIN(id) FROM user)) AS id) AS t2 WHERE t1.id >= t2.id ORDER BY t1.id LIMIT 1";
$result = $pdo->query($where);

$uid = $result[0]['uid'];


if($stock>0){

	//日志
	$log = 'coupon.log';

	$content = '我是:'.$uid.',我读到:'.$stock.PHP_EOL;
	$f = file_put_contents($log, $content, FILE_APPEND);

	$where = "SELECT `id` FROM `user_coupon` WHERE `pcode`='$code' and `uid`='$uid'  ";

	$exist = $pdo->query($where);

	if(true){

		$pdo->beginTransaction();

		try{


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

			if(!$result){
				$content = '我是:'.$uid.'库存不足'.PHP_EOL;
				$f = file_put_contents($log, $content, FILE_APPEND);
				throw new Exception('库存不足');
			}


		}catch(Exception $e){
			$pdo->rollback();
		}


		$pdo->commit();



	}

}

echo $stock;

$pdo->destruct();



?>