<?php
// --------------------------------------------------------
// - 功能说明 : 网络
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2010-10-19
// --------------------------------------------------------
require "../../core/core.php";
include "web_config.php";


// 操作的处理:
if ($op) {
	if ($op == "edit_yuyue") {
		include "web.edit_yuyue.php";
		exit;
	}

	if ($op == "edit_fangke") {
		include "web.edit_fangke.php";
		exit;
	}

	if ($op == "edit_multi") {
		include "web.edit_multi.php";
		exit;
	}

	if ($op == "log") {
		include "web.log.php";
		exit;
	}

	if ($op == "repeatcheck") {
		include "web.repeatcheck.php";
		exit;
	}

	if ($op == "delete") {
		$ids = explode(",", $_GET["id"]);
		$del_ok = $del_bad = 0; $op_data = array();
		foreach ($ids as $opid) {
			if (($opid = intval($opid)) > 0) {
				$tmp_data = $db->query_first("select * from $table where id='$opid' limit 1");
				if ($db->query("delete from $table where id='$opid' limit 1")) {
					$del_ok++;
					$op_data[] = $tmp_data;
				} else {
					$del_bad++;
				}
			}
		}

		if ($del_ok > 0) {
			$log->add("delete", "删除数据", serialize($op_data));
		}

		if ($del_bad > 0) {
			msg_box("删除成功 $del_ok 条资料，删除失败 $del_bad 条资料。", "back", 1);
		} else {
			msg_box("删除成功", "back", 1);
		}
	}

	if ($op == "repeat_del") {
		$rc = $_GET["str"];
		if ($rc != '') {
			list($a, $b, $c) = explode("_", $rc, 3);
			$rs_arr = $db->query("select * from count_web where type_id=".intval($a)." and date=".intval($b)." and kefu='".$c."' order by id asc", "");
			if (count($rs_arr) > 1) {
				for ($i = 1; $i < count($rs_arr); $i++) {

					// 备份是必须的:
					$back = @serialize($rs_arr[$i]);
					@file_put_contents("repeat_log.txt", date("Y-m-d H:i:s ").$realname." ".$back."\r\n", FILE_APPEND);

					$cur_id = $rs_arr[$i]["id"];
					$db->query("delete from count_web where id=$cur_id limit 1");
				}
				msg_box("处理成功", "back", 1, 2);
			} else {
				exit_html("未查询到重复数据，请联系其他管理人员确认是否已经被处理过了。");
			}
		} else {
			exit_html("参数不正确");
		}
		exit;
	}

}


// 计算统计数据:
$cal_field = explode(" ", "click click_local click_other zero_talk ok_click ok_click_local ok_click_other talk talk_local talk_other orders order_local order_other come come_local come_other");


$bt = $b = date("Ymd", $date_time);
$et = $e = date("Ymd", $month_end);


$cur_kefu = $_GET["kefu"];
if ($cur_kefu) {
	// 查询单个客服数据:
	$list = $db->query("select * from $table where hid=$hid and sub_id=$sub_id and kefu='$cur_kefu' and date>=$b and date<=$e order by date asc,kefu asc", "date");

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

	// 处理:
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

} else {

	// 处理字段:
	$f_arr = array();
	foreach ($cal_field as $v) {
		$f_arr[] = 'sum('.$v.') as '.$v;
	}
	$f_str = implode(", ", $f_arr);


	//查询总医院汇总数据:
	$list = $db->query("select date, $f_str from $table where hid=$hid and sub_id=$sub_id and date>=$b and date<=$e group by date order by date asc,kefu asc", "date");

	$dt_count = count($list);

	// 计算汇总:
	/*
	$list = $dt_count = array();
	foreach ($tmp_list as $v) {
		$dt = $v["date"];
		$dt_count[$dt] += 1;
		foreach ($v as $a => $b) {
			if ($b && is_numeric($b)) {
				$list[$dt][$a] = floatval($list[$dt][$a]) + $b;
			}
		}
	}
	*/

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

	// 处理:
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

}


// 当日总数据:
$day_count = $db->query("select date,click_all,zero_talk,wangcha from count_web_day where hid='$hid' and sub_id='$sub_id' and date>=$bt and date<=$et", "date");

foreach ($day_count as $v) {
	$sum_list["click_all"] += $v["click_all"];
	$sum_list["zero_talk"] += $v["zero_talk"];
	$sum_list["wangcha"] += $v["wangcha"];
}


// 是否能添加或修改数据:
$can_edit_data = 0;
if ($debug_mode || in_array($uinfo["part_id"], array(9)) || in_array($uid, explode(",", $type_detail["uids"]))) {
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
.hs_cur {font-weight:bold; color:red !important; padding:0 3px; }
.hs_nor {padding:0 3px; }

.a_kefu {color:; }
.a_kefu_dy {color:#ff59ff; }
.a_kefu_sx {color:#0000ff; }

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

function data_edit(link, o) {
	set_high_light('');
	parent.load_src(1, link, 600, 250);
	return false;
}

function load_config(link, o) {
	parent.load_src(1, link, 600, 250);
	return false;
}

function load_yang(link, o) {
	parent.load_src(1, link, 600, 400);
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
	parent.load_src(1, "/m/count/show_detail_web.php");
	return false;
}

function repeat_check_confirm() {
	return confirm("该功能数据量大加载比较缓慢，是否确定要打开？如果点“确定”打开，请耐心等待。");
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
$_out[] = '<a href="#" onclick="show_huizong('.$hid.'); return false;" style="color:#8000ff" title="查看汇总数据">汇总</a>';

echo implode(" <font color=silver>|</font> ", $_out);
?>

				<input type="hidden" name="sub_id" id="sub_id" value="">
				<input type="hidden" name="op" value="change_sub">
				<input type="hidden" name="date" value="<?php echo $_GET["date"]; ?>">
			</form>
			<script type="text/javascript">
			function set_sub_id(id) {
				byid("sub_id").value = id;
				byid("xiangmu_change_001").submit();
			}
			function show_huizong(hid) {
				self.location = "/m/count/web_huizong.php?hid="+hid;
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
				<a href="set_sort.php" onclick="load_config(this.href, this); return false;">排序设置</a>
			</form>
		</td>

		<td align="center">
			<button onclick="location='web_compare.php'" class="buttonb" title="查看客服数据对比">数据对比</button>&nbsp;&nbsp;
			<button onclick="location='web_compare_week.php?month=<?php echo date("Y-m", $date_time); ?>'" class="buttonb" title="查看周数据对比">周对比</button>&nbsp;&nbsp;
			<button onclick="location='web_report.php'" class="buttonb" title="查看统计数据">统计数据</button>&nbsp;&nbsp;
			<button onclick="location='web_chart.php'" class="buttonb" title="查看趋势图">趋势图</button>&nbsp;&nbsp;
		</td>

		<td width="350" align="right">
<?php if ($debug_mode || $uinfo["part_id"] == 9) { ?>
			<a href="?op=log" target="_blank">查看/搜索日志</a>&nbsp;&nbsp;
			<a href="javascript:;" onclick="show_data_detail();">数据明细</a>&nbsp;&nbsp;
<?php } ?>

<?php if ($debug_mode || $username == "admin") { ?>
			<a href="config.php" onclick="load_config(this.href, this); return false;">设置</a>&nbsp;&nbsp;
<?php } ?>

<?php if ($debug_mode || $username == "admin" || check_power("report")) { ?>
			<a href="web_yang.php" onclick="load_yang(this.href, this); return false;">报表</a>&nbsp;&nbsp;
<?php } ?>

			<a href="#refresh" onclick="self.location.reload(); return false;" title="刷新当前页面">刷新</a>&nbsp;&nbsp;
		</td>
	</tr>
</table>

<div id="kf_select">
	<div id="date_tips">客服：</div>
<?php
array_unshift($kefu_list, "");
foreach ($kefu_list as $_kfname) {
	$base_class = $kefu_class_arr[$_kfname];
	$tips = "白班客服";
	if ($base_class == "a_kefu_dy") $tips = "大夜班客服";
	if ($base_class == "a_kefu_sx") $tips = "实习客服";
	$_class = $_kfname == $_GET["kefu"] ? "hs_cur" : "hs_nor";
	$kf_show_name = $_kfname;
	if ($_kfname == '') $kf_show_name = "全部";
?>
	<nobr><a class="<?php echo $_class." ".$base_class; ?>" title="<?php echo $tips; ?>" href="?date=<?php echo $_GET["date"]; ?>&kefu=<?php echo urlencode($_kfname); ?>" onfocus="this.blur()"><?php echo $kf_show_name; ?></a></nobr>
<?php
	}
?>
</div>

<div class="main_title"><?php echo $h_name." ".$sub_name; ?> - <?php echo date("Y-n", $date_time); ?> 网络统计数据</div>

<!-- 浮动表头 注意：此技术需要指定每个单元格的宽度否则上下表格可能不对齐 -->
<table id="float_head" style="display:none; border-bottom:0;" width="100%" align="center" class="list">
	<tr>
		<td class="head" style="width:6%;" align="center">日期</td>

		<td class="head" style="width:4%;" align="center" style="color:red">系统<br>总点击</td>
		<td class="head" style="width:4%;" align="center" style="color:red">总点击</td>
		<td class="head" style="width:3%;" align="center">本地</td>
		<td class="head" style="width:3%;" align="center">外地</td>
		<td class="head" style="width:4%;" align="center" style="color:red">总有效</td>
		<td class="head" style="width:3%;" align="center">本地</td>
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

		<td class="head" style="width:5%;" align="center" style="color:red">咨询<br>预约率</td>
		<td class="head" style="width:5%;" align="center" style="color:red">预到<br>就诊率</td>
		<td class="head" style="width:5%;" align="center" style="color:red">咨询<br>就诊率</td>
		<td class="head" style="width:5%;" align="center" style="color:red">有效<br>咨询率</td>
		<td class="head" style="width:5%;" align="center" style="color:red">有效<br>预约率</td>

		<td class="head" style="width:7%;" align="center">操作</td>
	</tr>
</table>

<table id="data_list" width="100%" align="center" class="list">
	<tr id="data_head">
		<td class="head" style="width:6%;" align="center">日期</td>

		<td class="head" style="width:4%;" align="center" style="color:red">系统<br>总点击</td>
		<td class="head" style="width:4%;" align="center" style="color:red">总点击</td>
		<td class="head" style="width:3%;" align="center">本地</td>
		<td class="head" style="width:3%;" align="center">外地</td>
		<td class="head" style="width:4%;" align="center" style="color:red">总有效</td>
		<td class="head" style="width:3%;" align="center">本地</td>
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

		<td class="head" style="width:5%;" align="center" style="color:red">咨询<br>预约率</td>
		<td class="head" style="width:5%;" align="center" style="color:red">预到<br>就诊率</td>
		<td class="head" style="width:5%;" align="center" style="color:red">咨询<br>就诊率</td>
		<td class="head" style="width:5%;" align="center" style="color:red">有效<br>咨询率</td>
		<td class="head" style="width:5%;" align="center" style="color:red">有效<br>预约率</td>

		<td class="head" style="width:7%;" align="center">操作</td>
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
	if (intval($li["click"]) + intval($li["ok_click"]) + intval($li["talk"]) + intval($li["orders"]) + intval($li["come"]) > 0) {
		$rijun_days += 1;
	}

?>

	<tr>
		<td class="item" align="center"><?php echo date("n", $date_time); ?>月<?php echo $i; ?>日</td>
		<td class="item" align="center" style="color:red">
<?php if ($uinfo["character_id"] == 16 || $uinfo["character_id"] == 28 || check_power("fangke") || $debug_mode) { ?>
			<a href="data_edit.php?type=click_all&date=<?php echo $cur_date; ?>" onclick="data_edit(this.href,this);return false;"><?php echo $li["kf_click"] ? $li["kf_click"] : '(添加)'; ?></a>
<?php } else { ?>
			<?php echo $li["kf_click"] ? $li["kf_click"] : ''; ?>
<?php } ?>
		</td>
		<td class="item" align="center" style="color:red"><?php echo $li["click"]; ?></td>
		<td class="item" align="center"><?php echo $li["click_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["click_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["ok_click"]; ?></td>
		<td class="item" align="center"><?php echo $li["ok_click_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["ok_click_other"]; ?></td>

		<td class="item" align="center" style="color:red">
<?php if ($uinfo["character_id"] == 16 || $uinfo["character_id"] == 28 || check_power("fangke") || $debug_mode) { ?>
			<a href="data_edit.php?type=zero_talk&date=<?php echo $cur_date; ?>" onclick="data_edit(this.href,this);return false;"><?php echo $li["zero_talk"] ? $li["zero_talk"] : '(添加)'; ?></a>
<?php } else { ?>
			<?php echo $li["zero_talk"] ? $li["zero_talk"] : ''; ?>
<?php } ?>
		</td>

		<td class="item" align="center" style="color:red"><?php echo $li["talk"]; ?></td>
		<td class="item" align="center"><?php echo $li["talk_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["talk_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["orders"]; ?></td>
		<td class="item" align="center"><?php echo $li["order_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["order_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["come"]; ?></td>
		<td class="item" align="center"><?php echo $li["come_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["come_other"]; ?></td>

		<td class="item" align="center" style="color:red">
<?php if ($uinfo["character_id"] == 16 || $uinfo["character_id"] == 23 || check_power("yuyue") || $debug_mode) { ?>
			<a href="data_edit.php?type=wangcha&date=<?php echo $cur_date; ?>" onclick="data_edit(this.href,this);return false;"><?php echo $li["wangcha"] ? $li["wangcha"] : '(添加)'; ?></a>
<?php } else { ?>
			<?php echo $li["wangcha"] ? $li["wangcha"] : ''; ?>
<?php } ?>
		</td>

		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_1"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_2"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_3"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_4"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_5"]); ?>%</td>

		<td class="item" align="center">
<?php if ($cur_kefu && $can_edit_data) { ?>

<?php if ($uinfo["character_id"] == 16 || $uinfo["character_id"] == 28 || check_power("fangke") || $debug_mode) { ?>
			<a href="?op=edit_fangke&kefu=<?php echo urlencode($cur_kefu); ?>&date=<?php echo date("Y-m-", $date_time).$i; ?>" onclick="edit(this.href, this);return false;" title="修改访客点击数据">点击</a>
<?php } ?>

<?php if ($uinfo["character_id"] == 16 || $uinfo["character_id"] == 23 || check_power("yuyue") || $debug_mode) { ?>
			<a href="?op=edit_yuyue&kefu=<?php echo urlencode($cur_kefu); ?>&date=<?php echo date("Y-m-", $date_time).$i; ?>" onclick="edit(this.href, this);return false;" title="修改预约|到院数据">到院</a>
<?php } ?>

<?php } ?>
		</td>
	</tr>

<?php } ?>



	<tr>
		<td colspan="25" class="huizong">数据汇总 (<?php echo $rijun_days; ?>天)</td>
	</tr>

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

		<td class="item" align="center">
			-
		</td>
	</tr>

	<tr>
		<td class="item" align="center">日均</td>
		<td class="item" align="center" style="color:red"><?php echo @round($sum_list["click_all"] / $rijun_days, 1); ?></td>
		<td class="item" align="center" style="color:red"><?php echo @round($sum_list["click"] / $rijun_days, 1); ?></td>
		<td class="item" align="center"><?php echo @round($sum_list["click_local"] / $rijun_days, 1); ?></td>
		<td class="item" align="center"><?php echo @round($sum_list["click_other"] / $rijun_days, 1); ?></td>
		<td class="item" align="center" style="color:red"><?php echo @round($sum_list["ok_click"] / $rijun_days, 1); ?></td>
		<td class="item" align="center"><?php echo @round($sum_list["ok_click_local"] / $rijun_days, 1); ?></td>
		<td class="item" align="center"><?php echo @round($sum_list["ok_click_other"] / $rijun_days, 1); ?></td>
		<td class="item" align="center" style="color:red"><?php echo @round($sum_list["zero_talk"] / $rijun_days, 1); ?></td>

		<td class="item" align="center" style="color:red"><?php echo @round($sum_list["talk"] / $rijun_days, 1); ?></td>
		<td class="item" align="center"><?php echo @round($sum_list["talk_local"] / $rijun_days, 1); ?></td>
		<td class="item" align="center"><?php echo @round($sum_list["talk_other"] / $rijun_days, 1); ?></td>
		<td class="item" align="center" style="color:red"><?php echo @round($sum_list["orders"] / $rijun_days, 1); ?></td>
		<td class="item" align="center"><?php echo @round($sum_list["order_local"] / $rijun_days, 1); ?></td>
		<td class="item" align="center"><?php echo @round($sum_list["order_other"] / $rijun_days, 1); ?></td>
		<td class="item" align="center" style="color:red"><?php echo @round($sum_list["come"] / $rijun_days, 1); ?></td>
		<td class="item" align="center"><?php echo @round($sum_list["come_local"] / $rijun_days, 1); ?></td>
		<td class="item" align="center"><?php echo @round($sum_list["come_other"] / $rijun_days, 1); ?></td>
		<td class="item" align="center" style="color:red"><?php echo @round($sum_list["wangcha"] / $rijun_days, 1); ?></td>

		<td class="item" align="center" style="color:red"></td>
		<td class="item" align="center" style="color:red"></td>
		<td class="item" align="center" style="color:red"></td>
		<td class="item" align="center" style="color:red"></td>
		<td class="item" align="center" style="color:red"></td>

		<td class="item" align="center">
			-
		</td>
	</tr>



</table>

<br>
<a name="bottom"></a>

<div id="kf_select">
	<div id="date_tips">客服：</div>
<?php
foreach ($kefu_list as $_kfname) {
	$base_class = $kefu_class_arr[$_kfname];
	$_class = $_kfname == $_GET["kefu"] ? "hs_cur" : "hs_nor";
	$kf_show_name = $_kfname;
	if ($_kfname == '') $kf_show_name = "全部";
?>
	<nobr><a class="<?php echo $_class." ".$base_class; ?>" href="?date=<?php echo $_GET["date"]; ?>&kefu=<?php echo urlencode($_kfname); ?>" onfocus="this.blur()"><?php echo $kf_show_name; ?></a></nobr>
<?php
	}
?>
</div>
<br>
<br>
<center><a href="#">返回页面顶部</a></center>
<br>
<div class="rate_tips">
咨询预约率 = 预约人数 / 总点击<br>
预到就诊率 = 实际到院人数 / 预计到院人数<br>
咨询就诊率 = 实际到院人数 / 总点击<br>
有效咨询率 = 有效点击 / 总点击<br>
有效预约率 = 预约人数 / 有效点击<br>
</div>
<br>
<br>

</body>
</html>