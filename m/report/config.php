<?php
/*
// 作者: 幽兰 (weelia@126.com)
*/


function wee_merge($arr1, $arr2) {
	foreach ($arr2 as $k => $v) {
		$arr1[$k] = $v;
	}
	return $arr1;
}

function _my_show($arr, $default_value='', $link='', $click='') {
	$s = '';
	foreach ($arr as $v) {
		if ($v == $default_value) {
			$s .= '<b>'.$v.'</b>';
		} else {
			$s .= '<a href="'.$link.'" onclick="'.$click.'">'.$v.'</a>';
		}
	}
	return $s;
}


function make_edit($hid, $month, $fname, $default_value) {
	global $sub_id, $can_edit;
	$id = $hid."_".str_replace("-", "", $month)."_".$fname;

	$edit = 0;
	if ($fname == "fuzeren") {
		$edit = 1;
	}

	if ($sub_id == 0 && in_array($fname, explode(" ", "dabiaozhishu1 dabiaozhishu2 dabiaozhishu3 jianglijishu1 jianglijishu2 jianglijishu3 jianglizhibiao1 jianglizhibiao2 jianglizhibiao3 mubiao1 mubiao2 mubiao3"))) {
		$edit = 1;
	}

	if (in_array($fname, array("h_jiuzhen", "h_wangcha", "h_renjun"))) {
		$edit = 1;
	}

	if ($edit && $can_edit) {
		$show_value = $default_value == "" ? "<font color=#7ba4ee>添加</font>" : $default_value;
		return '<a id="'.$id.'" href="#" onclick="medit(\''.$hid.'\', \''.$month.'\', \''.$fname.'\', \''.$default_value.'\'); return false;">'.$show_value.'</a>';
	} else {
		return $default_value;
	}
}


function num($num, $zeor_fill='') {
	if (substr_count($num, "%") > 0) return $num;
	//if ($num != '') $num = round($num);
	if ($num >= 10000) {
		$num = $num / 10000;
		if (substr_count($num, ".") > 0) {
			$num = round($num, 1);
		}
		$num .= "万";
	}
	if ($zeor_fill != '') {
		if ($num == "0" || $num == "") {
			$num = $zeor_fill;
		}
	}
	return $num;
}


function _safe_word($s) {
	$s = strip_tags($s);
	$s = str_replace("\\", "", $s);
	$s = str_replace("/", "", $s);
	$s = str_replace("%", "", $s);
	$s = str_replace("*", "", $s);
	$s = str_replace("#", "", $s);
	$s = str_replace("&", "", $s);
	$s = str_replace("|", "", $s);
	$s = str_replace("'", "", $s);
	$s = str_replace('"', "", $s);
	$s = str_replace("_", "", $s);
	$s = str_replace(";", "", $s);
	$s = str_replace("$", "", $s);
	$s = str_replace("=", "", $s);
	$s = str_replace("union", "", $s);
	$s = str_replace("like", "", $s);
	$s = str_replace("and", "", $s);
	$s = str_replace("or", "", $s);
	$s = str_replace("<", "", $s);
	$s = str_replace(">", "", $s);
	$s = str_replace("{", "", $s);
	$s = str_replace("}", "", $s);
	$s = str_replace("(", "", $s);
	$s = str_replace(")", "", $s);
	$s = str_replace(",", " ", $s);

	return $s;
}


$can_view = 0;
if ($debug_mode || $uinfo["jiuzhen_view"]) {
	$can_view = 1;
}
if ($can_view == 0) {
	exit_html("对不起，你没有查看权限...");
}

$can_edit = 0;
if ($debug_mode || $uinfo["jiuzhen_edit"]) {
	$can_edit = 1;
}


if ($op == "set_area") {
	$_SESSION["jiuzhen"]["area"] = $_GET["area"];
}

if ($op == "set_depart") {
	$_SESSION["jiuzhen"]["depart"] = $_GET["depart"];
}

$limit_area = $_SESSION["jiuzhen"]["area"];
$limit_depart = $_SESSION["jiuzhen"]["depart"];
if ($limit_area == "" && $limit_depart == "") {
	if (count($hospital_ids) > 10) {
		$limit_depart = $_SESSION["jiuzhen"]["depart"] = "妇科";
	}
}



// 汇总方法：
$sum_type_arr = array(
	"0" => "汇总",
	"1" => "PC",
	"2" => "手机",
	"3" => "微信",
);

if ($op == "sub_change") {
	$_SESSION["jiuzhen"]["sub_id"] = intval($_GET["sub_id"]);
}

$sub_id = intval($_SESSION["jiuzhen"]["sub_id"]);
$sub_name = $sum_type_arr[$sub_id];



// 处理年月:
if ($op == "set_year") {
	$_SESSION["jiuzhen"]["year"] = $_GET["year"];
}
if ($op == "set_month") {
	$_SESSION["jiuzhen"]["month"] = $_GET["month"];
}
$year = $_SESSION["jiuzhen"]["year"];
if ($year == "") {
	$year = $_SESSION["jiuzhen"]["year"] = date("Y"); //默认为今年
}
$month = $_SESSION["jiuzhen"]["month"];
if ($month == "") {
	$month = $_SESSION["jiuzhen"]["month"] = date("n"); //默认为本月
}

$date_time = strtotime($year."-".$month."-01 0:00:00");

if ($op == "date_navi") {
	if ($_GET["go"] == "last") {
		$date_time = strtotime("-1 month", $date_time);
	} else if ($_GET["go"] == "next") {
		$date_time = strtotime("+1 month", $date_time);
	} else {
		$date_time = strtotime(date("Y-m"));
	}
	$year = $_SESSION["jiuzhen"]["year"] = date("Y", $date_time);
	$month = $_SESSION["jiuzhen"]["month"] = date("n", $date_time);
}


$date_end = strtotime("+1 month", $date_time) - 1;

$date_y_m = date("Y-m", $date_time);
$date_ym = date("Ym", $date_time);
$month_end_ym = date("Ym", $date_end);

// Ymd 格式的开始和结束:
$date_ymd_begin = date("Ymd", $date_time);
$date_ymd_end = date("Ymd", $date_end);

// 已过天数 总天数
$month_is_cur = 0;
if ($date_ym == date("Ym")) {
	$month_is_cur = 1;
	$month_cur_days = date("j");
	$month_all_days = get_month_days($date_y_m);
	$date_end = strtotime(date("Y-m-d")." 0:00:00") - 1;
} else {
	$month_cur_days = $month_all_days = get_month_days($date_y_m);
}

// 上月起始和结束日期:
$lastm_ymd_begin = date("Ymd", strtotime("-1 month", $date_time));
$lastm_ymd_end = date("Ymd", ($date_time - 1));
$lastm_days = date("j", ($date_time - 1));


// 可用 年,月 数组
$y_array = $m_array = $d_array = array();
for ($i = date("Y"); $i >= (date("Y") - 2); $i--) $y_array[] = $i;
for ($i = 1; $i <= 12; $i++) $m_array[] = $i;
for ($i = 1; $i <= 31; $i++) {
	if ($i <= 28 || checkdate(date("n", $date_time), $i, date("Y", $date_time))) {
		$d_array[] = $i;
	}
}




// 读取所有可以管理的医院并统计报表:
if (count($hospital_ids) > 0) {
	$hids = implode(",", $hospital_ids);
} else {
	exit_html("对不起，你没有医院权限，无法查看任何报表...");
}

$skiped_hid = $db->query("select * from jiuzhen_skip_hid order by month asc", "month", "skip_hids");
$skiped_hid_arr = array();
if (is_array($skiped_hid) && count($skiped_hid) > 0) {
	foreach ($skiped_hid as $k => $v) {
		$skiped_hid_arr[$k] = explode(",", trim($v));
	}
}


// op=skip_hid  忽略医院处理
if ($op == "skip_hid") {
	$_m = intval($_GET["month"]);
	$_hid = intval($_GET["hid"]);
	if ($_m > 0 && $_hid > 0) {
		if (array_key_exists($_m, $skiped_hid_arr)) {
			$new_skiped_hid_arr = $skiped_hid_arr[$_m];
		} else {
			$new_skiped_hid_arr = array();
		}
		if (!in_array($_hid, $new_skiped_hid_arr)) {
			$new_skiped_hid_arr[] = $_hid;
		}
		$s = trim(trim(implode(",", $new_skiped_hid_arr)), ",");
		if (array_key_exists($_m, $skiped_hid)) {
			$db->query("update jiuzhen_skip_hid set skip_hids='{$s}' where month={$_m} limit 1");
		} else {
			$db->query("insert into jiuzhen_skip_hid set month={$_m}, skip_hids='{$s}'");
		}
	}
	echo '<script> history.go(-1); </script>';
	exit;
}


if ($op == "remove_skip") {
	$_m = intval($_GET["m"]);
	$_hid = intval($_GET["hid"]);
	if ($_m > 0 && $_hid > 0) {
		$line = $db->query("select * from jiuzhen_skip_hid where month='$_m' limit 1", 1);
		if ($line) {
			$arr = explode(",", $line["skip_hids"]);
			foreach ($arr as $k => $v) {
				if ($v == $_hid) {
					unset($arr[$k]);
				}
			}
			$s = implode(",", $arr);
			$db->query("update jiuzhen_skip_hid set skip_hids='$s' where month='$_m' limit 1");
		}
	}
	echo '<script> history.go(-1); </script>';
	exit;
}


//echo date("Y-m-d H:i:s", $date_end);

?>