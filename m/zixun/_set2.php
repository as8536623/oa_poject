<?php
/*
// - 功能说明 : 咨询报表时间设置
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2013-4-19
*/
require "../../core/core.php";
$table = "zixun_hour_set";


// 医院:
$hospital_arr = $db->query("select id,name,area,sort from hospital order by name asc", 'id');

// 查询当前设置:
$hour_set_arr = $db->query("select * from $table", "hid");


// 操作的处理:
if ($_POST) {
	//echo "<pre>";
	//print_r($_POST);

	foreach ($_POST["h_set"] as $_hid => $h_set) {
		// 查询以前有木有:
		if ($hour_set_arr[$_hid]) {
			$db->query("update $table set h_set='$h_set' where hid='$_hid' limit 1");
		} else {
			if ($h_set != '') {
				$time = time();
				$db->query("insert into $table set hid='$_hid', h_set='$h_set'");
			}
		}
	}

	header("location: set.php");
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

.time_h {display:"block"; float:left; width:22px; height:15px; line-height:15px; font-family:"Tahoma"; text-align:center; }
.time_h:hover {color:red; background:url("img/time_round_bg.png") no-repeat; }
.time_h_sel {display:"block"; float:left; color:red; width:22px; height:15px; line-height:15px; font-family:"Tahoma"; font-weight:bold; background:url("img/time_round_bg.png") no-repeat; text-align:center; }
</style>
<script type="text/javascript">
function set_h(o, hour, hid) {
	if (o.className == "time_h") {
		o.className = "time_h_sel";
	} else {
		o.className = "time_h";
	}

	var select_h = "";
	var ls = byid("time_select_"+hid).getElementsByTagName("A");
	for (var i=0; i<ls.length; i++) {
		if (ls[i].className == "time_h_sel") {
			select_h += (select_h != '' ? "," : "") + ls[i].innerHTML;
		}
	}
	byid("h_set_hidden_"+hid).value = select_h;

	update_select_tips(hid);
}

function update_select_tips(hid) {
	var last_hour = '';
	var tip_arr = new Array();
	var tip_index = 0;
	var ls = byid("time_select_"+hid).getElementsByTagName("A");
	for (var i=0; i<ls.length; i++) {
		if (ls[i].className == "time_h_sel") {
			if (last_hour != '') {
				tip_arr[tip_index++] = last_hour+"~"+ls[i].innerHTML+"";
			}
			last_hour = ls[i].innerHTML;
		}
	}
	if (tip_arr.length == 0) {
		byid("h_set_tips_"+hid).innerHTML = '(请设置时间段)';
	} else {
		byid("h_set_tips_"+hid).innerHTML = tip_arr.join(" &nbsp; ");
	}
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

<form method="POST" onsubmit="return submit_check();">
<table id="hour_set" class="round_table hour_set_list sortable" cellpadding="0" cellspacing="0" width="100%">
	<tr class="head">
		<td class="ac column_sortable" width="6%" title="点击可排序">地区</td>
		<td class="ac column_sortable" width="10%" title="点击可排序">医院</td>
		<td class="ac column_sortable" width="6%" title="点击可排序">优先度</td>
		<td class="al sorttable_nosort">&nbsp;选择时间范围</td>
		<td class="al column_sortable" title="点击可排序">当前设置</td>
	</tr>

<?php
foreach ($hospital_arr as $_hid => $_hinfo) {
	$h_set = $hour_set_arr[$_hid];
?>
	<tr class="data">
		<td class="ac"><?php echo $_hinfo["area"]; ?></td>
		<td class="ac"><?php echo $_hinfo["name"]; ?></td>
		<td class="ac"><?php echo $_hinfo["sort"]; ?></td>
		<td class="al">
			<div id="time_select_<?php echo $_hid; ?>">
<?php
$t_arr = array(8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,0,1,2,3,4,5,6,7);
foreach ($t_arr as $v) {
?>
				<a href="javascript:;" class="time_h" onclick="set_h(this, <?php echo $v; ?>, <?php echo $_hid; ?>);" title="<?php echo $v; ?>点"><?php echo $v; ?></a>
<?php } ?>
			</div>
			<input type="hidden" name="h_set[<?php echo $_hid; ?>]" id="h_set_hidden_<?php echo $_hid; ?>" value="<?php echo $h_set["h_set"]; ?>">
		</td>
		<td class="al" id="h_set_tips_<?php echo $_hid; ?>"></td>
	</tr>
<?php } ?>

</table>


<div class="button_line">
	<input type="submit" class="submit" value="保存设置">
</div>

</form>

<script type="text/javascript">

var ls = document.getElementsByTagName("input");
for (var i=0; i<ls.length; i++) {
	var o = ls[i];
	if (o.type == "hidden") {
		var hid = o.id.replace("h_set_hidden_", "");

		var sel_arr = o.value.split(",");
		var ls2 = byid("time_select_"+hid).getElementsByTagName("A");
		for (var j=0; j<ls2.length; j++) {
			if (in_array(ls2[j].innerHTML, sel_arr)) {
				ls2[j].className = "time_h_sel";
			}
		}

		update_select_tips(hid);
	}
}
</script>


</body>
</html>