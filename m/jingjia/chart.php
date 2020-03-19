<?php
// --------------------------------------------------------
// - 功能说明 : 网络数据 趋势图
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2012-03-16
// --------------------------------------------------------
require "../../core/core.php";
include "../../res/chart/FusionCharts_Gen.php";
$table = "jingjia_xiaofei";
if (count($hospital_ids) == 0) {
	exit_html("管理员没有为你分配医院，不能使用此功能。");
}

$change_op = $_GET["go"];
if (!$hid || $change_op != '') {
	// 医院切换序列:
	$hids = implode(",", $hospital_ids);
	$h_list = $db->query("select id,name from hospital where id in ($hids) order by sort desc, name asc", "", "id");

	if (!$hid) {
		$check_hid = $h_list[0];
	}
	if ($change_op == "prev") {
		$cur_k = array_search($hid, $h_list);
		if ($cur_k > 0) {
			$check_hid = $h_list[$cur_k - 1];
		} else {
			msg_box("已经是最前一家医院了", "back", 1, 2);
		}
	}
	if ($change_op == "next") {
		$cur_k = array_search($hid, $h_list);
		if ($cur_k < count($h_list) - 1) {
			$check_hid = $h_list[$cur_k + 1];
		} else {
			msg_box("已经是最后一家医院了", "back", 1, 2);
		}
	}
	if ($check_hid > 0) {
		$_SESSION["hospital_id"] = $check_hid;
		header("location: ?");
	}
	exit;
}

$h_name = $db->query("select name from hospital where id=$hid limit 1", 1, "name");


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

$list = $db->query("select date,x1 from $table where hid=$hid and date>=$day_begin and date<=$day_end order by date asc", "date");


// 统计图:
$FC1 = new FusionCharts("Line","1000","400");
$FC1->setSWFPath("/res/chart/");
$FC1->setChartParams("decimalPrecision=0; formatNumberScale=0; baseFontSize=10; baseFont=Arial; chartBottomMargin=0; outCnvBaseFontSize=12; shownames=1; rotateNames=1; showValues=1; hoverCapSepChar=: ; chartBottomMargin=10;" );

$FC2 = new FusionCharts("Line","1000","400");
$FC2->setSWFPath("/res/chart/");
$FC2->setChartParams("decimalPrecision=0; formatNumberScale=0; baseFontSize=10; baseFont=Arial; chartBottomMargin=0; outCnvBaseFontSize=12; shownames=1; rotateNames=1; showValues=1; hoverCapSepChar=: ; chartBottomMargin=10;" );

$FC3 = new FusionCharts("Line","1000","400");
$FC3->setSWFPath("/res/chart/");
$FC3->setChartParams("decimalPrecision=0; formatNumberScale=0; baseFontSize=10; baseFont=Arial; chartBottomMargin=0; outCnvBaseFontSize=12; shownames=1; rotateNames=1; showValues=1; hoverCapSepChar=: ; chartBottomMargin=10;" );



foreach ($list as $dt => $v) {
	$date = date("n-j", _wee_date($dt));
	$FC1->addCategory($date);
	//$FC2->addCategory($date);
	//$FC3->addCategory($date);
}

$sum = $max = array();
foreach ($list as $dt => $v) {
	$FC1->addChartData($v["x1"], "name=".date("n-j", _wee_date($dt)));
	$sum["x1"] += intval($v["x1"]);
	$max["x1"] = max($max["x1"], $v["x1"]);

	$tb = strtotime(date("Y-m-d", _wee_date($dt))." 0:0:0");
	$te = strtotime(date("Y-m-d", _wee_date($dt))." 23:59:59");


	// 预约人数:
	$orders = $db->query("select count(*) as c from patient_{$hid} where part_id=2 and addtime>=$tb and addtime<=$te", 1, "c");
	$renjun = @round($v["x1"] / $orders);
	$FC2->addChartData($renjun, "name=".date("n-j", _wee_date($dt)));
	$sum["x2"] += intval($renjun);
	$max["x2"] = max($max["x2"], $renjun);


	// 到院人数:
	$comes = $db->query("select count(*) as c from patient_{$hid} where part_id=2 and status=1 and order_date>=$tb and order_date<=$te", 1, "c");
	$renjun = @round($v["x1"] / $comes);
	$FC3->addChartData($renjun, "name=".date("n-j", _wee_date($dt)));
	$sum["x3"] += intval($renjun);
	$max["x3"] = max($max["x3"], $renjun);
}

$days = count($list);
$pj1 = @round($sum["x1"] / $days);
$pj2 = @round($sum["x2"] / $days);
$pj3 = @round($sum["x3"] / $days);

$max_x1 = intval($max["x1"] / 10) * 15;
if (strlen($max_x1) > 2) {
	$per = intval("1".str_repeat("0", strlen($max_x1) - 2));
	$max_x1 = intval($max_x1 / $per) * $per;
}

$FC1->setChartParams("yAxisMaxValue=".$max_x1."");

$FC1->addTrendLine("startValue={$pj1};color=ff0000;displayvalue= ;showOnTop=1");
$FC2->addTrendLine("startValue={$pj2};color=ff0000;displayvalue= ;showOnTop=1");
$FC3->addTrendLine("startValue={$pj3};color=ff0000;displayvalue= ;showOnTop=1");


function _wee_date($s) {
	return strtotime(substr($s, 0, 4)."-".substr($s, 4, 2)."-".substr($s, 6, 2)." 0:0:0");
}


?>
<html>
<head>
<title>竞价消费趋势图</title>
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
function load_url(s) {
	parent.load_box(1, 'src', s);
}
</script>
</head>

<body>
<div style="margin:15px 0 0 0px; text-align:center;">
	<button onclick="load_url('m/chhos.php'); return false;" class="buttonb" title="切换到其他医院">切换医院</button>&nbsp;
	<button onclick="location = '?go=prev'; return false;" class="button" title="切换到上一家医院">上</button>&nbsp;
	<button onclick="location = '?go=next'; return false;" class="button" title="切换到下一家医院">下</button>&nbsp;
</div>

<!-- <form method="GET" action="" onsubmit="">
	<input name="btime" id="begin_time" class="input" style="width:80px" value="<?php echo $_GET["btime"]; ?>"> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'begin_time',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="选择时间">&nbsp;～&nbsp;
	<input name="etime" id="end_time" class="input" style="width:80px" value="<?php echo $_GET["etime"]; ?>"> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'end_time',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="选择时间">&nbsp;&nbsp;
	<input type="submit" class="button" value="确定">&nbsp;&nbsp;
</form> -->

<div style="text-align:center; margin-top:20px;">
	<div style="margin-top:10px; text-align:center; font-size:14px; font-family:'微软雅黑' !important; "><?php echo $h_name; ?> <?php echo date("Y-m-d", _wee_date($day_begin))."～".date("Y-m-d", _wee_date($day_end)); ?> 百度竞价消费趋势图 (平均：<?php echo $pj1; ?>)</div>
	<div style="margin-top:0px; ">
		<?php $FC1->renderChart(); ?>
	</div>


	<div style="margin-top:10px; text-align:center; font-size:14px; font-family:'微软雅黑' !important; "><?php echo $h_name; ?> <?php echo date("Y-m-d", _wee_date($day_begin))."～".date("Y-m-d", _wee_date($day_end)); ?> 百度竞价消费预约人均趋势图 (平均：<?php echo $pj2; ?>)</div>
	<div style="margin-top:0px; ">
		<?php $FC2->renderChart(); ?>
	</div>


	<div style="margin-top:10px; text-align:center; font-size:14px; font-family:'微软雅黑' !important; "><?php echo $h_name; ?> <?php echo date("Y-m-d", _wee_date($day_begin))."～".date("Y-m-d", _wee_date($day_end)); ?> 百度竞价消费就诊人均趋势图 (平均：<?php echo $pj3; ?>)</div>
	<div style="margin-top:0px; ">
		<?php $FC3->renderChart(); ?>
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
