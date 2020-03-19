<?php
/*
// - 功能说明 : 咨询报表时间设置
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2013-4-19
*/
require "../../core/core.php";
require "config.inc.php";
$table = "zixun_hour_set";

if ($hospitals == '') {
	exit("对不起，您没有医院权限！");
}

// 医院:
$hospital_arr = $db->query("select id,name,area,sort from hospital where id in ($hospitals) order by name asc", 'id');

// 查询当前设置:
$hour_set_arr = $db->query("select * from $table", "hid");

if ($op == "edit") {
	include "set.edit.php";
	exit;
}

// 页面开始 ------------------------
?>
<html>
<head>
<title>咨询报表时间设置</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/sorttable_keep.js" language="javascript"></script>
<style type="text/css">
.column_sortable {cursor:pointer; color:blue; font-family:"微软雅黑"; }
.sorttable_nosort {font-family:"微软雅黑"; }
.hour_set_list {border:1px solid #97e6a5; }
.hour_set_list .head td {border:1px solid #e7e7e7; background:#f2f8f9; padding:4px 3px 3px 3px; font-weight:bold; }
.hour_set_list .data td {border:1px solid #e7e7e7; padding:4px 3px 3px 3px; }
.al {text-align:left; }
.ac {text-align:center; }
.yh {font-family:"微软雅黑"; }
</style>

<script type="text/javascript">
function h_set(hid) {
	var link = "/m/zixun/set.php?op=edit&hid="+hid;
	parent.load_src(1, link, 700, 200);
	return false;
}
</script>
</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center">咨询报表时间设置</td><td class="bar_right"></td></tr></table></div>
	<div class="header_center" style="font-family:'微软雅黑'; color:red; ">
		(如果不设置，则按系统默认时间段处理：8~12 12~16 16~20 20~23)
	</div>
	<div class="headers_oprate"><button onclick="self.location.reload();return false;" class="button">刷新</button></div>
</div>
<!-- 头部 end -->

<div class="space"></div>

<table id="hour_set" class="round_table hour_set_list sortable" cellpadding="0" cellspacing="0" width="100%">
	<tr class="head">
		<td class="ac column_sortable" width="" title="点击可排序">地区</td>
		<td class="ac column_sortable" width="" title="点击可排序">医院</td>
		<td class="ac column_sortable" width="" title="点击可排序">优先度</td>
		<td class="al column_sortable" width="60%" title="点击可排序">时间段设置</td>
		<td class="ac sorttable_nosort" width="">操作</td>
	</tr>

<?php
foreach ($hospital_arr as $_hid => $_hinfo) {
	$h_set = $hour_set_arr[$_hid];
?>
	<tr class="data" onmouseover="mi(this)" onmouseout="mo(this)">
		<td class="ac"><?php echo $_hinfo["area"]; ?></td>
		<td class="ac"><?php echo $_hinfo["name"]; ?></td>
		<td class="ac"><?php echo $_hinfo["sort"]; ?></td>
		<td class="al yh" id="h_set_<?php echo $_hid; ?>"><?php echo implode(" &nbsp;", hour_set_to_show(explode(",", $h_set["h_set"]))); ?></td>
		<td class="ac"><button onclick="h_set(<?php echo $_hid; ?>)" class="button">修改</button></td>
	</tr>
<?php } ?>

</table>

</body>
</html>