<?php
// --------------------------------------------------------
// - 功能说明 : 网络数据 趋势图
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2012-03-16
// --------------------------------------------------------
require "../../core/core.php";
include "../../res/chart/FusionCharts_Gen.php";
include "web_config.php";

$table = "count_web";



// 最近N条记录的趋势图:
if ($_GET["btime"]) {
	// 超过N天校正
	if (strtotime($_GET["etime"]) - strtotime($_GET["btime"]) > 31*24*3600) {
		$_GET["btime"] = date("Y-m-d", strtotime("-31 day", strtotime($_GET["etime"])));
	}
	$day_begin = str_replace("-", "", $_GET["btime"]);
	$day_end = str_replace("-", "", $_GET["etime"]);
} else {
	$day_begin = date("Ymd", strtotime("-30 day"));
	$day_end = date("Ymd");
	$_GET["btime"] = date("Y-m-d", _wee_date($day_begin));
	$_GET["etime"] = date("Y-m-d", _wee_date($day_end));
}

$list = $db->query("select date,sum(ok_click) as ok_click, sum(talk) as talk, sum(come) as come from (select date, ok_click, talk, come from $table where hid=$hid and sub_id=$sub_id and date>=$day_begin and date<=$day_end) as t1 group by date order by date asc", "date");


// 统计图:
$FC1 = new FusionCharts("Line","800","200");
$FC1->setSWFPath("/res/chart/");
$FC1->setChartParams("decimalPrecision=0; formatNumberScale=1; baseFontSize=10; baseFont=Arial; chartBottomMargin=0; outCnvBaseFontSize=12; shownames=1; rotateNames=1; showValues=1; hoverCapSepChar=: ; chartBottomMargin=10;" );

$FC2 = new FusionCharts("Line","800","200");
$FC2->setSWFPath("/res/chart/");
$FC2->setChartParams("decimalPrecision=0; formatNumberScale=1; baseFontSize=10; baseFont=Arial; chartBottomMargin=0; outCnvBaseFontSize=12; shownames=1; rotateNames=1; showValues=1; hoverCapSepChar=: ; chartBottomMargin=10;" );

$FC3 = new FusionCharts("Line","800","200");
$FC3->setSWFPath("/res/chart/");
$FC3->setChartParams("decimalPrecision=0; formatNumberScale=1; baseFontSize=10; baseFont=Arial; chartBottomMargin=0; outCnvBaseFontSize=12; shownames=1; rotateNames=1; showValues=1; hoverCapSepChar=: ; chartBottomMargin=10;" );


foreach ($list as $dt => $v) {
	$date = date("n-j", _wee_date($dt));
	$FC1->addCategory($date);
	$FC2->addCategory($date);
	$FC3->addCategory($date);
}

$sum = array();
foreach ($list as $dt => $v) {
	$FC1->addChartData($v["ok_click"], "name=".date("n-j", _wee_date($dt)));
	$FC2->addChartData($v["talk"], "name=".date("n-j", _wee_date($dt)));
	$FC3->addChartData($v["come"], "name=".date("n-j", _wee_date($dt)));

	$sum["ok_click"] += intval($v["ok_click"]);
	$sum["talk"] += intval($v["talk"]);
	$sum["come"] += intval($v["come"]);
}

$days = @ceil((_wee_date($day_end) - _wee_date($day_begin)) / 24 / 3600);
$pj1 = round($sum["ok_click"] / $days);
$pj2 = round($sum["talk"] / $days);
$pj3 = round($sum["come"] / $days);

$FC1->addTrendLine("startValue={$pj1};color=ff0000;displayvalue=平均值;showOnTop=1");
$FC2->addTrendLine("startValue={$pj2};color=ff0000;displayvalue=平均值;showOnTop=1");
$FC3->addTrendLine("startValue={$pj3};color=ff0000;displayvalue=平均值;showOnTop=1");


function _wee_date($s) {
	return strtotime(substr($s, 0, 4)."-".substr($s, 4, 2)."-".substr($s, 6, 2)." 0:0:0");
}


?>
<html>
<head>
<title>趋势图</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<script src='/res/chart/FusionCharts.js' language='javascript'></script>
<style>
body {padding:5px 8px; }
.main_title {margin:0 auto; padding-top:30px; padding-bottom:15px; text-align:center; font-weight:bold; font-size:12px; font-family:"宋体"; }
.item {padding:8px 3px 6px 3px !important; }
.head {padding:12px 3px !important;}

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

.item {padding:8px 3px 6px 3px !important; }

.head {padding:12px 3px !important;}

.rate_tips {padding:30px 0 0 30px; line-height:24px; }

.item {font-family:"Tahoma"; }
</style>

<script language="javascript">
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

function write_dt(da, db) {
	byid("begin_time").value = da;
	byid("end_time").value = db;
}
</script>
</head>

<body>
<div style="margin:15px 0 0 0px; text-align:center;">
	<form method="GET">
		<b>项目：</b>
		<select name="hid" id="hid" class="combo" onchange="this.form.submit()">
			<option value="" style="color:gray">-请选择项目-</option>
			<?php echo list_option($types, "_key_", "_value_", $hid); ?>
		</select>&nbsp;
		<button class="button" onclick="hgo('up',this);">上</button>&nbsp;
		<button class="button" onclick="hgo('down',this);">下</button>&nbsp;
		&nbsp;&nbsp;
		<input name="btime" id="begin_time" class="input" style="width:80px" value="<?php echo $_GET["btime"]; ?>"> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'begin_time',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="选择时间">&nbsp;～&nbsp;
		<input name="etime" id="end_time" class="input" style="width:80px" value="<?php echo $_GET["etime"]; ?>"> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'end_time',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="选择时间">&nbsp;&nbsp;
		<input type="submit" class="button" value="确定">&nbsp;&nbsp;


<?php
$lmb = strtotime("-1 month", strtotime($_GET["btime"]));
$lme = strtotime($_GET["btime"]) - 1;
$nmb = strtotime("+1 month", strtotime($_GET["btime"]));
$nme = strtotime("+1 month", $nmb) - 1;
?>
		<input type="button" class="button" onclick="write_dt('<?php echo date("Y-m-d", $lmb); ?>', '<?php echo date("Y-m-d", $lme); ?>'); this.form.submit();" value="上月">&nbsp;
		<input type="button" class="button" onclick="write_dt('<?php echo date("Y-m-d", strtotime("-30 day")); ?>', '<?php echo date("Y-m-d"); ?>'); this.form.submit();" value="本月">&nbsp;
		<input type="button" class="button" onclick="write_dt('<?php echo date("Y-m-d", $nmb); ?>', '<?php echo date("Y-m-d", $nme); ?>'); this.form.submit();" value="下月">&nbsp;&nbsp;

		<input type="hidden" name="op" value="change_type">
	</form>&nbsp;&nbsp;&nbsp;
</div>

<div style="text-align:center; margin-top:20px;">
	<div style="margin-top:10px; text-align:center; font-size:14px; font-family:'微软雅黑' !important; "><?php echo $type_detail["name"]; ?> <?php echo date("Y-m-d", _wee_date($day_begin))."～".date("Y-m-d", _wee_date($day_end)); ?> 就诊趋势图 (平均：<?php echo $pj3; ?>)</div>
	<div style="margin-top:0px; ">
		<?php $FC3->renderChart(); ?>
	</div>

	<div style="margin-top:10px; text-align:center; font-size:14px; font-family:'微软雅黑' !important; "><?php echo $type_detail["name"]; ?> <?php echo date("Y-m-d", _wee_date($day_begin))."～".date("Y-m-d", _wee_date($day_end)); ?> 预约趋势图 (平均：<?php echo $pj2; ?>)</div>
	<div style="margin-top:0px; ">
		<?php $FC2->renderChart(); ?>
	</div>

	<div style="margin-top:10px; text-align:center; font-size:14px; font-family:'微软雅黑' !important; "><?php echo $type_detail["name"]; ?> <?php echo date("Y-m-d", _wee_date($day_begin))."～".date("Y-m-d", _wee_date($day_end)); ?> 总有效趋势图 (平均：<?php echo $pj1; ?>)</div>
	<div style="margin-top:0px; ">
		<?php $FC1->renderChart(); ?>
	</div>
</div>

<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>

</body>
</html>
