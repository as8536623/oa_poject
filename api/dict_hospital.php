<?php
/*
// ˵��: ��ȡҽԺ�б� - json
// ����: ���� (weelia@126.com)
// ʱ��: 2014-3-18
*/
include "../core/db.php";
include "../core/class.fastjson.php";
include "check_crc.php";

include "log_function.php";
_wee_log("dict_hospital.php", "", "");

$list = $db->query("select id,name,full_name from hospital order by full_name asc", "id", "full_name");

echo FastJSON::convert($list);

?>