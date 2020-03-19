<?php
/*
// 说明: 统计数据首页
// 作者: 幽兰 (weelia@126.com)
// 时间: 2010-07-10
*/
$back_url = base64_encode($_SERVER["PHP_SELF"]);

$date = $_GET["date"] ? $_GET["date"] : date("Ymd");
$date_time = strtotime(substr($date,0,4)."-".substr($date,4,2)."-".substr($date,6,2)." 0:0:0");

$y_array = $m_array = $d_array = array();
for ($i = date("Y"); $i >= (date("Y") - 2); $i--) $y_array[] = $i;
for ($i = 1; $i <= 12; $i++) $m_array[] = $i;

//$max_days = cal_days_in_month(CAL_GREGORIAN, date("n", $date_time), date("Y", $date_time)); //该函数未启用...
for ($i = 1; $i <= 31; $i++) {
	if ($i <= 28 || checkdate(date("n", $date_time), $i, date("Y", $date_time))) {
		$d_array[] = $i;
	}
}


// 医院信息:
$h_info = $db->query("select * from hospital where id=$hid limit 1", 1);
$h_name = $h_info["name"];

// 所有下挂站点:
$h_sites = $db->query("select * from sites where hid=$hid", "id");

/*
// -----------   查询统计数据 ---------------
*/
// 定义单元格格式:
$list_heads = array(
	"下挂站点" => array("width"=>"150", "align"=>"center"),
	"IP" => array("width"=>"", "align"=>"center"),
	"PV" => array("width"=>"", "align"=>"center"),
	"点击" => array("width"=>"", "align"=>"center"),
	"有效点击" => array("width"=>"", "align"=>"center"),
	"添加人" => array("width"=>"", "align"=>"center"),
	"操作" => array("width"=>"150", "align"=>"center"),
);

// 列表显示类:
$t = load_class("table");
$t->set_head($list_heads, '', '');
$t->table_class = "new_list";

$h_site_ids = implode(",", array_keys($h_sites));
if ($h_site_ids) {
	$list = $db->query("select * from $table where date='$date' and site_id in ($h_site_ids)", "site_id");
}

$sum = array();
foreach ($h_sites as $h_sid => $v) {
	$li = $list[$h_sid];

	$r = $op = array();
	$r["下挂站点"] = '<span class="site_name"><a href="http://'.$v["url"].'" target="_blank" title="点击打开网站">'.$v["url"].'</a></span>';
	if ($li) {

		// sum汇总:
		foreach ($li as $a => $b) {
			if (is_numeric($b)) {
				$sum[$a] = floatval($sum[$a]) + $b;
			}
		}

		$r["IP"] = "<font color=red>".$li["ip"]."</font> | <font color=green>".$li["ip_local"]."</font> | ".$li["ip_other"];
		$r["PV"] = "<font color=red>".$li["pv"]."</font> | <font color=green>".$li["pv_local"]."</font> | ".$li["pv_other"];
		$r["点击"] = "<font color=red>".$li["click"]."</font> | <font color=green>".$li["click_local"]."</font> | ".$li["click_other"];
		$r["有效点击"] = "<font color=red>".$li["ok_click"]."</font> | <font color=green>".$li["ok_click_local"]."</font> | ".$li["ok_click_other"];
		$r["添加人"] = $li["u_realname"];

		if (check_power("edit")) {
			$op[] = "<a href='?op=edit&id=".$li["id"]."&back_url=".$back_url."' class='op' title='修改'>修改</a>";
		}
		if (check_power("delete")) {
			$op[] = "<a href='?op=delete&id=".$li["id"]."' onclick='return isdel()' class='op'>删除</a>";
		}
		$r["操作"] = implode(" ", $op);
	} else {
		$r["IP"] = $r["PV"] = $r["点击"] = $r["有效点击"] = $r["添加人"] = "";
		if (check_power("add")) {
			$op[] = "<a href='?op=add&date=$date&site_id=$h_sid&back_url=".$back_url."' class='op' title='添加数据'>添加</a>";
		}
		$r["操作"] = implode(" ", $op);
	}
	$t->add($r);
}

$t->add_tip_line("");
$sum["下挂站点"] = '<span class="site_name">汇总</span>';
if (count($list) > 0) {
	$sum["IP"] = "<font color=red>".$sum["ip"]."</font> | <font color=green>".$sum["ip_local"]."</font> | ".$sum["ip_other"];
	$sum["PV"] = "<font color=red>".$sum["pv"]."</font> | <font color=green>".$sum["pv_local"]."</font> | ".$sum["pv_other"];
	$sum["点击"] = "<font color=red>".$sum["click"]."</font> | <font color=green>".$sum["click_local"]."</font> | ".$sum["click_other"];
	$sum["有效点击"] = "<font color=red>".$sum["ok_click"]."</font> | <font color=green>".$sum["ok_click_local"]."</font> | ".$sum["ok_click_other"];
	$sum["添加人"] = '-';
	$sum["操作"] = '-';
}
$t->add($sum);



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