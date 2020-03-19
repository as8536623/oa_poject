<?php defined("ROOT") or exit("Error."); ?>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title>收益统计报表</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<style>
.b {font-weight:bold; }
.m20 {margin-left:20px; }
.m40 {margin-left:40px; }
form {display:inline; }
.tip_head {color:black; font-weight:bold; margin-left:0px; margin-top:20px; margin-bottom:8px; text-align:center; }

#quick_set_date {padding-top:0px; }
.quick_date {color:silver; }
.quick_date a {padding:0 3px; }

.report_title {margin-top:0px; margin-bottom:20px; text-align:center; font-size:16px; font-weight:normal; font-family:"黑体"; }
</style>
<script language="javascript">
function update_date(o) {
	var str = '';
	str = o.y.value;
	if (o.m.value < 10) str += "0";
	str += o.m.value;
	if (o.d.value < 10) str += "0";
	str += o.d.value;
	location.href = "?date="+str;
	return false;
}

function go_date(day_id, o) {
	location.href = "?date="+byid(day_id).value;
	if (o) {
		o.disabled = true;
	}
}

function confirm_delete() {
	return confirm("删除后不能恢复，是否确定要删除？");
}

function update_date(type, o) {
	byid("date_"+type).value = parseInt(o.innerHTML, 10);

	var a = parseInt(byid("date_1").value, 10);
	var b = parseInt(byid("date_2").value, 10);
	var c = parseInt(byid("date_3").value, 10);

	var s = a + '' + (b<10 ? "0" : "") + b + "" + (c<10 ? "0" : "") + c;

	byid("date").value = s;
	byid("ch_date").submit();
}

function write_dt(da, db, o) {
	byid("begin_time").value = da;
	byid("end_time").value = db;
	o.innerHTML = "请稍等...";
	byid("ch_date").submit();
}
</script>
</head>

<body>

<?php if ($_GET["print"] != 1) { ?>

<div style="margin:12px 0 0 0px;">
	<form id="ch_date" method="GET" style="display:block; float:left;">
		<b>起始时间： </b><input name="btime" id="begin_time" class="input" style="width:100px" value="<?php echo $_GET["btime"]; ?>"> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'begin_time',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="选择时间">&nbsp;

		<b>终止时间： </b><input name="etime" id="end_time" class="input" style="width:100px" value="<?php echo $_GET["etime"]; ?>"> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'end_time',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="选择时间">&nbsp;

		<input type="submit" value="生成报表" class="buttonb">&nbsp;&nbsp;&nbsp;

		<span id="quick_set_date">
			速填： <span class="quick_date">
<?php
$now = time();
$show_dates = array(
	//"今天" => array($now, $now),
	//"昨天" => array($yesterday_begin, $yesterday_begin),
	//"本周" => array($this_week_begin, $this_week_end),
	//"上周" => array($last_week_begin, $last_week_end),
	//"今年" => array($this_year_begin, $this_year_end),
	//"近一个月" => array($near_1_month_begin, $now),
	//"近三个月" => array($near_3_month_begin, $now),
	//"近一年" => array($near_1_year_begin, $now),
	"1-7" => array(strtotime(date("Y-m-")."1"), strtotime(date("Y-m-")."7")),
	"8-14" => array(strtotime(date("Y-m-")."8"), strtotime(date("Y-m-")."14")),
	"15-21" => array(strtotime(date("Y-m-")."15"), strtotime(date("Y-m-")."21")),
	"22-28" => array(strtotime(date("Y-m-")."22"), strtotime(date("Y-m-")."28")),
	"本月" => array($this_month_begin, $this_month_end),
	"上月" => array($last_month_begin, $last_month_end),
);
$tmp = array();
foreach ($show_dates as $x => $y) {
	$tmp[] = '<a href="javascript:;" onclick="write_dt(\''.date("Y-m-d", $y[0]).'\',\''.date("Y-m-d", $y[1]).'\', this)">'.$x.'</a>';
}
echo implode("|", $tmp);
?>
			</span>
		</span>&nbsp;
	</form>

	<div style="float:right; text-align:right;">
<?php if ($_GET["btime"] && $_GET["etime"]) { ?>
		<a href="?btime=<?php echo $_GET["btime"]; ?>&etime=<?php echo $_GET["etime"]; ?>&print=1" id="print_a" target="_blank" style="text-align:center; color:black; background:url('/res/img/button_long.gif'); height:20px; width:64px; overflow:hidden; ">打印本页</a>
<?php } ?>
	</div>

	<div class="clear"></div>
</div>

<div style="height:30px;">&nbsp;</div>

<?php } ?>

<!-- ------------------------------------------- 数据部分 ---------------------------------------------- -->

<?php if ($_GET["btime"] && $_GET["etime"]) { ?>

<div class="report_title"><?php echo $h_name; ?> <?php echo $_GET["btime"]." ～ ".$_GET["etime"]; ?> 收益统计报表</div>

<!-- 数据列表 begin -->
<div class="headers" style="margin:20px 0 6px 0;">
	<div class="headers_title" style="width:60%"><b>门诊收费</b></div>
	<div class="headers_oprate"></div>
</div>
<form name="mainform">
<?php echo $t->show(); ?>
</form>
<!-- 数据列表 end -->

<?php if ($hconfig["住院收费项目"] != '' && count($list2) > 0) { ?>
<!-- 数据列表 begin -->
<div class="space"></div>
<div class="headers" style="margin:20px 0 6px 0;">
	<div class="headers_title" style="width:60%"><b>住院收费</b></div>
	<div class="headers_oprate"></div>
</div>
<form name="mainform">
<?php echo $t2->show(); ?>
</form>
<!-- 数据列表 end -->
<?php } ?>

<div class="space"></div>
<table width="100%" style="margin:20px 0 6px 0;">
	<tr>
		<td style="width:50%; text-align:center;">初诊总人数：<b><?php echo $patient_all; ?></b></td>
		<td style="width:50%; text-align:center;">总营业额：<b><?php echo $yingyee_all; ?></b></td>
	</tr>
</table>

<?php } ?>

<?php if ($_GET["print"] == 1) { ?>
<script type="text/javascript">
window.print();
</script>
<?php } ?>


</body>
</html>