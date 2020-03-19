<?php
/*
// - 功能说明 : 搜索
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2009-05-02 15:47
*/

// 搜索提交之后:
if ($_GET["from"] == "search") {
	list($a, $url_end) = explode("?", $_SERVER["REQUEST_URI"], 2);
	$url_end = str_replace("op=search", "", $url_end);
	$url = "/m/patient/patient.php?".$url_end;

	// 记录本次参数(用于下次搜索时 修改搜索条件) 2011-11-03
	$_SESSION["search_condition"] = @serialize($_GET);

	echo '正在搜索，请稍候...'."\r\n";
	echo '<script>'."\r\n";
	echo 'parent.byid("sys_frame").src = "'.$url.'";'."\r\n";
	echo 'setTimeout("parent.load_src(0)", 300);'."\r\n";
	echo '</script>'."\r\n";
	exit;
}

$p_type = $uinfo["part_id"]; // 0,1,2,3,4


// 最近6个月：
$t_6m = strtotime("-6 month");

// 客服 导医
$kefu_23_list = $db->query("select distinct author from $table where part_id in (2,3) and author!='' and addtime>$t_6m order by binary author", "", "author");

$kefu_4_list = $db->query("select distinct author from $table where part_id=4 and author!='' and addtime>$t_6m order by binary author");

// 医生
$doctor_list = $db->query("select name from doctor where hospital_id='$hid'");

// 疾病
$disease_list = $db->query("select id,name from disease where hospital_id=$hid");

// 科室
$depart_list = $db->query("select id,name from depart where hospital_id=$hid");

// 搜索引擎
$engine_list = $db->query("select id,name from engine", "id", "name");

// 媒体来源
$media_from_array = explode(" ", "网络 电话");
$media_2 = $db->query("select name from media where (hospital_id=0 or hospital_id=$hid) order by sort desc,addtime asc", "", "name");
foreach ($media_2 as $v) {
	if ($v != '' && !in_array($v, $media_from_array)) {
		$media_from_array[] = $v;
	}
}

// 时间定义
// 昨天
$yesterday_begin = strtotime("-1 day");
// 明天
$tomorrow_begin = strtotime("+1 day");
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

// 本周
$weekday = date("w");
if ($weekday == 0) $weekday = 7; //每周的开始为周一, 而不是周日
$this_week_begin = mktime(0, 0, 0, date("m"), (date("d") - $weekday + 1));



$se = array();
if ($_SESSION["search_condition"]) {
	$se = @unserialize($_SESSION["search_condition"]);
}


?>
<html>
<head>
<title>病人搜索</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/wee_date.js" language="javascript"></script>
<style>
.sep {color:gray; padding:0 3px 0 3px; }
.head_tips {border:1px solid #79acc1; background:#fffaf7; padding:4px 10px 2px 10px;  }
</style>
<script language="javascript">
function write_dt(da, db) {
	byid("begin_time").value = da;
	byid("end_time").value = db;
}
</script>
</head>

<body>

<div class="head_tips">默认会记录上次搜索条件，如需清空记忆的条件，全新搜索，请点击这里：<a href="?op=new_search" title="清空记忆的条件，重新搜索"><b>[清空记忆条件]</b></a></div>
<div class="space"></div>

<form name="mainform" action="?" method="GET">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">关键词</td>
	</tr>
	<tr>
		<td class="left">关键词：</td>
		<td class="right"><input name="key" class="input" style="width:150px" value="<?php echo $se["engine_key"]; ?>"> <span class="intro">(留空则忽略此条件)</span></td>
	</tr>
	<tr>
		<td colspan="2" class="head">时间限制</td>
	</tr>
	<tr>
		<td class="left">时间类型：</td>
		<td class="right">
			<select name="time_type" class="combo">
				<option value="" style="color:gray">--请选择--</option>
<?php
$time_arr = array("order_date" => "预约时间", "addtime" => "客服添加时间");
echo list_option($time_arr, "_key_", "_value_", $se["time_type"]);
?>
			</select>
			<span class="intro">选择搜索的时间类型，默认为预约时间</span>
		</td>
	</tr>
	<tr>
		<td class="left">起始时间：</td>
		<td class="right">
			<input name="btime" id="begin_time" class="input" style="width:150px" value="<?php echo $se["btime"]; ?>"> <img src="/res/img/calendar.gif" id="order_date" onClick="wee_date_show_picker('begin_time')" align="absmiddle" style="cursor:pointer" title="选择时间"> <br>速填：
<?php
	$show_day = array(
		"昨天" => array($yesterday_begin, 0),
		"今天" => array(time(), 0),
		"明天" => array($tomorrow_begin, 0),
		"后天" => array(strtotime("+2 day"), 0),

		"周六" => array(strtotime("next Saturday"), 0),
		"周日" => array(strtotime("next Sunday"), 0),

		"上周" => array(strtotime("-7 day", $this_week_begin), $this_week_begin - 1),
		"本周" => array($this_week_begin, strtotime("+6 day", $this_week_begin)),
		"下周" => array(strtotime("+7 day", $this_week_begin), strtotime("+13 day", $this_week_begin)),

		"本月" => array($this_month_begin, $this_month_end),
		"上月" => array($last_month_begin, $last_month_end),
		"今年" => array($this_year_begin, $this_year_end),

		"近一个月" => array($near_1_month_begin, time()),
		"近三个月" => array($near_3_month_begin, time()),
		"近一年" => array($near_1_year_begin, time())
	);

	$tmp = array();
	foreach ($show_day as $d1 => $d2) {
		if ($d2[1] == 0) $d2[1] = $d2[0];
		$tmp[] = '<a href="javascript:write_dt(\''.date("Y-m-d", $d2[0]).'\', \''.date("Y-m-d", $d2[1]).'\')">'.$d1.'</a>';
	}

	echo implode('<span class="sep">|</span>', $tmp);
?>
		</td>
	</tr>
	<tr>
		<td class="left">终止时间：</td>
		<td class="right"><input name="etime" id="end_time" class="input" style="width:150px" value="<?php echo $se["etime"]; ?>"> <img src="/res/img/calendar.gif" id="order_date" onClick="wee_date_show_picker('end_time')" align="absmiddle" style="cursor:pointer" title="选择时间"></td>
	</tr>

	<tr>
		<td colspan="2" class="head">人员搜索</td>
	</tr>

<?php if ($debug_mode || $uinfo["part_admin"] || in_array($uinfo["part_id"], array(2,4))) { ?>
	<tr>
		<td class="left">搜客服：</td>
		<td class="right">
			<select name="kefu_23_name" class="combo">
				<option value='' style="color:gray">--请选择--</option>
				<?php echo list_option($kefu_23_list, '_value_', '_value_', $se["kefu_23_name"]); ?>
			</select>
			<span class="intro">指定要搜索的客服 (不选则忽略此条件)</span>
		</td>
	</tr>
<?php } ?>

<?php if ($debug_mode || $uinfo["part_admin"]) { ?>
	<tr>
		<td class="left">搜医生：</td>
		<td class="right">
			<select name="doctor_name" class="combo">
				<option value='' style="color:gray">--请选择--</option>
				<?php echo list_option($doctor_list, 'name', 'name', $se["doctor_name"]); ?>
			</select>
			<span class="intro">指定要搜索的接待医生 (不选则忽略此条件)</span>
		</td>
	</tr>
<?php } ?>

	<tr>
		<td colspan="2" class="head">更多搜索项</td>
	</tr>

	<tr>
		<td class="left">赴约状态：</td>
		<td class="right">
			<select name="come" class="combo">
				<option value='' style="color:gray">--请选择--</option>
<?php
$come_arr = array("1" => "已到", "-1" => "未到");
echo list_option($come_arr, '_key_', '_value_', $se["come"])
?>
			</select>
			<span class="intro">(不选则忽略此条件)</span>
		</td>
	</tr>
	<tr>
		<td class="left">疾病类型：</td>
		<td class="right">
			<select name="disease" class="combo">
				<option value='' style="color:gray">--请选择--</option>
				<?php echo list_option($disease_list, "id", "name", $se["disease"]); ?>
			</select>
			<span class="intro">(不选则忽略此条件)</span>
		</td>
	</tr>

	<tr>
		<td class="left">部门：</td>
		<td class="right">
			<select name="part_id" class="combo">
				<option value='' style="color:gray">--请选择--</option>
<?php
$part_id_arr = array(2 => "网络", 3 => "电话", 4 => "导医");
echo list_option($part_id_arr, "_key_", "_value_", $se["part_id"]);
?>
			</select>
			<span class="intro">(不选则忽略此条件)</span>
		</td>
	</tr>


<?php if (count($depart_list) > 0) { ?>
	<tr>
		<td class="left">科室：</td>
		<td class="right">
			<select name="depart" class="combo">
				<option value='' style="color:gray">--请选择--</option>
				<?php echo list_option($depart_list, "id", "name", $se["depart"]); ?>
			</select>
			<span class="intro">(不选则忽略此条件)</span>
		</td>
	</tr>
<?php } ?>

	<tr>
		<td class="left">媒体来源：</td>
		<td class="right">
			<select name="media" class="combo">
				<option value='' style="color:gray">--请选择--</option>
				<?php echo list_option($media_from_array, "_value_", "_value_", $se["media"]); ?>
			</select>
			<span class="intro">(不选则忽略此条件)</span>
		</td>
	</tr>

</table>

<input type="hidden" name="op" value="search">
<input type="hidden" name="from" value="search">

<div class="button_line">
	<input type="submit" class="submit" value="搜索">
</div>

</form>
</body>
</html>