<?php
/*
// ˵��: ��ȡĳҽԺý����Դ - json
// ����: ���� (weelia@126.com)
// ʱ��: 2014-03-18
*/
include "../core/db.php";
include "../core/class.fastjson.php";
include "check_crc.php";

$hid = intval($_GET["hid"]);
if ($hid <= 0) {
	exit("����hid����...");
}

$list = $db->query("select name from media where hospital_id=0 or hospital_id=$hid order by sort desc, id asc", "", "name");
array_unshift($list, "�绰");
array_unshift($list, "����");

echo FastJSON::convert($list);

?>