<?php
// --------------------------------------------------------
// - ����˵�� : ��������
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2011-08-04
// --------------------------------------------------------
require "../../core/core.php";

if ($hid == 0) {
	msg_box("�Բ���û��ѡ��ҽԺ������ִ�иò�����", "back", 1, 5);
}

$table = "patient_".$hid;

// ҽԺ����:
$h_name = $db->query("select name from hospital where id=$hid limit 1", "1", "name");

// �·ݣ��������������ʼʱ�䣩:
$month = $_GET["m"];
if (!$month) {
	$_GET["m"] = $month = date("Y-m");
}

$m_begin = strtotime($month."-1 0:0:0");
$m_end = strtotime("+1 month", $m_begin) - 1;

// ��ѯ����:
if ($key = $_GET["key"]) {
	$where = " (content like '%{$key}%' or memo like '%{$key}%') and";
}

// �������е���:
$area_all = $area_ori = $db->query("select area,count(area) as c from (select if(is_local=1,'����',if(area='', 'δ֪', area)) as area from $table where $where order_date>=$m_begin and order_date<=$m_end) as t1 group by area order by c desc", "area", "c");

/*
echo $db->sql;
echo "<pre>";
print_r($area_all);
exit;
*/



// �ϲ�����:
$area_use = array();
$first = array_keys($area_all);
$first = array_shift($first); //�������ĳ���һ��϶��Ǳ��ز���
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

// ��ѯ���µ����пͷ�:
$kefu_arr = $db->query("select distinct author from $table where $where order_date>=$m_begin and order_date<=$m_end order by binary author", "", "author");

// ����ԤԼ��:
$order_all = $db->query("select author,count(author) as c from $table where $where order_date>=$m_begin and order_date<=$m_end group by author", "author", "c");
$order_come = $db->query("select author,count(author) as c from $table where $where status=1 and order_date>=$m_begin and order_date<=$m_end group by author", "author", "c");


// ÿ����������һ�β�ѯ:
$all = $come = array();
foreach ($area_use as $v) {
	// �ܼ�:
	if ($v == "����") {
		$list = $db->query("select author,count(author) as c from $table where $where is_local=1 and order_date>=$m_begin and order_date<=$m_end group by author", "author", "c");
	} else if ($v == "δ֪") {
		$list = $db->query("select author,count(author) as c from $table where $where is_local!=1 and area='' and order_date>=$m_begin and order_date<=$m_end group by author", "author", "c");
	} else {
		$list = $db->query("select author,count(author) as c from $table where $where area like '{$v}%' and order_date>=$m_begin and order_date<=$m_end group by author", "author", "c");
	}
	$all[$v] = $list;


	// �ѵ�:
	if ($v == "����") {
		$list = $db->query("select author,count(author) as c from $table where $where status=1 and is_local=1 and order_date>=$m_begin and order_date<=$m_end group by author", "author", "c");
	} else if ($v == "δ֪") {
		$list = $db->query("select author,count(author) as c from $table where $where status=1 and is_local!=1 and area='' and order_date>=$m_begin and order_date<=$m_end group by author", "author", "c");
	} else {
		$list = $db->query("select author,count(author) as c from $table where $where status=1 and area like '{$v}%' and order_date>=$m_begin and order_date<=$m_end group by author", "author", "c");
	}
	$come[$v] = $list;
}


?>
<html>
<head>
<title>���ݱ���</title>
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
		<b>�·ݣ�</b>
		<input name="m" id="time_month" class="input" style="width:100px" value="<?php echo $_GET["m"]; ?>"> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'time_month',dateFmt:'yyyy-MM'})" align="absmiddle" style="cursor:pointer" title="ѡ���·�">&nbsp;
		<b title="��������ѯ���ݺͱ�ע">��ѯ�ؼ��ʣ�</b>
		<input name="key" class="input" style="width:100px" value="<?php echo $_GET["key"]; ?>">&nbsp;
		<input type="submit" class="button" value="ȷ��">
	</form>&nbsp;&nbsp;&nbsp;&nbsp;
	<button onclick="load_url('m/chhos.php'); return false;" class="buttonb" title="�л�������ҽԺ">�л�ҽԺ</button>&nbsp;&nbsp;
</div>


<div class="report_tips"><?php echo $h_name; ?> ԤԼ���˵������� (��)</div>

<!-- ������ -->
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

<div class="report_tips"><?php echo $h_name; ?> ԤԼ���˵������� (�ͷ�)</div>
<table class="list" width="100%">
	<tr>
		<th class="head hb"></th>

		<th class="head hb hl red" colspan="2">���е���</th>

<?php foreach ($area_use as $v) { ?>
		<th class="head hb hl red" colspan="2"><?php echo $v; ?></th>
<?php } ?>
	</tr>

	<tr>
		<th class="head hb">�ͷ�</th>

		<th class="head hb hl red">ȫ��</th>
		<th class="head hb red">�ѵ�</th>

<?php foreach ($area_use as $v) { ?>
		<th class="head hb hl red">ȫ��</th>
		<th class="head hb red">�ѵ�</th>
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
* ע������עΪ��δ֪���ģ��ǵ���ѡ��Ϊ����ء������������δ��д������ȷ���Ǳ��в��ˡ�
<br>

</body>
</html>