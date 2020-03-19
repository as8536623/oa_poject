<?php
// --------------------------------------------------------
// - 功能说明 : 设置到院
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2011-09-14
// --------------------------------------------------------

$status_array = array(0 => '等待', 1 => '已到', 2 => '未到');

if (!$id) {
	exit("参数错误.");
}

// 主治医生:
$doctor_list = $db->query("select id,name from doctor where hospital_id='$hid'");


if ($_POST) {
	$p = $_POST;
	$r = array(); 
	$save_field = explode(" ", "zhuanjia_num status doctor");
	foreach ($save_field as $v) {
		if ($v && isset($p[$v]) && $p[$v] != $line[$v]) {
			$r[$v] = $p[$v];
		}
	}
	if ($line["status"] != 1) {
		$r["order_date"] = strtotime($p["order_date"]);
	}

	// 字段修改记录:
	if (count($r) > 0) {
		$logs = patient_modify_log($r, $line, "zhuanjia_num status doctor order_date");
		if ($logs) {
			$r["edit_log"] = $logs;
		}
	}

	if (count($r) > 0) {
		$sqldata = $db->sqljoin($r);
		$sql = "update $table set $sqldata where id='$id' limit 1";
		ob_start();
		$rs = $db->query($sql);
		$error = ob_get_clean();
		if ($error) {
			echo $error;
			exit;
		}
		if ($rs) {
			$str = "资料提交成功！";
		} else {
			$str = "提交出错，请稍后再试。";
		}
	} else {
		$str = "资料无变动";
	}

	//user_op_log("修改到院状态[".$line["name"]."]");

	echo '<script type="text/javascript">'."\r\n";
	echo 'parent.msg_box("'.$str.'");'."\r\n";
	if (isset($r["status"])) {
		if ($r["status"] == 1) {
			echo 'parent.document.getElementById("line_'.$id.'").style.color = "red";'."\r\n";
		} else {
			echo 'parent.document.getElementById("line_'.$id.'").style.color = "";'."\r\n";
		}
		//echo 'parent.document.getElementById("line_'.$id.'_status").innerHTML = "'.$status_array[$r["status"]].'";'."\r\n";
	}
	echo 'parent.close_divs();'."\r\n";
	echo '</script>'."\r\n";
	exit;
}



// page begin ----------------------------------------------------
?>
<html>
<head>
<title><?php echo $line["name"]; ?> - 设置到院</title>
<meta http-equiv="Content-Type" content="text/html;charset=gbk">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/jquery.min.js" language="javascript"></script>
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<style>
.left {text-align:right; }
.right {padding:4px 0px; }
</style>
<script language="javascript">
function check_data() {
	var oForm = document.mainform;
	return true;
}
</script>
</head>

<body oncontextmenu="return false">
<form name="mainform" action="" method="POST" onSubmit="return check_data()">
<table width="100%" style="margin-top:10px;">
	<tr>
		<td class="left">到院状态：</td>
		<td class="right">
<?php
if ($line["status"] != 1 || $username == "admin") {
	foreach ($status_array as $k => $v) {
		$chk = $k == $line["status"] ? "checked" : "";
?>
			<input type="radio" name="status" value="<?php echo $k; ?>" id="lab_<?php echo $k; ?>" <?php echo $chk; ?>><label for="lab_<?php echo $k; ?>" <?php if ($chk) echo ' style="color:red;"'; ?>><?php echo $v; ?></label>&nbsp;
<?php
	}
} else {
?>
			<font color="red">(已到院 不可修改)</font>
<?php } ?>
		</td>
	</tr>
<?php
if ($line["status"] != 1) {	
?>
    <tr>
		<td class="left">到院时间：</td>
		<td class="right"><input name="order_date" value="<?php echo date("Y-m-d H:i:s",time()) ?>" class="input" style="width:150px" id="order_date"> <img src="/res/img/calendar.gif" onClick="picker({el:'order_date',dateFmt:'yyyy-MM-dd HH:mm:ss' })" align="absmiddle" style="cursor:pointer" title="选择时间"></td>
	</tr>
<?php
}
?>
	<tr>
		<td class="left" width="70">医生：</td>
		<td class="right">
			<select style="width:90px;" name="doctor" class="combo">
				<option value="" style="color:gray">-主治医生-</option>
				<?php echo list_option($doctor_list, 'name', 'name', $line["doctor"]); ?>
			</select>
		</td>
	</tr>
	<!-- <tr>
		<td class="left">备注：</td>
		<td class="right"><?php echo $line["memo"] ? text_show($line["memo"]) : "(无备注)"; ?></td>
	</tr> -->
	<tr>
		<td class="left">门诊号：</td>
		<td class="right"><input name="zhuanjia_num" style="width:120px; height:24px;" value="<?php echo $line["zhuanjia_num"]; ?>" class="input" /></td>
	</tr>
</table>

<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
<input type="hidden" name="crc" value="<?php echo $_GET["crc"]; ?>">

<div class="button_line">
	<input type="submit" class="buttonb" value="提交资料">
</div>

</form>

</body>
</html>