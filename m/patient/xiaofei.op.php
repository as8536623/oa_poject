<?php
/*
// 说明: op
// 作者: 幽兰 (weelia@126.com)
// 时间: 2010-06-21 19:24
*/

if ($id > 0) {
	$line = $db->query("select * from $table where id=$id limit 1", 1);
	$crc = intval($_REQUEST["crc"]);
	if ($line["addtime"] != $crc) {
		exit_html("安全校验crc不通过");
	}
}

if ($op == "view") {
	include "xiaofei.view.php";
	exit;
}

if ($op == "huifang") {
	include "xiaofei.huifang.php";
	exit;
}

if ($op == "set_xiaofei") {
	include "xiaofei.set_xiaofei.php";
	exit;
}



?>