<?php
// --------------------------------------------------------
// - 功能说明 : 地区报表
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2011-08-04
// --------------------------------------------------------
require "../../core/core.php";

if ($hid == 0) {
	msg_box("对不起，没有选择医院，不能执行该操作！", "back", 1, 5);
}

$table = "patient_".$hid;

// 医院名称:
$h_name = $db->query("select name from hospital where id=$hid limit 1", "1", "name");

// 月份（包括计算该月起始时间）:
$month = $_GET["m"];
if (!$month) {
	$_GET["m"] = $month = date("Y-m");
}

$m_begin = strtotime($month."-1 0:0:0");
$m_end = strtotime("+1 month", $m_begin) - 1;

// 查询条件:
if ($key = $_GET["key"]) {
	$where = " (content like '%{$key}%' or memo like '%{$key}%') and";
}

// 计算所有地区:
$area_all = $area_ori = $db->query("select area,count(area) as c from (select if(is_local=1,'本市',if(area='', '未知', area)) as area from $table where $where order_date>=$m_begin and order_date<=$m_end) as t1 group by area order by c desc", "area", "c");

/*
echo $db->sql;
echo "<pre>";
print_r($area_all);
exit;
*/



// 合并城市:
$area_use = array();
$first = array_keys($area_all);
$first = array_shift($first); //数量最多的城市一般肯定是本地病人
$area_use[] = $first;
array_shift($area_all);

$area_merge = array();
foreach ($area_all as $k => $v) {
	if (substr_count($k, " ") > 0) {
		list($a, $b) = explode(" ", $k);
	} else {
		$a = $b = $k;
	}
	$area_merge[$a][] = $b;
}

foreach ($area_merge as $k => $v) {
	if (count($area_use) >= 10) {
		break;
	}
	$area_use[] = $k;
}

// 查询本月的所有客服:
$kefu_arr = $db->query("select distinct author from $table where $where order_date>=$m_begin and order_date<=$m_end order by binary author", "", "author");

// 所有预约量:
$order_all = $db->query("select author,count(author) as c from $table where $where order_date>=$m_begin and order_date<=$m_end group by author", "author", "c");
$order_come = $db->query("select author,count(author) as c from $table where $where status=1 and order_date>=$m_begin and order_date<=$m_end group by author", "author", "c");


// 每个地区进行一次查询:
$all = $come = array();
foreach ($area_use as $v) {
	// 总计:
	if ($v == "本市") {
		$list = $db->query("select author,count(author) as c from $table where $where is_local=1 and order_date>=$m_begin and order_date<=$m_end group by author", "author", "c");
	} else if ($v == "未知") {
		$list = $db->query("select author,count(author) as c from $table where $where is_local!=1 and area='' and order_date>=$m_begin and order_date<=$m_end group by author", "author", "c");
	} else {
		$list = $db->query("select author,count(author) as c from $table where $where area like '{$v}%' and order_date>=$m_begin and order_date<=$m_end group by author", "author", "c");
	}
	$all[$v] = $list;


	// 已到:
	if ($v == "本市") {
		$list = $db->query("select author,count(author) as c from $table where $where status=1 and is_local=1 and order_date>=$m_begin and order_date<=$m_end group by author", "author", "c");
	} else if ($v == "未知") {
		$list = $db->query("select author,count(author) as c from $table where $where status=1 and is_local!=1 and area='' and order_date>=$m_begin and order_date<=$m_end group by author", "author", "c");
	} else {
		$list = $db->query("select author,count(author) as c from $table where $where status=1 and area like '{$v}%' and order_date>=$m_begin and order_date<=$m_end group by author", "author", "c");
	}
	$come[$v] = $list;
}


?>
<html>
<head>
<title>数据报表</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<style>
form {display:inline; }
.red {color:red !important; }

.report_tips {padding:20px 0 10px 0; text-align:center; font-size:14px; font-weight:bold;  }

.list {border:2px solid #A5D1D1 !important; }
.head {}
.item {text-align:center; padding:6px 3px 4px 3px !important; }

.hl {border-left:2px solid #D0E8E8 !important; }
.hr {border-right:2px solid #D0E8E8 !important; }
.ht {border-top:2px solid #D0E8E8 !important; }
.hb {border-bottom:2px solid #D0E8E8 !important; }
</style>
<script type="text/javascript">
function load_url(s) {
	parent.load_box(1, 'src', s);
}
</script>
</head>

<body>
<div style="margin:10px 0 0 0px;">
	<form method="GET">
		<b>月份：</b>
		<input name="m" id="time_month" class="input" style="width:100px" value="<?php echo $_GET["m"]; ?>"> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'time_month',dateFmt:'yyyy-MM'})" align="absmiddle" style="cursor:pointer" title="选择月份">&nbsp;
		<b title="将搜索咨询内容和备注">查询关键词：</b>
		<input name="key" class="input" style="width:100px" value="<?php echo $_GET["key"]; ?>">&nbsp;
		<input type="submit" class="button" value="确定">
	</form>&nbsp;&nbsp;&nbsp;&nbsp;
	<button onclick="load_url('m/chhos.php'); return false;" class="buttonb" title="切换到其他医院">切换医院</button>&nbsp;&nbsp;
</div>


<div class="report_tips"><?php echo $h_name; ?> 预约病人地区分析 (总)</div>

<!-- 总数据 -->
<table class="list" width="100%">
	<tr>
<?php
$arr = array_keys($area_ori);
$arr = array_slice($arr, 0, 15);
foreach ($arr as $v) {
?>
		<th class="head"><?php echo $v; ?></th>
<?php } ?>
	</tr>

	<tr>
<?php
foreach ($arr as $v) {
?>
		<td class="item"><?php echo $area_ori[$v]; ?></td>
<?php } ?>
	</tr>
</table>

<br>

<div class="report_tips"><?php echo $h_name; ?> 预约病人地区分析 (客服)</div>
<table class="list" width="100%">
	<tr>
		<th class="head hb"></th>

		<th class="head hb hl red" colspan="2">所有地区</th>

<?php foreach ($area_use as $v) { ?>
		<th class="head hb hl red" colspan="2"><?php echo $v; ?></th>
<?php } ?>
	</tr>

	<tr>
		<th class="head hb">客服</th>

		<th class="head hb hl red">全部</th>
		<th class="head hb red">已到</th>

<?php foreach ($area_use as $v) { ?>
		<th class="head hb hl red">全部</th>
		<th class="head hb red">已到</th>
<?php } ?>
	</tr>

<?php foreach ($kefu_arr as $kf) { ?>

	<tr onmouseover="mi(this)" onmouseout="mo(this)">
		<td class="item"><?php echo $kf; ?></td>

		<td class="item hl"><?php echo $order_all[$kf]; ?></td>
		<td class="item"><?php echo $order_come[$kf]; ?></td>

<?php foreach ($area_use as $v) { ?>
		<td class="item hl"><?php echo $all[$v][$kf]; ?></td>
		<td class="item"><?php echo $come[$v][$kf]; ?></td>
<?php } ?>
	</tr>

<?php } ?>

</table>

<br>
* 注：凡标注为“未知”的，是地区选择为“外地”，但具体地区未填写，可以确定非本市病人。
<br>

</body>
</html>