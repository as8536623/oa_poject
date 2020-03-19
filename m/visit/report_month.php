<?php
/*
// 说明: 月报表
// 作者: 幽兰 (weelia@126.com)
// 时间: 2010-09-27
*/
require "../../core/core.php";

$back_url = base64_encode($_SERVER["PHP_SELF"]);

if ($_GET["date"] && strlen($_GET["date"]) == 6) {
	$date = $_GET["date"];
} else {
	$date = date("Ym"); //本月
	$_GET["date"] = $date;
}
$date_time = strtotime(substr($date,0,4)."-".substr($date,4,2)."-01 0:0:0");

// 可用 年,月 数组
$y_array = $m_array = $d_array = array();
for ($i = date("Y"); $i >= (date("Y") - 2); $i--) $y_array[] = $i;
for ($i = 1; $i <= 12; $i++) $m_array[] = $i;
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

// 表头定义:
$list_heads = array(
	"日期" => array("width"=>"100", "align"=>"center"),
	"IP" => array("width"=>"", "align"=>"center", "color"=>"red"),
	"本地a" => array("width"=>"", "align"=>"center"),
	"外地a" => array("width"=>"", "align"=>"center"),
	"PV" => array("width"=>"", "align"=>"center", "color"=>"red"),
	"本地b" => array("width"=>"", "align"=>"center"),
	"外地b" => array("width"=>"", "align"=>"center"),
	"总点击" => array("width"=>"", "align"=>"center", "color"=>"red"),
	"本地c" => array("width"=>"", "align"=>"center"),
	"外地c" => array("width"=>"", "align"=>"center"),
	"有效点击" => array("width"=>"", "align"=>"center", "color"=>"red"),
	"本地d" => array("width"=>"", "align"=>"center"),
	"外地d" => array("width"=>"", "align"=>"center"),
	"零对话" => array("width"=>"", "align"=>"center", "color"=>"red"),
	"当日约" => array("width"=>"", "align"=>"center"),
	"预约当日" => array("width"=>"", "align"=>"center"),
	"到院" => array("width"=>"", "align"=>"center"),
);

// 列表显示类:
$t = load_class("table");
$t->set_head($list_heads, '', '');
$t->table_class = "new_list";


// 该月结束:
$month_end = strtotime("+1 month", $date_time);

$b = date("Ymd", $date_time);
$e = date("Ymd", $month_end);

$ori_list = $db->query("select * from visit where hid=$hid and date>=$b and date<$e order by date asc,site_id asc");

// 计算统计数据:
$cal_field = explode(" ", "ip ip_local ip_other pv pv_local pv_other click click_local click_other zero_talk ok_click ok_click_local ok_click_other");
// 处理:
$sum_list = array();
foreach ($ori_list as $v) {
	foreach ($cal_field as $f) {
		$sum_list[$v["date"]][$f] = floatval($sum_list[$v["date"]][$f]) + $v[$f];
	}
}


// 查询预约数量和到院数量
$p1 = $db->query("select order_date,status from patient_{$hid} where order_date>=$date_time and order_date<$month_end");
$p2 = $db->query("select addtime,status from patient_{$hid} where addtime>=$date_time and addtime<$month_end");
/*
$pa1 : 各日应到院数量
$pq2 : 各日已到院数量
$pa3 : 客服在该天约了多少人
$pa4 : 在该日约的人最终有多少到院了
*/
$pa1 = $pa2 = $pa3 = $pa4 = array();
foreach ($p1 as $v) {
	$day = date("d", $v["order_date"]);
	$pa1[$day] = intval($pa1[$day]) + 1;
	if ($v["status"] == 1) {
		$pa2[$day] = intval($pa2[$day]) + 1;
	}
}
foreach ($p2 as $v) {
	$day = date("d", $v["order_date"]);
	$pa3[$day] = intval($pa3[$day]) + 1;
	if ($v["status"] == 1) {
		$pa4[$day] = intval($pa4[$day]) + 1;
	}
}


$sum = array();
foreach ($d_array as $i) {
	$i_date = sprintf($date."%02d", $i);

	$r = array();

	// 统计数据
	$r["IP"] = $sum_list[$i_date]["ip"];
	$r["本地a"] = $sum_list[$i_date]["ip_local"];
	$r["外地a"] = $sum_list[$i_date]["ip_other"];
	$r["PV"] = $sum_list[$i_date]["pv"];
	$r["本地b"] = $sum_list[$i_date]["pv_local"];
	$r["外地b"] = $sum_list[$i_date]["pv_other"];
	$r["总点击"] = $sum_list[$i_date]["click"];
	$r["本地c"] = $sum_list[$i_date]["click_local"];
	$r["外地c"] = $sum_list[$i_date]["click_other"];
	$r["有效点击"] = $sum_list[$i_date]["ok_click"];
	$r["本地d"] = $sum_list[$i_date]["ok_click_local"];
	$r["外地d"] = $sum_list[$i_date]["ok_click_other"];
	$r["零对话"] = $sum_list[$i_date]["zero_talk"];

	$r["当日约"] = $pa3[$i];
	$r["预约当日"] = $pa1[$i];
	$r["到院"] = $pa2[$i];

	// 其他:
	$r["日期"] = $i."日";

	$t->add($r);

	// sum汇总:
	foreach ($r as $a => $b) {
		if (is_numeric($b)) {
			$sum[$a] = floatval($sum[$a]) + $b;
		}
	}
}

// 汇总行:
$r = array();

// 统计数据
$r = $sum;
$r["日期"] = "合计";
$t->add_tip_line("");
$t->add($r);




/*
// ------------------ 函数 -------------------
*/
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
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>

<style>
#date_tips {float:left; font-weight:bold; padding-top:1px; margin-left:10px; }
#ch_date {float:left; margin-left:20px; }
.site_name {display:block; padding:4px 0px;}
.site_name, .site_name a {font-family:"Arial", "Tahoma"; }
.ch_date_a b, .ch_date_a a {font-family:"Arial"; }
.ch_date_a b {border:0px; padding:1px 5px 1px 5px; color:red; }
.ch_date_a a {border:0px; padding:1px 5px 1px 5px; }
.ch_date_a a:hover {border:1px solid silver; padding:0px 4px 0px 4px; }
.ch_date_b {padding-top:8px; text-align:left; width:80%; color:silver; }
.ch_date_b a {padding:0 3px; }

.main_title {margin:0 auto; padding-top:24px; text-align:left; margin-left:10px; font-weight:bold; font-size:12px; font-family:"宋体"; }
</style>

<script language="javascript">

function update_date(type, o) {
	byid("date_"+type).value = parseInt(o.innerHTML, 10);

	var a = parseInt(byid("date_1").value, 10);
	var b = parseInt(byid("date_2").value, 10);

	var s = a + '' + (b<10 ? "0" : "") + b;

	byid("date").value = s;
	byid("ch_date").submit();
}

</script>

</head>

<body>

<div style="margin:10px 0 0 0px;">
	<div id="date_tips">请选择日期：</div>
	<form id="ch_date" method="GET">
		<span class="ch_date_a">年：<?php echo my_show($y_array, date("Y", $date_time), "update_date(1,this)"); ?>&nbsp;&nbsp;&nbsp;</span>
		<span class="ch_date_a">月：<?php echo my_show($m_array, date("m", $date_time), "update_date(2,this)"); ?>&nbsp;&nbsp;&nbsp;</span>

		<input type="hidden" id="date_1" value="<?php echo date("Y", $date_time); ?>">
		<input type="hidden" id="date_2" value="<?php echo date("n", $date_time); ?>">
		<input type="hidden" name="date" id="date" value="">
	</form>
	<div class="clear"></div>
</div>


<div class="main_title"><?php echo $h_name; ?> - <?php echo date("Y-n", $date_time); ?> 网站统计数据</div>

<div class="space"></div>

<?php

echo $t->show();

?>



<div class="space"></div>
</body>
</html>