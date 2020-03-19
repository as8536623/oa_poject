<?php
// --------------------------------------------------------
// - 功能说明 : 网络 数据对比 按周
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2010-11-04 15:09
// --------------------------------------------------------
require "../../core/core.php";
include "web_config.php";
$table = "count_web";

//exit_html("该功能正在调整中...");


// 初始值为本月:
if ($_GET["month"] == '') {
	$_GET["month"] = date("Y-m", mktime(0,0,0,date("m"), 1));
}

$m = strtotime($_GET["month"]."-1 0:0:0");
$m_end = date("d", strtotime("+1 month", $m) - 1);
$week_array = array(
	1 => array(date("Y-m-1", $m), date("Y-m-7", $m)),
	2 => array(date("Y-m-8", $m), date("Y-m-14", $m)),
	3 => array(date("Y-m-15", $m), date("Y-m-21", $m)),
	4 => array(date("Y-m-22", $m), date("Y-m-".$m_end, $m)),
);




// 处理数据:
if ($hid && $_GET["month"]) {

	$all = array();

	// 查询每周数据:
	foreach ($week_array as $wi => $w) {

		// 时间段:
		$btime = strtotime($w[0]." 0:0:0");
		$etime = strtotime($w[1]." 23:59:59");

		$b = date("Ymd", $btime);
		$e = date("Ymd", $etime);

		//查询总医院汇总数据:
		$tmp_list = $db->query("select * from $table where hid=$hid and sub_id=$sub_id and date>=$b and date<=$e order by kefu asc,date asc");

		// 计算汇总:
		$list = $dt_count = array();
		foreach ($tmp_list as $v) {
			$dt = $v["kefu"];
			$dt_count[$dt] += 1;
			foreach ($v as $a => $b) {
				if ($b && is_numeric($b)) {
					$list[$dt][$a] = floatval($list[$dt][$a]) + $b;
				}
			}
		}

		// 计算数据:
		foreach ($list as $k => $v) {
			// 咨询预约率:
			$list[$k]["per_1"] = @round($v["talk"] / $v["click"] * 100, 2);
			// 预到就诊率:
			$list[$k]["per_2"] = @round($v["come"] / $v["orders"] * 100, 2);
			// 咨询就诊率:
			$list[$k]["per_3"] = @round($v["come"] / $v["click"] * 100, 2);
			// 有效咨询率:
			$list[$k]["per_4"] = @round($v["ok_click"] / $v["click"] * 100, 2);
			// 有效预约率:
			$list[$k]["per_5"] = @round($v["talk"] / $v["ok_click"] * 100, 2);
		}

		// 计算统计数据:
		$cal_field = explode(" ", "click click_local click_other zero_talk ok_click ok_click_local ok_click_other talk talk_local talk_other orders order_local order_other come come_local come_other");
		// 处理:
		$sum_list = array();
		foreach ($list as $v) {
			foreach ($cal_field as $f) {
				$sum_list[$f] = floatval($sum_list[$f]) + $v[$f];
			}
		}

		// 加入总数组:
		foreach ($list as $k => $v) {
			$all[$k][$wi]["per_1"] = $v["per_1"];
			$all[$k][$wi]["per_2"] = $v["per_2"];
			$all[$k][$wi]["per_3"] = $v["per_3"];
			$all[$k][$wi]["per_4"] = $v["per_4"];
			$all[$k][$wi]["per_5"] = $v["per_5"];
		}

		/*
		$all["sum"][$wi]["per_1"] = @round($sum_list["per_1"] / count($list), 2);
		$all["sum"][$wi]["per_2"] = @round($sum_list["per_2"] / count($list), 2);
		$all["sum"][$wi]["per_3"] = @round($sum_list["per_3"] / count($list), 2);
		$all["sum"][$wi]["per_4"] = @round($sum_list["per_4"] / count($list), 2);
		$all["sum"][$wi]["per_5"] = @round($sum_list["per_5"] / count($list), 2);
		*/

		// 咨询预约率:
		$all["sum"][$wi]["per_1"] = @round($sum_list["talk"] / $sum_list["click"] * 100, 2);
		// 预到就诊率:
		$all["sum"][$wi]["per_2"] = @round($sum_list["come"] / $sum_list["orders"] * 100, 2);
		// 咨询就诊率:
		$all["sum"][$wi]["per_3"] = @round($sum_list["come"] / $sum_list["click"] * 100, 2);
		// 有效咨询率:
		$all["sum"][$wi]["per_4"] = @round($sum_list["ok_click"] / $sum_list["click"] * 100, 2);
		// 有效预约率:
		$all["sum"][$wi]["per_5"] = @round($sum_list["talk"] / $sum_list["ok_click"] * 100, 2);


	}
}


// 页面开始 ------------------------
?>
<html>
<head>
<title>网络数据统计 - 按周</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<style>
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

.a_kefu {color:; }
.a_kefu_dy {color:#ff59ff; }
.a_kefu_sx {color:#0000ff; }

.main_title {margin:0 auto; padding-top:30px; padding-bottom:15px; text-align:center; font-weight:bold; font-size:12px; font-family:"宋体"; }

.list {border:2px solid #43A75C !important; }
.item {padding:8px 3px 6px 3px !important; }

.hl {border-left:2px solid #ADE0BA !important; }
.hr {border-right:2px solid #ADE0BA !important; }
.ht {border-top:2px solid #ADE0BA !important; }
.hb {border-bottom:2px solid #ADE0BA !important; }

.rate_tips {padding:30px 0 0 30px; line-height:24px; }

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

function hgo(dir, o) {
	var obj = byid("hid");
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

<body>
<div style="margin:10px 0 0 0px;">
	<div id="date_tips">医院项目：</div>
	<form method="GET" style="margin-left:30px;">
		<select name="hid" id="hid" class="combo" onchange="this.form.submit()">
			<option value="" style="color:gray">-请选择项目-</option>
			<?php echo list_option($types, "_key_", "_value_", $hid); ?>
		</select>&nbsp;
		<button class="button" onclick="hgo('up',this);">上</button>&nbsp;
		<button class="button" onclick="hgo('down',this);">下</button>
		<input type="hidden" name="month" value="<?php echo $_GET["month"]; ?>">
		<input type="hidden" name="op" value="change_type">
	</form>&nbsp;&nbsp;&nbsp;

	<b>月份：</b>
	<form method="GET">
		<input name="month" id="time_month" class="input" style="width:100px" value="<?php echo $_GET["month"]; ?>"> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'time_month',dateFmt:'yyyy-MM'})" align="absmiddle" style="cursor:pointer" title="选择月份">
		<input type="submit" class="button" value="确定">
	</form>
</div>


<?php if ($hid && $_GET["month"]) { ?>

<div class="main_title"><?php echo $type_detail["name"]; ?> <?php echo $_GET["month"]; ?> 周数据对比</div>

<table width="100%" align="center" class="list">
	<tr>
		<td class="head hb" align="center"></td>
		<td class="head hl hb" colspan="4" align="center" style="color:red">咨询预约率</td>
		<td class="head hl hb" colspan="4" align="center" style="color:red">预到就诊率</td>
		<td class="head hl hb" colspan="4" align="center" style="color:red">咨询就诊率</td>
		<td class="head hl hb" colspan="4" align="center" style="color:red">有效咨询率</td>
		<td class="head hl hb" colspan="4" align="center" style="color:red">有效预约率</td>
	</tr>
	<tr>
		<td class="head hb" align="center">客服</td>

		<td class="head hl hb" align="center">1-7</td>
		<td class="head hb" align="center">8-14</td>
		<td class="head hb" align="center">15-21</td>
		<td class="head hb" align="center">22-31</td>

		<td class="head hl hb" align="center">1-7</td>
		<td class="head hb" align="center">8-14</td>
		<td class="head hb" align="center">15-21</td>
		<td class="head hb" align="center">22-31</td>

		<td class="head hl hb" align="center">1-7</td>
		<td class="head hb" align="center">8-14</td>
		<td class="head hb" align="center">15-21</td>
		<td class="head hb" align="center">22-31</td>

		<td class="head hl hb" align="center">1-7</td>
		<td class="head hb" align="center">8-14</td>
		<td class="head hb" align="center">15-21</td>
		<td class="head hb" align="center">22-31</td>

		<td class="head hl hb" align="center">1-7</td>
		<td class="head hb" align="center">8-14</td>
		<td class="head hb" align="center">15-21</td>
		<td class="head hb" align="center">22-31</td>

	</tr>

<?php
foreach ($kefu_list as $i) {

	$class = $kefu_class_arr[$i];

	$tips = "白班客服";
	if ($class == "a_kefu_dy") $tips = "大夜班客服";
	if ($class == "a_kefu_sx") $tips = "实习客服";

	$li = $all[$i];
	if (!is_array($li)) {
		$li = array();
	}

?>
	<tr>
		<td class="item" align="center"><nobr class="<?php echo $class; ?>" title="<?php echo $tips; ?>"><?php echo $i; ?></nobr></td>

		<td class="item hl" align="center"><?php echo floatval($li["1"]["per_1"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["2"]["per_1"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["3"]["per_1"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["4"]["per_1"]); ?>%</td>

		<td class="item hl" align="center"><?php echo floatval($li["1"]["per_2"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["2"]["per_2"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["3"]["per_2"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["4"]["per_2"]); ?>%</td>

		<td class="item hl" align="center"><?php echo floatval($li["1"]["per_3"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["2"]["per_3"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["3"]["per_3"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["4"]["per_3"]); ?>%</td>

		<td class="item hl" align="center"><?php echo floatval($li["1"]["per_4"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["2"]["per_4"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["3"]["per_4"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["4"]["per_4"]); ?>%</td>

		<td class="item hl" align="center"><?php echo floatval($li["1"]["per_5"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["2"]["per_5"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["3"]["per_5"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["4"]["per_5"]); ?>%</td>

	</tr>

<?php } ?>

		<td class="item ht" align="center">汇总</td>

		<td class="item hl ht" align="center"><?php echo floatval($all["sum"]["1"]["per_1"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["2"]["per_1"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["3"]["per_1"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["4"]["per_1"]); ?>%</td>

		<td class="item hl ht" align="center"><?php echo floatval($all["sum"]["1"]["per_2"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["2"]["per_2"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["3"]["per_2"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["4"]["per_2"]); ?>%</td>

		<td class="item hl ht" align="center"><?php echo floatval($all["sum"]["1"]["per_3"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["2"]["per_3"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["3"]["per_3"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["4"]["per_3"]); ?>%</td>

		<td class="item hl ht" align="center"><?php echo floatval($all["sum"]["1"]["per_4"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["2"]["per_4"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["3"]["per_4"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["4"]["per_4"]); ?>%</td>

		<td class="item hl ht" align="center"><?php echo floatval($all["sum"]["1"]["per_5"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["2"]["per_5"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["3"]["per_5"]); ?>%</td>
		<td class="item ht" align="center"><?php echo floatval($all["sum"]["4"]["per_5"]); ?>%</td>

	</tr>
</table>

<div class="rate_tips">
咨询预约率 = 预约人数 / 总点击<br>
预到就诊率 = 实际到院人数 / 预计到院人数<br>
咨询就诊率 = 实际到院人数 / 总点击<br>
有效咨询率 = 有效点击 / 总点击<br>
有效预约率 = 预约人数 / 有效点击<br>
</div>

<?php } ?>

<br>
<br>

</body>
</html>
