<?php
/*
// ˵��: ����
// ����: ���� (weelia@126.com)
// ʱ��: 2013-8-27
*/
$table = "count_web";

$op = $_REQUEST["op"];

// ��������:
$config_name = "����ʽ_".$uid;
$sort_type = $db->query("select value from count_config where name='$config_name' limit 1", 1, "value");
$sort_type = @intval($sort_type);
if (empty($sort_type)) {
	$sort_type = 1;
}
if ($sort_type == 1) {
	$sort_by = "sort desc,id asc";
} else if ($sort_type == 2) {
	$sort_by = "zx_sort desc,id asc";
} else {
	$sort_by = "name asc,id asc";
}



// ���пɹ�����Ŀ:
if ($debug_mode || in_array($uinfo["part_id"], array(9))) {
	$types = $db->query("select id,name from hospital order by $sort_by", "id", "name");
} else {
	$hids = implode(",", $hospital_ids);
	$types = $db->query("select id,name from hospital where id in ($hids) order by $sort_by", "id", "name");
}
if (count($types) == 0) {
	exit_html("�Բ���û�п��Թ������Ŀ");
}

if ($op == "change_type") {
	$_SESSION["hospital_id"] = intval($_GET["hid"]);
}

if ($op == "change_sub") {
	$_SESSION["sub_id"] = intval($_GET["sub_id"]);
}

if (!isset($_SESSION["hospital_id"]) || empty($_SESSION["hospital_id"])) {
	$type_ids = array_keys($types);
	$_SESSION["hospital_id"] = $type_ids[0];
}

$hid = $_SESSION["hospital_id"];
$h_name = $types[$hid];



if ($_GET["date"] && strlen($_GET["date"]) == 6) {
	$date = $_GET["date"];
} else {
	if (date("j") == 1) {
		$date = date("Ym", strtotime("-1 month")); //ÿ����1�Ž���ʱ��Ĭ����Ȼ��ʾ�ϸ��µ��б� �����ֹ��л����¸��� @ 2011-11-30
	} else {
		$date = date("Ym"); //����
	}
	$_GET["date"] = $date;
}

$date_time = strtotime(substr($date,0,4)."-".substr($date,4,2)."-01 0:0:0");

// 2013-08 ��ʽ����:
$date_show = date("Y-m", $date_time);

// ���½���ʱ���:
$month_end = strtotime("+1 month", $date_time) - 1;


// ���� ��,�� ����
$y_array = $m_array = $d_array = array();
for ($i = date("Y"); $i >= (date("Y") - 2); $i--) $y_array[] = $i;
for ($i = 1; $i <= 12; $i++) $m_array[] = $i;
$days = get_month_days($date_show);
for ($i = 1; $i <= $days; $i++) $d_array[] = $i;


$type_detail = $db->query("select * from count_web_type where hid=$hid limit 1", 1);

// �ͷ�:
$kefu_list = $type_detail["kefu"] ? explode(",", trim(trim($type_detail["kefu"]), ",")) : array();
$kefu_dy_list = $type_detail["kefu_dy"] ? explode(",", trim(trim($type_detail["kefu_dy"]), ",")) : array();
$kefu_sx_list = $type_detail["kefu_sx"] ? explode(",", trim(trim($type_detail["kefu_sx"]), ",")) : array();

// �ͷ��б���ʽ
$kefu_class_arr = array();
foreach ($kefu_list as $v) {
	$kefu_class_arr[$v] = "a_kefu";
}
foreach ($kefu_dy_list as $v) {
	if (!array_key_exists($v, $kefu_class_arr)) {
		$kefu_class_arr[$v] = "a_kefu_dy";
	}
}
foreach ($kefu_sx_list as $v) {
	if (!array_key_exists($v, $kefu_class_arr)) {
		$kefu_class_arr[$v] = "a_kefu_sx";
	}
}

$kefu_list = array_keys($kefu_class_arr);



$sub_type_arr = $db->query("select * from count_web_sub_type order by sort desc, id asc", "id", "name");

// �ӷ���:
$sub_id = intval($_SESSION["sub_id"]);
if ($sub_id == 0) {
	$sub_id = $_SESSION["sub_id"] = 1; //Ĭ����ĿΪ: PC
}

if ($sub_id > 0) {
	$sub_name = $sub_type_arr[$sub_id];
}

?>