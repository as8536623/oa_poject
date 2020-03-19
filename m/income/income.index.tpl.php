<?php defined("ROOT") or exit("Error."); ?>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style>
.b {font-weight:bold; }
.m20 {margin-left:20px; }
.m40 {margin-left:40px; }
form {display:inline; }
.tip_head {color:black; font-weight:bold; margin-left:0px; margin-top:20px; margin-bottom:8px; text-align:center; }

#date_tips {float:left; font-weight:bold; padding-top:1px; margin-left:0px; }
#ch_date {float:left; margin-left:20px; }
.ch_date_a b, .ch_date_a a {font-family:"Arial"; }
.ch_date_a b {border:0px; padding:1px 5px 1px 5px; color:red; }
.ch_date_a a {border:0px; padding:1px 5px 1px 5px; }
.ch_date_a a:hover {border:1px solid silver; padding:0px 4px 0px 4px; }
.ch_date_b {padding-top:8px; text-align:left; width:80%; color:silver; }
.ch_date_b a {padding:0 3px; }
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
</script>
</head>

<body>
<!-- 头部 begin -->
<!-- <div class="headers">
	<div class="headers_title" style="width:100%; text-align:center; margin-top:15px;">
		<form id="ch_date" method="GET" onsubmit="return update_date(this)">
			<select name="y" class="combo">
				<?php echo list_option($y_array, "_value_", "_value_", substr($date, 0, 4)); ?>
			</select>
			<select name="m" class="combo">
				<?php echo list_option($m_array, "_value_", "_value_", intval(substr($date, 4, 2))); ?>
			</select>
			<select name="d" class="combo" onchange="update_date(byid('ch_date'))">
				<?php echo list_option($d_array, "_value_", "_value_", intval(substr($date, 6, 2))); ?>
			</select>
			<input type="submit" class="button" value="确定">
		</form>&nbsp;
		<input type="button" class="button" value="←前" title="向前一天" onclick="go_date('pre_day',this)">
		<input type="button" class="button" value="今天" title="查看今天" onclick="go_date('today_day',this)">
		<input type="button" class="button" value="后→" title="向后一天" onclick="go_date('next_day',this)">
		<input type="hidden" id="pre_day" value="<?php echo date("Ymd", strtotime("-1 day", $date_time)); ?>">
		<input type="hidden" id="next_day" value="<?php echo date("Ymd", strtotime("+1 day", $date_time)); ?>">
		<input type="hidden" id="today_day" value="<?php echo date("Ymd"); ?>">
	</div>
</div> -->
<!-- 头部 end -->

<div style="margin:10px 0 0 0px;">
	<div id="date_tips">请选择日期：</div>
	<form id="ch_date" method="GET">
		<span class="ch_date_a">年：<?php echo my_show($y_array, date("Y", $date_time), "update_date(1,this)"); ?>&nbsp;&nbsp;&nbsp;</span>
		<span class="ch_date_a">月：<?php echo my_show($m_array, date("m", $date_time), "update_date(2,this)"); ?>&nbsp;&nbsp;&nbsp;</span>
		<br>
		<span class="ch_date_a">日：<?php echo my_show($d_array, date("d", $date_time), "update_date(3,this)"); ?></span>

		<div class="ch_date_b"><a href="?date=<?php echo date("Ymd"); ?>">今天</a> <a href="?date=<?php echo date("Ymd", strtotime("-1 day")); ?>">昨天</a> <a href="?date=<?php echo date("Ymd", strtotime("-1 day", $date_time)); ?>">←前一天</a> <a href="?date=<?php echo date("Ymd", strtotime("+1 day", $date_time)); ?>">后一天→</a></div>

		<input type="hidden" id="date_1" value="<?php echo date("Y", $date_time); ?>">
		<input type="hidden" id="date_2" value="<?php echo date("n", $date_time); ?>">
		<input type="hidden" id="date_3" value="<?php echo date("j", $date_time); ?>">
		<input type="hidden" name="date" id="date" value="">
	</form>
	<div class="clear"></div>
</div>

<!-- 数据列表 begin -->
<div class="headers" style="margin:20px 0 6px 0;">
	<div class="headers_title" style="width:60%"><b>门诊收费</b> (<?php echo date("Y-n-j", $date_time); ?>)</div>
	<div class="headers_oprate"><input type="button" value="添加" onclick="location='?op=add&fee_type=0&date=<?php echo $date; ?>&back_url=<?php echo $back_url; ?>'" class="button" title="添加数据"></div>
</div>
<form name="mainform">
<?php echo $t->show(); ?>
</form>
<!-- 数据列表 end -->

<?php if ($hconfig["住院收费项目"] != '') { ?>
<!-- 数据列表 begin -->
<div class="space"></div>
<div class="headers" style="margin:20px 0 6px 0;">
	<div class="headers_title" style="width:60%"><b>住院收费</b> (<?php echo date("Y-n-j", $date_time); ?>)</div>
	<div class="headers_oprate"><input type="button" value="添加" onclick="location='?op=add&fee_type=1&date=<?php echo $date; ?>&back_url=<?php echo $back_url; ?>'" class="button" title="添加数据"></div>
</div>
<form name="mainform">
<?php echo $t2->show(); ?>
</form>
<!-- 数据列表 end -->
<?php } ?>

<div class="space"></div>
<table width="100%" style="margin:20px 0 6px 0;">
	<tr>
		<td style="width:50%; text-align:center;">当日初诊总人数：<b><?php echo $patient_all; ?></b></td>
		<td style="width:50%; text-align:center;">当日总营业额：<b><?php echo $yingyee_all; ?></b></td>
	</tr>
</table>

<div class="space"></div>

<div class="m40">
<?php echo $logs_str; ?>
</div>


<div class="space"></div>
</body>
</html>