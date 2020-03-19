<?php
defined("ROOT") or exit("Error.");
?>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>

<style>
#date_tips {float:left; font-weight:bold; padding-top:1px; margin-left:10px; }
#ch_date {float:left; margin-left:20px; }
.site_name {display:block; padding:4px 0px;}
.site_name, .site_name a {font-family:"Arial", "Tahoma"; }
.ch_date_a b, .ch_date_a a {font-family:"Arial"; }
.ch_date_a b {border:0px; padding:1px 5px 1px 5px; color:red; }
.ch_date_a a {border:0px; padding:1px 5px 1px 5px; }
.ch_date_a a:hover {border:1px solid silver; padding:0px 4px 0px 4px; }
.ch_date_b {padding-top:8px; text-align:left; width:80%; color:silver; }
.ch_date_b a {padding:0 3px; }

.main_title {margin:0 auto; padding-top:24px; text-align:left; margin-left:10px; font-weight:bold; font-size:12px; font-family:"宋体"; }
</style>

<script language="javascript">

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


<div class="main_title"><?php echo $h_name; ?> - <?php echo date("Y-n-j", $date_time); ?> 网站统计数据</div>

<div class="space"></div>

<?php echo $t->show(); ?>

<!-- <div class="main_title">搜索引擎数据</div>
<div class="space"></div>


<table class="list" width="100%">
	<tr>
		<td align="center" style="padding:10px;"><font color="silver">(解决之中)</font></td>
	</tr>
</table> -->



<div class="space"></div>
</body>
</html>