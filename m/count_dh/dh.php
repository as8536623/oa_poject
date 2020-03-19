<?php
// --------------------------------------------------------
// - 功能说明 : 网络
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2014-2-26
// --------------------------------------------------------
require "../../core/core.php";
include "dh_config.php";


// 操作的处理:
if ($op == "edit") {
	include "dh.edit.php";
	exit;
}


// 计算统计数据:
$cal_field = explode(" ", "a1 a2 a3 a4 b1 b2 b3 b4 c1 c2 c3 c4 d1 d2 d3");


$bt = $b = date("Ymd", $date_time);
$et = $e = date("Ymd", $month_end);


$cur_kefu = $_GET["kefu"];

$con = "hid=$hid";

if ($sub_id > 0) {
	$con .= " and sub_id=$sub_id";
}

if ($cur_kefu != '') {
	$con .= " and kefu='$cur_kefu'";
}

// 处理字段:
$f_arr = array();
foreach ($cal_field as $v) {
	$f_arr[] = 'sum('.$v.') as '.$v;
}
$f_str = implode(", ", $f_arr);


//查询总医院汇总数据:
$list = $db->query("select date, $f_str from $table where $con and date>=$b and date<=$e group by date order by date asc,kefu asc", "date");

$dt_count = count($list);

// 计算数据:
foreach ($list as $k => $v) {
	$list[$k]["per_1"] = @round($v["b1"] / $v["a1"] * 100, 2);
	$list[$k]["per_2"] = @round($v["c1"] / $v["b1"] * 100, 2);
	$list[$k]["per_3"] = @round($v["d1"] / $v["c1"] * 100, 2);
	$list[$k]["per_4"] = @round($v["d1"] / $v["a1"] * 100, 2);
}

// 处理:
$sum_list = array();
foreach ($list as $v) {
	foreach ($cal_field as $f) {
		$sum_list[$f] = floatval($sum_list[$f]) + $v[$f];
	}
}

$sum_list["per_1"] = @round($sum_list["b1"] / $sum_list["a1"] * 100, 2);
$sum_list["per_2"] = @round($sum_list["c1"] / $sum_list["b1"] * 100, 2);
$sum_list["per_3"] = @round($sum_list["d1"] / $sum_list["c1"] * 100, 2);
$sum_list["per_4"] = @round($sum_list["d1"] / $sum_list["a1"] * 100, 2);




// 是否能添加或修改数据:
$can_edit_data = 0;

if ($debug_mode || in_array($uinfo["part_id"], array(9)) || check_power("edit") ) {
	$can_edit_data = 1;
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


// 页面开始 ------------------------
?>
<html>
<head>
<title>网络数据统计</title>
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

/*
#kf_select {height:25px; overflow:hidden; margin-top:10px; background:url("/res/img/tab_bg.jpg") repeat-x; }

.hs_tab_cur {margin-left:5px; float:left; }
.hs_tab_cur .hs_tab_left {float:left; width:3px; height:25px; background:url("/res/img/tab_cur_left.jpg") no-repeat; }
.hs_tab_cur .hs_tab_center {float:left; height:25px; background:url("/res/img/tab_cur_center.jpg") repeat-x; }
.hs_tab_cur .hs_tab_right {float:left; width:3px; height:25px; background:url("/res/img/tab_cur_right.jpg") no-repeat; }
.hs_tab_cur a {font-weight:bold; text-decoration:none; display:block; line-height:25px; padding:0 3px; color:red; }

.hs_tab_nor {margin-left:5px; float:left; }
.hs_tab_nor .hs_tab_left {float:left; width:3px; height:25px; background:url("/res/img/tab_nor_left.jpg") no-repeat; }
.hs_tab_nor .hs_tab_center {float:left; height:25px; background:url("/res/img/tab_nor_center.jpg") repeat-x; }
.hs_tab_nor .hs_tab_right {float:left; width:3px; height:25px; background:url("/res/img/tab_nor_right.jpg") no-repeat; }
.hs_tab_nor a {font-weight:normal; text-decoration:none; display:block; line-height:25px; padding:0 3px; }
*/

#kf_select {width:98%; margin-top:10px; }
.hs_cur {font-weight:bold; color:red; padding:0 3px; }
.hs_nor {padding:0 3px; }

.huizong {border:0; padding:5px 0 5px 10px; text-align:left; font-style:italic; background-color:#f0e1d7; }
</style>

<script language="javascript">
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

function edit(url, obj) {
	set_high_light(obj);
	parent.load_src(1, url, 800, 400);
	return false;
}

function show_data_detail() {
	parent.load_src(1, "/m/count_dh/show_detail_dh.php");
	return false;
}

function hgo(dir, o) {
	var obj = byid("h_id_select");
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

function edit_line(o, kefu, date) {
	parent.load_src(1, "/m/count_dh/dh.php?op=edit&kefu="+(kefu)+"&date="+(date), 900, 500);
	return false;
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
				<input type="hidden" name="kefu" value="<?php echo $_GET["kefu"]; ?>">
			</form>
		</td>

		<td width="" align="left">
			<div id="date_tips">项目：</div>
			<form id="xiangmu_change_001" method="GET">
<?php
$_out = array();
foreach ($sub_type_arr as $_id => $_name) {
	if ($sub_id == $_id) {
		$_out[] = '<a href="#" style="color:red; font-weight:bold;">'.$_name.'</a>';
	} else {
		$_out[] = '<a href="#" onclick="set_sub_id('.$_id.'); return false;">'.$_name.'</a>';
	}
}
if ($sub_id == 0) {
	$_out[] = '<a href="#" onclick="set_sub_id(0); return false;" style="color:red; font-weight:bold;" title="查看汇总数据">汇总</a>';
} else {
	$_out[] = '<a href="#" onclick="set_sub_id(0); return false;" style="color:#8000ff" title="查看汇总数据">汇总</a>';
}

echo implode(" <font color=silver>|</font> ", $_out);
?>

				<input type="hidden" name="sub_id" id="sub_id" value="">
				<input type="hidden" name="op" value="change_sub">
				<input type="hidden" name="date" value="<?php echo $_GET["date"]; ?>">
				<input type="hidden" name="kefu" value="<?php echo $_GET["kefu"]; ?>">
			</form>
			<script type="text/javascript">
			function set_sub_id(id) {
				byid("sub_id").value = id;
				byid("xiangmu_change_001").submit();
			}
			function show_huizong(hid) {
				//self.location = "/m/count_dh/web_huizong.php?hid="+hid;
			}
			</script>
		</td>

		<td width="" align="right">
		</td>
	</tr>
</table>

<table style="margin:10px 0 0 0px;" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="350" align="left">
			<div id="date_tips">医院：</div>
			<form method="GET">
				<select name="hid" id="h_id_select" class="combo" onchange="this.form.submit()">
					<option value="" style="color:gray">-请选择-</option>
					<?php echo list_option($types, "_key_", "_value_", $hid); ?>
				</select>&nbsp;
				<button class="button" onclick="hgo('up',this);">上</button>&nbsp;
				<button class="button" onclick="hgo('down',this);">下</button>&nbsp;&nbsp;
				<input type="hidden" name="op" value="change_type">
				<input type="hidden" name="date" value="<?php echo $_GET["date"]; ?>">
				<!-- <a href="set_sort.php" onclick="load_config(this.href, this); return false;">排序设置</a> -->
<?php if ($debug_mode || $uinfo["part_id"] == 9 || check_power("check")) { ?>
				<script type="text/javascript">
				function set_kf() {
					parent.load_src(1, "/m/count_dh/set_kefu.php", 700, 400);
					return false;
				}
				</script>
				<nobr><a class="" href="javascript:;" onclick="set_kf();">设置客服</a></nobr>
			<!-- <nobr><a class="" href="">设置管理员</a></nobr> -->
<?php } ?>
			</form>
		</td>

		<td align="center">
			&nbsp;
			<!-- <button onclick="location='web_compare.php'" class="buttonb" title="查看客服数据对比">数据对比</button>&nbsp;&nbsp;
			<button onclick="location='web_compare_week.php?month=<?php echo date("Y-m", $date_time); ?>'" class="buttonb" title="查看周数据对比">周对比</button>&nbsp;&nbsp;
			<button onclick="location='web_report.php'" class="buttonb" title="查看统计数据">统计数据</button>&nbsp;&nbsp;
			<button onclick="location='web_chart.php'" class="buttonb" title="查看趋势图">趋势图</button>&nbsp;&nbsp; -->
		</td>

		<td width="350" align="right">
<?php if ($debug_mode || $uinfo["part_id"] == 9) { ?>
			<!-- <a href="?op=log" target="_blank">查看/搜索日志</a>&nbsp;&nbsp;
			<a href="javascript:;" onclick="show_data_detail();">数据明细</a>&nbsp;&nbsp; -->
<?php } ?>

<?php if ($debug_mode || $username == "admin") { ?>
			<!-- <a href="config.php" onclick="load_config(this.href, this); return false;">设置</a>&nbsp;&nbsp; -->
<?php } ?>

			<!-- <a href="web_yang.php" onclick="load_yang(this.href, this); return false;">报表</a>&nbsp;&nbsp; -->

			<a href="#refresh" onclick="self.location.reload(); return false;" title="刷新当前页面">刷新</a>&nbsp;&nbsp;
		</td>
	</tr>
</table>

<div id="kf_select">
	<div id="date_tips">客服：</div>
<?php
array_unshift($kefu_list, "");
foreach ($kefu_list as $_kfname) {
	$_class = $_kfname == $_GET["kefu"] ? "hs_cur" : "hs_nor";
	$kf_show_name = $_kfname;
	if ($_kfname == '') $kf_show_name = "全部";
?>
	<nobr><a class="<?php echo $_class; ?>" href="?date=<?php echo $_GET["date"]; ?>&kefu=<?php echo urlencode($_kfname); ?>" onfocus="this.blur()"><?php echo $kf_show_name; ?></a></nobr>
<?php
	}
?>

</div>

<div class="main_title"><?php echo $h_name; ?> <?php echo date("Y-n", $date_time); ?> <?php echo $_GET["kefu"] ? $_GET["kefu"] : "所有客服"; ?>  <?php echo $sub_name; ?> 统计数据</div>

<!-- 浮动表头 注意：此技术需要指定每个单元格的宽度否则上下表格可能不对齐 -->
<table id="float_head" style="display:none; border-bottom:0;" width="100%" align="center" class="list">
	<tr>
		<td class="head" style="width:5%;" align="center">日期</td>

		<td class="head" style="width:5%;" align="center" style="color:red">总电话</td>
		<td class="head" style="width:5%;" align="center">已接</td>
		<td class="head" style="width:5%;" align="center">未接</td>
		<td class="head" style="width:5%;" align="center">网电合作</td>

		<td class="head" style="width:5%;" align="center" style="color:red">总有效</td>
		<td class="head" style="width:4%;" align="center">已接</td>
		<td class="head" style="width:4%;" align="center">未接</td>
		<td class="head" style="width:5%;" align="center">网电合<br>作有效</td>

		<td class="head" style="width:5%;" align="center" style="color:red">总预约</td>
		<td class="head" style="width:4%;" align="center">已接</td>
		<td class="head" style="width:4%;" align="center">未接</td>
		<td class="head" style="width:5%;" align="center">网电合<br>作预约</td>

		<td class="head" style="width:5%;" align="center" style="color:red">总就诊</td>
		<td class="head" style="width:5%;" align="center">电话<br>就诊</td>
		<td class="head" style="width:5%;" align="center">网电合<br>作就诊</td>

		<td class="head" style="width:5%;" align="center" style="color:red">有效<br>咨询率</td>
		<td class="head" style="width:5%;" align="center" style="color:red">有效<br>预约率</td>
		<td class="head" style="width:5%;" align="center" style="color:red">预约<br>就诊率</td>
		<td class="head" style="width:5%;" align="center" style="color:red">咨询<br>就诊率</td>

		<td class="head" style="width:4%;" align="center">操作</td>
	</tr>
</table>

<table id="data_list" width="100%" align="center" class="list">
	<tr id="data_head">
		<td class="head" style="width:5%;" align="center">日期</td>

		<td class="head" style="width:5%;" align="center" style="color:red">总电话</td>
		<td class="head" style="width:5%;" align="center">已接</td>
		<td class="head" style="width:5%;" align="center">未接</td>
		<td class="head" style="width:5%;" align="center">网电<br>合作</td>

		<td class="head" style="width:5%;" align="center" style="color:red">总有效</td>
		<td class="head" style="width:4%;" align="center">已接</td>
		<td class="head" style="width:4%;" align="center">未接</td>
		<td class="head" style="width:5%;" align="center">网电合<br>作有效</td>

		<td class="head" style="width:5%;" align="center" style="color:red">总预约</td>
		<td class="head" style="width:4%;" align="center">已接</td>
		<td class="head" style="width:4%;" align="center">未接</td>
		<td class="head" style="width:5%;" align="center">网电合<br>作预约</td>

		<td class="head" style="width:5%;" align="center" style="color:red">总就诊</td>
		<td class="head" style="width:5%;" align="center">电话<br>就诊</td>
		<td class="head" style="width:5%;" align="center">网电合<br>作就诊</td>

		<td class="head" style="width:5%;" align="center" style="color:red">有效<br>咨询率</td>
		<td class="head" style="width:5%;" align="center" style="color:red">有效<br>预约率</td>
		<td class="head" style="width:5%;" align="center" style="color:red">预约<br>就诊率</td>
		<td class="head" style="width:5%;" align="center" style="color:red">咨询<br>就诊率</td>

		<td class="head" style="width:4%;" align="center">操作</td>
	</tr>

<?php
$rijun_days = 0;
foreach ($d_array as $i) {
	$cur_date = date("Ymd", strtotime(date("Y-m-", $date_time).$i." 0:0:0"));
	$li = $list[$cur_date];
	if (!is_array($li)) {
		$li = array();
	}
	$mode = "add";
	if (!empty($li)) {
		$mode = "edit";
	}
	$li["kf_click"] = $day_count[$cur_date]["click_all"];
	$li["zero_talk"] = $day_count[$cur_date]["zero_talk"];
	$li["wangcha"] = $day_count[$cur_date]["wangcha"];

	// 日均的天数统计:
	if (intval($li["a1"]) + intval($li["b1"]) + intval($li["c1"]) + intval($li["d1"]) > 0) {
		$rijun_days += 1;
	}

?>

	<tr>
		<td class="item" align="center"><?php //echo date("n", $date_time); ?><?php echo $i; ?>日</td>

		<td class="item" align="center" style="color:red"><?php echo $li["a1"]; ?></td>
		<td class="item" align="center"><?php echo $li["a2"]; ?></td>
		<td class="item" align="center"><?php echo $li["a3"]; ?></td>
		<td class="item" align="center"><?php echo $li["a4"]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo $li["b1"]; ?></td>
		<td class="item" align="center"><?php echo $li["b2"]; ?></td>
		<td class="item" align="center"><?php echo $li["b3"]; ?></td>
		<td class="item" align="center"><?php echo $li["b4"]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo $li["c1"]; ?></td>
		<td class="item" align="center"><?php echo $li["c2"]; ?></td>
		<td class="item" align="center"><?php echo $li["c3"]; ?></td>
		<td class="item" align="center"><?php echo $li["c4"]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo $li["d1"]; ?></td>
		<td class="item" align="center"><?php echo $li["d3"]; ?></td>
		<td class="item" align="center"><?php echo $li["d2"]; ?></td>


		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_1"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_2"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_3"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_4"]); ?>%</td>

		<td class="item" align="center">
<?php if ($sub_id > 0 && $cur_kefu != '' && $can_edit_data) { ?>
			<a href="javascript:;" onclick="edit_line(this, '<?php echo urlencode($cur_kefu); ?>', '<?php echo date("Y-m-", $date_time).$i; ?>'); return false;">修改</a>
<?php } else { ?>
			<font color="gray" title="选择具体客服后修改">--</font>
<?php } ?>
		</td>
	</tr>

<?php } ?>


	<tr>
		<td colspan="25" class="huizong">数据汇总 (<?php echo $rijun_days; ?>天)</td>
	</tr>

	<tr>
		<td class="item" align="center">汇总</td>

		<td class="item" align="center" style="color:red"><?php echo $sum_list["a1"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["a2"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["a3"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["a4"]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo $sum_list["b1"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["b2"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["b3"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["b4"]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo $sum_list["c1"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["c2"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["c3"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["c4"]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo $sum_list["d1"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["d3"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["d2"]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo @floatval($sum_list["per_1"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo @floatval($sum_list["per_2"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo @floatval($sum_list["per_3"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo @floatval($sum_list["per_4"]); ?>%</td>

		<td class="item" align="center">-</td>
	</tr>

	<tr>
		<td class="item" align="center">日均</td>

		<td class="item" align="center" style="color:red"><?php echo @round($sum_list["a1"] / $rijun_days, 1); ?></td>
		<td class="item" align="center"><?php echo @round($sum_list["a2"] / $rijun_days, 1); ?></td>
		<td class="item" align="center"><?php echo @round($sum_list["a3"] / $rijun_days, 1); ?></td>
		<td class="item" align="center"><?php echo @round($sum_list["a4"] / $rijun_days, 1); ?></td>

		<td class="item" align="center" style="color:red"><?php echo @round($sum_list["b1"] / $rijun_days, 1); ?></td>
		<td class="item" align="center"><?php echo @round($sum_list["b2"] / $rijun_days, 1); ?></td>
		<td class="item" align="center"><?php echo @round($sum_list["b3"] / $rijun_days, 1); ?></td>
		<td class="item" align="center"><?php echo @round($sum_list["b4"] / $rijun_days, 1); ?></td>

		<td class="item" align="center" style="color:red"><?php echo @round($sum_list["c1"] / $rijun_days, 1); ?></td>
		<td class="item" align="center"><?php echo @round($sum_list["c2"] / $rijun_days, 1); ?></td>
		<td class="item" align="center"><?php echo @round($sum_list["c3"] / $rijun_days, 1); ?></td>
		<td class="item" align="center"><?php echo @round($sum_list["c4"] / $rijun_days, 1); ?></td>

		<td class="item" align="center" style="color:red"><?php echo @round($sum_list["d1"] / $rijun_days, 1); ?></td>
		<td class="item" align="center"><?php echo @round($sum_list["d3"] / $rijun_days, 1); ?></td>
		<td class="item" align="center"><?php echo @round($sum_list["d2"] / $rijun_days, 1); ?></td>

		<td class="item" align="center" style="color:red"></td>
		<td class="item" align="center" style="color:red"></td>
		<td class="item" align="center" style="color:red"></td>
		<td class="item" align="center" style="color:red"></td>

		<td class="item" align="center">-</td>
	</tr>

</table>

<br>
<a name="bottom"></a>

<div id="kf_select">
	<div id="date_tips">客服：</div>
<?php
foreach ($kefu_list as $_kfname) {
	$_class = $_kfname == $_GET["kefu"] ? "hs_cur" : "hs_nor";
	$kf_show_name = $_kfname;
	if ($_kfname == '') $kf_show_name = "全部";
?>
	<nobr><a class="<?php echo $_class; ?>" href="?date=<?php echo $_GET["date"]; ?>&kefu=<?php echo urlencode($_kfname); ?>" onfocus="this.blur()"><?php echo $kf_show_name; ?></a></nobr>
<?php
	}
?>
</div>
<br>
<br>
<center><a href="#">返回页面顶部</a></center>
<br>
<br>
<br>

</body>
</html>