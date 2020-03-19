<?php
/*
// - 功能说明 : 竞价消费报表 (仅查看功能)
// - 创建作者 : 幽兰 (weelia@126.com)
// - 创建时间 : 2011-11-02
*/
$table = "jingjia_xiaofei";
require "../../core/core.php";

if (!$hid) {
	echo '<script type="text/javascript">'."\r\n";
	echo 'alert("对不起，您还没有选择医院，请点击“确定”，然后选择一家医院。");'."\r\n";
	echo 'parent.load_box(1, "src", "/m/chhos.php");'."\r\n";
	echo '</script>'."\r\n";
	exit;
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
		header("location: report.php");
	}
	exit;
}

$h_name = $db->query("select name from hospital where id=$hid limit 1", 1, "name");

// 所有竞价字段:
$all_field_arr = $db->query("select fieldname, name from jingjia_field_set order by fieldname asc", "fieldname", "name");

// 当前医院字段设置:
$h_field = $db->query("select fields from jingjia_hospital_set where hid=$hid limit 1", 1, "fields");
if ($h_field != '') {
	$h_field_arr = explode(",", $h_field);
} else {
	$h_field_arr = array_keys($all_field_arr); //使用全局
}

// 是否显示总消费：
$show_xiaofei_count = 1;






// 页面开始 ------------------------
?>
<html>
<head>
<title>竞价消费报表</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style>
.head, .head a {font-family:"微软雅黑","Verdana"; }
.item {font-family:"Tahoma"; padding:8px 3px 6px 3px !important; }
.footer_op_left {font-family:"Tahoma"; }

.date_tips {padding:10px 0 10px 5px; font-weight:bold; }
</style>
<script language="javascript">
function load_url(s) {
	parent.load_box(1, 'src', s);
}
</script>
</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $h_name; ?> 竞价消费报表</td><td class="bar_right"></td></tr></table></div>
	<div class="header_center">
		<button onclick="load_url('m/chhos.php'); return false;" class="buttonb" title="切换到其他医院">切换医院</button>&nbsp;&nbsp;
		<button onclick="location = 'report_mingxi.php';" class="buttonb" title="点击查看明细报表">明细报表</button>&nbsp;&nbsp;
		<button onclick="location = 'report.php?go=prev'; return false;" class="button" title="切换到上一家医院">上</button>&nbsp;
		<button onclick="location = 'report.php?go=next'; return false;" class="button" title="切换到下一家医院">下</button>&nbsp;
	</div>
	<div class="headers_oprate"><button onclick="history.back()" class="button" title="返回上一页">返回</button></div>
</div>
<!-- 头部 end -->

<div class="space"></div>

<!-- 按年查看 -->
<!-- 今年，去年，前年的记录 -->
<?php
$y = intval(date("Y"));
$time_arr = array(
	"今年" => array($y."0101", $y."1231"),
	"去年" => array(($y - 1)."0101", ($y - 1)."1231"),
	"前年" => array(($y - 2)."0101", ($y - 2)."1231"),
);

// 计算统计数据:
$data = array();
foreach ($time_arr as $k => $v) {
	$data[$k] = $db->query("select sum(xiaofei) as xiaofei, sum(x1) as x1, sum(x2) as x2, sum(x3) as x3, sum(x4) as x4, sum(x5) as x5, sum(x6) as x6, sum(x7) as x7, sum(x8) as x8, sum(x9) as x9, sum(x10) as x10 from $table where hid=$hid and date>=".$v[0]." and date<=".$v[1]." ", 1);
}
?>
<div class="date_tips">按年份输出(最近3年)：</div>
<table width="100%" align="center" class="list">
	<tr>
		<td class="head" align="center" width="100">年份</td>
<?php if ($show_xiaofei_count) { ?>
		<td class="head" align="center">总消费</td>
<?php } ?>
<?php foreach ($h_field_arr as $k) { ?>
		<td class="head" align="center"><?php echo $all_field_arr[$k]; ?></td>
<?php } ?>
	</tr>

<?php foreach ($time_arr as $k => $v) { ?>
	<tr>
		<td class="item" align="center"><?php echo $k; ?></td>
<?php if ($show_xiaofei_count) { ?>
		<td class="item" align="center"><?php echo $data[$k]["xiaofei"]; ?></td>
<?php } ?>
<?php foreach ($h_field_arr as $k2) { ?>
		<td class="item" align="center"><?php echo $data[$k][$k2]; ?></td>
<?php } ?>
	</tr>
<?php } ?>
</table>

<br>


<!-- 按月份查看 -->
<!-- 最近6个月的记录 -->
<?php
$thism = strtotime(date("Y-m")."-01 0:0:0");
$time_arr = array();
for ($i = 0; $i < 12; $i++) {
	$m = strtotime("-".$i." month", $thism);
	$time_arr[date("Y-m", $m)] = array(date("Ym01", $m), date("Ym31", $m));
}

// 计算统计数据:
$data = array();
foreach ($time_arr as $k => $v) {
	$data[$k] = $db->query("select sum(xiaofei) as xiaofei, sum(x1) as x1, sum(x2) as x2, sum(x3) as x3, sum(x4) as x4, sum(x5) as x5, sum(x6) as x6, sum(x7) as x7, sum(x8) as x8, sum(x9) as x9, sum(x10) as x10 from $table where hid=$hid and date>=".$v[0]." and date<=".$v[1]." ", 1);
}
?>
<div class="date_tips">按月份输出(最近12个月)：</div>
<table width="100%" align="center" class="list">
	<tr>
		<td class="head" align="center" width="100">月份</td>
<?php if ($show_xiaofei_count) { ?>
		<td class="head" align="center">总消费</td>
<?php } ?>
<?php foreach ($h_field_arr as $k) { ?>
		<td class="head" align="center"><?php echo $all_field_arr[$k]; ?></td>
<?php } ?>
	</tr>

<?php foreach ($time_arr as $k => $v) { ?>
	<tr>
		<td class="item" align="center"><?php echo $k; ?></td>
<?php if ($show_xiaofei_count) { ?>
		<td class="item" align="center"><?php echo $data[$k]["xiaofei"]; ?></td>
<?php } ?>
<?php foreach ($h_field_arr as $k2) { ?>
		<td class="item" align="center"><?php echo $data[$k][$k2]; ?></td>
<?php } ?>
	</tr>
<?php } ?>
</table>





</body>
</html>