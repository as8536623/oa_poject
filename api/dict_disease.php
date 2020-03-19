<?php
/*
// 说明: 获取某医院疾病列表 - json
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

$list = $db->query("select id,name from disease where hospital_id=$hid order by id asc", "id", "name");

echo FastJSON::convert($list);

?>