<?php
// --------------------------------------------------------
// - 功能说明 : 数据报表
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2013-01-08
// --------------------------------------------------------
require "../../core/core.php";
include "web_config.php";

error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", "Off");
ini_set("log_errors", 0);

if ($_GET["op"] == "set_xm") {
	$_SESSION["web_count_yang_xm"] = $_GET["xm"];
}

if ($_SESSION["web_count_yang_xm"]) {
	$checked_xm_arr = $_SESSION["web_count_yang_xm"];
} else {
	$checked_xm_arr = array(1, 2);
}

$sub_ids = implode(",", $checked_xm_arr);


// 初始值为本月:
if ($_GET["month"] == '') {
	$_GET["month"] = date("Y-m", mktime(0,0,0,date("m"), 1));
}

$m = strtotime($_GET["month"]."-1 0:0:0");
$m_end = date("d", strtotime("+1 month", $m) - 1);


$cal_field = explode(" ", "click click_local click_other zero_talk ok_click ok_click_local ok_click_other talk talk_local talk_other orders order_local order_other come come_local come_other");

// 处理字段:
$f = array();
foreach ($cal_field as $v) {
	$f[] = 'sum('.$v.') as '.$v;
}
$f = implode(", ", $f);


// 要查询的字段信息:
$m_arr = array();


// 查询前3个月的:
for ($i = 3; $i >= 1; $i--) {
	$tb = strtotime("-{$i} month", $m);
	$te = strtotime("+1 month", $tb) - 1;
	$days = get_month_days(date("Y-m", $tb));
	$m_arr[] = array("name" => date("Y-m", $tb), "tb" => $tb, "te" => $te, "days" => $days);
}

// 处理数据: 该月的四周
if (date("Ym", $m) == date("Ym")) {  //月份选择，如果是本月
	if (date("j") > 7) {
		$m_arr[] = array("name" => "1-7", "tb" => strtotime(date("Y-m-01", $m)), "te" => strtotime(date("Y-m-07", $m)), "days" => 7);
	}
	if (date("j") > 14) {
		$m_arr[] = array("name" => "8-14", "tb" => strtotime(date("Y-m-08", $m)), "te" => strtotime(date("Y-m-14", $m)), "days" => 7);
	}
	if (date("j") > 21) {
		$m_arr[] = array("name" => "15-21", "tb" => strtotime(date("Y-m-15", $m)), "te" => strtotime(date("Y-m-21", $m)), "days" => 7);
	}
	if (date("j") > 28) {
		$m_arr[] = array("name" => "22-28", "tb" => strtotime(date("Y-m-22", $m)), "te" => strtotime(date("Y-m-28", $m)), "days" => 7);
	}
} else {
	$m_arr[] = array("name" => "1-7", "tb" => strtotime(date("Y-m-01", $m)), "te" => strtotime(date("Y-m-07", $m)), "days" => 7);
	$m_arr[] = array("name" => "8-14", "tb" => strtotime(date("Y-m-08", $m)), "te" => strtotime(date("Y-m-14", $m)), "days" => 7);
	$m_arr[] = array("name" => "15-21", "tb" => strtotime(date("Y-m-15", $m)), "te" => strtotime(date("Y-m-21", $m)), "days" => 7);
	$m_arr[] = array("name" => "22-28", "tb" => strtotime(date("Y-m-22", $m)), "te" => strtotime(date("Y-m-28", $m)), "days" => 7);
}

// 竞价消费：
$rule = '';
if (in_array(1, $checked_xm_arr) && in_array(2, $checked_xm_arr)) {
	$rule = "sum(x1)";
} else if (in_array(1, $checked_xm_arr)) {
	$rule = "(sum(x1)-sum(x5))";
} else if (in_array(2, $checked_xm_arr)) {
	$rule = "sum(x5)";
}



$rs = array();
$rijun = array();

foreach ($m_arr as $tid => $def) {

	$days = $def["days"];
	$b = date("Ymd", $def["tb"]);
	$e = date("Ymd", $def["te"]);

	//查询汇总数据:
	$tmp = $db->query("select $f from $table where hid=$hid and sub_id in ($sub_ids) and date>=$b and date<=$e order by date asc", 1);

	$rs[$tid] = $tmp;

	// 咨询预约率:
	$rs[$tid]["per_1"] = @round($rs[$tid]["talk"] / $rs[$tid]["click"] * 100, 2);
	// 预到就诊率:
	$rs[$tid]["per_2"] = @round($rs[$tid]["come"] / $rs[$tid]["orders"] * 100, 2);
	// 咨询就诊率:
	$rs[$tid]["per_3"] = @round($rs[$tid]["come"] / $rs[$tid]["click"] * 100, 2);
	// 有效咨询率:
	$rs[$tid]["per_4"] = @round($rs[$tid]["ok_click"] / $rs[$tid]["click"] * 100, 2);
	// 有效预约率:
	$rs[$tid]["per_5"] = @round($rs[$tid]["talk"] / $rs[$tid]["ok_click"] * 100, 2);

	$rs[$tid]["come_avg"] = @round($rs[$tid]["come"] / $days, 1);


	foreach ($cal_field as $k) {
		$v = $rs[$tid][$k];
		$rijun[$tid][$k] = @round($v / $days);
	}

	// 读取竞价消费：
	$sql = "select $rule as c from jingjia_xiaofei where hid=$hid and date>=$b and date<=$e";
	$fei = $db->query($sql, 1, "c");
	$xiaofei[$tid] = round($fei / $days);
	$xiaofei_per[$tid] = round($xiaofei[$tid] / $rs[$tid]["come_avg"]);

}



// 页面开始 ------------------------
?>
<html>
<head>
<title>网络数据统计</title>
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

.main_title {margin:0 auto; padding-top:30px; padding-bottom:15px; text-align:center; font-weight:bold; font-size:12px; font-family:"宋体"; }

.hl {border-left:1px solid #ADE0BA !important; }
.hr {border-right:1px solid #ADE0BA !important; }
.ht {border-top:1px solid #ADE0BA !important; }
.hb {border-bottom:1px solid #ADE0BA !important; }

.rate_tips {padding:30px 0 0 30px; line-height:24px; }

#cur_hospital_all_xiangmu {padding-top:20px; text-align:center; }
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
		<input type="hidden" name="check_all" value="<?php echo $_GET["check_all"]; ?>">
		<input type="hidden" name="op" value="change_type">
	</form>&nbsp;&nbsp;&nbsp;

	<b>月份：</b>
	<form method="GET">
		<input name="month" id="time_month" class="input" style="width:100px" value="<?php echo $_GET["month"]; ?>"> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'time_month',dateFmt:'yyyy-MM'})" align="absmiddle" style="cursor:pointer" title="选择月份">
		<input type="submit" class="button" value="确定">
	</form>
</div>


<form id="xiangmu_huizong" method="GET">
<div id="cur_hospital_all_xiangmu">
	<input type="submit" onclick="set_check_all(this);" class="buttonb" value="汇总全部">&nbsp;&nbsp;
	<span id="xm_check_area">
<?php
$_out = array();
$xm_arr = array(1 => "PC", 2 => "手机");
foreach ($xm_arr as $_id => $_name) {
	$chk = '';
	if (in_array($_id, $checked_xm_arr)) {
		$chk = "checked";
	}
	$_out[] = '<input type="checkbox" '.$chk.' name="xm[]" value="'.$_id.'" id="xm_'.$_id.'"><label for="xm_'.$_id.'">'.$_name.'</label>';
}
echo implode(" ", $_out);
?>
	</span>
	<input type="hidden" name="btime" value="<?php echo $_GET["btime"]; ?>">
	<input type="hidden" name="etime" value="<?php echo $_GET["etime"]; ?>">
	<input type="hidden" name="op" value="set_xm">
	<input type="submit" class="button" value="汇总">
</div>
<script type="text/javascript">
function set_check_all() {
	var objs = byid("xm_check_area").getElementsByTagName("INPUT");
	for (var i=0; i<objs.length; i++) {
		objs[i].checked = true;
	}
	byid("check_all_01").value = "1";
}
</script>
</form>


<div class="main_title"><?php echo $type_detail["name"]; ?> <?php echo $_GET["month"]; ?> 统计</div>

<table width="100%" align="center" class="list">
	<tr>
		<td class="head hb" align="center">名称</td>
<?php foreach ($m_arr as $tid => $def) { ?>
		<td class="head hb" align="center"><?php echo $def["name"]; ?></td>
<?php } ?>
	</tr>

	<tr>
		<td class="item" align="center">日均就诊</td>
<?php foreach ($m_arr as $tid => $def) { ?>
		<td class="item" align="center"><?php echo $rs[$tid]["come_avg"]; ?></td>
<?php } ?>
	</tr>

	<tr>
		<td class="item" align="center">日均消费</td>
<?php foreach ($m_arr as $tid => $def) { ?>
		<td class="item" align="center"><?php echo $xiaofei[$tid]; ?></td>
<?php } ?>
	</tr>

	<tr>
		<td class="item" align="center">人均成本</td>
<?php foreach ($m_arr as $tid => $def) { ?>
		<td class="item" align="center"><?php echo $xiaofei_per[$tid]; ?></td>
<?php } ?>
	</tr>

	<tr>
		<td class="item" align="center">总点击</td>
<?php foreach ($m_arr as $tid => $def) { ?>
		<td class="item" align="center"><?php echo $rijun[$tid]["click"]; ?></td>
<?php } ?>
	</tr>

	<tr>
		<td class="item" align="center">总有效</td>
<?php foreach ($m_arr as $tid => $def) { ?>
		<td class="item" align="center"><?php echo $rijun[$tid]["ok_click"]; ?></td>
<?php } ?>
	</tr>

	<tr>
		<td class="item" align="center">咨询就诊率</td>
<?php foreach ($m_arr as $tid => $def) { ?>
		<td class="item" align="center"><?php echo $rs[$tid]["per_3"]; ?>%</td>
<?php } ?>
	</tr>

</table>

<br>
<br>

</body>
</html>
