<?php
/*
// - ����˵�� : income.php
// - �������� : ���� (weelia@126.com)
// - ����ʱ�� : 2010-07-07
*/
$mod = $table = "income";
require "../../core/core.php";

if (!$hid) {
	exit_html("�Բ���û��ѡ��ҽԺ������ִ�иò�����");
}

if (!$hconfig["�����շ���Ŀ"]) {
	exit_html("��������ҽԺ�շ���Ŀ����ҽԺ�����У�");
}

if ($op) {
	include $mod.".op.php";
}

include $mod.".index.php";
?>