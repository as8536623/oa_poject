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

if ($op == "add") {
	include "patient.add.php";
	exit;
}

if ($op == "edit") {
	include "patient.edit.php";
	exit;
}

if ($op == "view") {
	include "patient.view.php";
	exit;
}

if ($op == "search") {
	include "patient.search.php";
	exit;
}

if ($_GET["op"] == "new_search") {
	$_SESSION["search_condition"] = '';
	header("location: ?op=search");
	exit;
}

if ($op == "set_come") {
	include "patient.set_come.php";
	exit;
}

if ($op == "set_huifang_kf") {
	include "patient.set_huifang_kf.php";
	exit;
}

if ($op == "huifang") {
	include "patient.huifang.php";
	exit;
}

if ($op == "set_xiaofei") {
	include "patient.set_xiaofei.php";
	exit;
}

if ($op == "tongyuansou") {
	include "patient.tongyuansou.php";
	exit;
}

if ($op == "delete") {
	$del_ok = 0;
	if ($id > 0) {
		if ($db->query("delete from $table where id='$id' limit 1")) {
			$del_ok = 1;
			$op_data = $line;
			$del_name = $line["name"];
		}
	}

	if ($del_ok) {
		$log->add("删除", "删除预约病人: ".$del_name, $op_data, $table);
	}

	if ($del_ok) {
		msg_box("成功删除“".$del_name."”", "back", 1);
	} else {
		msg_box("删除失败。", "back", 1);
	}
}


?>