<?php

// 数据定义

$back_url = base64_encode($_SERVER["PHP_SELF"]);

$y_array = $m_array = $d_array = array();
for ($i = date("Y"); $i >= (date("Y") - 2); $i--) $y_array[] = $i;
for ($i = 1; $i <= 12; $i++) $m_array[] = $i;
for ($i = 1; $i <= 31; $i++) $d_array[] = $i;

$date = $_GET["date"] ? $_GET["date"] : date("Ymd");
$date_time = strtotime(substr($date,0,4)."-".substr($date,4,2)."-".substr($date,6,2)." 0:0:0");

$patient_all = $yingyee_all = 0;


/*
  ---------------------   门诊   ------------------------
*/

// 定义单元格格式:
$list_heads = array(
	"医生" => array("width"=>"", "align"=>"center"),
	"初诊人数" => array("width"=>"", "align"=>"center"),
	"复诊人数" => array("width"=>"", "align"=>"center"),
	"流失人数" => array("width"=>"", "align"=>"center"),
);

$shoufei_array = explode("|", $hconfig["门诊收费项目"]);
foreach ($shoufei_array as $k) {
	if ($k) {
		$list_heads[$k] = array("width"=>"", "align"=>"center");
	}
}

$list_heads["营业额"] = array("width"=>"", "align"=>"center");
$list_heads["人均消费"] = array("width"=>"", "align"=>"center");
$list_heads["录入"] = array("width"=>"", "align"=>"center");
$list_heads["操作"] = array("width"=>"80", "align"=>"center");


// 列表显示类:
$t = load_class("table");
$t->set_head($list_heads, '', '');
$t->table_class = "new_list";

$list = $db->query("select * from $table where hid=$hid and fee_type=0 and date=$date order by doctor_id", "id");
if (!is_array($list)) {
	exit_html("Error: ".$db->sql);
}

$sum_list = array();
$logs1 = $logs2 = array();

foreach ($list as $id => $li) {
	$r = array();
	$r["医生"] = $li["doctor_name"];
	$r["初诊人数"] = $li["chuzhen"];
	$r["复诊人数"] = $li["fuzhen"];
	$r["流失人数"] = $li["liushi"];

	$tmp = unserialize($li["detail"]);
	$r = array_merge($r, $tmp);

	$r["营业额"] = $li["yingyee"];
	$r["人均消费"] = $li["renjun"];
	$r["录入"] = $li["u_realname"];
	if ($li["log"] != '') {
		//$r["添加人"] .= ' <a href="#" title="'.rtrim(str_replace("<br>", "\r\n", $li["log"])).'">☆</a>';
		$logs1[] = '<div class="m20">医生: <b>'.$r["医生"].'</b></div><div class="m40">'.$li["log"]."</div>";
	}

	$op = array();
	if (check_power("edit")) {
		$op[] = '<a href="?op=edit&id='.$li["id"]."&fee_type=".$li["fee_type"].'">修改</a>';
	}
	if (check_power("delete")) {
		$op[] = '<a href="?op=delete&id='.$li["id"].'" onclick="return confirm_delete()">删除</a>';
	}
	$r["操作"] = implode(" ", $op);

	foreach ($r as $k => $v) {
		if (is_numeric($v)) {
			$sum_list[$k] = floatval($sum_list[$k]) + $v;
		}
	}

	$t->add($r);
}

if (count($list) > 0) {
	$t->add_tip_line("");
	$sum_list["医生"] = "汇总";
	if ($sum_list["人均消费"] > 0) {
		$sum_list["人均消费"] = round($sum_list["人均消费"] / count($list), 2);
	}
	$sum_list["操作"] = "-";
	$t->add($sum_list);

	$patient_all += $sum_list["初诊人数"];
	$yingyee_all += $sum_list["营业额"];
}


/*
  --------------------   住院   -----------------------
*/

if ($hconfig["住院收费项目"] != '') {

	// 定义单元格格式:
	$list_heads = array(
		"医生" => array("width"=>"", "align"=>"center"),
		"住院人数" => array("width"=>"", "align"=>"center"),
	);

	$shoufei_array = explode("|", $hconfig["住院收费项目"]);
	foreach ($shoufei_array as $k) {
		if ($k) {
			$list_heads[$k] = array("width"=>"", "align"=>"center");
		}
	}

	$list_heads["营业额"] = array("width"=>"", "align"=>"center");
	$list_heads["人均消费"] = array("width"=>"", "align"=>"center");
	$list_heads["录入"] = array("width"=>"", "align"=>"center");
	$list_heads["操作"] = array("width"=>"80", "align"=>"center");


	// 列表显示类:
	$t2 = load_class("table");
	$t2->set_head($list_heads, '', '');
	$t2->table_class = "new_list";

	$list = $db->query("select * from $table where hid=$hid and fee_type=1 and date=$date order by doctor_id", "id");
	if (!is_array($list)) {
		exit_html("Error: ".$db->sql);
	}

	$sum_list = array();

	foreach ($list as $id => $li) {
		$r = array();
		$r["医生"] = $li["doctor_name"];
		$r["住院人数"] = $li["zhuyuan"];

		$tmp = unserialize($li["detail"]);
		$r = array_merge($r, $tmp);

		$r["营业额"] = $li["yingyee"];
		$r["人均消费"] = $li["renjun"];
		$r["录入"] = $li["u_realname"];

		if ($li["log"] != '') {
			//$r["添加人"] .= ' <a href="#" title="'.rtrim(str_replace("<br>", "\r\n", $li["log"])).'">☆</a>';
			$logs2[] = '<div class="m20">医生: <b>'.$r["医生"].'</b></div><div class="m40">'.$li["log"]."</div>";
		}

		$op = array();
		if (check_power("edit")) {
			$op[] = '<a href="?op=edit&id='.$li["id"]."&fee_type=".$li["fee_type"].'">修改</a>';
		}
		if (check_power("delete")) {
			$op[] = '<a href="?op=delete&id='.$li["id"].'" onclick="return confirm_delete()">删除</a>';
		}
		$r["操作"] = implode(" ", $op);

		foreach ($r as $k => $v) {
			if (is_numeric($v)) {
				$sum_list[$k] = floatval($sum_list[$k]) + $v;
			}
		}

		$t2->add($r);
	}

	if (count($list) > 0) {
		$t2->add_tip_line("");
		$sum_list["医生"] = "汇总";
		if ($sum_list["人均消费"] > 0) {
			$sum_list["人均消费"] = round($sum_list["人均消费"] / count($list), 2);
		}
		$sum_list["操作"] = "-";
		$t2->add($sum_list);

		$yingyee_all += $sum_list["营业额"];
	}
}

$logs_str = '';
if (count($logs1) > 0) {
	$logs_str .= '<div class="b">门诊收费修改记录</div>'.implode("", $logs1);
	$logs_str .= "<br>";
}
if (count($logs2) > 0) {
	$logs_str .= '<div class="b">住院收费修改记录</div>'.implode("", $logs2);
}

include $mod.".index.tpl.php";


function my_show($arr, $default_value='', $click='') {
	$s = '';
	foreach ($arr as $v) {
		if ($v == $default_value) {
			$s .= '<b>'.$v.'</b>';
		} else {
			$s .= '<a href="#" onclick="'.$click.'">'.$v.'</a>';
		}
	}
	return $s;
}

?>