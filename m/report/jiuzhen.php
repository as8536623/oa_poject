<?php
/*
// - 功能说明 : 就诊报表
// - 创建作者 : 幽兰 (weelia@126.com)
// - 创建时间 : 2011-08-22
*/
require "../../core/core.php";
include "config.php";
$table = "jiuzhen_report";

$t_begin = now();
$sql_time = array();


function sql_time_log($sql) {
	global $sql_begin;
	$t_use = round(now() - $sql_begin, 4);
	return $t_use."s ".$sql;
}


// 上月:
$lastm_begin = strtotime("-1 month", $date_time);
$lastm_end = $date_time - 1;

$lastm_ym = date("Ym", $lastm_begin);
$lastm_ymd_begin = date("Ymd", $lastm_begin);
$lastm_ymd_end = date("Ymd", $lastm_end);


// 最近6个月
$his_month = array();
for ($i = 1; $i <= 6; $i++) {
	$tmp = strtotime("-{$i} month", $date_time);
	$his_month[date("Ym", $tmp)] = date("Y-m", $tmp);
}
asort($his_month);


// 要忽略的医院:
$not_hids = '';
if (array_key_exists($date_ym, $skiped_hid)) {
	$not_hids = " and id not in (".$skiped_hid[$date_ym].")";
}


// 查询
$h_arr = $db->query("select id,name,area,depart from hospital where id in ($hids) $not_hids order by sort desc, id asc", "id");



$area_arr = $depart_arr = array();
$area_qita = $depart_qita = 0;
foreach ($h_arr as $_hid => $h) {
	$hid_name[$_hid] = $h["name"];
	$h["area"] = trim($h["area"]);
	$h["depart"] = trim($h["depart"]);
	if ($h["area"] == '' || $h["area"] == "其它") {
		$area_qita++;
	} else {
		$area_arr[$h["area"]] = intval($area_arr[$h["area"]]) + 1;
	}
	if ($h["depart"] == '' || $h["depart"] == "其它") {
		$depart_qita++;
	} else {
		$depart_arr[$h["depart"]] = intval($depart_arr[$h["depart"]]) + 1;
	}
}

arsort($area_arr);
arsort($depart_arr);

if ($area_qita > 0) {
	$area_arr["其它"] = $area_qita;
}
if ($depart_qita > 0) {
	$depart_arr["其它"] = $depart_qita;
}


// 限制地区或科室处理:
if ($limit_area) {
	foreach ($h_arr as $k => $li) {
		if ($li["area"] != $limit_area || ($limit_area == "其它" && $li["area"] == "") ) {
			unset($h_arr[$k]);
		}
	}
}
if ($limit_depart) {
	foreach ($h_arr as $k => $li) {
		if ($li["depart"] != $limit_depart || ($limit_depart == "其它" && $li["depart"] == "") ) {
			unset($h_arr[$k]);
		}
	}
}


$show_hids = array_keys($h_arr);
$show_hids_str = implode(",", $show_hids);


$sql_begin = now();
$tmp = $db->query("select * from $table where hid in ($show_hids_str) and month=$date_ym and sub_id=$sub_id");
$sql_time[] = sql_time_log($db->sql);

$data = array();
foreach ($tmp as $li) {
	$data[$li["hid"]] = @unserialize($li["config"]);
}
//echo "<pre>";
//print_r($data);
unset($tmp);


// 查询最近6个月的历史记录:
$from_month = date("Ym", strtotime("-6 month", $date_time));
$sql_begin = now();
$tmp = $db->query("select * from $table where hid in ($show_hids_str) and month>=$from_month and month<$date_ym and sub_id=$sub_id");
$sql_time[] = sql_time_log($db->sql);

$his_data = array();
foreach ($tmp as $li) {
	$his_data[$li["hid"]][$li["month"]] = @unserialize($li["config"]);
}
unset($tmp);


if (date("Ym", $date_time) < date("Ym")) {
	$已过天数 = $month_all_days; //小于当前真实月份
} else if (date("Ym", $date_time) > date("Ym")) {
	$已过天数 = "0"; //大于当前真实月份
} else {
	$已过天数 = date("j") - 1; //本月
}


// 查询 网络数据统计
$to_load_field = explode(" ", "ip ip_local ip_other pv pv_local pv_other click click_local click_other ok_click ok_click_local ok_click_other talk talk_local talk_other orders order_local order_other come come_local come_other");
$s_array = '';
foreach ($to_load_field as $v) {
	$s_array[] = 'sum('.$v.') as '.$v;
}
$field_str = implode(", ", $s_array);

$c_lv = "#66cc99";
$c_huang = "#ffcc00";


// 手机公式:
$shouji_condition_id = 3;
$s = $db->query("select * from index_module_set where id=$shouji_condition_id limit 1", 1, "sum_condition");
$shouji_condition_s = 'media_from in ("'.str_replace("+", '","', $s).'")';

// 微信公式:
$weixin_condition_id = 2;
$s = $db->query("select * from index_module_set where id=$weixin_condition_id limit 1", 1, "sum_condition");
$weixin_condition_s = 'media_from in ("'.str_replace("+", '","', $s).'")';


foreach ($show_hids as $_hid) {
	$_hname = $hid_name[$_hid];
	$res = $pc = $sj = $wx = array();

	// 首页汇总数据:
	$sql_begin = now();
	$d = $db->query("select data from patient_data where hid=$_hid limit 1", 1, "data");
	$sql_time[] = sql_time_log($db->sql);
	$idata = (array) @unserialize($d);

	// PC
	if ($sub_id == 0 || $sub_id == 1) {

		$pc["已完成1"] = $db->query("select sum(come) as c from count_web where hid=$_hid and sub_id=1 and date>=$date_ymd_begin and date<=$date_ymd_end", 1, "c");
		$pc["已完成2"] = $db->query("select sum(wangcha) as c from count_web_day where hid=$_hid and sub_id=1 and date>=$date_ymd_begin and date<=$date_ymd_end", 1, "c");
		$pc["已完成3"] = $db->query("select (sum(x1)+sum(x2)+sum(x3)+sum(x4)-sum(x5)) as c from jingjia_xiaofei where hid=$_hid and date>=$date_ymd_begin and date<=$date_ymd_end", 1, "c");

		$pc["预计1"] = @round($pc["已完成1"] / $已过天数 * $month_all_days);
		$pc["预计2"] = @round($pc["已完成2"] / $已过天数 * $month_all_days);
		$pc["预计3"] = @round($pc["已完成3"] / ($pc["已完成1"] + $pc["已完成2"]));

		$pc["本月完成比例1"] = $data[$_hid]["mubiao1"] ? ((@round(($pc["预计1"] + $pc["预计2"]) / $data[$_hid]["mubiao1"], 3) * 100)."%") : "";
		$pc["本月完成比例2"] = '';
		$pc["本月完成比例3"] = $data[$_hid]["mubiao3"] ? ((@round($pc["预计3"] / $data[$_hid]["mubiao3"], 2) * 100)."%") : "";

		$pc["上月已完成1"] = $his_data[$_hid][$lastm_ym]["h_jiuzhen"];
		$pc["上月已完成2"] = $his_data[$_hid][$lastm_ym]["h_wangcha"];
		$pc["上月已完成3"] = $db->query("select (sum(x1)+sum(x2)+sum(x3)+sum(x4)-sum(x5)) as c from jingjia_xiaofei where hid=$_hid and date>=$lastm_ymd_begin and date<=$lastm_ymd_end", 1, "c");

		$pc["上月均值1"] = @round($pc["上月已完成1"] / $lastm_days, 1);
		$pc["上月均值2"] = @round($pc["上月已完成2"] / $lastm_days);
		$pc["上月均值3"] = $his_data[$_hid][$lastm_ym]["h_renjun"];

		$pc["目前均值1"] = @round($pc["已完成1"] / $已过天数, 1);
		$pc["目前均值2"] = @round($pc["已完成2"] / $已过天数);
		$pc["目前均值3"] = @round($pc["已完成3"] / ($pc["已完成1"] + $pc["已完成2"]));

		$pc["增幅1"] = $pc["预计1"] - $pc["上月已完成1"];
		$pc["增幅2"] = '';
		$pc["增幅3"] = $pc["目前均值3"] - $his_data[$_hid][$lastm_ym]["h_renjun"];

		$pc["已过天数"] = $已过天数;
	}


	// 手机
	if ($sub_id == 0 || $sub_id == 2) {

		//$sj["已完成1"] = $idata["手机"]["实到"]["本月"] - $idata["手机"]["实到"]["今日"];
		$sj["已完成1"] = $db->query("select count(*) as c from patient_{$_hid} where order_date>=$date_time and order_date<=$date_end and status=1 and $shouji_condition_s", 1, "c");
		$sj["已完成2"] = '';
		$sj["已完成3"] = $db->query("select (sum(x5)+sum(x6)) as c from jingjia_xiaofei where hid=$_hid and date>=$date_ymd_begin and date<=$date_ymd_end", 1, "c");

		$sj["预计1"] = @round($sj["已完成1"] / $已过天数 * $month_all_days);
		$sj["预计2"] = '';
		$sj["预计3"] = @round($sj["已完成3"] / ($sj["已完成1"] + $sj["已完成2"]));

		$sj["上月已完成1"] = $db->query("select count(*) as c from patient_{$_hid} where order_date>=$lastm_begin and order_date<=$lastm_end and status=1 and $shouji_condition_s", 1, "c");
		$sj["上月已完成2"] = '';
		$sj["上月已完成3"] = $db->query("select (sum(x5)-sum(x6)) as c from jingjia_xiaofei where hid=$_hid and date>=$lastm_ymd_begin and date<=$lastm_ymd_end", 1, "c");


		$sj["增幅1"] = $sj["预计1"] - $sj["上月已完成1"];
		$sj["增幅2"] = '';
		$sj["增幅3"] = $sj["已完成3"] - @round($sj["上月已完成3"] / ($sj["上月已完成1"] + $sj["上月已完成2"]));

		$sj["上月均值1"] = @round($sj["上月已完成1"] / $lastm_days, 1);
		$sj["上月均值2"] = @round($sj["上月已完成2"] / $lastm_days);
		$sj["上月均值3"] = $his_data[$_hid][$lastm_ym]["h_renjun"];

		$sj["目前均值1"] = @round($sj["已完成1"] / $已过天数, 1);
		$sj["目前均值2"] = @round($sj["已完成2"] / $已过天数);
		$sj["目前均值3"] = @round($sj["已完成3"] / ($sj["已完成1"] + $sj["已完成2"]));

		$sj["已过天数"] = $已过天数;
	}

	// 微信:
	if ($sub_id == 0 || $sub_id == 3) {
		$x = substr_count($_hname, "肝") > 0 ? 1200 : 600;

		//$wx["已完成1"] = $idata["微信"]["实到"]["本月"] - $idata["微信"]["实到"]["今日"];
		$wx["已完成1"] = $db->query("select count(*) as c from patient_{$_hid} where order_date>=$date_time and order_date<=$date_end and status=1 and $weixin_condition_s", 1, "c");
		$wx["已完成2"] = '';
		$wx["已完成3"] = $wx["已完成1"] * $x;


		$wx["预计1"] = @round($wx["已完成1"] / $已过天数 * $month_all_days);
		$wx["预计2"] = '';
		$wx["预计3"] = '';

		$wx["上月已完成1"] = $db->query("select count(*) as c from patient_{$_hid} where order_date>=$lastm_begin and order_date<=$lastm_end and status=1 and $weixin_condition_s", 1, "c");
		$wx["上月已完成2"] = '';
		$wx["上月已完成3"] = $wx["上月已完成1"] * $x;

		$wx["增幅1"] = $wx["预计1"] - $wx["上月已完成1"];
		$wx["增幅2"] = '';
		$wx["增幅3"] = $sj["已完成3"] - @round($sj["上月已完成3"] / $wx["上月已完成1"]);

		$wx["上月均值1"] = @round($wx["上月已完成1"] / $lastm_days, 1);
		$wx["上月均值2"] = '';
		$wx["上月均值3"] = $his_data[$_hid][$lastm_ym]["h_renjun"];

		$wx["目前均值1"] = @round($wx["已完成1"] / $已过天数);
		$wx["目前均值2"] = '';
		$wx["目前均值3"] = '';

		$wx["已过天数"] = $已过天数;
	}

	if ($sub_id == 0) {
		$res["已完成1"] = $pc["已完成1"] + $sj["已完成1"] + $wx["已完成1"];
		$res["已完成2"] = $pc["已完成2"] + $sj["已完成2"] + $wx["已完成2"];
		$res["已完成3"] = $pc["已完成3"] + $sj["已完成3"] + $wx["已完成3"];

		$res["预计1"] = @round($res["已完成1"] / $已过天数 * $month_all_days);
		$res["预计2"] = @round($res["已完成2"] / $已过天数 * $month_all_days);
		$res["预计3"] = @round($res["已完成3"] / ($res["已完成1"] + $res["已完成2"]));

		$res["本月完成比例1"] = $data[$_hid]["mubiao1"] ? ((@round(($res["预计1"] + $res["预计2"]) / $data[$_hid]["mubiao1"], 3) * 100)."%") : "";
		$res["本月完成比例2"] = '';
		$res["本月完成比例3"] = $data[$_hid]["mubiao3"] ? ((@round($res["预计3"] / $data[$_hid]["mubiao3"], 2) * 100)."%") : "";

		$res["比例颜色1"] = $res["本月完成比例1"] ? (intval($res["本月完成比例1"]) >= 100 ? $c_lv : $c_huang) : "";
		$res["比例颜色3"] = $res["本月完成比例3"] ? (intval($res["本月完成比例3"]) < 100 ? $c_lv : $c_huang) : "";

		$res["上月已完成1"] = $his_data[$_hid][$lastm_ym]["h_jiuzhen"];
		$res["上月已完成2"] = $pc["上月已完成2"] + $sj["上月已完成2"] + $wx["上月已完成2"];
		$res["上月已完成3"] = $pc["上月已完成3"] + $sj["上月已完成3"] + $wx["上月已完成3"];

		$res["上月均值1"] = @round($res["上月已完成1"] / $lastm_days, 1);
		$res["上月均值2"] = @round($res["上月已完成2"] / $lastm_days);;
		$res["上月均值3"] = $his_data[$_hid][$lastm_ym]["h_renjun"];

		$res["目前均值1"] = @round($res["已完成1"] / $已过天数, 1);
		$res["目前均值2"] = '';
		$res["目前均值3"] = $res["预计3"];

		$res["增幅1"] = $res["预计1"] - $res["上月已完成1"];
		$res["增幅2"] = '';
		$res["增幅3"] = $res["预计3"] - $res["上月均值3"];

		$res["增幅颜色1"] = $res["增幅1"] ? (intval($res["增幅1"]) > 0 ? $c_lv : $c_huang) : "";
		$res["增幅颜色3"] = $res["增幅3"] ? (intval($res["增幅3"]) > 0 ? $c_huang : $c_lv) : "";

		$res["已过天数"] = $已过天数;
	}

	if ($sub_id == 0) {
		$data[$_hid] = wee_merge($data[$_hid], $res);
	} else if ($sub_id == 1) {
		$data[$_hid] = wee_merge($data[$_hid], $pc);
	} else if ($sub_id == 2) {
		$data[$_hid] = wee_merge($data[$_hid], $sj);
	} else {
		$data[$_hid] = wee_merge($data[$_hid], $wx);
	}
}


$t_end = now();

$t_used = round($t_end - $t_begin, 4);



// 页面开始 ------------------------
?>
<html>
<head>
<title>就诊报表</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<style>
.jiuzhen_t a {color: #003e00 !important; font-family:"Tahoma"; }
.jiuzhen_t a:hover {color: #00d500 !important; font-family:"Tahoma"; }
.head, .head a {font-family:"微软雅黑","Verdana"; font-weight:bold;  }
.item {font-family:"Tahoma"; padding:8px 3px 6px 3px !important; }
.footer_op_left {font-family:"Tahoma"; }

.jiuzhen_t {border:2px solid #47b48b; }
.jiuzhen_t .head {background:#f6f6f6; border:1px solid #d8d8d8;  }
.jiuzhen_t td {padding:4px 2px 2px 2px; border:1px solid #e0e0e0; }
.tt {border-top:2px solid #78cd8f !important; }
.tl {border-left:2px solid #d8d8d8 !important; }
.tr {border-right:2px solid #d8d8d8 !important; }
.tb {border-bottom:2px solid #d8d8d8 !important; }

#date_tips {float:left; font-weight:bold; padding-top:3px; }
#ch_date {float:left; margin-left:0px; }
.site_name {display:block; padding:4px 0px;}
.site_name, .site_name a {font-family:"Arial", "Tahoma"; }
.ch_date_a b, .ch_date_a a {font-family:"Arial"; }
.ch_date_a b {border:0px; padding:1px 5px 1px 5px; color:red; }
.ch_date_a a {border:0px; padding:1px 5px 1px 5px; }
.ch_date_a a:hover {border:1px solid silver; padding:0px 4px 0px 4px; }
.ch_date_b {padding-top:8px; text-align:left; width:80%; color:silver; }
.ch_date_b a {padding:0 3px; }

.rp_title {margin-top:30px; text-align:center; font-size:16px; font-family:"微软雅黑"; }

.num {font-size:10px; font-family:"Tahoma"; color:gray; }

.bg_color1, .bg_color1 td {background-color:#f2eee6; }
.bg_color2, .bg_color2 td {background-color:white; }

.a_12 {font-size:12px !important; }
</style>

<script language="javascript">
function byid(id) {
	return document.getElementById(id);
}

function load_url(s) {
	parent.load_box(1, 'src', s);
}

function edit(url) {
	parent.load_src(1, url, 600, 500);
}

// 鼠标移入
function mi(id) {
	var color = "#ffe8d9";
	byid(id+"_1").style.backgroundColor = color;
	byid(id+"_2").style.backgroundColor = color;
	byid(id+"_3").style.backgroundColor = color;
}

// 鼠标移出
function mo(id) {
	byid(id+"_1").style.backgroundColor = "";
	byid(id+"_2").style.backgroundColor = "";
	byid(id+"_3").style.backgroundColor = "";
}

window.onscroll = function () {
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
};

function update_date(type, o) {
	var value = o.innerHTML;
	if (type == "1") {
		var url = "jiuzhen.php?op=set_year&year="+value;
	} else {
		var url = "jiuzhen.php?op=set_month&month="+value;
	}
	self.location = url;
}

function date_navi(s) {
	var url = "jiuzhen.php?op=date_navi&go="+s;
	self.location = url;
}

function confirm_skip_hid(o) {
	return confirm("您确认要忽略该医院吗（仅对当前月份生效）");
}

function set_area(s) {
	var url = "jiuzhen.php?op=set_area&area="+s;
	self.location = url;
}

function set_depart(s) {
	var url = "jiuzhen.php?op=set_depart&depart="+s;
	self.location = url;
}


<?php if ($can_edit) { ?>
function medit(hid, month, fname, default_value) {
	var url = "/m/report/jiuzhen_edit.php?hid="+hid+"&month="+month+"&fname="+fname+"&default_value="+default_value;
	parent.load_src(1, url, 400, 200);
}
<?php } ?>

</script>
</head>

<body>
<table width="100%">
	<tr>
		<td align="left">
			<div style="margin:0px 0 0 0px; ">
				<form id="ch_date" method="GET">
					<span class="ch_date_a">年：<?php echo _my_show($y_array, $year, "#", "return update_date(1,this)"); ?>&nbsp;&nbsp;&nbsp;</span>
					<span class="ch_date_a">月：<?php echo _my_show($m_array, $month, "#", "return update_date(2,this)"); ?>&nbsp;&nbsp;&nbsp;</span>
					&nbsp;
					<button onclick="date_navi('last'); return false;" class="button" title="查看上一个月的报表">上月</button>&nbsp;&nbsp;
					<button onclick="date_navi('current'); return false;" class="button" title="查看本月报表">本月</button>&nbsp;&nbsp;
					<button onclick="date_navi('next'); return false;" class="button" title="查看下一个月的报表">下月</button>&nbsp;&nbsp;
					<button onclick="self.location.reload();" class="button" title="刷新本页面">刷新</button>
				</form>
				<div class="clear"></div>
			</div>
		</td>

		<td align="left">
<?php
$_out = array();
foreach ($sum_type_arr as $_id => $_name) {
	if ($sub_id == $_id) {
		$_out[] = '<a href="#" style="color:red; font-weight:bold;">'.$_name.'</a>';
	} else {
		$_out[] = '<a href="?op=sub_change&sub_id='.$_id.'">'.$_name.'</a>';
	}
}
echo implode(" <font color=silver>|</font> ", $_out);
?>
		</td>

		<td align="right">
			<select class="combo" name="" onchange="set_area(this.value); return false;">
				<option value="" style="color:gray">-地区归类-</option>
				<?php echo list_option($area_arr, "_key_", "_key_", $limit_area); ?>
			</select>

			<select class="combo" name="" onchange="set_depart(this.value); return false;">
				<option value="" style="color:gray">-科室归类-</option>
				<?php echo list_option($depart_arr, "_key_", "_key_", $limit_depart); ?>
			</select>
		</td>
	</tr>
</table>




<div class="rp_title">
	<?php echo date("Y年m月", $date_time); ?> 就诊报表 <?php echo $show_data."(".count($show_hids).")"; ?>&nbsp;&nbsp;
<?php if ($uinfo["part_id"] == 9 || $debug_mode) { ?>
	<a href="javascript:;" onclick="copy_history_data(); return false;" class="a_12">复制历史数据</a>&nbsp;&nbsp;
	<script type="text/javascript">
	function copy_history_data() {
		var url = "/m/report/copy_history_data.php";
		parent.load_src(1, url, 800, 500);
	}
	</script>
	<a href="javascript:;" onclick="fill_last_month_come(); return false;" class="a_12">自动填上月就诊</a>
	<script type="text/javascript">
	function fill_last_month_come() {
		var url = "/m/report/fill_last_month_come.php";
		parent.load_src(1, url, 800, 500);
	}
	</script>
<?php } ?>
</div>

<table width="100%" align="center" class="jiuzhen_t" id="data_list" style="margin-top:15px;">
	<div id="data_head">
	<tr>
		<td class="head" align="center" width="5%" rowspan="2">医院名称</td>
		<td class="head" align="center" width="5%" rowspan="2">负责人</td>
		<td class="head" align="center" width="5%" rowspan="2">统计项目</td>
		<td class="head" align="center" width="30%" colspan="6">最近历史记录</td>
		<td class="head" align="center" width="5%" rowspan="2">达标指数</td>
		<td class="head" align="center" width="5%" rowspan="2">奖励基数</td>
		<td class="head" align="center" width="5%" rowspan="2">奖励指标</td>
		<td class="head" align="center" width="5%" rowspan="2">目标</td>
		<td class="head" align="center" width="5%" rowspan="2">本月完成比例</td>
		<td class="head" align="center" width="5%" rowspan="2">预计</td>
		<td class="head" align="center" width="5%" rowspan="2">对比上月增幅</td>
		<td class="head" align="center" width="5%" rowspan="2">上月均值</td>
		<td class="head" align="center" width="5%" rowspan="2">目前均值</td>
		<td class="head" align="center" width="5%" rowspan="2" style="color:red">已完成</td>
		<td class="head" align="center" width="5%" rowspan="2">已过天数</td>
	</tr>
	<tr>
<?php foreach ($his_month as $v) { ?>
		<td class="head" align="center" width="5%" style="font-family:Tahoma; font-weight:normal"><?php echo $v; ?></td>
<?php } ?>
	</tr>
	</div>

<?php
	$bg_index = 0;
	foreach ($show_hids as $_hid) {
		$_hname = $hid_name[$_hid];
		$line = $data[$_hid];
		$his_line = $his_data[$_hid];
		$bg = $bg_index++ % 2 ? "bg_color1" : "bg_color2";

?>
	<tr id="<?php echo $_hid; ?>_1" class="<?php echo $bg; ?>" onmouseover="mi(<?php echo $_hid; ?>)" onmouseout="mo(<?php echo $_hid; ?>)">
		<td class="item tt" align="center" rowspan="3">
			<b><?php echo $_hname; ?></b><br>
<?php if ($can_edit) { ?>
			<a href="?op=skip_hid&month=<?php echo date("Ym", $date_time); ?>&hid=<?php echo $_hid; ?>" onclick="return confirm_skip_hid(this)" title="本月忽略该医院">忽略</a>
<?php } ?>
		</td>
		<td class="item tt" align="center" rowspan="3"><?php echo make_edit($_hid, $date_y_m, "fuzeren", str_replace("-", "<br>", $line["fuzeren"])); ?></td>
		<td class="item tt" align="center">就诊数</td>

<?php foreach ($his_month as $ma => $mb) { ?>
		<td class="item tt" align="center"><?php echo make_edit($_hid, $mb, "h_jiuzhen", $his_line[$ma]["h_jiuzhen"]); ?></td>
<?php } ?>

		<td class="item tt" align="center"><?php echo make_edit($_hid, $date_y_m, "dabiaozhishu1", $line["dabiaozhishu1"]); ?></td>
		<td class="item tt" align="center"><?php echo make_edit($_hid, $date_y_m, "jianglijishu1", $line["jianglijishu1"]); ?></td>
		<td class="item tt" align="center"><?php echo make_edit($_hid, $date_y_m, "jianglizhibiao1", $line["jianglizhibiao1"]); ?></td>
		<td class="item tt" align="center"><?php echo make_edit($_hid, $date_y_m, "mubiao1", num($line["mubiao1"])); ?></td>
		<td class="item tt" align="center" style="background-color:<?php echo $line["比例颜色1"]; ?>"><?php echo num($line["本月完成比例1"]); ?></td>
		<td class="item tt" align="center"><?php echo num($line["预计1"]); ?></td>
		<td class="item tt" align="center" style="background-color:<?php echo $line["增幅颜色1"]; ?>"><?php echo num($line["增幅1"]); ?></td>
		<td class="item tt" align="center"><?php echo num($line["上月均值1"]); ?></td>
		<td class="item tt" align="center"><?php echo num($line["目前均值1"]); ?></td>
		<td class="item tt" align="center" style="color:red"><?php echo num($line["已完成1"]); ?></td>
		<td class="item tt" align="center" rowspan="3"><?php echo num($line["已过天数"]); ?></td>
	</tr>
	<tr id="<?php echo $_hid; ?>_2" class="<?php echo $bg; ?>" onmouseover="mi(<?php echo $_hid; ?>)" onmouseout="mo(<?php echo $_hid; ?>)">
		<td class="item" align="center">网查</td>

<?php foreach ($his_month as $ma => $mb) { ?>
		<td class="item" align="center"><?php echo make_edit($_hid, $mb, "h_wangcha", $his_line[$ma]["h_wangcha"]); ?></td>
<?php } ?>

		<td class="item" align="center"><?php echo make_edit($_hid, $date_y_m, "dabiaozhishu2", $line["dabiaozhishu2"]); ?></td>
		<td class="item" align="center"><?php echo make_edit($_hid, $date_y_m, "jianglijishu2", $line["jianglijishu2"]); ?></td>
		<td class="item" align="center"><?php echo make_edit($_hid, $date_y_m, "jianglizhibiao2", $line["jianglizhibiao2"]); ?></td>
		<td class="item" align="center"><?php echo make_edit($_hid, $date_y_m, "mubiao2", num($line["mubiao2"])); ?></td>
		<td class="item" align="center"><?php echo num($line["本月完成比例2"]); ?></td>
		<td class="item" align="center"><?php echo num($line["预计2"]); ?></td>
		<td class="item" align="center"><?php echo num($line["增幅2"]); ?></td>
		<td class="item" align="center"><?php echo num($line["上月均值2"]); ?></td>
		<td class="item" align="center"><?php echo num($line["目前均值2"]); ?></td>
		<td class="item" align="center" style="color:red"><?php echo num($line["已完成2"]); ?></td>
	</tr>
	<tr id="<?php echo $_hid; ?>_3" class="<?php echo $bg; ?>" onmouseover="mi(<?php echo $_hid; ?>)" onmouseout="mo(<?php echo $_hid; ?>)">
		<td class="item" align="center">人均成本</td>

<?php foreach ($his_month as $ma => $mb) { ?>
		<td class="item" align="center"><?php echo make_edit($_hid, $mb, "h_renjun", $his_line[$ma]["h_renjun"]); ?></td>
<?php } ?>

		<td class="item" align="center"><?php echo make_edit($_hid, $date_y_m, "dabiaozhishu3", $line["dabiaozhishu3"]); ?></td>
		<td class="item" align="center"><?php echo make_edit($_hid, $date_y_m, "jianglijishu3", $line["jianglijishu3"]); ?></td>
		<td class="item" align="center"><?php echo make_edit($_hid, $date_y_m, "jianglizhibiao3", $line["jianglizhibiao3"]); ?></td>
		<td class="item" align="center" title="人均目标"><?php echo  make_edit($_hid, $date_y_m, "mubiao3", num($line["mubiao3"])); ?></td>
		<td class="item" align="center" style="background-color:<?php echo $line["比例颜色3"]; ?>"><?php echo num($line["本月完成比例3"]); ?></td>
		<td class="item" align="center"><?php echo num($line["预计3"]); ?></td>
		<td class="item" align="center" style="background-color:<?php echo $line["增幅颜色3"]; ?>"><?php echo num($line["增幅3"]); ?></td>
		<td class="item" align="center"><?php echo num($line["上月均值3"]); ?></td>
		<td class="item" align="center"><?php echo num($line["目前均值3"]); ?></td>
		<td class="item" align="center" style="color:red"><?php echo num($line["已完成3"]); ?></td>
	</tr>
<?php } ?>

</table>

<!-- 浮动表头，和主表头的结构要一样，且每个单元格都必须设置宽度 -->
<table width="100%" align="center" class="jiuzhen_t" id="float_head" style="display:none; border-bottom:0;">
	<tr>
		<td class="head" align="center" width="5%" rowspan="2">医院名称</td>
		<td class="head" align="center" width="5%" rowspan="2">负责人</td>
		<td class="head" align="center" width="5%" rowspan="2">统计项目</td>
		<td class="head" align="center" width="30%" colspan="6">最近历史记录</td>
		<td class="head" align="center" width="5%" rowspan="2">达标指数</td>
		<td class="head" align="center" width="5%" rowspan="2">奖励基数</td>
		<td class="head" align="center" width="5%" rowspan="2">奖励指标</td>
		<td class="head" align="center" width="5%" rowspan="2">目标</td>
		<td class="head" align="center" width="5%" rowspan="2">本月完成比例</td>
		<td class="head" align="center" width="5%" rowspan="2">预计</td>
		<td class="head" align="center" width="5%" rowspan="2">对比上月增幅</td>
		<td class="head" align="center" width="5%" rowspan="2">上月均值</td>
		<td class="head" align="center" width="5%" rowspan="2">目前均值</td>
		<td class="head" align="center" width="5%" rowspan="2" style="color:red">已完成</td>
		<td class="head" align="center" width="5%" rowspan="2">已过天数</td>
	</tr>
	<tr>
<?php foreach ($his_month as $v) { ?>
		<td class="head" align="center" width="5%" style="font-family:Tahoma; font-weight:normal"><?php echo $v; ?></td>
<?php } ?>
	</tr>
</table>

<br>

<div style="text-align:center"><a href="#">回顶部</a>&nbsp;</div>
<br>

<?php
if ($can_edit) {
	$s = $skiped_hid[$date_ym];
	if ($s != '') {
		$arr = explode(",", $s);
		foreach ($arr as $_h) {
			$h_name = $db->query("select name from hospital where id=$_h limit 1", 1, "name");
			$harr[] = '<a href="?op=remove_skip&m='.$date_ym.'&hid='.$_h.'" title="点击取消忽略">'.$h_name.'</a>';
		}
		echo "本月已被忽略的科室：".implode("、", $harr);
	}
}

?>


<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>

<div style="color:silver;">页面生成用时：<?php echo $t_used; ?></div>

<?php
echo "<pre>";
//print_r($skiped_hid);
//echo implode("<br>", $sql_time);
//print_r($_SESSION);
//print_r($data);
//print_r($res);
//print_r($pc);
//print_r($sj);
//print_r($wx);
//print_r($idata);

?>

</body>
</html>