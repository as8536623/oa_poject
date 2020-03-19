<?php defined("ROOT") or exit("Error."); ?>
<?php

// 时间定义
// 昨天
$yesterday_begin = strtotime("-1 day");
// 本周
$weekday = date("w");
if ($weekday == 0) $weekday = 7; //每周的开始为周一, 而不是周日
$this_week_begin = mktime(0, 0, 0, date("m"), (date("d") - $weekday + 1));
$this_week_end = strtotime("+6 days", $this_week_begin);
// 上周
$last_week_begin = strtotime("-7 days", $this_week_begin);
$last_week_end = strtotime("-1 days", $this_week_begin);
// 本月
$this_month_begin = mktime(0,0,0,date("m"), 1);
$this_month_end = strtotime("+1 month", $this_month_begin) - 1;
// 上个月
$last_month_end = $this_month_begin - 1;
$last_month_begin = strtotime("-1 month", $this_month_begin);
//今年
$this_year_begin = mktime(0,0,0,1,1);
$this_year_end = strtotime("+1 year", $this_year_begin) - 1;
// 最近一个月
$near_1_month_begin = strtotime("-1 month");
// 最近三个月
$near_3_month_begin = strtotime("-3 month");
// 最近一年
$near_1_year_begin = strtotime("-12 month");

?>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<style>
#quick_set_date {padding-top:3px; }
.quick_date {color:silver; }
.quick_date a {padding:0 3px; }
</style>
<script language="javascript">
function write_dt(da, db) {
	byid("begin_time").value = da;
	byid("end_time").value = db;
}
</script>
</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $pinfo["title"]." - 搜索"; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"><input type="button" value="返回" onclick="history.back()" class="button"></div>
</div>
<!-- 头部 end -->

<div class="space"></div>

<div class="description">
	<div class="d_title">提示：</div>
	<!-- <li class="d_item">没想好怎么说</li> -->
</div>

<div class="space"></div>

<form name="mainform" id="mainform" method="GET">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">搜索条件</td>
	</tr>

	<tr>
		<td class="left">关键词：</td>
		<td class="right"><input name="key" class="input" style="width:150px" value=""> <span class="intro">(留空则忽略此条件)</span></td>
	</tr>

	<tr>
		<td class="left">医生：</td>
		<td class="right">
			<select name="doctor_id" class="combo">
				<option value="" style="color:gray">-请选择医生-</option>
				<?php
				$doctor_list = $db->query("select id,name from doctor where hospital_id=$hid", "id", "name");
				echo list_option($doctor_list, "_key_", "_value_", $line["doctor_id"]);
				?>
			</select>
		</td>
	</tr>

	<tr>
		<td class="left">起始时间：</td>
		<td class="right"><input name="btime" id="begin_time" class="input" style="width:150px" value=""> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'begin_time',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="选择时间">
		<div id="quick_set_date">
			速填：<span class="quick_date">
<?php
$now = time();
$show_dates = array(
	"今天" => array($now, $now),
	"昨天" => array($yesterday_begin, $yesterday_begin),
	"本周" => array($this_week_begin, $this_week_end),
	"上周" => array($last_week_begin, $last_week_end),
	"本月" => array($this_month_begin, $this_month_end),
	"上月" => array($last_month_begin, $last_month_end),
	"今年" => array($this_year_begin, $this_year_end),
	"近一个月" => array($near_1_month_begin, $now),
	"近三个月" => array($near_3_month_begin, $now),
	"近一年" => array($near_1_year_begin, $now),
);
$tmp = array();
foreach ($show_dates as $x => $y) {
	$tmp[] = '<a href="javascript:write_dt(\''.date("Y-m-d", $y[0]).'\',\''.date("Y-m-d", $y[1]).'\')">'.$x.'</a>';
}
echo implode("|", $tmp);
?>
				</span>
			</div>
		</td>
	</tr>
	<tr>
		<td class="left">终止时间：</td>
		<td class="right"><input name="etime" id="end_time" class="input" style="width:150px" value=""> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'end_time',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="选择时间"> </td>
	</tr>


</table>
<div class="space"></div>

<input type="hidden" name="op" value="list">
<input type="hidden" name="from" value="search">
<div class="button_line"><input type="submit" class="submit" value="提交资料"></div>

</form>

<div class="space"></div>
</body>
</html>