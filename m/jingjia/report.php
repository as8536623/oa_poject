<?php
/*
// - ����˵�� : �������ѱ��� (���鿴����)
// - �������� : ���� (weelia@126.com)
// - ����ʱ�� : 2011-11-02
*/
$table = "jingjia_xiaofei";
require "../../core/core.php";

if (!$hid) {
	echo '<script type="text/javascript">'."\r\n";
	echo 'alert("�Բ�������û��ѡ��ҽԺ��������ȷ������Ȼ��ѡ��һ��ҽԺ��");'."\r\n";
	echo 'parent.load_box(1, "src", "/m/chhos.php");'."\r\n";
	echo '</script>'."\r\n";
	exit;
}

$change_op = $_GET["go"];
if (!$hid || $change_op != '') {
	// ҽԺ�л�����:
	$hids = implode(",", $hospital_ids);
	$h_list = $db->query("select id,name from hospital where id in ($hids) order by sort desc, name asc", "", "id");

	if (!$hid) {
		$check_hid = $h_list[0];
	}
	if ($change_op == "prev") {
		$cur_k = array_search($hid, $h_list);
		if ($cur_k > 0) {
			$check_hid = $h_list[$cur_k - 1];
		} else {
			msg_box("�Ѿ�����ǰһ��ҽԺ��", "back", 1, 2);
		}
	}
	if ($change_op == "next") {
		$cur_k = array_search($hid, $h_list);
		if ($cur_k < count($h_list) - 1) {
			$check_hid = $h_list[$cur_k + 1];
		} else {
			msg_box("�Ѿ������һ��ҽԺ��", "back", 1, 2);
		}
	}
	if ($check_hid > 0) {
		$_SESSION["hospital_id"] = $check_hid;
		header("location: report.php");
	}
	exit;
}

$h_name = $db->query("select name from hospital where id=$hid limit 1", 1, "name");

// ���о����ֶ�:
$all_field_arr = $db->query("select fieldname, name from jingjia_field_set order by fieldname asc", "fieldname", "name");

// ��ǰҽԺ�ֶ�����:
$h_field = $db->query("select fields from jingjia_hospital_set where hid=$hid limit 1", 1, "fields");
if ($h_field != '') {
	$h_field_arr = explode(",", $h_field);
} else {
	$h_field_arr = array_keys($all_field_arr); //ʹ��ȫ��
}

// �Ƿ���ʾ�����ѣ�
$show_xiaofei_count = 1;






// ҳ�濪ʼ ------------------------
?>
<html>
<head>
<title>�������ѱ���</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style>
.head, .head a {font-family:"΢���ź�","Verdana"; }
.item {font-family:"Tahoma"; padding:8px 3px 6px 3px !important; }
.footer_op_left {font-family:"Tahoma"; }

.date_tips {padding:10px 0 10px 5px; font-weight:bold; }
</style>
<script language="javascript">
function load_url(s) {
	parent.load_box(1, 'src', s);
}
</script>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $h_name; ?> �������ѱ���</td><td class="bar_right"></td></tr></table></div>
	<div class="header_center">
		<button onclick="load_url('m/chhos.php'); return false;" class="buttonb" title="�л�������ҽԺ">�л�ҽԺ</button>&nbsp;&nbsp;
		<button onclick="location = 'report_mingxi.php';" class="buttonb" title="����鿴��ϸ����">��ϸ����</button>&nbsp;&nbsp;
		<button onclick="location = 'report.php?go=prev'; return false;" class="button" title="�л�����һ��ҽԺ">��</button>&nbsp;
		<button onclick="location = 'report.php?go=next'; return false;" class="button" title="�л�����һ��ҽԺ">��</button>&nbsp;
	</div>
	<div class="headers_oprate"><button onclick="history.back()" class="button" title="������һҳ">����</button></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>

<!-- ����鿴 -->
<!-- ���꣬ȥ�꣬ǰ��ļ�¼ -->
<?php
$y = intval(date("Y"));
$time_arr = array(
	"����" => array($y."0101", $y."1231"),
	"ȥ��" => array(($y - 1)."0101", ($y - 1)."1231"),
	"ǰ��" => array(($y - 2)."0101", ($y - 2)."1231"),
);

// ����ͳ������:
$data = array();
foreach ($time_arr as $k => $v) {
	$data[$k] = $db->query("select sum(xiaofei) as xiaofei, sum(x1) as x1, sum(x2) as x2, sum(x3) as x3, sum(x4) as x4, sum(x5) as x5, sum(x6) as x6, sum(x7) as x7, sum(x8) as x8, sum(x9) as x9, sum(x10) as x10 from $table where hid=$hid and date>=".$v[0]." and date<=".$v[1]." ", 1);
}
?>
<div class="date_tips">��������(���3��)��</div>
<table width="100%" align="center" class="list">
	<tr>
		<td class="head" align="center" width="100">���</td>
<?php if ($show_xiaofei_count) { ?>
		<td class="head" align="center">������</td>
<?php } ?>
<?php foreach ($h_field_arr as $k) { ?>
		<td class="head" align="center"><?php echo $all_field_arr[$k]; ?></td>
<?php } ?>
	</tr>

<?php foreach ($time_arr as $k => $v) { ?>
	<tr>
		<td class="item" align="center"><?php echo $k; ?></td>
<?php if ($show_xiaofei_count) { ?>
		<td class="item" align="center"><?php echo $data[$k]["xiaofei"]; ?></td>
<?php } ?>
<?php foreach ($h_field_arr as $k2) { ?>
		<td class="item" align="center"><?php echo $data[$k][$k2]; ?></td>
<?php } ?>
	</tr>
<?php } ?>
</table>

<br>


<!-- ���·ݲ鿴 -->
<!-- ���6���µļ�¼ -->
<?php
$thism = strtotime(date("Y-m")."-01 0:0:0");
$time_arr = array();
for ($i = 0; $i < 12; $i++) {
	$m = strtotime("-".$i." month", $thism);
	$time_arr[date("Y-m", $m)] = array(date("Ym01", $m), date("Ym31", $m));
}

// ����ͳ������:
$data = array();
foreach ($time_arr as $k => $v) {
	$data[$k] = $db->query("select sum(xiaofei) as xiaofei, sum(x1) as x1, sum(x2) as x2, sum(x3) as x3, sum(x4) as x4, sum(x5) as x5, sum(x6) as x6, sum(x7) as x7, sum(x8) as x8, sum(x9) as x9, sum(x10) as x10 from $table where hid=$hid and date>=".$v[0]." and date<=".$v[1]." ", 1);
}
?>
<div class="date_tips">���·����(���12����)��</div>
<table width="100%" align="center" class="list">
	<tr>
		<td class="head" align="center" width="100">�·�</td>
<?php if ($show_xiaofei_count) { ?>
		<td class="head" align="center">������</td>
<?php } ?>
<?php foreach ($h_field_arr as $k) { ?>
		<td class="head" align="center"><?php echo $all_field_arr[$k]; ?></td>
<?php } ?>
	</tr>

<?php foreach ($time_arr as $k => $v) { ?>
	<tr>
		<td class="item" align="center"><?php echo $k; ?></td>
<?php if ($show_xiaofei_count) { ?>
		<td class="item" align="center"><?php echo $data[$k]["xiaofei"]; ?></td>
<?php } ?>
<?php foreach ($h_field_arr as $k2) { ?>
		<td class="item" align="center"><?php echo $data[$k][$k2]; ?></td>
<?php } ?>
	</tr>
<?php } ?>
</table>





</body>
</html>