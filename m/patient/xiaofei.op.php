<?php
/*
// ˵��: op
// ����: ���� (weelia@126.com)
// ʱ��: 2010-06-21 19:24
*/

if ($id > 0) {
	$line = $db->query("select * from $table where id=$id limit 1", 1);
	$crc = intval($_REQUEST["crc"]);
	if ($line["addtime"] != $crc) {
		exit_html("��ȫУ��crc��ͨ��");
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