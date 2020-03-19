<?php
/*
// - 功能说明 : 所有来院
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2011-05-23
*/
require "../../core/core.php";
check_power('', $pinfo) or msg_box("没有打开权限...", "back", 1);
set_time_limit(0);

// 医院列表:
$h_id_name = $db->query("select id,name from hospital order by sort desc,id asc", "id", "name");

// 可选月份:
$date_list = array();
for($i=0; $i<6; $i++) {
	$date_list[] = date("Y-m", strtotime("-{$i} month"));
}

$ty_list = array(0=>"整个医院", 2=>"网络", 3=>"电话", 1=>"网查");


$op = $_GET["op"];

// 处理时间:
if ($op == "show") {
	if ($_GET["m"] == "") $_GET["m"] = date("Y-m");
	$m = $_GET["m"];
	$tb = strtotime($m);
	$te = strtotime("+1 month", $tb);

	// 判断该月有多少天
	$d_array = array();
	for ($i = 1; $i <= 31; $i++) {
		if ($i <= 28 || checkdate(date("n", $tb), $i, date("Y", $tb))) {
			$d_array[] = $i;
		}
	}

	$time_ty = "order_date";
	$sqlwhere = "$time_ty>=$tb and $time_ty<$te and status=1";

	if ($_GET["ty"] == 2) {
		$sqlwhere .= " and part_id=2";
	} else if ($_GET["ty"] == 3) {
		$sqlwhere .= " and part_id=3";
	} else if ($_GET["ty"] == 1) {
		$sqlwhere .= " and media_from='网络' and part_id not in (2,3)";
	}

	$data = array();
	foreach ($h_id_name as $hh_id => $hh_name) {
		$data[$hh_id] = $db->query("select date_format(from_unixtime(addtime),'%e') as d,count(date_format(from_unixtime(addtime),'%e')) as e from `patient_{$hh_id}` where $sqlwhere group by date_format(from_unixtime(addtime),'%e') order by d asc", "d", "e");
	}

}

$title = '来院总报表';
?>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style>
#tiaojian {margin:15px 0 0 0px; text-align:center; }
form {display:inline; }

#result {margin-left:50px; }
.h_name {font-weight:bold; margin-top:20px; }
.h_kf {margin-left:20px; }
.kf_li {border-bottom:0px dotted silver; }

.dh td, .dt td, .ds td {border:1px solid #E4E4E4; padding:4px 3px 2px 3px; text-align:center; }
.dh td {font-weight:bold; background:#EFF8F8; }
.ds td {background:#FFF2EC; }
</style>
</head>

<body>
<div id="tiaojian">
<?php
$arr = array();
foreach ($date_list as $v) {
	$bold = ($v == $_GET["m"]) ? 1 : 0;
	$arr[] = $bold ? ('<b style="color:red">'.$v.'</b>') : ('<a href="?op=show&m='.$v.'&ty='.$_GET["ty"].'">'.$v.'</a>');
}
?>
	<b>查看月份：</b><?php echo implode(' <font color="silver">|</font> ', $arr); ?>&nbsp;&nbsp;&nbsp;&nbsp;
<?php
$arr = array();
foreach ($ty_list as $k => $v) {
	$bold = $k == intval($_GET["ty"]) ? 1 : 0;
	$arr[] = $bold ? ('<b style="color:red">'.$v.'</b>') : ('<a href="?op=show&m='.$_GET["m"].'&ty='.$k.'">'.$v.'</a>');
}
?>
	<b>类别：</b><?php echo implode(' <font color="silver">|</font> ', $arr); ?>
</div>

<?php if ($op == "show") { ?>
<table width="100%"  style="border:2px solid #DFDFDF; background:#FAFCFC; margin-top:15px;">
	<tr class="dh">
		<td>医院/科室</td>
<?php foreach ($d_array as $i) { ?>
		<td><?php echo $i; ?>日</td>
<?php } ?>
		<td>总计</td>
	</tr>

<?php foreach ($h_id_name as $hh_id => $hh_name) { ?>
	<tr class="dt" onmouseover="mi(this)" onmouseout="mo(this)">
		<td><?php echo $hh_name; ?></td>
<?php
$c1 = 0;
foreach ($d_array as $i) {
	$c1 += intval($data[$hh_id][$i]);
?>
		<td><?php echo intval($data[$hh_id][$i]); ?></td>
<?php } ?>
		<td><?php echo $c1; ?></td>
	</tr>
<?php } ?>

</table>
<?php } ?>


</body>
</html>