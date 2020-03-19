<?php
// --------------------------------------------------------
// - 功能说明 : 网络汇总统计
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2010-10-19
// --------------------------------------------------------
require "../../core/core.php";
include "web_config.php";

$worklog = explode(",", $uinfo["worklog"]);

$table = "count_web";

$bt = $b = date("Ymd", $date_time);
$et = $e = date("Ymd", $month_end);



//查询总医院汇总数据:
$f = "click click_local click_other zero_talk ok_click ok_click_local ok_click_other talk talk_local talk_other orders order_local order_other come come_local come_other";
$f_arr = explode(" ", $f);
foreach ($f_arr as $k => $v) {
	$f_arr[$k] = "sum({$v}) as {$v}";
}
$f_str = implode(", ", $f_arr);

$list = $db->query("select date,$f_str from $table where hid=$hid and date>=$b and date<=$e group by date", "date");


// 总天数
$days_all = count($list);


// 计算数据:
foreach ($list as $k => $v) {
	// 咨询预约率:
	$list[$k]["per_1"] = @round($v["talk"] / $v["click"] * 100, 2);
	// 预约就诊率:
	$list[$k]["per_2"] = @round($v["come"] / $v["orders"] * 100, 2);
	// 咨询就诊率:
	$list[$k]["per_3"] = @round($v["come"] / $v["click"] * 100, 2);
	// 有效咨询率:
	$list[$k]["per_4"] = @round($v["ok_click"] / $v["click"] * 100, 2);
	// 有效预约率:
	$list[$k]["per_5"] = @round($v["talk"] / $v["ok_click"] * 100, 2);
}

// 计算总数据:
$cal_field = explode(" ", $f);
$sum_list = array();
foreach ($list as $v) {
	foreach ($cal_field as $f) {
		$sum_list[$f] = floatval($sum_list[$f]) + $v[$f];
	}
}

// 咨询预约率:
$sum_list["per_1"] = @round($sum_list["talk"] / $sum_list["click"] * 100, 2);
// 预约就诊率:
$sum_list["per_2"] = @round($sum_list["come"] / $sum_list["orders"] * 100, 2);
// 咨询就诊率:
$sum_list["per_3"] = @round($sum_list["come"] / $sum_list["click"] * 100, 2);
// 有效咨询率:
$sum_list["per_4"] = @round($sum_list["ok_click"] / $sum_list["click"] * 100, 2);
// 有效预约率:
$sum_list["per_5"] = @round($sum_list["talk"] / $sum_list["ok_click"] * 100, 2);


// 当日总数据:
$day_count = $db->query("select date,sum(click_all) as click_all, sum(zero_talk) as zero_talk, sum(wangcha) as wangcha from count_web_day where hid=$hid and date>=$bt and date<=$et group by date", "date");

foreach ($day_count as $v) {
	$sum_list["click_all"] += $v["click_all"];
	$sum_list["zero_talk"] += $v["zero_talk"];
	$sum_list["wangcha"] += $v["wangcha"];
}


// 日均
$per_day = array();
foreach ($sum_list as $k => $v) {
	$per_day[$k] = @round($v / $days_all, 1);
}


// 目标

$_cur_month = date("Ym", $date_time);

$fs = array();
$fs[] = "click_all";
$fs[] = "click";
$fs[] = "click_local";
$fs[] = "click_other";
$fs[] = "ok_click";
$fs[] = "ok_click_local";
$fs[] = "ok_click_other";
$fs[] = "zero_talk";

$fs[] = "talk";
$fs[] = "talk_local";
$fs[] = "talk_other";
$fs[] = "orders";
$fs[] = "order_local";
$fs[] = "order_other";
$fs[] = "come";
$fs[] = "come_local";
$fs[] = "come_other";
$fs[] = "wangcha";

$fs[] = "per_1";
$fs[] = "per_2";
$fs[] = "per_3";
$fs[] = "per_4";
$fs[] = "per_5";

$mubiao_data = $db->query("select * from count_mubiao where hid=$hid and month='$_cur_month' limit 1", 1);
$mubiao_value = @unserialize($mubiao_data["config"]);

$mubiao = array();
foreach ($fs as $v) {
	if ($mubiao_value[$v] != '') {
		$mubiao[$v] = '<a href="#" onclick="set_mubiao(\''.$_cur_month.'\', \''.$v.'\'); return false;">'.$mubiao_value[$v].'</a>';
	} else {
		$mubiao[$v] = '<a href="#" onclick="set_mubiao(\''.$_cur_month.'\', \''.$v.'\'); return false;">添加</a>';
	}
}





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


$title = $h_name." 汇总数据";


// 页面开始 ------------------------
?>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style>
* {font-family:"Tahoma"; }
body {padding:5px 8px; }
form {display:inline; }
#date_tips {float:left; font-weight:bold; padding-top:1px; }
#ch_date {float:left; margin-left:20px; }
.site_name {display:block; padding:4px 0px;}
.site_name, .site_name a {font-family:"Arial", "Tahoma"; }
.ch_date_a b, .ch_date_a a {font-family:"Arial"; }
.ch_date_a b {border:0px; padding:1px 5px 1px 5px; color:red; }
.ch_date_a a {border:0px; padding:1px 5px 1px 5px; }
.ch_date_a a:hover {border:1px solid silver; padding:0px 4px 0px 4px; }
.ch_date_b {padding-top:8px; text-align:left; width:80%; color:silver; }
.ch_date_b a {padding:0 3px; }

.main_title {margin:0 auto; padding-top:24px; padding-bottom:10px; text-align:center; font-weight:; font-size:16px; font-family:"微软雅黑"; }

.item {padding:8px 3px 6px 3px !important; }
.list .head {padding-top:6px; padding-bottom:4px; padding-left:1px; padding-right:1px; }

.head_2 {}
.head_2 td {padding:4px 3px 2px 3px; border:1px solid #e3e3e3; background:#ffeadf }

.rate_tips {padding:30px 0 0 30px; line-height:24px; }

.tr_high_light td {background:#FFE1D2; }
.huizong {border:1px solid #e0e0e0; padding:4px 0 3px 6px; color:#43a75c }
</style>

<script language="javascript">
function set_mubiao(month, field) {
	var url = "/m/count/set_mubiao.php?month="+month+"&field="+field;
	parent.load_src(1, url, 600, 250);
	return false;
}

function update_date(type, o) {
	byid("date_"+type).value = parseInt(o.innerHTML, 10);

	var a = parseInt(byid("date_1").value, 10);
	var b = parseInt(byid("date_2").value, 10);

	var s = a + '' + (b<10 ? "0" : "") + b;

	byid("date").value = s;
	byid("ch_date").submit();
	return false;
}

function float_head() {
	var s_top = document.body.scrollTop;
	var top = byid("data_list").offsetTop;
	var top_head = byid("data_head").offsetHeight;

	if (s_top >= (0 + top + top_head)) {
		var o = byid("float_head");
		o.style.display = "";
		o.style.position = "absolute";
		o.style.left = byid("data_list").style.left;
		o.style.top = s_top;
	} else {
		byid("float_head").style.display = "none";
	}
}

function hgo(dir, o) {
	var obj = byid("type_id_002");
	if (dir == "up") {
		if (obj.selectedIndex > 1) {
			obj.selectedIndex = obj.selectedIndex - 1;
			obj.onchange();
			o.disabled = true;
		} else {
			parent.msg_box("已经是最前了", 3);
		}
	}
	if (dir == "down") {
		if (obj.selectedIndex < obj.options.length-1) {
			obj.selectedIndex = obj.selectedIndex + 1;
			obj.onchange();
			o.disabled = true;
		} else {
			parent.msg_box("已经是最后一个了", 3);
		}
	}
}
</script>
</head>

<body onscroll="float_head()">
<table style="margin:10px 0 0 0px;" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="580" align="left">
			<div id="date_tips">请选择日期：</div>
			<form id="ch_date" method="GET">
				<span class="ch_date_a">年：<?php echo my_show($y_array, date("Y", $date_time), "return update_date(1,this)"); ?>&nbsp;&nbsp;&nbsp;</span>
				<span class="ch_date_a">月：<?php echo my_show($m_array, date("m", $date_time), "return update_date(2,this)"); ?>&nbsp;&nbsp;&nbsp;</span>

				<input type="hidden" id="date_1" value="<?php echo date("Y", $date_time); ?>">
				<input type="hidden" id="date_2" value="<?php echo date("n", $date_time); ?>">
				<input type="hidden" name="date" id="date" value="">
				<input type="hidden" name="hid" value="<?php echo $_GET["hid"]; ?>">
			</form>
		</td>

		<td width="" align="left">
		</td>

		<td width="" align="right">
			<a href="#refresh" onclick="self.location.reload(); return false;" title="刷新当前页面">刷新</a>&nbsp;
		</td>
	</tr>
</table>

<table style="margin:10px 0 0 0px;" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="300" align="left">
			<div id="date_tips">医院项目：</div>
			<form method="GET">
				<select name="hid" id="type_id_002" class="combo" onchange="this.form.submit()">
					<option value="" style="color:gray">-请选择项目-</option>
					<?php echo list_option($types, "_key_", "_value_", $hid); ?>
				</select>&nbsp;
				<input type="hidden" name="op" value="change_type">
				<button class="button" onclick="hgo('up',this);">上</button>&nbsp;
				<button class="button" onclick="hgo('down',this);">下</button>
				<input type="hidden" name="date" value="<?php echo $_GET["date"]; ?>">
			</form>
		</td>

		<td align="center">
		</td>

		<td width="350" align="right">
		</td>
	</tr>
</table>


<div class="main_title"><?php echo $title; ?> - <?php echo date("Y-n", $date_time); ?></div>

<table id="float_head" style="display:none; border-bottom:0;" width="100%" align="center" class="list">
	<tr>
		<td class="head" style="width:5%;" align="center">日期</td>

		<td class="head" style="width:4%;" align="center" style="color:red">系统<br>总点击</td>
		<td class="head" style="width:4%;" align="center" style="color:red">总点击</td>
		<td class="head" style="width:4%;" align="center">本地</td>
		<td class="head" style="width:4%;" align="center">外地</td>
		<td class="head" style="width:4%;" align="center" style="color:red">总有效</td>
		<td class="head" style="width:4%;" align="center">本地</td>
		<td class="head" style="width:3%;" align="center">外地</td>
		<td class="head" style="width:4%;" align="center" style="color:red">零对话</td>

		<td class="head" style="width:4%;" align="center" style="color:red">当天约</td>
		<td class="head" style="width:3%;" align="center">本地</td>
		<td class="head" style="width:3%;" align="center">外地</td>
		<td class="head" style="width:4%;" align="center" style="color:red">预计到院</td>
		<td class="head" style="width:3%;" align="center">本地</td>
		<td class="head" style="width:3%;" align="center">外地</td>
		<td class="head" style="width:4%;" align="center" style="color:red">实际到院</td>
		<td class="head" style="width:3%;" align="center">本地</td>
		<td class="head" style="width:3%;" align="center">外地</td>
		<td class="head" style="width:4%;" align="center" style="color:red">网查</td>

		<td class="head" style="width:6%;" align="center" style="color:red">咨询<br>预约率</td>
		<td class="head" style="width:6%;" align="center" style="color:red">预到<br>就诊率</td>
		<td class="head" style="width:6%;" align="center" style="color:red">咨询<br>就诊率</td>
		<td class="head" style="width:6%;" align="center" style="color:red">有效<br>咨询率</td>
		<td class="head" style="width:6%;" align="center" style="color:red">有效<br>预约率</td>
	</tr>
</table>

<table id="data_list" width="100%" align="center" class="list">
	<tr id="data_head">
		<td class="head" style="width:5%;" align="center">日期</td>

		<td class="head" style="width:4%;" align="center" style="color:red">系统<br>总点击</td>
		<td class="head" style="width:4%;" align="center" style="color:red">总点击</td>
		<td class="head" style="width:4%;" align="center">本地</td>
		<td class="head" style="width:4%;" align="center">外地</td>
		<td class="head" style="width:4%;" align="center" style="color:red">总有效</td>
		<td class="head" style="width:4%;" align="center">本地</td>
		<td class="head" style="width:3%;" align="center">外地</td>
		<td class="head" style="width:4%;" align="center" style="color:red">零对话</td>

		<td class="head" style="width:4%;" align="center" style="color:red">当天约</td>
		<td class="head" style="width:3%;" align="center">本地</td>
		<td class="head" style="width:3%;" align="center">外地</td>
		<td class="head" style="width:4%;" align="center" style="color:red">预计到院</td>
		<td class="head" style="width:3%;" align="center">本地</td>
		<td class="head" style="width:3%;" align="center">外地</td>
		<td class="head" style="width:4%;" align="center" style="color:red">实际到院</td>
		<td class="head" style="width:3%;" align="center">本地</td>
		<td class="head" style="width:3%;" align="center">外地</td>
		<td class="head" style="width:4%;" align="center" style="color:red">网查</td>

		<td class="head" style="width:6%;" align="center" style="color:red">咨询<br>预约率</td>
		<td class="head" style="width:6%;" align="center" style="color:red">预到<br>就诊率</td>
		<td class="head" style="width:6%;" align="center" style="color:red">咨询<br>就诊率</td>
		<td class="head" style="width:6%;" align="center" style="color:red">有效<br>咨询率</td>
		<td class="head" style="width:6%;" align="center" style="color:red">有效<br>预约率</td>
	</tr>

<?php
foreach ($list as $cur_date => $li) {
	$li["kf_click"] = $day_count[$cur_date]["click_all"];
	$li["zero_talk"] = $day_count[$cur_date]["zero_talk"];
	$li["wangcha"] = $day_count[$cur_date]["wangcha"];

?>

	<tr>
		<td class="item" align="center"><?php echo date("n.j", strtotime(int_date_to_date($cur_date))); ?></td>

		<td class="item" align="center" style="color:red"><?php echo $li["kf_click"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["click"]; ?></td>
		<td class="item" align="center"><?php echo $li["click_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["click_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["ok_click"]; ?></td>
		<td class="item" align="center"><?php echo $li["ok_click_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["ok_click_other"]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo $li["zero_talk"]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo $li["talk"]; ?></td>
		<td class="item" align="center"><?php echo $li["talk_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["talk_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["orders"]; ?></td>
		<td class="item" align="center"><?php echo $li["order_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["order_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["come"]; ?></td>
		<td class="item" align="center"><?php echo $li["come_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["come_other"]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo $li["wangcha"]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_1"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_2"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_3"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_4"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_5"]); ?>%</td>
	</tr>

<?php } ?>

	<!-- <tr>
		<td colspan="30" class="huizong">数据汇总</td>
	</tr> -->

	<tr>
		<td class="item" align="center">汇总</td>
		<td class="item" align="center" style="color:red"><?php echo $sum_list["click_all"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $sum_list["click"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["click_local"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["click_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $sum_list["ok_click"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["ok_click_local"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["ok_click_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $sum_list["zero_talk"]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo $sum_list["talk"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["talk_local"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["talk_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $sum_list["orders"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["order_local"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["order_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $sum_list["come"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["come_local"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["come_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $sum_list["wangcha"]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo @floatval($sum_list["per_1"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo @floatval($sum_list["per_2"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo @floatval($sum_list["per_3"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo @floatval($sum_list["per_4"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo @floatval($sum_list["per_5"]); ?>%</td>
	</tr>

	<tr>
		<td class="item" align="center">日均</td>
		<td class="item" align="center" style="color:red"><?php echo $per_day["click_all"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $per_day["click"]; ?></td>
		<td class="item" align="center"><?php echo $per_day["click_local"]; ?></td>
		<td class="item" align="center"><?php echo $per_day["click_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $per_day["ok_click"]; ?></td>
		<td class="item" align="center"><?php echo $per_day["ok_click_local"]; ?></td>
		<td class="item" align="center"><?php echo $per_day["ok_click_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $per_day["zero_talk"]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo $per_day["talk"]; ?></td>
		<td class="item" align="center"><?php echo $per_day["talk_local"]; ?></td>
		<td class="item" align="center"><?php echo $per_day["talk_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $per_day["orders"]; ?></td>
		<td class="item" align="center"><?php echo $per_day["order_local"]; ?></td>
		<td class="item" align="center"><?php echo $per_day["order_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $per_day["come"]; ?></td>
		<td class="item" align="center"><?php echo $per_day["come_local"]; ?></td>
		<td class="item" align="center"><?php echo $per_day["come_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $per_day["wangcha"]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo @floatval($sum_list["per_1"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo @floatval($sum_list["per_2"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo @floatval($sum_list["per_3"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo @floatval($sum_list["per_4"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo @floatval($sum_list["per_5"]); ?>%</td>
	</tr>

	<tr>
		<td class="item" align="center">日目标</td>
		<td id="mubiao_click_all" class="item" align="center" style="color:red"><?php echo $mubiao["click_all"]; ?></td>
		<td id="mubiao_click" class="item" align="center" style="color:red"><?php echo $mubiao["click"]; ?></td>
		<td id="mubiao_click_local" class="item" align="center"><?php echo $mubiao["click_local"]; ?></td>
		<td id="mubiao_click_other" class="item" align="center"><?php echo $mubiao["click_other"]; ?></td>
		<td id="mubiao_ok_click" class="item" align="center" style="color:red"><?php echo $mubiao["ok_click"]; ?></td>
		<td id="mubiao_ok_click_local" class="item" align="center"><?php echo $mubiao["ok_click_local"]; ?></td>
		<td id="mubiao_ok_click_other" class="item" align="center"><?php echo $mubiao["ok_click_other"]; ?></td>
		<td id="mubiao_zero_talk" class="item" align="center" style="color:red"><?php echo $mubiao["zero_talk"]; ?></td>

		<td id="mubiao_talk" class="item" align="center" style="color:red"><?php echo $mubiao["talk"]; ?></td>
		<td id="mubiao_talk_local" class="item" align="center"><?php echo $mubiao["talk_local"]; ?></td>
		<td id="mubiao_talk_other" class="item" align="center"><?php echo $mubiao["talk_other"]; ?></td>
		<td id="mubiao_orders" class="item" align="center" style="color:red"><?php echo $mubiao["orders"]; ?></td>
		<td id="mubiao_order_local" class="item" align="center"><?php echo $mubiao["order_local"]; ?></td>
		<td id="mubiao_order_other" class="item" align="center"><?php echo $mubiao["order_other"]; ?></td>
		<td id="mubiao_come" class="item" align="center" style="color:red"><?php echo $mubiao["come"]; ?></td>
		<td id="mubiao_come_local" class="item" align="center"><?php echo $mubiao["come_local"]; ?></td>
		<td id="mubiao_come_other" class="item" align="center"><?php echo $mubiao["come_other"]; ?></td>
		<td id="mubiao_wangcha" class="item" align="center" style="color:red"><?php echo $mubiao["wangcha"]; ?></td>

		<td id="mubiao_per_1" class="item" align="center" style="color:red"><?php echo $mubiao["per_1"]; ?></td>
		<td id="mubiao_per_2" class="item" align="center" style="color:red"><?php echo $mubiao["per_2"]; ?></td>
		<td id="mubiao_per_3" class="item" align="center" style="color:red"><?php echo $mubiao["per_3"]; ?></td>
		<td id="mubiao_per_4" class="item" align="center" style="color:red"><?php echo $mubiao["per_4"]; ?></td>
		<td id="mubiao_per_5" class="item" align="center" style="color:red"><?php echo $mubiao["per_5"]; ?></td>
	</tr>

</table>
<br>
<br>
<br>
<?php if ($debug_mode || !empty($worklog)) { ?>
<?php
$gongzuo_date_arr = array();
$gongzuo_date_arr[date("Ymd")] = "今天";
for ($i = 1; $i <= 6; $i++) {
	$t = strtotime("-".$i." days");
	$gongzuo_date_arr[date("Ymd", $t)] = date("n.j", $t);
}



function _show_content($int_date, $type_name) {
	global $db, $hid, $realname, $worklog, $debug_mode;
	$line = $db->query("select * from count_worklog where hid=$hid and date='$int_date' and type='$type_name' order by id desc limit 1", 1);
	$data_id = $int_date."_".$type_name;
	$s = '';
	if ($line["id"] > 0) {
		if (in_array($type_name."_view", $worklog) || $debug_mode) {
			$s .= '<span id="'.$data_id.'">'.text_show($line["content"]).' &nbsp;<font color="red">'.$line["author"]."@".date("m-d H:i", $line["addtime"]).'</font></span>';
		} else {
			$s .= "-";
		}
		if ($realname == $line["author"] || $debug_mode) { //可以修改自己的
			$s .= ' &nbsp;<a href="javascript:;" onclick="edit_content(\''.$int_date.'\', \''.$type_name.'\', \''.$data_id.'\');">修改</a>';
		}
	} else {
		if (in_array($type_name."_view", $worklog) || $debug_mode) {
			$s .= '<span id="'.$data_id.'"></span>';
		}
		if (in_array($type_name."_edit", $worklog) || $debug_mode) {
			$s .= ' &nbsp;<a href="javascript:;" onclick="edit_content(\''.$int_date.'\', \''.$type_name.'\', \''.$data_id.'\');">添加</a>';
		}
	}
	return $s;
}


?>
<style type="text/css">
.gongzuo_tips { padding:10px 3px; font-size:16px; font-family:"微软雅黑"; text-align:center; }
.pl10 {padding-left:10px !important; }
.top {vertical-align:top; }
</style>
<script type="text/javascript">
function edit_content(int_date, type_name, data_id) {
	var url = "/m/count/worklog_edit.php?date="+int_date+"&type="+type_name+"&data_id="+data_id;
	parent.load_src(1, url, 550, 250);
	return false;
}
</script>
<div id="gongzuo">
	<div class="gongzuo_tips"><?php echo $h_name; ?> 工作报表</div>
	<table class="list" width="100%">
		<tr>
			<td class="head" width="8%" align="center">日期</td>
			<td class="head pl10" width="24%">咨询分析</td>
			<td class="head pl10" width="24%">执行分析</td>
			<td class="head pl10" width="24%">主管分析</td>
			<td class="head pl10" width="24%">主任分析</td>
		</tr>

<?php foreach ($gongzuo_date_arr as $d => $s) { ?>
		<tr onmouseover="mi(this)" onmouseout="mo(this)">
			<td class="item top" align="center"><?php echo $s; ?></td>
			<td class="item pl10 top"><?php echo _show_content($d, "zixun"); ?></td>
			<td class="item pl10 top"><?php echo _show_content($d, "zhixing"); ?></td>
			<td class="item pl10 top"><?php echo _show_content($d, "zhuguan"); ?></td>
			<td class="item pl10 top"><?php echo _show_content($d, "zhuren"); ?></td>
		</tr>
<?php } ?>

	</table>
</div>

<?php } ?>

<br>
<br>

</body>
</html>