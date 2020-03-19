<?php
/*
// - ����˵�� : ���ﱨ��
// - �������� : ���� (weelia@126.com)
// - ����ʱ�� : 2011-08-22
*/
require "../../core/core.php";
include "config.php";
$table = "jiuzhen_report";

$t_begin = now();
$sql_time = array();


function sql_time_log($sql) {
	global $sql_begin;
	$t_use = round(now() - $sql_begin, 4);
	return $t_use."s ".$sql;
}


// ����:
$lastm_begin = strtotime("-1 month", $date_time);
$lastm_end = $date_time - 1;

$lastm_ym = date("Ym", $lastm_begin);
$lastm_ymd_begin = date("Ymd", $lastm_begin);
$lastm_ymd_end = date("Ymd", $lastm_end);


// ���6����
$his_month = array();
for ($i = 1; $i <= 6; $i++) {
	$tmp = strtotime("-{$i} month", $date_time);
	$his_month[date("Ym", $tmp)] = date("Y-m", $tmp);
}
asort($his_month);


// Ҫ���Ե�ҽԺ:
$not_hids = '';
if (array_key_exists($date_ym, $skiped_hid)) {
	$not_hids = " and id not in (".$skiped_hid[$date_ym].")";
}


// ��ѯ
$h_arr = $db->query("select id,name,area,depart from hospital where id in ($hids) $not_hids order by sort desc, id asc", "id");



$area_arr = $depart_arr = array();
$area_qita = $depart_qita = 0;
foreach ($h_arr as $_hid => $h) {
	$hid_name[$_hid] = $h["name"];
	$h["area"] = trim($h["area"]);
	$h["depart"] = trim($h["depart"]);
	if ($h["area"] == '' || $h["area"] == "����") {
		$area_qita++;
	} else {
		$area_arr[$h["area"]] = intval($area_arr[$h["area"]]) + 1;
	}
	if ($h["depart"] == '' || $h["depart"] == "����") {
		$depart_qita++;
	} else {
		$depart_arr[$h["depart"]] = intval($depart_arr[$h["depart"]]) + 1;
	}
}

arsort($area_arr);
arsort($depart_arr);

if ($area_qita > 0) {
	$area_arr["����"] = $area_qita;
}
if ($depart_qita > 0) {
	$depart_arr["����"] = $depart_qita;
}


// ���Ƶ�������Ҵ���:
if ($limit_area) {
	foreach ($h_arr as $k => $li) {
		if ($li["area"] != $limit_area || ($limit_area == "����" && $li["area"] == "") ) {
			unset($h_arr[$k]);
		}
	}
}
if ($limit_depart) {
	foreach ($h_arr as $k => $li) {
		if ($li["depart"] != $limit_depart || ($limit_depart == "����" && $li["depart"] == "") ) {
			unset($h_arr[$k]);
		}
	}
}


$show_hids = array_keys($h_arr);
$show_hids_str = implode(",", $show_hids);


$sql_begin = now();
$tmp = $db->query("select * from $table where hid in ($show_hids_str) and month=$date_ym and sub_id=$sub_id");
$sql_time[] = sql_time_log($db->sql);

$data = array();
foreach ($tmp as $li) {
	$data[$li["hid"]] = @unserialize($li["config"]);
}
//echo "<pre>";
//print_r($data);
unset($tmp);


// ��ѯ���6���µ���ʷ��¼:
$from_month = date("Ym", strtotime("-6 month", $date_time));
$sql_begin = now();
$tmp = $db->query("select * from $table where hid in ($show_hids_str) and month>=$from_month and month<$date_ym and sub_id=$sub_id");
$sql_time[] = sql_time_log($db->sql);

$his_data = array();
foreach ($tmp as $li) {
	$his_data[$li["hid"]][$li["month"]] = @unserialize($li["config"]);
}
unset($tmp);


if (date("Ym", $date_time) < date("Ym")) {
	$�ѹ����� = $month_all_days; //С�ڵ�ǰ��ʵ�·�
} else if (date("Ym", $date_time) > date("Ym")) {
	$�ѹ����� = "0"; //���ڵ�ǰ��ʵ�·�
} else {
	$�ѹ����� = date("j") - 1; //����
}


// ��ѯ ��������ͳ��
$to_load_field = explode(" ", "ip ip_local ip_other pv pv_local pv_other click click_local click_other ok_click ok_click_local ok_click_other talk talk_local talk_other orders order_local order_other come come_local come_other");
$s_array = '';
foreach ($to_load_field as $v) {
	$s_array[] = 'sum('.$v.') as '.$v;
}
$field_str = implode(", ", $s_array);

$c_lv = "#66cc99";
$c_huang = "#ffcc00";


// �ֻ���ʽ:
$shouji_condition_id = 3;
$s = $db->query("select * from index_module_set where id=$shouji_condition_id limit 1", 1, "sum_condition");
$shouji_condition_s = 'media_from in ("'.str_replace("+", '","', $s).'")';

// ΢�Ź�ʽ:
$weixin_condition_id = 2;
$s = $db->query("select * from index_module_set where id=$weixin_condition_id limit 1", 1, "sum_condition");
$weixin_condition_s = 'media_from in ("'.str_replace("+", '","', $s).'")';


foreach ($show_hids as $_hid) {
	$_hname = $hid_name[$_hid];
	$res = $pc = $sj = $wx = array();

	// ��ҳ��������:
	$sql_begin = now();
	$d = $db->query("select data from patient_data where hid=$_hid limit 1", 1, "data");
	$sql_time[] = sql_time_log($db->sql);
	$idata = (array) @unserialize($d);

	// PC
	if ($sub_id == 0 || $sub_id == 1) {

		$pc["�����1"] = $db->query("select sum(come) as c from count_web where hid=$_hid and sub_id=1 and date>=$date_ymd_begin and date<=$date_ymd_end", 1, "c");
		$pc["�����2"] = $db->query("select sum(wangcha) as c from count_web_day where hid=$_hid and sub_id=1 and date>=$date_ymd_begin and date<=$date_ymd_end", 1, "c");
		$pc["�����3"] = $db->query("select (sum(x1)+sum(x2)+sum(x3)+sum(x4)-sum(x5)) as c from jingjia_xiaofei where hid=$_hid and date>=$date_ymd_begin and date<=$date_ymd_end", 1, "c");

		$pc["Ԥ��1"] = @round($pc["�����1"] / $�ѹ����� * $month_all_days);
		$pc["Ԥ��2"] = @round($pc["�����2"] / $�ѹ����� * $month_all_days);
		$pc["Ԥ��3"] = @round($pc["�����3"] / ($pc["�����1"] + $pc["�����2"]));

		$pc["������ɱ���1"] = $data[$_hid]["mubiao1"] ? ((@round(($pc["Ԥ��1"] + $pc["Ԥ��2"]) / $data[$_hid]["mubiao1"], 3) * 100)."%") : "";
		$pc["������ɱ���2"] = '';
		$pc["������ɱ���3"] = $data[$_hid]["mubiao3"] ? ((@round($pc["Ԥ��3"] / $data[$_hid]["mubiao3"], 2) * 100)."%") : "";

		$pc["���������1"] = $his_data[$_hid][$lastm_ym]["h_jiuzhen"];
		$pc["���������2"] = $his_data[$_hid][$lastm_ym]["h_wangcha"];
		$pc["���������3"] = $db->query("select (sum(x1)+sum(x2)+sum(x3)+sum(x4)-sum(x5)) as c from jingjia_xiaofei where hid=$_hid and date>=$lastm_ymd_begin and date<=$lastm_ymd_end", 1, "c");

		$pc["���¾�ֵ1"] = @round($pc["���������1"] / $lastm_days, 1);
		$pc["���¾�ֵ2"] = @round($pc["���������2"] / $lastm_days);
		$pc["���¾�ֵ3"] = $his_data[$_hid][$lastm_ym]["h_renjun"];

		$pc["Ŀǰ��ֵ1"] = @round($pc["�����1"] / $�ѹ�����, 1);
		$pc["Ŀǰ��ֵ2"] = @round($pc["�����2"] / $�ѹ�����);
		$pc["Ŀǰ��ֵ3"] = @round($pc["�����3"] / ($pc["�����1"] + $pc["�����2"]));

		$pc["����1"] = $pc["Ԥ��1"] - $pc["���������1"];
		$pc["����2"] = '';
		$pc["����3"] = $pc["Ŀǰ��ֵ3"] - $his_data[$_hid][$lastm_ym]["h_renjun"];

		$pc["�ѹ�����"] = $�ѹ�����;
	}


	// �ֻ�
	if ($sub_id == 0 || $sub_id == 2) {

		//$sj["�����1"] = $idata["�ֻ�"]["ʵ��"]["����"] - $idata["�ֻ�"]["ʵ��"]["����"];
		$sj["�����1"] = $db->query("select count(*) as c from patient_{$_hid} where order_date>=$date_time and order_date<=$date_end and status=1 and $shouji_condition_s", 1, "c");
		$sj["�����2"] = '';
		$sj["�����3"] = $db->query("select (sum(x5)+sum(x6)) as c from jingjia_xiaofei where hid=$_hid and date>=$date_ymd_begin and date<=$date_ymd_end", 1, "c");

		$sj["Ԥ��1"] = @round($sj["�����1"] / $�ѹ����� * $month_all_days);
		$sj["Ԥ��2"] = '';
		$sj["Ԥ��3"] = @round($sj["�����3"] / ($sj["�����1"] + $sj["�����2"]));

		$sj["���������1"] = $db->query("select count(*) as c from patient_{$_hid} where order_date>=$lastm_begin and order_date<=$lastm_end and status=1 and $shouji_condition_s", 1, "c");
		$sj["���������2"] = '';
		$sj["���������3"] = $db->query("select (sum(x5)-sum(x6)) as c from jingjia_xiaofei where hid=$_hid and date>=$lastm_ymd_begin and date<=$lastm_ymd_end", 1, "c");


		$sj["����1"] = $sj["Ԥ��1"] - $sj["���������1"];
		$sj["����2"] = '';
		$sj["����3"] = $sj["�����3"] - @round($sj["���������3"] / ($sj["���������1"] + $sj["���������2"]));

		$sj["���¾�ֵ1"] = @round($sj["���������1"] / $lastm_days, 1);
		$sj["���¾�ֵ2"] = @round($sj["���������2"] / $lastm_days);
		$sj["���¾�ֵ3"] = $his_data[$_hid][$lastm_ym]["h_renjun"];

		$sj["Ŀǰ��ֵ1"] = @round($sj["�����1"] / $�ѹ�����, 1);
		$sj["Ŀǰ��ֵ2"] = @round($sj["�����2"] / $�ѹ�����);
		$sj["Ŀǰ��ֵ3"] = @round($sj["�����3"] / ($sj["�����1"] + $sj["�����2"]));

		$sj["�ѹ�����"] = $�ѹ�����;
	}

	// ΢��:
	if ($sub_id == 0 || $sub_id == 3) {
		$x = substr_count($_hname, "��") > 0 ? 1200 : 600;

		//$wx["�����1"] = $idata["΢��"]["ʵ��"]["����"] - $idata["΢��"]["ʵ��"]["����"];
		$wx["�����1"] = $db->query("select count(*) as c from patient_{$_hid} where order_date>=$date_time and order_date<=$date_end and status=1 and $weixin_condition_s", 1, "c");
		$wx["�����2"] = '';
		$wx["�����3"] = $wx["�����1"] * $x;


		$wx["Ԥ��1"] = @round($wx["�����1"] / $�ѹ����� * $month_all_days);
		$wx["Ԥ��2"] = '';
		$wx["Ԥ��3"] = '';

		$wx["���������1"] = $db->query("select count(*) as c from patient_{$_hid} where order_date>=$lastm_begin and order_date<=$lastm_end and status=1 and $weixin_condition_s", 1, "c");
		$wx["���������2"] = '';
		$wx["���������3"] = $wx["���������1"] * $x;

		$wx["����1"] = $wx["Ԥ��1"] - $wx["���������1"];
		$wx["����2"] = '';
		$wx["����3"] = $sj["�����3"] - @round($sj["���������3"] / $wx["���������1"]);

		$wx["���¾�ֵ1"] = @round($wx["���������1"] / $lastm_days, 1);
		$wx["���¾�ֵ2"] = '';
		$wx["���¾�ֵ3"] = $his_data[$_hid][$lastm_ym]["h_renjun"];

		$wx["Ŀǰ��ֵ1"] = @round($wx["�����1"] / $�ѹ�����);
		$wx["Ŀǰ��ֵ2"] = '';
		$wx["Ŀǰ��ֵ3"] = '';

		$wx["�ѹ�����"] = $�ѹ�����;
	}

	if ($sub_id == 0) {
		$res["�����1"] = $pc["�����1"] + $sj["�����1"] + $wx["�����1"];
		$res["�����2"] = $pc["�����2"] + $sj["�����2"] + $wx["�����2"];
		$res["�����3"] = $pc["�����3"] + $sj["�����3"] + $wx["�����3"];

		$res["Ԥ��1"] = @round($res["�����1"] / $�ѹ����� * $month_all_days);
		$res["Ԥ��2"] = @round($res["�����2"] / $�ѹ����� * $month_all_days);
		$res["Ԥ��3"] = @round($res["�����3"] / ($res["�����1"] + $res["�����2"]));

		$res["������ɱ���1"] = $data[$_hid]["mubiao1"] ? ((@round(($res["Ԥ��1"] + $res["Ԥ��2"]) / $data[$_hid]["mubiao1"], 3) * 100)."%") : "";
		$res["������ɱ���2"] = '';
		$res["������ɱ���3"] = $data[$_hid]["mubiao3"] ? ((@round($res["Ԥ��3"] / $data[$_hid]["mubiao3"], 2) * 100)."%") : "";

		$res["������ɫ1"] = $res["������ɱ���1"] ? (intval($res["������ɱ���1"]) >= 100 ? $c_lv : $c_huang) : "";
		$res["������ɫ3"] = $res["������ɱ���3"] ? (intval($res["������ɱ���3"]) < 100 ? $c_lv : $c_huang) : "";

		$res["���������1"] = $his_data[$_hid][$lastm_ym]["h_jiuzhen"];
		$res["���������2"] = $pc["���������2"] + $sj["���������2"] + $wx["���������2"];
		$res["���������3"] = $pc["���������3"] + $sj["���������3"] + $wx["���������3"];

		$res["���¾�ֵ1"] = @round($res["���������1"] / $lastm_days, 1);
		$res["���¾�ֵ2"] = @round($res["���������2"] / $lastm_days);;
		$res["���¾�ֵ3"] = $his_data[$_hid][$lastm_ym]["h_renjun"];

		$res["Ŀǰ��ֵ1"] = @round($res["�����1"] / $�ѹ�����, 1);
		$res["Ŀǰ��ֵ2"] = '';
		$res["Ŀǰ��ֵ3"] = $res["Ԥ��3"];

		$res["����1"] = $res["Ԥ��1"] - $res["���������1"];
		$res["����2"] = '';
		$res["����3"] = $res["Ԥ��3"] - $res["���¾�ֵ3"];

		$res["������ɫ1"] = $res["����1"] ? (intval($res["����1"]) > 0 ? $c_lv : $c_huang) : "";
		$res["������ɫ3"] = $res["����3"] ? (intval($res["����3"]) > 0 ? $c_huang : $c_lv) : "";

		$res["�ѹ�����"] = $�ѹ�����;
	}

	if ($sub_id == 0) {
		$data[$_hid] = wee_merge($data[$_hid], $res);
	} else if ($sub_id == 1) {
		$data[$_hid] = wee_merge($data[$_hid], $pc);
	} else if ($sub_id == 2) {
		$data[$_hid] = wee_merge($data[$_hid], $sj);
	} else {
		$data[$_hid] = wee_merge($data[$_hid], $wx);
	}
}


$t_end = now();

$t_used = round($t_end - $t_begin, 4);



// ҳ�濪ʼ ------------------------
?>
<html>
<head>
<title>���ﱨ��</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<style>
.jiuzhen_t a {color: #003e00 !important; font-family:"Tahoma"; }
.jiuzhen_t a:hover {color: #00d500 !important; font-family:"Tahoma"; }
.head, .head a {font-family:"΢���ź�","Verdana"; font-weight:bold;  }
.item {font-family:"Tahoma"; padding:8px 3px 6px 3px !important; }
.footer_op_left {font-family:"Tahoma"; }

.jiuzhen_t {border:2px solid #47b48b; }
.jiuzhen_t .head {background:#f6f6f6; border:1px solid #d8d8d8;  }
.jiuzhen_t td {padding:4px 2px 2px 2px; border:1px solid #e0e0e0; }
.tt {border-top:2px solid #78cd8f !important; }
.tl {border-left:2px solid #d8d8d8 !important; }
.tr {border-right:2px solid #d8d8d8 !important; }
.tb {border-bottom:2px solid #d8d8d8 !important; }

#date_tips {float:left; font-weight:bold; padding-top:3px; }
#ch_date {float:left; margin-left:0px; }
.site_name {display:block; padding:4px 0px;}
.site_name, .site_name a {font-family:"Arial", "Tahoma"; }
.ch_date_a b, .ch_date_a a {font-family:"Arial"; }
.ch_date_a b {border:0px; padding:1px 5px 1px 5px; color:red; }
.ch_date_a a {border:0px; padding:1px 5px 1px 5px; }
.ch_date_a a:hover {border:1px solid silver; padding:0px 4px 0px 4px; }
.ch_date_b {padding-top:8px; text-align:left; width:80%; color:silver; }
.ch_date_b a {padding:0 3px; }

.rp_title {margin-top:30px; text-align:center; font-size:16px; font-family:"΢���ź�"; }

.num {font-size:10px; font-family:"Tahoma"; color:gray; }

.bg_color1, .bg_color1 td {background-color:#f2eee6; }
.bg_color2, .bg_color2 td {background-color:white; }

.a_12 {font-size:12px !important; }
</style>

<script language="javascript">
function byid(id) {
	return document.getElementById(id);
}

function load_url(s) {
	parent.load_box(1, 'src', s);
}

function edit(url) {
	parent.load_src(1, url, 600, 500);
}

// �������
function mi(id) {
	var color = "#ffe8d9";
	byid(id+"_1").style.backgroundColor = color;
	byid(id+"_2").style.backgroundColor = color;
	byid(id+"_3").style.backgroundColor = color;
}

// ����Ƴ�
function mo(id) {
	byid(id+"_1").style.backgroundColor = "";
	byid(id+"_2").style.backgroundColor = "";
	byid(id+"_3").style.backgroundColor = "";
}

window.onscroll = function () {
	var s_top = document.body.scrollTop;
	var top = byid("data_list").offsetTop;
	var top_head = byid("data_head").offsetHeight;

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

function update_date(type, o) {
	var value = o.innerHTML;
	if (type == "1") {
		var url = "jiuzhen.php?op=set_year&year="+value;
	} else {
		var url = "jiuzhen.php?op=set_month&month="+value;
	}
	self.location = url;
}

function date_navi(s) {
	var url = "jiuzhen.php?op=date_navi&go="+s;
	self.location = url;
}

function confirm_skip_hid(o) {
	return confirm("��ȷ��Ҫ���Ը�ҽԺ�𣨽��Ե�ǰ�·���Ч��");
}

function set_area(s) {
	var url = "jiuzhen.php?op=set_area&area="+s;
	self.location = url;
}

function set_depart(s) {
	var url = "jiuzhen.php?op=set_depart&depart="+s;
	self.location = url;
}


<?php if ($can_edit) { ?>
function medit(hid, month, fname, default_value) {
	var url = "/m/report/jiuzhen_edit.php?hid="+hid+"&month="+month+"&fname="+fname+"&default_value="+default_value;
	parent.load_src(1, url, 400, 200);
}
<?php } ?>

</script>
</head>

<body>
<table width="100%">
	<tr>
		<td align="left">
			<div style="margin:0px 0 0 0px; ">
				<form id="ch_date" method="GET">
					<span class="ch_date_a">�꣺<?php echo _my_show($y_array, $year, "#", "return update_date(1,this)"); ?>&nbsp;&nbsp;&nbsp;</span>
					<span class="ch_date_a">�£�<?php echo _my_show($m_array, $month, "#", "return update_date(2,this)"); ?>&nbsp;&nbsp;&nbsp;</span>
					&nbsp;
					<button onclick="date_navi('last'); return false;" class="button" title="�鿴��һ���µı���">����</button>&nbsp;&nbsp;
					<button onclick="date_navi('current'); return false;" class="button" title="�鿴���±���">����</button>&nbsp;&nbsp;
					<button onclick="date_navi('next'); return false;" class="button" title="�鿴��һ���µı���">����</button>&nbsp;&nbsp;
					<button onclick="self.location.reload();" class="button" title="ˢ�±�ҳ��">ˢ��</button>
				</form>
				<div class="clear"></div>
			</div>
		</td>

		<td align="left">
<?php
$_out = array();
foreach ($sum_type_arr as $_id => $_name) {
	if ($sub_id == $_id) {
		$_out[] = '<a href="#" style="color:red; font-weight:bold;">'.$_name.'</a>';
	} else {
		$_out[] = '<a href="?op=sub_change&sub_id='.$_id.'">'.$_name.'</a>';
	}
}
echo implode(" <font color=silver>|</font> ", $_out);
?>
		</td>

		<td align="right">
			<select class="combo" name="" onchange="set_area(this.value); return false;">
				<option value="" style="color:gray">-��������-</option>
				<?php echo list_option($area_arr, "_key_", "_key_", $limit_area); ?>
			</select>

			<select class="combo" name="" onchange="set_depart(this.value); return false;">
				<option value="" style="color:gray">-���ҹ���-</option>
				<?php echo list_option($depart_arr, "_key_", "_key_", $limit_depart); ?>
			</select>
		</td>
	</tr>
</table>




<div class="rp_title">
	<?php echo date("Y��m��", $date_time); ?> ���ﱨ�� <?php echo $show_data."(".count($show_hids).")"; ?>&nbsp;&nbsp;
<?php if ($uinfo["part_id"] == 9 || $debug_mode) { ?>
	<a href="javascript:;" onclick="copy_history_data(); return false;" class="a_12">������ʷ����</a>&nbsp;&nbsp;
	<script type="text/javascript">
	function copy_history_data() {
		var url = "/m/report/copy_history_data.php";
		parent.load_src(1, url, 800, 500);
	}
	</script>
	<a href="javascript:;" onclick="fill_last_month_come(); return false;" class="a_12">�Զ������¾���</a>
	<script type="text/javascript">
	function fill_last_month_come() {
		var url = "/m/report/fill_last_month_come.php";
		parent.load_src(1, url, 800, 500);
	}
	</script>
<?php } ?>
</div>

<table width="100%" align="center" class="jiuzhen_t" id="data_list" style="margin-top:15px;">
	<div id="data_head">
	<tr>
		<td class="head" align="center" width="5%" rowspan="2">ҽԺ����</td>
		<td class="head" align="center" width="5%" rowspan="2">������</td>
		<td class="head" align="center" width="5%" rowspan="2">ͳ����Ŀ</td>
		<td class="head" align="center" width="30%" colspan="6">�����ʷ��¼</td>
		<td class="head" align="center" width="5%" rowspan="2">���ָ��</td>
		<td class="head" align="center" width="5%" rowspan="2">��������</td>
		<td class="head" align="center" width="5%" rowspan="2">����ָ��</td>
		<td class="head" align="center" width="5%" rowspan="2">Ŀ��</td>
		<td class="head" align="center" width="5%" rowspan="2">������ɱ���</td>
		<td class="head" align="center" width="5%" rowspan="2">Ԥ��</td>
		<td class="head" align="center" width="5%" rowspan="2">�Ա���������</td>
		<td class="head" align="center" width="5%" rowspan="2">���¾�ֵ</td>
		<td class="head" align="center" width="5%" rowspan="2">Ŀǰ��ֵ</td>
		<td class="head" align="center" width="5%" rowspan="2" style="color:red">�����</td>
		<td class="head" align="center" width="5%" rowspan="2">�ѹ�����</td>
	</tr>
	<tr>
<?php foreach ($his_month as $v) { ?>
		<td class="head" align="center" width="5%" style="font-family:Tahoma; font-weight:normal"><?php echo $v; ?></td>
<?php } ?>
	</tr>
	</div>

<?php
	$bg_index = 0;
	foreach ($show_hids as $_hid) {
		$_hname = $hid_name[$_hid];
		$line = $data[$_hid];
		$his_line = $his_data[$_hid];
		$bg = $bg_index++ % 2 ? "bg_color1" : "bg_color2";

?>
	<tr id="<?php echo $_hid; ?>_1" class="<?php echo $bg; ?>" onmouseover="mi(<?php echo $_hid; ?>)" onmouseout="mo(<?php echo $_hid; ?>)">
		<td class="item tt" align="center" rowspan="3">
			<b><?php echo $_hname; ?></b><br>
<?php if ($can_edit) { ?>
			<a href="?op=skip_hid&month=<?php echo date("Ym", $date_time); ?>&hid=<?php echo $_hid; ?>" onclick="return confirm_skip_hid(this)" title="���º��Ը�ҽԺ">����</a>
<?php } ?>
		</td>
		<td class="item tt" align="center" rowspan="3"><?php echo make_edit($_hid, $date_y_m, "fuzeren", str_replace("-", "<br>", $line["fuzeren"])); ?></td>
		<td class="item tt" align="center">������</td>

<?php foreach ($his_month as $ma => $mb) { ?>
		<td class="item tt" align="center"><?php echo make_edit($_hid, $mb, "h_jiuzhen", $his_line[$ma]["h_jiuzhen"]); ?></td>
<?php } ?>

		<td class="item tt" align="center"><?php echo make_edit($_hid, $date_y_m, "dabiaozhishu1", $line["dabiaozhishu1"]); ?></td>
		<td class="item tt" align="center"><?php echo make_edit($_hid, $date_y_m, "jianglijishu1", $line["jianglijishu1"]); ?></td>
		<td class="item tt" align="center"><?php echo make_edit($_hid, $date_y_m, "jianglizhibiao1", $line["jianglizhibiao1"]); ?></td>
		<td class="item tt" align="center"><?php echo make_edit($_hid, $date_y_m, "mubiao1", num($line["mubiao1"])); ?></td>
		<td class="item tt" align="center" style="background-color:<?php echo $line["������ɫ1"]; ?>"><?php echo num($line["������ɱ���1"]); ?></td>
		<td class="item tt" align="center"><?php echo num($line["Ԥ��1"]); ?></td>
		<td class="item tt" align="center" style="background-color:<?php echo $line["������ɫ1"]; ?>"><?php echo num($line["����1"]); ?></td>
		<td class="item tt" align="center"><?php echo num($line["���¾�ֵ1"]); ?></td>
		<td class="item tt" align="center"><?php echo num($line["Ŀǰ��ֵ1"]); ?></td>
		<td class="item tt" align="center" style="color:red"><?php echo num($line["�����1"]); ?></td>
		<td class="item tt" align="center" rowspan="3"><?php echo num($line["�ѹ�����"]); ?></td>
	</tr>
	<tr id="<?php echo $_hid; ?>_2" class="<?php echo $bg; ?>" onmouseover="mi(<?php echo $_hid; ?>)" onmouseout="mo(<?php echo $_hid; ?>)">
		<td class="item" align="center">����</td>

<?php foreach ($his_month as $ma => $mb) { ?>
		<td class="item" align="center"><?php echo make_edit($_hid, $mb, "h_wangcha", $his_line[$ma]["h_wangcha"]); ?></td>
<?php } ?>

		<td class="item" align="center"><?php echo make_edit($_hid, $date_y_m, "dabiaozhishu2", $line["dabiaozhishu2"]); ?></td>
		<td class="item" align="center"><?php echo make_edit($_hid, $date_y_m, "jianglijishu2", $line["jianglijishu2"]); ?></td>
		<td class="item" align="center"><?php echo make_edit($_hid, $date_y_m, "jianglizhibiao2", $line["jianglizhibiao2"]); ?></td>
		<td class="item" align="center"><?php echo make_edit($_hid, $date_y_m, "mubiao2", num($line["mubiao2"])); ?></td>
		<td class="item" align="center"><?php echo num($line["������ɱ���2"]); ?></td>
		<td class="item" align="center"><?php echo num($line["Ԥ��2"]); ?></td>
		<td class="item" align="center"><?php echo num($line["����2"]); ?></td>
		<td class="item" align="center"><?php echo num($line["���¾�ֵ2"]); ?></td>
		<td class="item" align="center"><?php echo num($line["Ŀǰ��ֵ2"]); ?></td>
		<td class="item" align="center" style="color:red"><?php echo num($line["�����2"]); ?></td>
	</tr>
	<tr id="<?php echo $_hid; ?>_3" class="<?php echo $bg; ?>" onmouseover="mi(<?php echo $_hid; ?>)" onmouseout="mo(<?php echo $_hid; ?>)">
		<td class="item" align="center">�˾��ɱ�</td>

<?php foreach ($his_month as $ma => $mb) { ?>
		<td class="item" align="center"><?php echo make_edit($_hid, $mb, "h_renjun", $his_line[$ma]["h_renjun"]); ?></td>
<?php } ?>

		<td class="item" align="center"><?php echo make_edit($_hid, $date_y_m, "dabiaozhishu3", $line["dabiaozhishu3"]); ?></td>
		<td class="item" align="center"><?php echo make_edit($_hid, $date_y_m, "jianglijishu3", $line["jianglijishu3"]); ?></td>
		<td class="item" align="center"><?php echo make_edit($_hid, $date_y_m, "jianglizhibiao3", $line["jianglizhibiao3"]); ?></td>
		<td class="item" align="center" title="�˾�Ŀ��"><?php echo  make_edit($_hid, $date_y_m, "mubiao3", num($line["mubiao3"])); ?></td>
		<td class="item" align="center" style="background-color:<?php echo $line["������ɫ3"]; ?>"><?php echo num($line["������ɱ���3"]); ?></td>
		<td class="item" align="center"><?php echo num($line["Ԥ��3"]); ?></td>
		<td class="item" align="center" style="background-color:<?php echo $line["������ɫ3"]; ?>"><?php echo num($line["����3"]); ?></td>
		<td class="item" align="center"><?php echo num($line["���¾�ֵ3"]); ?></td>
		<td class="item" align="center"><?php echo num($line["Ŀǰ��ֵ3"]); ?></td>
		<td class="item" align="center" style="color:red"><?php echo num($line["�����3"]); ?></td>
	</tr>
<?php } ?>

</table>

<!-- ������ͷ��������ͷ�ĽṹҪһ������ÿ����Ԫ�񶼱������ÿ�� -->
<table width="100%" align="center" class="jiuzhen_t" id="float_head" style="display:none; border-bottom:0;">
	<tr>
		<td class="head" align="center" width="5%" rowspan="2">ҽԺ����</td>
		<td class="head" align="center" width="5%" rowspan="2">������</td>
		<td class="head" align="center" width="5%" rowspan="2">ͳ����Ŀ</td>
		<td class="head" align="center" width="30%" colspan="6">�����ʷ��¼</td>
		<td class="head" align="center" width="5%" rowspan="2">���ָ��</td>
		<td class="head" align="center" width="5%" rowspan="2">��������</td>
		<td class="head" align="center" width="5%" rowspan="2">����ָ��</td>
		<td class="head" align="center" width="5%" rowspan="2">Ŀ��</td>
		<td class="head" align="center" width="5%" rowspan="2">������ɱ���</td>
		<td class="head" align="center" width="5%" rowspan="2">Ԥ��</td>
		<td class="head" align="center" width="5%" rowspan="2">�Ա���������</td>
		<td class="head" align="center" width="5%" rowspan="2">���¾�ֵ</td>
		<td class="head" align="center" width="5%" rowspan="2">Ŀǰ��ֵ</td>
		<td class="head" align="center" width="5%" rowspan="2" style="color:red">�����</td>
		<td class="head" align="center" width="5%" rowspan="2">�ѹ�����</td>
	</tr>
	<tr>
<?php foreach ($his_month as $v) { ?>
		<td class="head" align="center" width="5%" style="font-family:Tahoma; font-weight:normal"><?php echo $v; ?></td>
<?php } ?>
	</tr>
</table>

<br>

<div style="text-align:center"><a href="#">�ض���</a>&nbsp;</div>
<br>

<?php
if ($can_edit) {
	$s = $skiped_hid[$date_ym];
	if ($s != '') {
		$arr = explode(",", $s);
		foreach ($arr as $_h) {
			$h_name = $db->query("select name from hospital where id=$_h limit 1", 1, "name");
			$harr[] = '<a href="?op=remove_skip&m='.$date_ym.'&hid='.$_h.'" title="���ȡ������">'.$h_name.'</a>';
		}
		echo "�����ѱ����ԵĿ��ң�".implode("��", $harr);
	}
}

?>


<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>

<div style="color:silver;">ҳ��������ʱ��<?php echo $t_used; ?></div>

<?php
echo "<pre>";
//print_r($skiped_hid);
//echo implode("<br>", $sql_time);
//print_r($_SESSION);
//print_r($data);
//print_r($res);
//print_r($pc);
//print_r($sj);
//print_r($wx);
//print_r($idata);

?>

</body>
</html>