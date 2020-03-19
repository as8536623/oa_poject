<?php
/*
// - ����˵�� : ����
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2009-05-02 15:47
*/

// �����ύ֮��:
if ($_GET["from"] == "search") {
	list($a, $url_end) = explode("?", $_SERVER["REQUEST_URI"], 2);
	$url_end = str_replace("op=search", "", $url_end);
	$url = "/m/patient/patient.php?".$url_end;

	// ��¼���β���(�����´�����ʱ �޸���������) 2011-11-03
	$_SESSION["search_condition"] = @serialize($_GET);

	echo '�������������Ժ�...'."\r\n";
	echo '<script>'."\r\n";
	echo 'parent.byid("sys_frame").src = "'.$url.'";'."\r\n";
	echo 'setTimeout("parent.load_src(0)", 300);'."\r\n";
	echo '</script>'."\r\n";
	exit;
}

$p_type = $uinfo["part_id"]; // 0,1,2,3,4


// ���6���£�
$t_6m = strtotime("-6 month");

// �ͷ� ��ҽ
$kefu_23_list = $db->query("select distinct author from $table where part_id in (2,3) and author!='' and addtime>$t_6m order by binary author", "", "author");

$kefu_4_list = $db->query("select distinct author from $table where part_id=4 and author!='' and addtime>$t_6m order by binary author");

// ҽ��
$doctor_list = $db->query("select name from doctor where hospital_id='$hid'");

// ����
$disease_list = $db->query("select id,name from disease where hospital_id=$hid");

// ����
$depart_list = $db->query("select id,name from depart where hospital_id=$hid");

// ��������
$engine_list = $db->query("select id,name from engine", "id", "name");

// ý����Դ
$media_from_array = explode(" ", "���� �绰");
$media_2 = $db->query("select name from media where (hospital_id=0 or hospital_id=$hid) order by sort desc,addtime asc", "", "name");
foreach ($media_2 as $v) {
	if ($v != '' && !in_array($v, $media_from_array)) {
		$media_from_array[] = $v;
	}
}

// ʱ�䶨��
// ����
$yesterday_begin = strtotime("-1 day");
// ����
$tomorrow_begin = strtotime("+1 day");
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

// ����
$weekday = date("w");
if ($weekday == 0) $weekday = 7; //ÿ�ܵĿ�ʼΪ��һ, ����������
$this_week_begin = mktime(0, 0, 0, date("m"), (date("d") - $weekday + 1));



$se = array();
if ($_SESSION["search_condition"]) {
	$se = @unserialize($_SESSION["search_condition"]);
}


?>
<html>
<head>
<title>��������</title>
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

<div class="head_tips">Ĭ�ϻ��¼�ϴ�����������������ռ����������ȫ���������������<a href="?op=new_search" title="��ռ������������������"><b>[��ռ�������]</b></a></div>
<div class="space"></div>

<form name="mainform" action="?" method="GET">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">�ؼ���</td>
	</tr>
	<tr>
		<td class="left">�ؼ��ʣ�</td>
		<td class="right"><input name="key" class="input" style="width:150px" value="<?php echo $se["engine_key"]; ?>"> <span class="intro">(��������Դ�����)</span></td>
	</tr>
	<tr>
		<td colspan="2" class="head">ʱ������</td>
	</tr>
	<tr>
		<td class="left">ʱ�����ͣ�</td>
		<td class="right">
			<select name="time_type" class="combo">
				<option value="" style="color:gray">--��ѡ��--</option>
<?php
$time_arr = array("order_date" => "ԤԼʱ��", "addtime" => "�ͷ����ʱ��");
echo list_option($time_arr, "_key_", "_value_", $se["time_type"]);
?>
			</select>
			<span class="intro">ѡ��������ʱ�����ͣ�Ĭ��ΪԤԼʱ��</span>
		</td>
	</tr>
	<tr>
		<td class="left">��ʼʱ�䣺</td>
		<td class="right">
			<input name="btime" id="begin_time" class="input" style="width:150px" value="<?php echo $se["btime"]; ?>"> <img src="/res/img/calendar.gif" id="order_date" onClick="wee_date_show_picker('begin_time')" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��"> <br>���
<?php
	$show_day = array(
		"����" => array($yesterday_begin, 0),
		"����" => array(time(), 0),
		"����" => array($tomorrow_begin, 0),
		"����" => array(strtotime("+2 day"), 0),

		"����" => array(strtotime("next Saturday"), 0),
		"����" => array(strtotime("next Sunday"), 0),

		"����" => array(strtotime("-7 day", $this_week_begin), $this_week_begin - 1),
		"����" => array($this_week_begin, strtotime("+6 day", $this_week_begin)),
		"����" => array(strtotime("+7 day", $this_week_begin), strtotime("+13 day", $this_week_begin)),

		"����" => array($this_month_begin, $this_month_end),
		"����" => array($last_month_begin, $last_month_end),
		"����" => array($this_year_begin, $this_year_end),

		"��һ����" => array($near_1_month_begin, time()),
		"��������" => array($near_3_month_begin, time()),
		"��һ��" => array($near_1_year_begin, time())
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
		<td class="left">��ֹʱ�䣺</td>
		<td class="right"><input name="etime" id="end_time" class="input" style="width:150px" value="<?php echo $se["etime"]; ?>"> <img src="/res/img/calendar.gif" id="order_date" onClick="wee_date_show_picker('end_time')" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��"></td>
	</tr>

	<tr>
		<td colspan="2" class="head">��Ա����</td>
	</tr>

<?php if ($debug_mode || $uinfo["part_admin"] || in_array($uinfo["part_id"], array(2,4))) { ?>
	<tr>
		<td class="left">�ѿͷ���</td>
		<td class="right">
			<select name="kefu_23_name" class="combo">
				<option value='' style="color:gray">--��ѡ��--</option>
				<?php echo list_option($kefu_23_list, '_value_', '_value_', $se["kefu_23_name"]); ?>
			</select>
			<span class="intro">ָ��Ҫ�����Ŀͷ� (��ѡ����Դ�����)</span>
		</td>
	</tr>
<?php } ?>

<?php if ($debug_mode || $uinfo["part_admin"]) { ?>
	<tr>
		<td class="left">��ҽ����</td>
		<td class="right">
			<select name="doctor_name" class="combo">
				<option value='' style="color:gray">--��ѡ��--</option>
				<?php echo list_option($doctor_list, 'name', 'name', $se["doctor_name"]); ?>
			</select>
			<span class="intro">ָ��Ҫ�����ĽӴ�ҽ�� (��ѡ����Դ�����)</span>
		</td>
	</tr>
<?php } ?>

	<tr>
		<td colspan="2" class="head">����������</td>
	</tr>

	<tr>
		<td class="left">��Լ״̬��</td>
		<td class="right">
			<select name="come" class="combo">
				<option value='' style="color:gray">--��ѡ��--</option>
<?php
$come_arr = array("1" => "�ѵ�", "-1" => "δ��");
echo list_option($come_arr, '_key_', '_value_', $se["come"])
?>
			</select>
			<span class="intro">(��ѡ����Դ�����)</span>
		</td>
	</tr>
	<tr>
		<td class="left">�������ͣ�</td>
		<td class="right">
			<select name="disease" class="combo">
				<option value='' style="color:gray">--��ѡ��--</option>
				<?php echo list_option($disease_list, "id", "name", $se["disease"]); ?>
			</select>
			<span class="intro">(��ѡ����Դ�����)</span>
		</td>
	</tr>

	<tr>
		<td class="left">���ţ�</td>
		<td class="right">
			<select name="part_id" class="combo">
				<option value='' style="color:gray">--��ѡ��--</option>
<?php
$part_id_arr = array(2 => "����", 3 => "�绰", 4 => "��ҽ");
echo list_option($part_id_arr, "_key_", "_value_", $se["part_id"]);
?>
			</select>
			<span class="intro">(��ѡ����Դ�����)</span>
		</td>
	</tr>


<?php if (count($depart_list) > 0) { ?>
	<tr>
		<td class="left">���ң�</td>
		<td class="right">
			<select name="depart" class="combo">
				<option value='' style="color:gray">--��ѡ��--</option>
				<?php echo list_option($depart_list, "id", "name", $se["depart"]); ?>
			</select>
			<span class="intro">(��ѡ����Դ�����)</span>
		</td>
	</tr>
<?php } ?>

	<tr>
		<td class="left">ý����Դ��</td>
		<td class="right">
			<select name="media" class="combo">
				<option value='' style="color:gray">--��ѡ��--</option>
				<?php echo list_option($media_from_array, "_value_", "_value_", $se["media"]); ?>
			</select>
			<span class="intro">(��ѡ����Դ�����)</span>
		</td>
	</tr>

</table>

<input type="hidden" name="op" value="search">
<input type="hidden" name="from" value="search">

<div class="button_line">
	<input type="submit" class="submit" value="����">
</div>

</form>
</body>
</html>