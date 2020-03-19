<?php
// --------------------------------------------------------
// - ����˵�� : ���� ���ݶԱ�
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2010-10-27 09:46
// --------------------------------------------------------
require "../../core/core.php";
include "web_config.php";

if (!$debug_mode) {
	//exit_html("�ù������ڵ�����...");
}

// Ҫ�������Ŀ:
if (count($_GET["sub_ids"]) > 0) {
	$_SESSION["sub_id_select"] = array();
	foreach ($_GET["sub_ids"] as $v) {
		$v = intval($v);
		if ($v > 0) {
			$_SESSION["sub_id_select"][] = $v;
		}
	}
}
if (count($_SESSION["sub_id_select"]) == 0) {
	$_SESSION["sub_id_select"] = array(1);
}
$sub_ids = $_SESSION["sub_id_select"];


$sub_name_arr = array();
foreach ($sub_ids as $v) {
	$sub_name_arr[] = $sub_type_arr[$v];
}
$sub_name = implode(" ", $sub_name_arr);

$sub_ids_str = implode(",", $sub_ids);


// ��ʼֵΪ����:
if ($_GET["btime"] == '') {
	$_GET["btime"] = date("Y-m-d", mktime(0,0,0,date("m"), 1));
}
if ($_GET["etime"] == '') {
	$_GET["etime"] = date("Y-m-d", strtotime("+1 month", strtotime($_GET["btime"]." 0:0:0")) - 1);
}


$cal_field = explode(" ", "click click_local click_other zero_talk ok_click ok_click_local ok_click_other talk talk_local talk_other orders order_local order_other come come_local come_other");

// �����ֶ�:
$f_arr = array();
foreach ($cal_field as $v) {
	$f_arr[] = 'sum('.$v.') as '.$v;
}
$f_str = implode(", ", $f_arr);


// ��������:
if ($_GET["btime"] && $_GET["etime"]) {

	// ʱ���:
	$btime = strtotime($_GET["btime"]." 0:0:0");
	$etime = strtotime($_GET["etime"]." 23:59:59");

	$b = date("Ymd", $btime);
	$e = date("Ymd", $etime);

	//��ѯ��ҽԺ��������:
	$list = $db->query("select kefu, $f_str from $table where hid=$hid and sub_id in ($sub_ids_str) and date>=$b and date<=$e group by kefu order by kefu asc", "kefu");

	$real_kefu_list = array_keys($list);

	//echo "<pre>";
	//print_r($list);
	//exit;


	// �������:
	/*
	$list = $dt_count = array();
	foreach ($tmp_list as $v) {
		$dt = $v["kefu"];
		if (!in_array($dt, $kefu_list)) {
			$kefu_list[] = $dt;
		}
		$dt_count[$dt] += 1;
		foreach ($v as $x => $y) {
			if ($y && is_numeric($y)) {
				$list[$dt][$x] = floatval($list[$dt][$x]) + $y;
			}
		}
	}
	*/

	// ��������:
	foreach ($list as $k => $v) {
		// ��ѯԤԼ��:
		$list[$k]["per_1"] = @round($v["talk"] / $v["click"] * 100, 1);
		// Ԥ��������:
		$list[$k]["per_2"] = @round($v["come"] / $v["orders"] * 100, 1);
		// ��ѯ������:
		$list[$k]["per_3"] = @round($v["come"] / $v["click"] * 100, 1);
		$list[$k]["per_31"] = @round($v["come_local"] / $v["click_local"] * 100, 1);
		$list[$k]["per_32"] = @round($v["come_other"] / $v["click_other"] * 100, 1);
		// ��Ч��ѯ��:
		$list[$k]["per_4"] = @round($v["ok_click"] / $v["click"] * 100, 1);
		// ��ЧԤԼ��:
		$list[$k]["per_5"] = @round($v["talk"] / $v["ok_click"] * 100, 1);
	}

	// ����ͳ������:

	// ����:
	$sum_list = array();
	foreach ($list as $v) {
		foreach ($cal_field as $f) {
			$sum_list[$f] = floatval($sum_list[$f]) + $v[$f];

			// ��ѯԤԼ��:
			$sum_list["per_1"] = @round($sum_list["talk"] / $sum_list["click"] * 100, 1);
			// Ԥ��������:
			$sum_list["per_2"] = @round($sum_list["come"] / $sum_list["orders"] * 100, 1);
			// ��ѯ������:
			$sum_list["per_3"] = @round($sum_list["come"] / $sum_list["click"] * 100, 1);
			$sum_list["per_31"] = @round($sum_list["come_local"] / $sum_list["click_local"] * 100, 1);
			$sum_list["per_32"] = @round($sum_list["come_other"] / $sum_list["click_other"] * 100, 1);
			// ��Ч��ѯ��:
			$sum_list["per_4"] = @round($sum_list["ok_click"] / $sum_list["click"] * 100, 1);
			// ��ЧԤԼ��:
			$sum_list["per_5"] = @round($sum_list["talk"] / $sum_list["ok_click"] * 100, 1);
		}
	}


	// ͳ�Ƹ�ҽԺ�����ܳɱ������ڼ����˾��ɱ�:
	$zongchengben = $db->query("select sum(xiaofei) as xiaofei from jingjia_xiaofei where hid=$hid and date>=$b and date<=$e", 1, "xiaofei");

}


// �Ƿ�����ӻ��޸�����:
$can_edit_data = 0;
if ($debug_mode || in_array($uinfo["part_id"], array(9)) || in_array($uid, explode(",", $type_detail["uids"]))) {
	$can_edit_data = 1;
}


$show_chengben = 0;
if ($debug_mode || $username == "admin" || $username == "������") {
	$show_chengben = 1;
}


/*
// ------------------ ���� -------------------
*/
function my_show($arr, $default_value='', $click='') {
	$s = '';
	foreach ($arr as $v) {
		if ($v == $default_value) {
			$s .= '<b>'.$v.'</b>';
		} else {
			$s .= '<a href="javascript:void(0);" onclick="'.$click.'">'.$v.'</a>';
		}
	}
	return $s;
}


// ҳ�濪ʼ ------------------------
?>
<html>
<head>
<title>��������ͳ��</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<script src="/res/sorttable.js" language="javascript"></script>
<style>
* {font-family:"Tahoma"; }
body {padding:5px 8px; }
form {display:inline; }
#date_tips {float:left; font-weight:bold; padding-top:1px; }
#ch_date {float:left; margin-left:20px; }
.site_name {display:block; padding:4px 0px;}
.site_name, .site_name a {font-family:"Arial", "Tahoma"; }
.ch_date_a b, .ch_date_a a {font-family:"Arial"; }
.ch_date_a b {border:0px; padding:1px 5px 1px 5px; color:red; }
.ch_date_a a {border:0px; padding:1px 5px 1px 5px; }
.ch_date_a a:hover {border:1px solid silver; padding:0px 4px 0px 4px; }
.ch_date_b {padding-top:8px; text-align:left; width:80%; color:silver; }
.ch_date_b a {padding:0 3px; }

.a_kefu {color:; }
.a_kefu_dy {color:#ff59ff; }
.a_kefu_sx {color:#0000ff; }

.main_title {margin:0 auto; padding-top:20px; padding-bottom:15px; text-align:center; font-weight:bold; font-size:12px; font-family:"����"; }

.item {padding:8px 3px 6px 3px !important; }

.rate_tips {padding:30px 0 0 30px; line-height:24px; }

.column_sortable {color:blue !important; cursor:pointer;}
.sorttable_nosort {color:gray; }

.huizong td {font-weight:bold; color:#8000ff !important; }

#cur_hospital_all_xiangmu {padding-top:20px; text-align:center; }
</style>

<script language="javascript">
function update_date(type, o) {
	byid("date_"+type).value = parseInt(o.innerHTML, 10);

	var a = parseInt(byid("date_1").value, 10);
	var b = parseInt(byid("date_2").value, 10);

	var s = a + '' + (b<10 ? "0" : "") + b;

	byid("date").value = s;
	byid("ch_date").submit();
}

function hgo(dir, o) {
	var obj = byid("hid");
	if (dir == "up") {
		if (obj.selectedIndex > 1) {
			obj.selectedIndex = obj.selectedIndex - 1;
			obj.onchange();
			o.disabled = true;
		} else {
			parent.msg_box("�Ѿ�����ǰ��", 3);
		}
	}
	if (dir == "down") {
		if (obj.selectedIndex < obj.options.length-1) {
			obj.selectedIndex = obj.selectedIndex + 1;
			obj.onchange();
			o.disabled = true;
		} else {
			parent.msg_box("�Ѿ������һ����", 3);
		}
	}
}

function write_dt(da, db) {
	byid("begin_time").value = da;
	byid("end_time").value = db;
}

</script>
</head>

<body>
<div style="margin:10px 0 0 0px; text-align:center; ">
	<b>ҽԺ��</b>
	<form method="GET" style="margin-left:6px;">
		<select name="hid" id="hid" class="combo" onchange="this.form.submit()">
			<option value="" style="color:gray">-��ѡ��ҽԺ-</option>
			<?php echo list_option($types, "_key_", "_value_", $hid); ?>
		</select>&nbsp;
		<button class="button" onclick="hgo('up',this);">��</button>&nbsp;
		<button class="button" onclick="hgo('down',this);">��</button>
		<input type="hidden" name="btime" value="<?php echo $_GET["btime"]; ?>">
		<input type="hidden" name="etime" value="<?php echo $_GET["etime"]; ?>">
		<input type="hidden" name="check_all" value="<?php echo $_GET["check_all"]; ?>">
		<!-- <input type="hidden" name="checked_xm" value="<?php echo $sub_ids_str; ?>"> -->
		<input type="hidden" name="op" value="change_type">
	</form>&nbsp;&nbsp;&nbsp;

	<b>ʱ��Σ�</b>
	<form method="GET">
		<input name="btime" id="begin_time" class="input" style="width:100px" value="<?php echo $_GET["btime"]; ?>"> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'begin_time',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��">&nbsp;&nbsp;��&nbsp;&nbsp;
		<input name="etime" id="end_time" class="input" style="width:100px" value="<?php echo $_GET["etime"]; ?>"> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'end_time',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��">&nbsp;&nbsp;
		<input type="submit" class="button" value="ȷ��">&nbsp;&nbsp;

<?php
$lmb = strtotime("-1 month", strtotime($_GET["btime"]));
$lme = strtotime($_GET["btime"]) - 1;
$nmb = strtotime("+1 month", strtotime($_GET["btime"]));
$nme = strtotime("+1 month", $nmb) - 1;
?>
		<input type="button" class="button" onclick="write_dt('<?php echo date("Y-m-d", $lmb); ?>', '<?php echo date("Y-m-d", $lme); ?>'); this.form.submit();" value="����">&nbsp;
		<input type="button" class="button" onclick="write_dt('<?php echo date("Y-m-d", $nmb); ?>', '<?php echo date("Y-m-d", $nme); ?>'); this.form.submit();" value="����">&nbsp;
		<input type="hidden" name="checked_xm" value="<?php echo $sub_ids_str; ?>">
		<input type="hidden" name="check_all" value="<?php echo $_GET["check_all"]; ?>">
	</form>
</div>



<form id="xiangmu_huizong" method="GET">
<div id="cur_hospital_all_xiangmu">
	<input type="submit" onclick="set_check_all(this);" class="buttonb" value="����ȫ��">&nbsp;&nbsp;
	<span id="xm_check_area">
<?php
$_out = array();
foreach ($sub_type_arr as $_id => $_name) {
	$chk = '';
	if (in_array($_id, $sub_ids)) {
		$chk = "checked";
	}
	$_out[] = '<input type="checkbox" '.$chk.' name="sub_ids[]" value="'.$_id.'" id="xm_'.$_id.'"><label for="xm_'.$_id.'">'.$_name.'</label>';
}
echo implode(" ", $_out);
?>
	</span>
	<input type="hidden" name="btime" value="<?php echo $_GET["btime"]; ?>">
	<input type="hidden" name="etime" value="<?php echo $_GET["etime"]; ?>">
	<input type="hidden" name="check_all" id="check_all_01" value="">
	<input type="submit" class="button" value="����">
</div>
<script type="text/javascript">
function set_check_all() {
	var objs = byid("xm_check_area").getElementsByTagName("INPUT");
	for (var i=0; i<objs.length; i++) {
		objs[i].checked = true;
	}
	byid("check_all_01").value = "1";
}
</script>
</form>


<?php if ($_GET["btime"] && $_GET["etime"]) { ?>

<div class="main_title"><?php echo $h_name." ".$sub_name; ?> <?php echo $_GET["btime"]; ?> �� <?php echo $_GET["etime"]; ?> �ͷ����ݶԱ�</div>

<table width="100%" align="center" class="list sortable">
	<tr>
		<td class="head column_sortable" title="���������" align="center">�ͷ�</td>
		<td class="head column_sortable" title="���������" align="center" style="color:red !important">�ܵ��</td>
		<td class="head column_sortable" title="���������" align="center">����</td>
		<td class="head column_sortable" title="���������" align="center">���</td>
		<td class="head column_sortable" title="���������" align="center" style="color:red !important">����Ч</td>
		<td class="head column_sortable" title="���������" align="center">����</td>
		<td class="head column_sortable" title="���������" align="center">���</td>

		<td class="head column_sortable" title="���������" align="center" style="color:red !important">ԤԼ</td>
		<td class="head column_sortable" title="���������" align="center">����</td>
		<td class="head column_sortable" title="���������" align="center">���</td>
		<td class="head column_sortable" title="���������" align="center" style="color:red !important">Ԥ�Ƶ�</td>
		<td class="head column_sortable" title="���������" align="center">����</td>
		<td class="head column_sortable" title="���������" align="center">���</td>
		<td class="head column_sortable" title="���������" align="center" style="color:red !important">ʵ�ʵ�</td>
		<td class="head column_sortable" title="���������" align="center">����</td>
		<td class="head column_sortable" title="���������" align="center">���</td>

		<td class="head column_sortable" title="���������" align="center" style="color:red !important">��ѯ<br>ԤԼ��</td>
		<td class="head column_sortable" title="���������" align="center" style="color:red !important">Ԥ��<br>������</td>

		<td class="head column_sortable" title="���������" align="center" style="color:red !important">��Ч<br>��ѯ��</td>
		<td class="head column_sortable" title="���������" align="center" style="color:red !important">��Ч<br>ԤԼ��</td>

		<td class="head column_sortable" title="���������" align="center" style="color:red !important">��ѯ<br>������</td>
		<td class="head column_sortable" title="������ѯ������" align="center">����</td>
		<td class="head column_sortable" title="�����ѯ������" align="center">���</td>

		<td class="head column_sortable" title="���������" align="center">��ʧ<br>����</td>
		<td class="head column_sortable" title="���������" align="center">���</td>
<?php if ($show_chengben) { ?>
		<td class="head column_sortable" title="���������" align="center">�˾��ɱ�</td>
<?php } ?>
	</tr>

<?php
foreach ($real_kefu_list as $i) {

	$class = $kefu_class_arr[$i];

	$tips = "�װ�ͷ�";
	if ($class == "a_kefu_dy") $tips = "��ҹ��ͷ�";
	if ($class == "a_kefu_sx") $tips = "ʵϰ�ͷ�";

	$li = $list[$i];
	if (!is_array($li)) {
		$li = array();
	}
	$show_name = $i;
	if (!in_array($i, $kefu_list)) {
		$show_name = ' <font style="text-decoration:line-through;color:silver;" title="�˿ͷ�����ְ">'.$i.'</font>';
	}

	// ��ʧ����:
	$liushi = @round($sum_list["come"] * $li["click"] / $sum_list["click"]) - $li["come"];
	if ($liushi > 0) {
		$liushi_sum += intval($liushi);
	} else {
		$liushi = '';
	}

	//��ʧ��:
	/*
	$liushilv = 0;
	if ($liushi > 0) {
		if ($sum_list["come"] > 0) {
			$liushilv = @round(100 * $liushi / $sum_list["come"], 1);
		} else {
			$liushilv = 100;
		}
	}
	*/

	// ���: ���=��Ա����ѯ������-ƽ����ѯ������
	$chaju = (floatval($li["per_3"]) - $sum_list["per_3"]);
	if ($chaju != 0) {
		$chaju = round($chaju, 1);
	}


	// �˾��ɱ�
	$chengben = '';
	if ($zongchengben > 0) {
		if ($li["come"] && $li["click"]) {
			$chengben = round((($zongchengben * $li["click"]) / $sum_list["click"]) / $li["come"]);
		}
	} else {
		$chengben = '<span title="û�в�ѯ���������ѣ��ɱ��޷�����">*</span>';
	}

?>
	<tr>
		<td class="item" align="center"><nobr class="<?php echo $class; ?>" title="<?php echo $tips; ?>"><?php echo $show_name; ?></nobr></td>
		<td class="item" align="center" style="color:red"><?php echo $li["click"]; ?></td>
		<td class="item" align="center"><?php echo $li["click_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["click_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["ok_click"]; ?></td>
		<td class="item" align="center"><?php echo $li["ok_click_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["ok_click_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["talk"]; ?></td>
		<td class="item" align="center"><?php echo $li["talk_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["talk_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["orders"]; ?></td>
		<td class="item" align="center"><?php echo $li["order_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["order_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["come"]; ?></td>
		<td class="item" align="center"><?php echo $li["come_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["come_other"]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_1"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_2"]); ?>%</td>

		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_4"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_5"]); ?>%</td>

		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_3"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["per_31"]); ?>%</td>
		<td class="item" align="center"><?php echo floatval($li["per_32"]); ?>%</td>

		<td class="item" align="center"><?php echo $liushi; ?></td>
		<td class="item" align="center"><?php echo $chaju ? ($chaju."%") : ""; ?></td>
<?php if ($show_chengben) { ?>
		<td class="item" align="center"><?php echo $chengben; ?></td>
<?php } ?>
	</tr>

<?php } ?>

	<tr class="huizong">
		<td class="item" align="center">����</td>
		<td class="item" align="center" style="color:red"><?php echo $sum_list["click"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["click_local"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["click_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $sum_list["ok_click"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["ok_click_local"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["ok_click_other"]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo $sum_list["talk"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["talk_local"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["talk_other"]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo $sum_list["orders"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["order_local"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["order_other"]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo $sum_list["come"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["come_local"]; ?></td>
		<td class="item" align="center"><?php echo $sum_list["come_other"]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo $sum_list["per_1"]; ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo $sum_list["per_2"]; ?>%</td>

		<td class="item" align="center" style="color:red"><?php echo $sum_list["per_4"]; ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo $sum_list["per_5"]; ?>%</td>

		<td class="item" align="center" style="color:red"><?php echo $sum_list["per_3"]; ?>%</td>
		<td class="item" align="center"><?php echo $sum_list["per_31"]; ?>%</td>
		<td class="item" align="center"><?php echo $sum_list["per_32"]; ?>%</td>

		<td class="item" align="center" style="color:red"><?php echo $liushi_sum; ?></td>
		<td class="item" align="center" style="color:red"></td>
<?php if ($show_chengben) { ?>
		<td class="item" align="center" style="color:red"><?php echo $zongchengben; ?></td>
<?php } ?>
	</tr>
</table>

<div class="rate_tips">
��ѯԤԼ�� = ԤԼ���� / �ܵ��<br>
Ԥ�������� = ʵ�ʵ�Ժ���� / Ԥ�Ƶ�Ժ����<br>
��ѯ������ = ʵ�ʵ�Ժ���� / �ܵ��<br>
��Ч��ѯ�� = ��Ч��� / �ܵ��<br>
��ЧԤԼ�� = ԤԼ���� / ��Ч���<br>
</div>

<?php } ?>

<br>
<br>


</body>
</html>