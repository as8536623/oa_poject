<?php
/*
// ˵��: ����session ���ú��ֻ���
// ����: ���� (weelia@126.com)
// ʱ��: 2012-12-13
*/

define("gSessionUseMemCache", 0);

$path = str_replace("\\", "/", dirname(__FILE__))."/";

if (!defined("gUseMemCache")) {
	include_once $path."function.mem_cache.php";
}

if (gUseMemCache && gSessionUseMemCache) {
	include_once $path."session_mem.php"; //����memcache�洢
} else {
	include_once $path."db.php";
	include_once $path."session_db.php"; //�������ݿ�洢�����ܻ���
}

?>