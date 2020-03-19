<?php
/*
// - ����˵�� : ��Ժʱ���޸Ĵ������Լ��طö��ٴε�Ժ�ı���
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2012-12-01
*/
require "../../core/core.php";
include "../../res/chart/FusionCharts_Gen.php";
set_time_limit(0);
$table = "patient_".$hid;
$h_name = $db->query("select name from hospital where id=$hid limit 1", 1, "name");

// ���鶨��:
$time_type_array = array("addtime" => "���ʱ��", "order_date" => "ԤԼʱ��");
$cishu_type_array = array("huifang" => "���طô���", "order_change" => "ԤԼ�޸Ĵ���");

// Ĭ�ϲ���:
if (!isset($_GET["btime"])) {
	$_GET["btime"] = date("Y-m-01");
	$_GET["etime"] = date("Y-m-d", strtotime("+1 month", strtotime($_GET["btime"])) - 1);
}

if (!isset($_GET["cishu_type"]) || $_GET["cishu_type"] == '') {
	$_GET["cishu_type"] = "huifang";
}

if (!isset($_GET["time_type"]) || $_GET["time_type"] == '') {
	$_GET["time_type"] = "order_date";
}

// Ĭ�ϲ��� end


$op = $_GET["op"];

// ����ʱ��:
if ($op == "show") {
	$where = array();

	$tb = strtotime($_GET["btime"]." 0:0:0");
	$te = strtotime($_GET["etime"]." 23:59:59");

	$cishu_type = $_GET["cishu_type"];
	$time_type = $_GET["time_type"];

	// �������Ƿ���Ч:
	if (!array_key_exists($cishu_type, $cishu_type_array)) {
		exit("��������: cishu_type ");
	}
	if (!array_key_exists($time_type, $time_type_array)) {
		exit("��������: time_type ");
	}

	if ($time_type == "order_date") {
		$where[] = "order_date>=".$tb." and order_date<=".$te;
	} else {
		$where[] = "addtime>=".$tb." and addtime<=".$te;
	}


	$sqlwhere = '';
	if (count($where) > 0) {
		$sqlwhere = "where ".implode(" and ", $where);
	}

	if ($cishu_type == "huifang") {
		$field = "status,huifang";
	} else {
		$field = "status,edit_log";
	}

	// �Ľ���: һ���Զ�ȡ����:
	$datas = $db->query("select $field from $table $sqlwhere");
	if ($debug_mode) {
		//echo $db->sql;
	}

	$res = array();
	$res_come = $res_not_come = array();
	if ($cishu_type == "huifang") {
		// ���ط÷���:
		foreach ($datas as $v) {
			$v_times = 0;
			if (trim($v["huifang"]) != '') {
				$v["huifang"] = str_replace("\r", "", $v["huifang"]);
				$v_arr = explode("\n", $v["huifang"]);
				foreach ($v_arr as $v2) {
					if (trim($v2) != '') {
						$v_times += 1;
					}
				}
			}
			$res[$v_times] = intval($res[$v_times]) + 1;
			if ($v["status"] == 1) {
				$res_come[$v_times] = intval($res_come[$v_times]) + 1;
			} else {
				$res_not_come[$v_times] = intval($res_not_come[$v_times]) + 1;
			}
		}
	} else {
		// ��ԤԼʱ���޸Ĵ�������:
		foreach ($datas as $v) {
			$v_times = 0;
			if (trim($v["edit_log"]) != '') {
				$v["edit_log"] = str_replace("\r", "", $v["edit_log"]);
				$v_arr = explode("\n", $v["edit_log"]);
				foreach ($v_arr as $v2) {
					if (substr_count($v2, "ԤԼʱ����") > 0) {
						$v_times += 1;
					}
				}
				if ($v["status"] == 1 && $v_times > 0) {
					$v_times -= 1; //����ѵ�Ժ��������һ��ʱ���޸ļ�¼�ǵ�ҽ�ģ�Ҫ��һ
				}
			}
			$res[$v_times] = intval($res[$v_times]) + 1;
			if ($v["status"] == 1) {
				$res_come[$v_times] = intval($res_come[$v_times]) + 1;
			} else {
				$res_not_come[$v_times] = intval($res_not_come[$v_times]) + 1;
			}
		}
	}

	ksort($res);

	// ��������
	$res_count = array_sum($res);
	$res_come_count = array_sum($res_come);
	$res_not_come_count = array_sum($res_not_come);

	$tip_pre = $cishu_type == "huifang" ? "�ط�" : "�޸�";

}

$title = '�طô����뵽Ժ��ϵ';

// ʱ�䶨��
// ����
$yesterday_begin = strtotime("-1 day");
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
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<script src='/res/chart/FusionCharts.js' language='javascript'></script>
<style>
#tiaojian {margin:10px 0 0 30px; }
form {display:inline; }

#result {margin-left:50px; }
.h_name {font-weight:bold; margin-top:20px; }
.h_kf {margin-left:20px; }
.kf_li {border-bottom:0px dotted silver; }

s {width: 20px; text-align:center; text-decoration:none; }
.dh td, .dt td, .ds td {border:1px solid #bce4c7; padding:4px 3px 2px 3px; text-align:center; }
.dh td {font-weight:bold; background:#EFF8F8; }
.ds td {background:#FFF2EC; }

u {text-decoration:none; color:#FF8888; }
i {font-style:normal !important;  color:#96CBCB; }

.w400 {width:400px }
.w800 {width:800px; margin-top:6px; }
.hr {border:0; margin:0; padding:0; height:3px; line-height:0; font-size:0; background-color:red; color:white; border-top:1px solid silver; }

#chart_1_border, #chart_2_border {height:300px; overflow:hidden; border:2px solid #EBEBEB; width:600px; }
#chart_1, #chart_2 {margin-top:-150px; }

.tdl {border-left:1px solid #66c17d; }
</style>
<script type="text/javascript">
function write_dt(da, db) {
	byid("begin_time").value = da;
	byid("end_time").value = db;
}
function check_data(form) {
	byid("submit_button_1").value = '�ύ��';
	byid("submit_button_1").disabled = true;
}

function m1(o) {
	o.style.backgroundColor = "#D8EBEB";
}
function m2(o) {
	o.style.backgroundColor = "";
}
</script>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $h_name." ".$title; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"><button onclick="history.back()" class="button">����</button></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>
<form method="GET" onsubmit="return check_data(this)">
<table class="list" width="100%" style="filter:progid:DXImageTransform.Microsoft.Gradient(startColorStr='#f2f2f2', endColorStr='#ffffff', gradientType='0')">
	<tr>
		<td>
			<div style="padding:6px 5px 2px 10px;">
				<b>ʱ�䣺</b>
				<span id="t_day">
					&nbsp; ��<input name="btime" id="begin_time" class="input" style="width:100px" value="<?php echo $_GET["btime"]; ?>"> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'begin_time',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��">
					&nbsp; ֹ��<input name="etime" id="end_time" class="input" style="width:100px" value="<?php echo $_GET["etime"]; ?>"> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'end_time',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��">
					&nbsp; ���
					<a href="javascript:write_dt('<?php echo date("Y-m-d", $this_month_begin); ?>','<?php echo date("Y-m-d", $this_month_end); ?>')">����</a>
					<a href="javascript:write_dt('<?php echo date("Y-m-d", $last_month_begin); ?>','<?php echo date("Y-m-d", $last_month_end); ?>')">����</a>&nbsp; &nbsp;
				</span>

				<b>������</b>
				<select name="time_type" class="combo">
					<option value="" style="color:gray">-ʱ������-</option>
					<?php echo list_option($time_type_array, "_key_", "_value_", $_GET["time_type"]); ?>
				</select>&nbsp;
				<select name="cishu_type" class="combo">
					<option value="" style="color:gray">-��������-</option>
					<?php echo list_option($cishu_type_array, "_key_", "_value_", $_GET["cishu_type"]); ?>
				</select>&nbsp;
			</div>
		</td>
		<td width="150" align="center">
			<input id="submit_button_1" type="submit" class="button" value="�ύ">
			<input type="hidden" name="op" value="show">
		</td>
	</tr>
</table>
</form>


<?php if ($op == "show") { ?>

<!-- ��ʾ�ٷֱȱ�ͼ -->
<!-- <div style="text-align:center">
	<div><?php //$FC->renderChart(); ?></div>
	<div class="w800" style="text-align:center"><b>ͳ�ƽ�� �� <?php echo $res_count; ?> ��</b></div>
</div> -->

<br>
<b>&nbsp;ͳ�ƽ��</b><br>

<table width="100%"  style="border:2px solid #43a75c; background:#FAFCFC; margin-top:6px;">
	<tr class="dh">
		<td width="10%" rowspan="2"><?php echo $tip_pre; ?>����</td>
		<td width="30%" style="border-left:2px solid #bce4c7;" colspan="2">���� (<?php echo $res_count; ?>��)</td>
		<td width="30%" style="border-left:2px solid #bce4c7;" colspan="3">�ѵ� (<?php echo $res_come_count; ?>��)</td>
		<td width="15%" style="border-left:2px solid #bce4c7;" colspan="3">δ�� (<?php echo $res_not_come_count; ?>��)</td>
	</tr>
	<tr class="dh">
		<!-- td rowspan="2" ���� -->

		<td width="15%" style="border-left:2px solid #bce4c7;">����</td>
		<td width="15%">��ռ�ٷֱ�</td>

		<td width="10%" style="border-left:2px solid #bce4c7;">����</td>
		<td width="10%">ռ�ѵ��ٷֱ�</td>
		<td width="10%">ռ�����ٷֱ�</td>

		<td width="10%" style="border-left:2px solid #bce4c7;">����</td>
		<td width="10%">ռδ���ٷֱ�</td>
		<td width="10%">ռ�����ٷֱ�</td>
	</tr>


<?php foreach ($res as $k => $v) { ?>
	<tr class="dt" onmouseover="mi(this)" onmouseout="mo(this)">
		<td><?php echo $k; ?></td>

		<td style="border-left:2px solid #bce4c7;"><?php echo $v; ?></td>
		<td><?php echo round(100 * $v / $res_count, 1)."%"; ?></td>

		<td style="border-left:2px solid #bce4c7;"><?php echo intval($res_come[$k]); ?></td>
		<td><?php echo round(100 * intval($res_come[$k]) / $res_come_count, 1)."%"; ?></td>
		<td><?php echo round(100 * intval($res_come[$k]) / $res_count, 1)."%"; ?></td>

		<td style="border-left:2px solid #bce4c7;"><?php echo intval($res_not_come[$k]); ?></td>
		<td><?php echo round(100 * intval($res_not_come[$k]) / $res_not_come_count, 1)."%"; ?></td>
		<td><?php echo round(100 * intval($res_not_come[$k]) / $res_count, 1)."%"; ?></td>
	</tr>
<?php } ?>

</table>


<br>
<div style="color:silver;">&nbsp;ִ�кķ�ʱ�䣺<?php echo round(now() - $pagebegintime, 4); ?> s</div>
<br>
<br>
<br>

<?php } ?>



</body>
</html>