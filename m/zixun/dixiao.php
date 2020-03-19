<?php
// --------------------------------------------------------
// - 功能说明 : 低效人员分析
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2013-4-25
// --------------------------------------------------------
require "../../core/core.php";
$table = "count_web";

// 所有可管理项目:
if ($debug_mode || in_array($uinfo["part_id"], array(9))) {
	$types = $db->query("select id,name from count_type where type='web' order by sort desc, id asc", "id", "name");
} else {
	$hids = implode(",", $hospital_ids);
	$types = $db->query("select id,name from count_type where type='web' and hid in ($hids) order by sort desc, id asc", "id", "name");
}
if (count($types) == 0) {
	exit("没有可以管理的项目");
}

$cur_type = $_SESSION["count_type_id_web"];
if (!$cur_type) {
	$type_ids = array_keys($types);
	$cur_type = $_SESSION["count_type_id_web"] = $type_ids[0];
}

// 操作的处理:
if ($op = $_REQUEST["op"]) {
	if ($op == "change_type") {
		$cur_type = $_SESSION["count_type_id_web"] = intval($_GET["type_id"]);
	}
}

$type_detail = $db->query("select * from count_type where id=$cur_type limit 1", 1);
$cur_kefu_list = $type_detail["kefu"] ? explode(",", $type_detail["kefu"]) : array();
$kefu_list = array();


// 误差指标：
$dixiao_line_set = $_COOKIE["dixiao_line_set"] ? $_COOKIE["dixiao_line_set"] : "2"; // 默认2%


// 从当前月往前xx个月:
$base_month = mktime(0,0,0,date("m"), 1); //本月1号
$m_arr = array();
//$m_arr[] = date("Ym", strtotime("+1 month", $base_month)); //下个月
$m_arr[] = date("Ym", $base_month); //本月
for($i = 1; $i <= 12; $i++) {
	$m = date("Ym", strtotime("-".$i." month", $base_month));
	$m_arr[] = $m;
}


$week_arr = array(
	1 => "1-7",
	2 => "8-15",
	3 => "16-21",
	4 => "22-31",
);


// 默认月份：
if ($_GET["month"] == '') {
	$_GET["month"] = date("Ym");
}

// 默认第一周:
if ($_GET["week"] == '') {
	$_GET["week"] = 1;
}


if ($_GET["month"] != '' && $_GET["week"] != '') {
	$m = _int_month_to_month($_GET["month"]);

	$w = $week_arr[intval($_GET["week"])];
	list($wa, $wb) = explode("-", $w);

	$date_btime = $m."-".$wa;
	$date_etime = $m."-".$wb;
}


$int_month = intval($_GET["month"]);
$int_week = intval($_GET["week"]);

//echo $date_btime;
//echo $date_etime;



// 处理数据:
if ($cur_type && $date_btime && $date_etime) {

	// 时间段:
	$btime = strtotime($date_btime." 0:0:0");
	$etime = strtotime($date_etime." 23:59:59");

	$b = date("Ymd", $btime);
	$e = date("Ymd", $etime);

	// 咨询备注:
	$memo = $db->query("select * from count_memo where type_id='$cur_type' and month='$int_month' and week='$int_week'", "kefu");

	//查询总医院汇总数据:
	$tmp_list = $db->query("select * from $table where type_id=$cur_type and date>=$b and date<=$e order by kefu asc,date asc");

	// 计算汇总:
	$list = $dt_count = array();
	foreach ($tmp_list as $v) {
		$dt = $v["kefu"];
		if (!in_array($dt, $kefu_list)) {
			$kefu_list[] = $dt;
		}
		$dt_count[$dt] += 1;
		foreach ($v as $x => $y) {
			if ($y && is_numeric($y)) {
				$list[$dt][$x] = floatval($list[$dt][$x]) + $y;
			}
		}
	}

	// 计算数据:
	foreach ($list as $k => $v) {
		// 咨询预约率:
		$list[$k]["per_1"] = @round($v["talk"] / $v["click"] * 100, 1);
		// 预到就诊率:
		$list[$k]["per_2"] = @round($v["come"] / $v["orders"] * 100, 1);
		// 咨询就诊率:
		$list[$k]["per_3"] = @round($v["come"] / $v["click"] * 100, 1);
		$list[$k]["per_31"] = @round($v["come_local"] / $v["click_local"] * 100, 1);
		$list[$k]["per_32"] = @round($v["come_other"] / $v["click_other"] * 100, 1);
		// 有效咨询率:
		$list[$k]["per_4"] = @round($v["ok_click"] / $v["click"] * 100, 1);
		// 有效预约率:
		$list[$k]["per_5"] = @round($v["talk"] / $v["ok_click"] * 100, 1);
	}

	// 计算统计数据:
	$cal_field = explode(" ", "click click_local click_other zero_talk ok_click ok_click_local ok_click_other talk talk_local talk_other orders order_local order_other come come_local come_other");
	// 处理:
	$sum_list = array();
	foreach ($list as $v) {
		foreach ($cal_field as $f) {
			$sum_list[$f] = floatval($sum_list[$f]) + $v[$f];

			// 咨询预约率:
			$sum_list["per_1"] = @round($sum_list["talk"] / $sum_list["click"] * 100, 1);
			// 预到就诊率:
			$sum_list["per_2"] = @round($sum_list["come"] / $sum_list["orders"] * 100, 1);
			// 咨询就诊率:
			$sum_list["per_3"] = @round($sum_list["come"] / $sum_list["click"] * 100, 1);
			$sum_list["per_31"] = @round($sum_list["come_local"] / $sum_list["click_local"] * 100, 1);
			$sum_list["per_32"] = @round($sum_list["come_other"] / $sum_list["click_other"] * 100, 1);
			// 有效咨询率:
			$sum_list["per_4"] = @round($sum_list["ok_click"] / $sum_list["click"] * 100, 1);
			// 有效预约率:
			$sum_list["per_5"] = @round($sum_list["talk"] / $sum_list["ok_click"] * 100, 1);
		}
	}


	// 统计该医院竞价总成本，用于计算人均成本:
	$_hid = $type_detail["hid"];
	$zongchengben = 0;
	if ($_hid > 0) {
		$zongchengben = $db->query("select sum(xiaofei) as xiaofei from jingjia_xiaofei where hid=$_hid and date>=$b and date<=$e", 1, "xiaofei");
		$sql1 = $db->sql;
	}
}


// 是否能添加或修改数据:
$can_edit_data = 0;
if ($debug_mode || in_array($uinfo["part_id"], array(9)) || in_array($uid, explode(",", $type_detail["uids"]))) {
	$can_edit_data = 1;
}


$show_chengben = 0;
if ($debug_mode || $username == "admin" || $username == "朱绍清") {
	$show_chengben = 1;
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
			$s .= '<a href="javascript:void(0);" onclick="'.$click.'">'.$v.'</a>';
		}
	}
	return $s;
}

function _int_month_to_month($m) {
	if (strlen($m) == 6) {
		return substr($m, 0, 4)."-".substr($m, 4, 2);
	}
	return $m;
}


// 页面开始 ------------------------
?>
<html>
<head>
<title>低效人员分析</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<script src="/res/sorttable_keep.js" language="javascript"></script>
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

.main_title {margin:0 auto; padding-top:30px; padding-bottom:15px; text-align:center; font-weight:bold; font-size:16px; font-family:"微软雅黑"; }

.item {padding:8px 3px 6px 3px !important; }

.rate_tips {padding:30px 0 0 30px; line-height:24px; }

.column_sortable {color:blue !important; cursor:pointer; font-family:"微软雅黑";  }
.sorttable_nosort {color:gray; font-family:"微软雅黑";  }

.huizong td {font-weight:bold; color:#8000ff !important; }

.month_sel {padding:10px 5px 3px 0px; text-align:left; }
.month_sel a {font-family:"Tahoma"; padding:1px 5px 1px 5px; border:0; }
.month_sel a:hover {border:1px solid silver; padding:0px 4px 0px 4px; }

.week_sel {padding:5px 5px 3px 0px; text-align:left; }
.week_sel a {font-family:"Tahoma"; padding:1px 5px 1px 5px; border:0; }
.week_sel a:hover {border:1px solid silver; padding:0px 4px 0px 4px; }

.not_good td {color:#ff0000; font-weight:bold; background-color:#f7d8d0; }
</style>

<script language="javascript">
var int_month = "<?php echo $int_month; ?>";
var int_week = "<?php echo $int_week; ?>";

function add_yuanyin(kefu) {
	parent.load_src(1, "/m/zixun/dixiao_memo.php?op=add_yuanyin&month="+int_month+"&week="+int_week+"&kefu="+encodeURIComponent(kefu), 500, 200);
}

function add_fangan(kefu) {
	parent.load_src(1, "/m/zixun/dixiao_memo.php?op=add_fangan&month="+int_month+"&week="+int_week+"&kefu="+encodeURIComponent(kefu), 500, 200);
}

function update_date(type, o) {
	byid("date_"+type).value = parseInt(o.innerHTML, 10);

	var a = parseInt(byid("date_1").value, 10);
	var b = parseInt(byid("date_2").value, 10);

	var s = a + '' + (b<10 ? "0" : "") + b;

	byid("date").value = s;
	byid("ch_date").submit();
}

function hgo(dir, o) {
	var obj = byid("type_id");
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

function write_dt(da, db) {
	byid("begin_time").value = da;
	byid("end_time").value = db;
}

function set_line_set() {
	var set = byid("line_set").value;
	set_cookie("dixiao_line_set", set, 10000000);
	self.location.reload();
}
</script>
</head>

<body>
<div style="margin:10px 0 0 0px;">
	<div id="date_tips">医院项目：</div>
	<form method="GET" style="margin-left:4px;">
		<select name="type_id" id="type_id" class="combo" onchange="this.form.submit()">
			<option value="" style="color:gray">-请选择项目-</option>
			<?php echo list_option($types, "_key_", "_value_", $cur_type); ?>
		</select>&nbsp;
		<button class="button" onclick="hgo('up',this);">上</button>&nbsp;
		<button class="button" onclick="hgo('down',this);">下</button>&nbsp;&nbsp;&nbsp;&nbsp;

		误差指标：<input class="input" id="line_set" value="<?php echo $_COOKIE["dixiao_line_set"] ? $_COOKIE["dixiao_line_set"] : "2"; ?>" style="width:30px;">% &nbsp;
		<button class="button" onclick="set_line_set(); return false;">保存</button>&nbsp; &nbsp; &nbsp; &nbsp;

		<button class="button" onclick="self.location.reload(); return false;">刷新</button>

		<input type="hidden" name="month" value="<?php echo $_GET["month"]; ?>">
		<input type="hidden" name="week" value="<?php echo $_GET["week"]; ?>">
		<input type="hidden" name="op" value="change_type">
	</form>&nbsp;&nbsp;&nbsp;
</div>

<div class="month_sel">
<?php
echo '<b class="yh">选择月份：</b>';
$a = array();
foreach ($m_arr as $v) {
	$mshow = _int_month_to_month($v);
	if ($v == $_GET["month"]) {
		$a[] = '<a href="?month='.$v.'&week='.$_GET["week"].'" style="color:red;font-weight:bold;">'.$mshow.'</a>';
	} else {
		$a[] = '<a href="?month='.$v.'&week='.$_GET["week"].'">'.$mshow.'</a>';
	}
}
echo implode(" ", $a);
?>
</div>

<div class="week_sel">
<?php
echo '<b class="yh">选择周数：</b>';
$a = array();
foreach ($week_arr as $k => $v) {
	$v = "第".$k."周[".$v."]";
	if ($k == $_GET["week"]) {
		$a[] = '<a href="?month='.$_GET["month"].'&week='.$k.'" style="color:red;font-weight:bold;">'.$v.'</a>';
	} else {
		$a[] = '<a href="?month='.$_GET["month"].'&week='.$k.'">'.$v.'</a>';
	}
}
echo implode("&nbsp;&nbsp;", $a);
?>
</div>



<?php if ($cur_type && $date_btime && $date_etime) { ?>

<div class="main_title"><?php echo $type_detail["name"]; ?> <?php echo $date_btime; ?> 到 <?php echo $date_etime; ?> 低效人员分析</div>

<table id="kefu_table" width="100%" align="center" class="list sortable">
	<tr>
		<td class="head column_sortable" title="点击可排序" align="center">客服姓名</td>
		<td class="head column_sortable" title="点击可排序" align="center">总点击</td>
		<td class="head column_sortable" title="点击可排序" align="center">预约</td>
		<td class="head column_sortable" title="点击可排序" align="center">到院</td>
		<td class="head column_sortable" title="点击可排序" align="center">咨就诊率</td>
		<td class="head column_sortable" title="点击可排序" align="center">平均咨率</td>
		<td class="head column_sortable" title="点击可排序" align="center">差距</td>
		<td class="head column_sortable" title="点击可排序" align="center" width="25%">原因分析</td>
		<td class="head column_sortable" title="点击可排序" align="center" width="25%">调整方案</td>
	</tr>

<?php
foreach ($kefu_list as $kefu_name) {
	$li = $list[$kefu_name];
	if (!is_array($li)) {
		$li = array();
	}
	$show_name = $kefu_name;
	if (!in_array($kefu_name, $cur_kefu_list)) {
		continue;
		//$show_name = ' <font style="text-decoration:line-through;color:silver;" title="此客服已离职">'.$kefu_name.'</font>';
	}

	// 流失人数:
	$liushi = @round($sum_list["come"] * $li["click"] / $sum_list["click"]) - $li["come"];
	if ($liushi > 0) {
		$liushi_sum += intval($liushi);
	} else {
		$liushi = '';
	}



	// 差距: 差距=该员工咨询就诊率-平均咨询就诊率
	$chaju = (floatval($li["per_3"]) - $sum_list["per_3"]);
	if ($chaju != 0) {
		$chaju = round($chaju, 1);
	}

	if ($chaju > 0) {
		//continue;
	}

	$line_class = "";
	if ((-1 * $dixiao_line_set) >= $chaju) {
		$line_class = "not_good";
	}

	// 指标

	// 人均成本
	$chengben = '';
	if ($zongchengben > 0) {
		if ($li["come"] && $li["click"]) {
			$chengben = round((($zongchengben * $li["click"]) / $sum_list["click"]) / $li["come"]);
		}
	} else {
		$chengben = '<span title="没有查询到竞价消费，成本无法计算">*</span>';
	}

?>
	<tr class="<?php echo $line_class; ?>">
		<td class="item" align="center"><nobr><?php echo $show_name; ?></nobr></td>
		<td class="item" align="center"><?php echo $li["click"]; ?></td>
		<td class="item" align="center"><?php echo $li["talk"]; ?></td>
		<td class="item" align="center"><?php echo $li["come"]; ?></td>
		<td class="item" align="center"><?php echo floatval($li["per_3"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($sum_list["per_3"]); ?>%</td>
		<td class="item" align="center"><?php echo $chaju; ?>%</td>
		<td class="item" align="center" class="memo">
			<table width="100%">
				<tr>
					<td width="90%" align="left" style="color:black; font-weight:normal;"><?php echo text_show($memo[$kefu_name]["yuanyin"]); ?></td>
					<td width="10%" align="center"><button onclick="add_yuanyin('<?php echo $kefu_name; ?>'); return false;" class="button" title="添加原因分析">添加</button></td>
				</tr>
			</table>
		</td>
		<td class="item" align="center" class="memo">
			<table width="100%">
				<tr>
					<td width="90%" align="left" style="color:black; font-weight:normal;"><?php echo text_show($memo[$kefu_name]["fangan"]); ?></td>
					<td width="10%" align="center"><button onclick="add_fangan('<?php echo $kefu_name; ?>'); return false;" class="button" title="添加解决方案">添加</button></td>
				</tr>
			</table>
		</td>
	</tr>

<?php } ?>

</table>

<?php } ?>

<br>
<br>


</body>
</html>
