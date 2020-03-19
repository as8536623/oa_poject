<?php defined("ROOT") or exit("Error."); ?>
<?php

// ʱ�䶨��
// ����
$yesterday_begin = strtotime("-1 day");
// ����
$weekday = date("w");
if ($weekday == 0) $weekday = 7; //ÿ�ܵĿ�ʼΪ��һ, ����������
$this_week_begin = mktime(0, 0, 0, date("m"), (date("d") - $weekday + 1));
$this_week_end = strtotime("+6 days", $this_week_begin);
// ����
$last_week_begin = strtotime("-7 days", $this_week_begin);
$last_week_end = strtotime("-1 days", $this_week_begin);
// ����
$this_month_begin = mktime(0,0,0,date("m"), 1);
$this_month_end = strtotime("+1 month", $this_month_begin) - 1;
// �ϸ���
$last_month_end = $this_month_begin - 1;
$last_month_begin = strtotime("-1 month", $this_month_begin);
//����
$this_year_begin = mktime(0,0,0,1,1);
$this_year_end = strtotime("+1 year", $this_year_begin) - 1;
// ���һ����
$near_1_month_begin = strtotime("-1 month");
// ���������
$near_3_month_begin = strtotime("-3 month");
// ���һ��
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
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $pinfo["title"]." - ����"; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"><input type="button" value="����" onclick="history.back()" class="button"></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>

<div class="description">
	<div class="d_title">��ʾ��</div>
	<!-- <li class="d_item">û�����ô˵</li> -->
</div>

<div class="space"></div>

<form name="mainform" id="mainform" method="GET">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">��������</td>
	</tr>

	<tr>
		<td class="left">�ؼ��ʣ�</td>
		<td class="right"><input name="key" class="input" style="width:150px" value=""> <span class="intro">(��������Դ�����)</span></td>
	</tr>

	<tr>
		<td class="left">ҽ����</td>
		<td class="right">
			<select name="doctor_id" class="combo">
				<option value="" style="color:gray">-��ѡ��ҽ��-</option>
				<?php
				$doctor_list = $db->query("select id,name from doctor where hospital_id=$hid", "id", "name");
				echo list_option($doctor_list, "_key_", "_value_", $line["doctor_id"]);
				?>
			</select>
		</td>
	</tr>

	<tr>
		<td class="left">��ʼʱ�䣺</td>
		<td class="right"><input name="btime" id="begin_time" class="input" style="width:150px" value=""> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'begin_time',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��">
		<div id="quick_set_date">
			���<span class="quick_date">
<?php
$now = time();
$show_dates = array(
	"����" => array($now, $now),
	"����" => array($yesterday_begin, $yesterday_begin),
	"����" => array($this_week_begin, $this_week_end),
	"����" => array($last_week_begin, $last_week_end),
	"����" => array($this_month_begin, $this_month_end),
	"����" => array($last_month_begin, $last_month_end),
	"����" => array($this_year_begin, $this_year_end),
	"��һ����" => array($near_1_month_begin, $now),
	"��������" => array($near_3_month_begin, $now),
	"��һ��" => array($near_1_year_begin, $now),
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
		<td class="left">��ֹʱ�䣺</td>
		<td class="right"><input name="etime" id="end_time" class="input" style="width:150px" value=""> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'end_time',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��"> </td>
	</tr>


</table>
<div class="space"></div>

<input type="hidden" name="op" value="list">
<input type="hidden" name="from" value="search">
<div class="button_line"><input type="submit" class="submit" value="�ύ����"></div>

</form>

<div class="space"></div>
</body>
</html>