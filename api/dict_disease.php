<?php
/*
// ˵��: ��ȡĳҽԺ�����б� - json
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

$list = $db->query("select id,name from disease where hospital_id=$hid order by id asc", "id", "name");

echo FastJSON::convert($list);

?>