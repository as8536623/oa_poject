<?php
//echo '<meta http-equiv="Content-Type" content="text/html; charset=gb2312">';
require "../core/core.php";
$table = "ku_list";

if ($tel = $_GET["mobile"]) {
	
	if (strlen($tel) >= 7) {
		$thetime = strtotime("-1 month");
		$list = $db->query("select * from $table where mobile=$tel  limit 1", 1);
		if ($list && count($list) > 0) {
			echo $tel.' 已存在！';
		}else{
			echo '';
			}
	}
}

if ($name =  $_POST["name"]) {
		
		$name1 = iconv('utf-8','gb2312',$name);
		$thetime = strtotime("-1 month");
		$list3 = $db->query("select * from $table where name='$name1'  limit 1", 1);
		if ($list3 && count($list3) > 0) {
			echo $name1.' 已存在！';
		}else{
			echo '';
			}
}

if ($qq = trim($_GET["qq"])) {
	if (strlen($qq) >= 5) {
		$thetime = strtotime("-1 month");
		$list1 = $db->query("select * from $table where qq=$qq  limit 1", 1);
		if ($list1 && count($list1) > 0) {
			echo $qq.' 已存在！';
		}else{
			echo '';
			}
	}
}

if ($weixin = trim(strval($_GET["weixin"]))) {
	if (strlen($weixin) >= 3) {
		$thetime = strtotime("-1 month");
		$list2 = $db->query("select * from $table where weixin='$weixin'  limit 1", 1);
		if ($list2 && count($list2) > 0) {
			echo $weixin.' 已存在！';
		}else{
			echo '';
			}
	}
}
?>