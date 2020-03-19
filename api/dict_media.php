<?php
/*
// 说明: 获取某医院媒体来源 - json
// 作者: 幽兰 (weelia@126.com)
// 时间: 2014-03-18
*/
include "../core/db.php";
include "../core/class.fastjson.php";
include "check_crc.php";

$hid = intval($_GET["hid"]);
if ($hid <= 0) {
	exit("参数hid错误...");
}

$list = $db->query("select name from media where hospital_id=0 or hospital_id=$hid order by sort desc, id asc", "", "name");
array_unshift($list, "电话");
array_unshift($list, "网络");

echo FastJSON::convert($list);

?>