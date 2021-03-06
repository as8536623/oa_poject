<?php
/*
// 说明: 配置
// 作者: 幽兰 (weelia@126.com)
// 时间: 2013-8-27
*/
$table = "count_dh";

$sub_type_arr = array(
	1 => "网络电话",
	2 => "市场电话",
	3 => "电视电话",
	4 => "其它",
);

$op = $_REQUEST["op"];

// 排序设置:
$config_name = "排序方式_".$uid;
$sort_type = $db->query("select value from count_config where name='$config_name' limit 1", 1, "value");
$sort_type = @intval($sort_type);
if (empty($sort_type)) {
	$sort_type = 1;
}
if ($sort_type == 1) {
	$sort_by = "sort desc,id asc";
} else {
	$sort_by = "name asc,id asc";
}



// 所有可管理项目:
if ($debug_mode || in_array($uinfo["part_id"], array(9))) {
	$types = $db->query("select id,name from hospital order by $sort_by", "id", "name");
} else {
	$hids = implode(",", $hospital_ids);
	$types = $db->query("select id,name from hospital where id in ($hids) order by $sort_by", "id", "name");
}
if (count($types) == 0) {
	exit_html("对不起，没有可以管理的项目");
}

if ($op == "change_type") {
	$_SESSION["hospital_id"] = intval($_GET["hid"]);
}

if ($op == "change_sub") {
	$_SESSION["dh_sub_id"] = intval($_GET["sub_id"]);
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
		$date = date("Ym", strtotime("-1 month")); //每个月1号进入时，默认仍然显示上个月的列表 可以手工切换到下个月 @ 2011-11-30
	} else {
		$date = date("Ym"); //本月
	}
	$_GET["date"] = $date;
}

$date_time = strtotime(substr($date,0,4)."-".substr($date,4,2)."-01 0:0:0");

// 2013-08 格式日期:
$date_show = date("Y-m", $date_time);

// 该月结束时间戳:
$month_end = strtotime("+1 month", $date_time) - 1;


// 可用 年,月 数组
$y_array = $m_array = $d_array = array();
for ($i = date("Y"); $i >= (date("Y") - 2); $i--) $y_array[] = $i;
for ($i = 1; $i <= 12; $i++) $m_array[] = $i;
$days = get_month_days($date_show);
for ($i = 1; $i <= $days; $i++) $d_array[] = $i;


$type_detail = $db->query("select * from count_dh_type where hid=$hid limit 1", 1);

// 客服:
$kefu_list = $type_detail["kefu"] ? explode(",", trim(trim($type_detail["kefu"]), ",")) : array();



// 子分类:
$sub_id = intval($_SESSION["dh_sub_id"]);
//if ($sub_id == 0) {
//	$sub_id = $_SESSION["dh_sub_id"] = 1; //默认项目
//}

if ($sub_id > 0) {
	$sub_name = $sub_type_arr[$sub_id];
} else {
	$sub_name = "汇总";
}

?>