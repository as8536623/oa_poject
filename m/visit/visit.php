<?php
/*
// - ����˵�� : visit.php
// - �������� : ���� (weelia@126.com)
// - ����ʱ�� : 2010-07-07
*/
$mod = "visit";
require "../../core/core.php";

if (!$hid) {
	exit_html("�Բ���û��ѡ��ҽԺ������ִ�иò�����");
}

$table = "visit";

if ($op) {
	include $mod.".op.php";
}

include $mod.".index.php";
?>