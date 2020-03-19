<?php
//
// - ����˵�� : ��ҳ
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2009-10-01 => 2013-01-07
//
require "../core/core.php";
define("BTOA_MAIN", 1);

$page_begintime = now();

function _get_month_days($month = '') {
	if ($month == '') $month = date("Y-m");
	return date("j", strtotime("+1 month", strtotime($month."-1 0:0:0")) - 1);
}

$part_id_name = $db->query("select id,name from sys_part", 'id', 'name');

// ʱ�䶨�� 2011-12-28:
// ʱ�����ʼ�㶼�� YYYY-MM-DD 00:00:00 �������� YYYY-MM-DD 23:59:59
$today_tb = mktime(0,0,0); //���쿪ʼ
$today_te = strtotime("+1 day", $today_tb) - 1; //�������

$yesterday_tb = strtotime("-1 day", $today_tb); //���쿪ʼ
$yesterday_te = $today_tb - 1; //�������

$month_tb = mktime(0, 0, 0, date("m"), 1); //���¿�ʼ
$month_te = strtotime("+1 month", $month_tb) - 1; //���½���

$lastmonth_tb = strtotime("-1 month", $month_tb); //���¿�ʼ
$lastmonth_te = $month_tb - 1; //���½���

$tb_tb = strtotime("-1 month", $month_tb); //ͬ��ʱ�俪ʼ
$tb_te = strtotime("-1 month", time()); //ͬ��ʱ�����
if (date("d", $tb_te) != date("d")) {
	$tb_te = $month_tb - 1;
}

// �±�:
$yuebi_tb = strtotime("-1 month", $today_tb);
if (date("d", $yuebi_tb) != date("d", $today_tb)) {
	$yuebi_tb = $yuebi_te = -1;
} else {
	$yuebi_te = $yuebi_tb + 24*3600;
}
// �ܱ�:
$zhoubi_tb = strtotime("-7 day", $today_tb);
$zhoubi_te = $zhoubi_tb + 24*3600;

// ȥ��ͬ�±�
$yb_tb = strtotime("-1 year", $month_tb);
$_days = _get_month_days(date("Y-m", $yb_tb));
if (date("j") > $_days) { //��ǰ�����Ѿ�����ȥ��ͬ�µ�������(����29�պ�ȥ���28��)
	$yb_te = strtotime(date("Y-m-", $yb_tb).$_days.date(" 23:59:59")); //�Ա�Ϊȥ��ͬ�µ�����
} else {
	$yb_te = strtotime(date("Y-m-", $yb_tb).date("d H:i:s"));
}

// ���ݲ�ѯ���մ����鶨��
$time_arr = array(
	"����" => array($today_tb, $today_te),
	"�±�" => array($yuebi_tb, $yuebi_te),
	"�ܱ�" => array($zhoubi_tb, $zhoubi_tb),
	"����" => array($yesterday_tb, $yesterday_te),
	"����" => array($month_tb, $today_te),
	"ͬ��" => array($tb_tb, $tb_te),
	"����" => array($lastmonth_tb, $lastmonth_te),
);


// �����Ȩ��:
$data_power = explode(",", $uinfo["data_power"]);

$power_show = array();

if ($debug_mode || in_array("all", $data_power)) {
	$power_show["��"] = "������";
}
if ($debug_mode || in_array("web", $data_power)) {
	$power_show["����"] = "����";
}
if ($debug_mode || in_array("tel", $data_power)) {
	$power_show["�绰"] = "�绰";
}
// ����:
$z_info = $db->query("select name,type,sum_condition from index_module_set where isshow=1");
foreach ($z_info as $li) {
	if ($debug_mode || in_array($li["name"], $data_power)) {
		$power_show[$li["name"]] = $li["name"];
	}
}

if (count($power_show) == 0) {
	exit("�Բ�����û���κ�Ȩ�ޣ�����ϵ����Ա��");
}

// ��ǰ���ã�
$cur_field_arr = explode(",", $uinfo["list_field"]);
if ($uinfo["list_field"] == '' && count($cur_field_arr) < 2) {
	// Ĭ������:  ��ǰ��������ѡ���ֶ�:
	$cur_field_arr = array();
	foreach ($power_show as $k => $v) {
		$cur_field_arr[] = $k.":����:ԤԼ";
		$cur_field_arr[] = $k.":����:ԤԼ";
		$cur_field_arr[] = $k.":����:ʵ��";
		$cur_field_arr[] = $k.":����:ʵ��";
		$cur_field_arr[] = $k.":����:����";
		$cur_field_arr[] = $k.":����:����";
		if ($k == "��") {
			$cur_field_arr[] = "��:����";
		}
		if ($k == "����") {
			$cur_field_arr[] = "����:����";
		}
		if (count($cur_field_arr) >= 12) {
			break;
		}
	}
}

/*
if ($debug_mode) {
	$cur_field_arr = array();
	$cur_field_arr[] = "��:����:ʵ��";
	$cur_field_arr[] = "��:����:ʵ��";
	$cur_field_arr[] = "��:����";
	$cur_field_arr[] = "����:����:ʵ��";
	$cur_field_arr[] = "����:����:ʵ��";
	$cur_field_arr[] = "����:����";
	$cur_field_arr[] = "����:����:ԤԼ";
	$cur_field_arr[] = "����:����:ԤԼ";
	$cur_field_arr[] = "΢��:����:ʵ��";
	$cur_field_arr[] = "΢��:����:ʵ��";
	$cur_field_arr[] = "�󻮲�:����:ʵ��";
	$cur_field_arr[] = "�󻮲�:����:ʵ��";
};
*/



$hospital_list = $db->query("select id,name,area from hospital where id in ($hospitals) order by sort desc,id asc", 'id');

$data_arr = $db->query("select * from patient_data where hid in ($hospitals) ", "hid");

$counter = array();
$hid_data_arr = array();
foreach ($data_arr as $_hid => $li) {
	$tmp = $li["data"];
	if ($tmp != '') {
		$res = @unserialize($tmp);
		$index = 0;
		foreach ($cur_field_arr as $v) {
			list($a, $b, $c) = explode(":", $v, 3);
			if ($v == "��:����") {
				$zengfu = intval($res["��"]["ʵ��"]["����"]) - intval($res["��"]["ʵ��"]["ͬ��"]);
				if ($zengfu > 0) {
					$counter["up_num"] += 1;
					$counter["up_count"] += $zengfu;
					$zengfu = '+'.$zengfu.' <img src="/res/img/yeji_up.gif" align="absmiddle">';
				} else if ($zengfu < 0) {
					$counter["down_num"] += 1;
					$counter["down_count"] += $zengfu;
					$zengfu = $zengfu.' <img src="/res/img/yeji_down.gif" align="absmiddle">';
				}
				$hid_data_arr[$_hid][$index++] = $zengfu;
			} else if ($v == "����:����") {
				$zengfu = intval($res["����"]["ʵ��"]["����"]) - intval($res["����"]["ʵ��"]["ͬ��"]);
				if ($zengfu > 0) {
					$zengfu = '+'.$zengfu.' <img src="/res/img/yeji_up.gif" align="absmiddle">';
				} else if ($zengfu < 0) {
					$zengfu = $zengfu.' <img src="/res/img/yeji_down.gif" align="absmiddle">';
				}
				$hid_data_arr[$_hid][$index++] = $zengfu;
			} else {
				$hid_data_arr[$_hid][$index++] = intval($res[$a][$c][$b]);
			}
		}
	}
}



$head_count = count($cur_field_arr) + 2;
$per_head_width = round(100 / $head_count, 2)."%";


// ����������:
/*
$hid_percent = array();
$up_all = $up_num = $down_all = $down_num = 0;
foreach ($hid_data_arr as $k => $v) {
	$per = $v["x3"] - $v["x4"];
	$hid_percent[$k] = $per;
	if ($per > 0) {
		$up_all += $per;
		$up_num += 1;
	}
	if ($per < 0) {
		$down_all += abs($per);
		$down_num += 1;
	}
}
*/


?>
<html>
<head>
<title>��̨��ҳ</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="../res/base.css?ver=20130126" rel="stylesheet" type="text/css">
<script src="../res/base.js?ver=20130126" language="javascript"></script>
<style type="text/css">
#come_list_area {margin:30px 0px 20px 0px; }
.come_list {border:1px solid #97e6a5; }
.come_head td {border:1px solid #e7e7e7; background:#f2f8f9; padding:4px 3px 3px 3px; font-weight:bold; }
.come_line td {border:1px solid #e7e7e7; padding:4px 3px 3px 3px; }
.al {text-align:left; }
.ac {text-align:center; }
.ar {text-align:right; padding-right:5px !important; }
.red {color:red; }
</style>
<script src="../res/sorttable_keep.js" language="javascript"></script>
<style type="text/css">
.column_sortable {cursor:pointer; color:blue; font-family:"΢���ź�"; }
</style>
<script type="text/javascript">
function set_table_head() {
	parent.load_src(1, "/m/list_set_head.php", 800, 500);
}

window.onscroll = function () {
	var s_top = document.body.scrollTop;
	var top = byid("data_list").offsetTop;
	var top_head = byid("data_head").offsetHeight;
	var top_width = byid("data_list").offsetWidth;
	byid("float_head").style.width = top_width;

	if (s_top >= (0 + top + top_head)) {
		var o = byid("float_head");
		o.style.display = "";
		o.style.position = "absolute";
		o.style.left = byid("data_list").style.left;
		o.style.top = s_top;
	} else {
		byid("float_head").style.display = "none";
	}
};
</script>
</head>

<body>

<a name="top"></a>

<div style='padding:20px 12px 12px 40px;'>
	<div style="line-height:24px">
<?php
$str = '���ã�<font color="#FF0000"><b>'.$realname.'</b></font>';

if ($uinfo["hospitals"] || $uinfo["part_id"] > 0) {
	if ($uinfo["part_id"] > 0) {
		$str .= '��(��ݣ�'.$part_id_name[$uinfo["part_id"]].")";
	}
}

$onlines = $db->query("select count(*) as count from sys_admin where online=1", 1, "count");
$str .= '������ <font color="red"><b>'.date("Y-m-d").'</b></font>';
$str .= '������<b><font color="red">'.substr("��һ����������", date("w")*2, 2).'</font></b>';
$str .= '���������� <font color="red"><b>'.$onlines.'</b></font> ��';

echo $str;
?>
	</div>


	<div id="come_list_area">

<?php
$_time = @file_get_contents("../data/update_data.txt");
$data_update_time = "δ֪";
if ($_time > 0) {
	$data_update_time = date("Y.n.j H:i:s", $_time);
}

?>
		<div style="width:900px; text-align:center; padding-bottom:5px;">
			ע����ҳ����Ϊ������������ʵʱ��������ÿ��10�����Զ����¡������£�<?php echo $data_update_time; ?>&nbsp; &nbsp;
			<button onClick="self.location.reload();" class="button" title="���ˢ��ҳ��">ˢ��</button>&nbsp; &nbsp;
			<button onClick="set_table_head(); return false;" class="buttonb" title="���ѡ���ͷ">ѡ���ͷ</button>&nbsp; &nbsp;
		</div>

		<!-- ������ͷ -->
		<table style="display:none;" id="float_head" class="come_list" cellpadding="0" cellspacing="0" width="0" >
			<tr class="come_head">
				<td class="ac red" width="<?php echo $per_head_width; ?>">����</td>
				<td class="ac red" width="<?php echo $per_head_width; ?>">ҽԺ</td>
				
<?php
foreach ($cur_field_arr as $v) {
list($a, $b, $c) = explode(":", $v, 3);
$_name = str_replace(":", "", $v);
$_name = str_replace("����", "��", $_name);
$_name = str_replace("����", "��", $_name);
$_name = str_replace("����", "��", $_name);
$_name = str_replace("ͬ��", "ͬ", $_name);

$_name = str_replace("ԤԼ", "Լ", $_name);
$_name = str_replace("Ԥ��", "Ԥ", $_name);
$_name = str_replace("ʵ��", "��", $_name);
$_name = str_replace("����", "��", $_name);
?>
				<td class="ac" width="<?php echo $per_head_width; ?>"><?php echo $_name; ?></td>
<?php } ?>
			</tr>
		</table>

		<div style="width:95%;">

			<!-- ���ݱ�� -->
			<table id="data_list" class="round_table come_list sortable" cellpadding="0" cellspacing="0" width="100%">
				<tr id="data_head" class="come_head">
					<td class="ac red column_sortable" title="���������" width="<?php echo $per_head_width; ?>">����</td>
					<td class="ac red column_sortable" title="���������" width="<?php echo $per_head_width; ?>">ҽԺ</td>
<?php
foreach ($cur_field_arr as $v) {
	list($a, $b, $c) = explode(":", $v, 3);
	$_name = str_replace(":", "", $v);
	$_name = str_replace("����", "��", $_name);
	$_name = str_replace("����", "��", $_name);
	$_name = str_replace("����", "��", $_name);
	$_name = str_replace("ͬ��", "ͬ", $_name);

	$_name = str_replace("ԤԼ", "Լ", $_name);
	$_name = str_replace("Ԥ��", "Ԥ", $_name);
	$_name = str_replace("ʵ��", "��", $_name);
	$_name = str_replace("����", "��", $_name);
?>
					<td class="ac column_sortable" title="���������" width="<?php echo $per_head_width; ?>"><?php echo $_name; ?></td>
<?php } ?>
				</tr>


<?php
	$skip_hospitals = array();
	foreach ($hospital_list as $_hid => $_li) {
		$line = $hid_data_arr[$_hid];
		if (array_sum($line) == 0) {
			$skip_hospitals[] = '<a href="main.php?do=change&hospital_id='.$_hid.'">'.$_li["name"].'</a>';
			continue;
		}
?>
				<tr onMouseOver="mi(this)" onMouseOut="mo(this)" class="come_line" style="color:<?php echo $_li["color"]; ?>">
					<td class="ac"><nobr>&nbsp;<?php echo $_li["area"]; ?>&nbsp;</nobr></td>
					<td class="ac"><nobr>&nbsp;<a href="main.php?do=change&hospital_id=<?php echo $_hid; ?>" style="color:<?php echo $_li["color"]; ?>" title="����л�����ҽԺ"><?php echo $_li["name"]; ?></a>&nbsp;</nobr></td>
<?php
$index = 0;
foreach ($cur_field_arr as $v) {
?>
					<td class="ac" title="<?php echo $v; ?>"><?php echo $line[$index++]; ?></td>
<?php } ?>
				</tr>
<?php
	}
?>
			</table>
		</div>
	</div>

	<div style="padding:10px 0 0 30px;">
		<a href="#top">�ض���</a><br>
		<br>
<?php if ($counter) { ?>
		* ����ҽԺ��<?php echo $counter["up_num"]; ?> �ң�������������<?php echo $counter["up_count"]; ?> ���½�ҽԺ��<?php echo $counter["down_num"]; ?> �ң������½�����<?php echo $counter["down_count"]; ?> <font color="silver">(����������ͳ��)</font><br>
<?php } ?>
		<?php if (count($skip_hospitals) > 0) echo ("* <b>����ҽԺ/���������ݣ��ѱ����ԣ�</b>".implode("��", $skip_hospitals)."<br>"); ?>
		* ҳ��ִ��ʱ�䣺<?php echo round(now() - $pagebegintime, 4); ?>s  <?php echo $log_time1." ".$log_time2; ?>
	</div>
</div>

</body>
</html>