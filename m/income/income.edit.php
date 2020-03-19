<?php defined("ROOT") or exit("Error."); ?>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style>
.data_box {width:300px; float:left; height:30px; padding-top:3px; }
.data_title {width:60px; text-align:right; float:left; padding-top:2px; }
.data_input {width:140px; text-align:left; float:left; }
</style>
<script language="javascript">
function check_data(f) {
	if (! f.doctor_id.value) {
		msg_box("请选择医生");
		f.doctor_id.focus();
		return false;
	}

	return true;
}


function check_doctor(o) {
	if (byid("mainform").op.value == "add") {
		byid("doctor_tips").innerHTML = '';
		var date = byid("mainform").date.value;
		var fee_type = byid("mainform").fee_type.value;
		if (date != '' && o.value > 0) {
			var xm = new ajax();
			xm.connect("/http/check_income_repeat.php", "GET", "&date="+date+"&doctor_id="+o.value+"&fee_type="+fee_type, check_doctor_do);
		}
	}
}

function check_doctor_do(o) {
	var out = ajax_out(o);
	if (out["status"] == "ok") {
		if (out["id"] > 0) {
			byid("doctor_tips").innerHTML = "该医生数据已添加，不能提交";
			if (confirm("该医生的数据已添加，点击“确定”载入修改；点击“取消”，选择其他医生。")) {
				location.replace("?op=edit&fee_type="+byid("mainform").fee_type.value+"&id="+out["id"]);
			}
		}
	}
}
</script>
</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $pinfo["title"]." - ".($op == "add" ? "新增" : "修改"); ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"><input type="button" value="返回" onclick="history.back()" class="button"></div>
</div>
<!-- 头部 end -->

<div class="space"></div>

<div class="description">
	<div class="d_title">提示：</div>
	<!-- <li class="d_item">没想好怎么说</li> -->
</div>

<div class="space"></div>

<form name="mainform" id="mainform" method="POST" onsubmit="return check_data(this)">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">基本数据</td>
	</tr>
	<!-- <tr>
		<td class="left">时间：</td>
		<td class="right"><input name="date" value="<?php echo $op == "add" ? ($_GET["date"] ? $_GET["date"] : date("Ymd")) : $line["date"]; ?>" class="input" style="width:120px"> <span class="intro">格式: 20090825 默认是今天</span></td>
	</tr> -->
	<tr>
		<td class="left">医生：</td>
		<td class="right">
			<select name="doctor_id" class="combo" onchange="check_doctor(this)">
				<option value="" style="color:gray">-请选择医生-</option>
				<?php
				$doctor_list = $db->query("select id,name from doctor where hospital_id=$hid", "id", "name");
				echo list_option($doctor_list, "_key_", "_value_", $line["doctor_id"]);
				?>
			</select>
			<span id="doctor_tips" style="color:red"></span>
			<span class="intro">医生必须选择</span>
		</td>
	</tr>

	<tr>
		<td colspan="2" class="head">当日人数</td>
	</tr>

<?php if ($fee_type == 0) { ?>
	<!-- 门诊 -->
	<tr>
		<td class="left">初诊人数：</td>
		<td class="right"><input name="chuzhen" value="<?php echo $line["chuzhen"]; ?>" class="input" style="width:120px"> <span class="intro">初诊人数</span></td>
	</tr>
	<tr>
		<td class="left">复诊人数：</td>
		<td class="right"><input name="fuzhen" value="<?php echo $line["fuzhen"]; ?>" class="input" style="width:120px"> <span class="intro">复诊人数</span></td>
	</tr>
	<tr>
		<td class="left">流失人数：</td>
		<td class="right"><input name="liushi" value="<?php echo $line["liushi"]; ?>" class="input" style="width:120px"> <span class="intro">流失人数</span></td>
	</tr>

<?php } else { ?>
	<!-- 住院 -->
	<tr>
		<td class="left">住院人数：</td>
		<td class="right"><input name="zhuyuan" value="<?php echo $line["zhuyuan"]; ?>" class="input" style="width:120px"> </td>
	</tr>
<?php } ?>

	<tr>
		<td colspan="2" class="head">当日收费详情</td>
	</tr>
	<tr>
		<td class="right" colspan="2" style="padding-left:12%;">
<?php
$detail = (array) @unserialize($line["detail"]);

$xiangmu = $fee_type == 0 ? $hconfig["门诊收费项目"] : $hconfig["住院收费项目"];
$xiangmu_array = explode("|", trim($xiangmu));
foreach (array_keys($detail) as $k) {
	if ($k && !in_array($k, $xiangmu_array)) $xiangmu_array[] = $k;
}
foreach ($xiangmu_array as $k) {
	if ($k) {
?>
			<div class="data_box">
				<div class="data_title"><?php echo $k; ?>：</div>
				<div class="data_input"><input name="fee_<?php echo $k; ?>" value="<?php echo $detail[$k]; ?>" class="input" style="width:120px"></div>
				<div class="clear"></div>
			</div>
<?php
	}
}
?>
		</td>
	</tr>

	<tr>
		<td colspan="2" class="head">当日营业额</td>
	</tr>
	<tr>
		<td class="left">营业额：</td>
		<td class="right"><input name="yingyee" value="<?php echo $line["yingyee"]; ?>" class="input" style="width:120px" disabled="true"> <span class="intro">自动计算</span></td>
	</tr>

	<tr>
		<td colspan="2" class="head">资料备注</td>
	</tr>
	<tr>
		<td class="left">备注内容：</td>
		<td class="right">
<?php
if ($line["memo"]) {
	echo str_replace(" ", "&nbsp;", str_replace("\r", "", str_replace("\n", "<br>", $line["memo"])));
} else {
	echo "(暂无备注)<br>";
} ?>
			<br>
			<b>添加备注： </b><br>
			<textarea name="memo" class="input" style="width:60%; height:60px; overflow:visible;"></textarea>
		</td>
	</tr>

</table>
<div class="space"></div>

<input type="hidden" name="fee_names" value="<?php echo implode("|", $xiangmu_array); ?>">

<input type="hidden" name="date" value="<?php echo $op == "add" ? $_GET["date"] : $line["date"]; ?>">
<input type="hidden" name="op" value="<?php echo $op; ?>">
<input type="hidden" name="fee_type" value="<?php echo $fee_type; ?>">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="back_url" value="<?php echo $back_url; ?>">
<div class="button_line"><input type="submit" class="submit" value="提交资料"></div>


</form>

<div class="space"></div>
</body>
</html>