<?php
/*
// - ����˵�� : �����б�
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2009-05-01 08:09
*/
require "../../core/core.php";
include_once ROOT."/core/patient_field_name.php";
$table = "patient_".$hid;

if ($hid == 0) {
	exit_html("�Բ���û��ѡ��ҽԺ������ִ�иò�����");
}

// ��ɫ���� 2010-07-31
$line_color = array('black', 'red', 'silver', '#F90','##FFFF80','#966', '#8000FF');
$line_color_tip = array("�ȴ�", "�ѵ�","δ��", "����","��Ч","����", "�ط�");
$area_id_name = array(0 => "δ֪", 1 => "����", 2 => "���");

// �Ƿ�����ʾ�����Ȩ�� @ 2012-07-17
$show_tel = 0;
if ($uinfo["show_tel"]) {
	$show_tel = 1;
}
if ($debug_mode) {
	$show_tel = 1;
}

// �����Ĵ���:
if ($op = $_GET["op"]) {
	include "patient.op.php";
}

include "patient.list.php";


function _show_disease($ids, $split = '|') {
	global $disease_id_name;
	if (!isset($disease_id_name)) {
		global $db, $hid;
		$disease_id_name = $db->query("select id,name from disease where hospital_id='$hid'", 'id', 'name');
	}
	if (substr_count($ids, ",") > 0) {
		$id_arr = explode(",", $ids);
		$res = array();
		foreach ($id_arr as $v) {
			$res[] = array_key_exists($v, $disease_id_name) ? $disease_id_name[$v] : $v;
		}
		return implode($split, $res);
	} else {
		return array_key_exists($ids, $disease_id_name) ? $disease_id_name[$ids] : $ids;
	}
}



?>