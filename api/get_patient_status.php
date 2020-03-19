<?php
/*
// 说明: 获取单个患者的到院情况
// 作者: 幽兰 (weelia@126.com)
// 时间: 2014-3-18
*/
include "../core/db.php";
include "../core/class.fastjson.php";
include "check_crc.php";


$hid = intval($_GET["hid"]);
$pid = intval($_GET["pid"]);

include "log_function.php";
_wee_log("get_patient_status.php", "", "hid=".$hid."&pid=".$pid);

if ($hid <= 0) {
	exit("-1:参数hid错误");
}
$hinfo = $db->query("select id,name from hospital where id=$hid limit 1", 1);
if (!is_array($hinfo) || $hinfo["id"] != $hid) {
	exit("-1:医院ID=[".$hid."]不存在");
}
$hname = $hinfo["name"];

$table = "patient_".$hid;

$pinfo = $db->query("select * from $table where id=$pid limit 1", "1");

if ($pinfo["id"] > 0) {
	echo ($pinfo["status"] == 1 ? "1" : "0").":".$pinfo["order_date"];
} else {
	echo "-1:患者ID=[".$pid."]不存在";
}

?>