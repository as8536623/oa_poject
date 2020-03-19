<?php
/*
// - 功能说明 : 病种成本分析
// - 创建作者 : 幽兰 (weelia@126.com)
// - 创建时间 : 2012-08-24
*/
$table = "disease_xiaofei";
require "../../core/core.php";

if (count($hospital_ids) == 0) {
	exit_html("管理员没有为你分配医院，不能使用此功能。");
}

//echo "<pre>";
//print_r($pinfo);
//echo "</pre>";

// 该用户是否具有修改权限:
$can_edit = @check_power("edit") ? 1 : 0;


// 初始值为本月:
if ($_GET["date"] && strlen($_GET["date"]) == 6) {
	$date = $_GET["date"];
} else {
	if (date("j") == 1) {
		$date = date("Ym", strtotime("-1 month")); //每个月1号进入时，默认仍然显示上个月的列表 可以手工切换到下个月 @ 2011-11-30
	} else {
		$date = date("Ym"); //本月
	}
	$_GET["date"] = $date;
}
$date_time = strtotime(substr($date,0,4)."-".substr($date,4,2)."-01 0:0:0");

// 可用 年,月 数组
$y_array = $m_array = $d_array = array();
for ($i = date("Y"); $i >= (date("Y") - 2); $i--) $y_array[] = $i;
for ($i = 1; $i <= 12; $i++) $m_array[] = $i;
for ($i = 1; $i <= 31; $i++) {
	if ($i <= 28 || checkdate(date("n", $date_time), $i, date("Y", $date_time))) {
		$d_array[] = $i;
	}
}

// 最近6个月
$his_month = array();
for ($i = 1; $i <= 6; $i++) {
	$tmp = strtotime("-{$i} month", $date_time);
	$his_month[date("Ym", $tmp)] = date("Y-m", $tmp);
}
asort($his_month);



$change_op = $_GET["go"];
if (!$hid || $change_op != '') {
	// 医院切换序列:
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
			msg_box("已经是最前一家医院了", "back", 1, 2);
		}
	}
	if ($change_op == "next") {
		$cur_k = array_search($hid, $h_list);
		if ($cur_k < count($h_list) - 1) {
			$check_hid = $h_list[$cur_k + 1];
		} else {
			msg_box("已经是最后一家医院了", "back", 1, 2);
		}
	}
	if ($check_hid > 0) {
		$_SESSION["hospital_id"] = $check_hid;
		header("location: disease_xiaofei.php");
	}
	exit;
}


// 读取病种
$disease_arr = $db->query("select id,name from disease where hospital_id='$hid' order by sort desc, id asc", "id", "name");

// 调用各疾病该月预约人数和到诊人数:
$d_begin = $date_time;
$d_end = strtotime("+1 month", $date_time) - 1;

// 预约人数:
$tem_arr = $db->query("select disease_id,count(disease_id) as c from patient_{$hid} where disease_id!='' and addtime>=$d_begin and addtime<=$d_end group by disease_id", "disease_id", "c");
// 疾病有多病种的情况:
$disease_yuyue_arr = array();
foreach ($tem_arr as $d_id => $d_count) {
	$d_id_arr = explode(",", $d_id);
	foreach ($d_id_arr as $v) {
		$v = intval($v);
		if ($v > 0) {
			$disease_yuyue_arr[$v] = intval($disease_yuyue_arr[$v]) + $d_count;
		}
	}
}

// 到诊人数:
$tem_arr = $db->query("select disease_id,count(disease_id) as c from patient_{$hid} where disease_id!='' and status=1 and order_date>=$d_begin and order_date<=$d_end group by disease_id", "disease_id", "c");
// 疾病有多病种的情况:
$disease_come_arr = array();
foreach ($tem_arr as $d_id => $d_count) {
	$d_id_arr = explode(",", $d_id);
	foreach ($d_id_arr as $v) {
		$v = intval($v);
		if ($v > 0) {
			$disease_come_arr[$v] = intval($disease_come_arr[$v]) + $d_count;
		}
	}
}


// 读取病种记录数据
$int_month = date("Ym", $date_time);
$disease_info = $db->query("select * from disease_xiaofei where hid=$hid and month=$int_month", "disease_id");



/*
// ------------------ 函数 -------------------
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


// 页面开始 ------------------------
?>
<html>
<head>
<title>病种成本分析</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style>
.head, .head a {font-family:"微软雅黑","Verdana"; }
.item {padding:8px 3px 6px 3px !important; }
.item, a {font-family:"Tahoma"; }
.footer_op_left {font-family:"Tahoma"; }

.jiuzhen_t {border:2px solid #47b48b; }
.jiuzhen_t .head {background:#f6f6f6; border:1px solid #d8d8d8;  }
.jiuzhen_t td {padding:4px 2px 2px 2px; border:1px solid #e0e0e0; }
.tt {border-top:2px solid #d8d8d8 !important; }
.tl {border-left:2px solid #d8d8d8 !important; }
.tr {border-right:2px solid #d8d8d8 !important; }
.tb {border-bottom:2px solid #d8d8d8 !important; }

.rp_title {margin-top:30px; text-align:center; font-size:16px; font-family:"微软雅黑"; }
.num {font-size:10px; font-family:"Tahoma"; color:gray; }

#date_tips {float:left; font-weight:bold; padding-top:3px; }
#ch_date {float:left; margin-left:20px; }
.site_name {display:block; padding:4px 0px;}
.site_name, .site_name a {font-family:"Arial", "Tahoma"; }
.ch_date_a b, .ch_date_a a {font-family:"Arial"; }
.ch_date_a b {border:0px; padding:1px 5px 1px 5px; color:red; }
.ch_date_a a {border:0px; padding:1px 5px 1px 5px; }
.ch_date_a a:hover {border:1px solid silver; padding:0px 4px 0px 4px; }
.ch_date_b {padding-top:8px; text-align:left; width:80%; color:silver; }
.ch_date_b a {padding:0 3px; }

.cell_input {border:0; width:95%; background:green; color:white; font-size:12px; }

</style>
<script language="javascript">
function byid(id) {
	return document.getElementById(id);
}

function set_date(s) {
	byid('date_input').value = s;
}

function load_url(s) {
	parent.load_box(1, 'src', s);
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
	byid("date_"+type).value = parseInt(o.innerHTML, 10);

	var a = parseInt(byid("date_1").value, 10);
	var b = parseInt(byid("date_2").value, 10);

	var s = a + '' + (b<10 ? "0" : "") + b;

	byid("date").value = s;
	byid("ch_date").submit();
}

function check_if_enter(evt, obj) {
	evt = window.event || evt;
	if (evt.keyCode == 13) {
		cell_submit(obj);
	} else {
		update_edit_flag(obj);
	}
}

function update_edit_flag(o) {
	o.name = "1";
}

function cell_edit(obj) {
	var oTD = obj.parentNode;
	var value = obj.innerHTML;
	if (value == "修改") value = '';
	var t = oTD.id.split("@");
	var disease_id = t[0];
	var edit_type = t[1];
	var hid = byid("cur_page_hid").value;
	var month = byid("cur_page_month").value;

	// edit area:
	// alert(hid+"@"+month+"@"+disease_id+"@"+edit_type);
	var input_id = hid+"@"+month+"@"+disease_id+"@"+edit_type;
	var s = '<input name="0" id="'+input_id+'" title="回车提交数据" onblur="cell_submit(this)" onchange="update_edit_flag(this)" onkeydown="check_if_enter(event, this)" class="cell_input" value="'+value+'">';
	oTD.innerHTML = s;
	oTD.getElementsByTagName("INPUT")[0].focus();
	setCursorPosition(byid(input_id), value.length+1);
}

//设置光标位置函数
function setCursorPosition(ctrl, pos) {
	if(ctrl.setSelectionRange){
		ctrl.focus();
		ctrl.setSelectionRange(pos, pos);
	} else if (ctrl.createTextRange) {
		var range = ctrl.createTextRange();
		range.collapse(true);
		range.moveEnd('character', pos);
		range.moveStart('character', pos);
		range.select();
	}
}

function cell_submit(obj) {
	var data_name = obj.id;
	var data = obj.value;
	if (obj.name == 1) { //name=1 修改过，否则未修改过
		var xm = new ajax();
		xm.connect("/m/jingjia/disease_xiaofei_update.php", "GET", "data_name="+data_name+"&data="+data, cell_submit_do);
		parent.msg_box("提交中...");
	} else {
		obj.parentNode.innerHTML = '<a href="#" onclick="cell_edit(this);return false;">'+(data == "" ? "修改" : data)+'</a>';
	}
}

function cell_submit_do(o) {
	parent.msg_box_hide();
	var out = ajax_out(o);
	if (out["status"] == "ok") {
		byid(out["update_id"]).innerHTML = '<a href="#" onclick="cell_edit(this);return false;">'+(out["value"] == "" ? "修改" : out["value"])+'</a>';
		// 更新数据:
		if (out["to_update"]) {
			for (var name in out["to_update"]) {
				byid(name).innerHTML = out["to_update"][name];
			}
		}
	} else {
		alert("数据提交失败，请重新提交");
	}
}


</script>
</head>

<body>

<div style="margin:12px 0 0 0px; ">
	<div id="date_tips">日期：</div>
	<form id="ch_date" method="GET">
		<span class="ch_date_a">年：<?php echo my_show($y_array, date("Y", $date_time), "return update_date(1,this)"); ?>&nbsp;&nbsp;&nbsp;</span>
		<span class="ch_date_a">月：<?php echo my_show($m_array, date("m", $date_time), "return update_date(2,this)"); ?>&nbsp;&nbsp;&nbsp;</span>
		&nbsp;
		<button onclick="byid('date').value='<?php echo date("Ym", strtotime("-1 month", $date_time)); ?>'; this.form.submit(); return false;" class="button" title="查看上一个月的报表">上月</button>&nbsp;&nbsp;
		<button onclick="byid('date').value='<?php echo date("Ym"); ?>'; this.form.submit(); return false;" class="button" title="查看本月报表">本月</button>&nbsp;&nbsp;
		<button onclick="byid('date').value='<?php echo date("Ym", strtotime("+1 month", $date_time)); ?>'; this.form.submit(); return false;" class="button" title="查看下一个月的报表">下月</button>&nbsp;&nbsp;
		&nbsp;
		&nbsp;
		<button onclick="load_url('m/chhos.php'); return false;" class="buttonb" title="切换到其他医院">切换医院</button>&nbsp;
		<button onclick="location = '?go=prev'; return false;" class="button" title="切换到上一家医院">上</button>&nbsp;
		<button onclick="location = '?go=next'; return false;" class="button" title="切换到下一家医院">下</button>&nbsp;

		<input type="hidden" id="date_1" value="<?php echo date("Y", $date_time); ?>">
		<input type="hidden" id="date_2" value="<?php echo date("n", $date_time); ?>">
		<input type="hidden" name="date" id="date" value="">
		<input type="hidden" name="show" value="<?php echo $_GET["show"]; ?>">
		<input type="hidden" name="showdata" value="<?php echo $_GET["showdata"]; ?>">
	</form>
	<div class="clear"></div>
</div>

<div class="rp_title"><?php echo $hinfo["name"]; ?> <?php echo date("Y年n月", $date_time); ?> 病种成本分析</div>

<table width="100%" align="center" class="jiuzhen_t" id="data_list" style="margin-top:15px;">
	<div id="data_head">
		<tr>
			<td class="head" align="center" width="10%" rowspan="2">病种</td>
			<td class="head" align="center" width="18%" colspan="2">网络人数情况</td>
			<td class="head" align="center" width="27%" colspan="3">网络广告费</td>
			<td class="head" align="center" width="9%" rowspan="2">预约人均成本</td>
			<td class="head" align="center" width="9%" rowspan="2">到诊人均成本</td>
			<td class="head" align="center" width="9%" rowspan="2">目标广告人均成本</td>
			<td class="head" align="center" width="9%" rowspan="2">目标人数</td>
			<td class="head" align="center" width="9%" rowspan="2">备注</td>
		</tr>
		<tr>
			<td class="head" align="center" width="9%">预约人数</td>
			<td class="head" align="center" width="9%">到诊人数</td>
			<td class="head" align="center" width="9%">确定投入</td>
			<td class="head" align="center" width="9%">无法确定投入分配</td>
			<td class="head" align="center" width="9%">总投入</td>
		</tr>
	</div>

<?php

function _show_data($type) {
	global $can_edit, $disease_info, $d_id;
	if ($can_edit) {
		echo '<a href="#" onclick="cell_edit(this);return false;">'.(($disease_info[$d_id][$type] != '') ? $disease_info[$d_id][$type] : '修改').'</a>';
	} else {
		echo $disease_info[$d_id][$type];
	}
}


foreach ($disease_arr as $d_id => $d_name) {
	$yuyue_renjun = $daozhen_renjun = '';
	if ($disease_info[$d_id]["touru_zong"] > 0 && intval($disease_yuyue_arr[$d_id]) > 0) {
		$yuyue_renjun = round($disease_info[$d_id]["touru_zong"] / intval($disease_yuyue_arr[$d_id]), 1);
	}
	if ($disease_info[$d_id]["touru_zong"] > 0 && intval($disease_come_arr[$d_id]) > 0) {
		$daozhen_renjun = round($disease_info[$d_id]["touru_zong"] / intval($disease_come_arr[$d_id]), 1);
	}
?>
	<tr>
		<td class="item" align="center"><?php echo $d_name; ?></td>
		<td class="item" align="center"><?php echo intval($disease_yuyue_arr[$d_id]); ?></td>
		<td class="item" align="center"><?php echo intval($disease_come_arr[$d_id]); ?></td>
		<td class="item" align="center" id="<?php echo $d_id; ?>@touru_queding"><?php _show_data("touru_queding"); ?></td>
		<td class="item" align="center" id="<?php echo $d_id; ?>@touru_buqueding"><?php _show_data("touru_buqueding"); ?></td>
		<td class="item" align="center" id="<?php echo $d_id; ?>@touru_zong"><?php echo $disease_info[$d_id]["touru_zong"]; ?></td>
		<td class="item" align="center" id="<?php echo $d_id; ?>@yuyue_renjun"><?php echo $yuyue_renjun; ?></td>
		<td class="item" align="center" id="<?php echo $d_id; ?>@daozhen_renjun"><?php echo $daozhen_renjun; ?></td>
		<td class="item" align="center" id="<?php echo $d_id; ?>@mubiao_renjun"><?php _show_data("mubiao_renjun"); ?></td>
		<td class="item" align="center" id="<?php echo $d_id; ?>@mubiao_renshu"><?php _show_data("mubiao_renshu"); ?></td>
		<td class="item" align="center" id="<?php echo $d_id; ?>@memo"><?php _show_data("memo"); ?></td>
	</tr>
<?php } ?>

</table>

<table width="100%" align="center" class="jiuzhen_t" id="float_head" style="display:none; border-bottom:0;">
	<tr>
		<td class="head" align="center" width="10%" rowspan="2">病种</td>
		<td class="head" align="center" width="18%" colspan="2">网络人数情况</td>
		<td class="head" align="center" width="27%" colspan="3">网络广告费</td>
		<td class="head" align="center" width="9%" rowspan="2">预约人均成本</td>
		<td class="head" align="center" width="9%" rowspan="2">到诊人均成本</td>
		<td class="head" align="center" width="9%" rowspan="2">目标广告人均成本</td>
		<td class="head" align="center" width="9%" rowspan="2">目标人数</td>
		<td class="head" align="center" width="9%" rowspan="2">备注</td>
	</tr>
	<tr>
		<td class="head" align="center" width="9%">预约人数</td>
		<td class="head" align="center" width="9%">到诊人数</td>
		<td class="head" align="center" width="9%">确定投入</td>
		<td class="head" align="center" width="9%">无法确定投入分配</td>
		<td class="head" align="center" width="9%">总投入</td>
	</tr>
</table>

<input type="hidden" id="cur_page_hid" value="<?php echo $hid; ?>" />
<input type="hidden" id="cur_page_month" value="<?php echo date("Ym", $date_time); ?>" />

</body>
</html>