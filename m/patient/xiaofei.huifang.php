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

if ($_POST) {
	$r = array();
	
	// 下次回访时间:
	$r["xf_huifang_nexttime"] = $_POST["xf_huifang_nexttime"] ? intval(str_replace("-", "", $_POST["xf_huifang_nexttime"])) : 0;
	if (isset($_POST["xf_huifang"]) && trim($_POST["xf_huifang"]) != '') {
		$r["xf_huifang"] = $line["xf_huifang"].date("Y-m-d H:i")." ".$realname.": ".trim($_POST["xf_huifang"])."\n";
	}

	if ($_POST["xf_memo"]) {
		$_POST["xf_memo"] = str_replace("'", " ", $_POST["xf_memo"]);
		$r["xf_memo"] = (rtrim($line["xf_memo"]) ? (rtrim($line["xf_memo"])."\n") : "").date("Y-m-d H:i ").$realname.": ".trim($_POST["xf_memo"]);
	}

	if (count($r) > 0) {
		$logs = patient_modify_log($r, $line, "order_date");
		if ($logs) {
			$r["edit_log"] = $logs;
		}

		$sqldata = $db->sqljoin($r);
		$sql = "update $table set $sqldata where id='$id' limit 1";
		ob_start();
		$rs = $db->query($sql);
		$error = ob_get_clean();
		if ($error) {
			echo "提交出错，请联系开发人员分析：<br>".$error;
			exit;
		}
		if ($rs) {
			$str = "资料提交成功！";
		} else {
			echo "提交出错，请联系开发人员分析：<br>".$db->sql;
			exit;
		}
	} else {
		$str = "资料无变动";
	}
	echo '<script type="text/javascript">'."\r\n";
	echo 'parent.msg_box("'.$str.'");'."\r\n";
	echo 'parent.load_src(0);'."\r\n";
	echo '</script>'."\r\n";
	exit;
}


$xf_huifang_time = $line["xf_huifang_nexttime"] ? int_date_to_date($line["xf_huifang_nexttime"]) : "";

// page begin ----------------------------------------------------
?>
<html>
<head>
<title><?php echo $line["name"]; ?> - 回访</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<style>
.view {border:2px solid #aedfbb; }
.view td {padding:5px 3px 3px 8px; border:1px solid #d3efdb; }
.view .h {font-weight:bold; background:#eaf7ed; text-align:left; padding-left:15px; }
.view .l {text-align:right; color:#000000; background:#f4fbf7; }
.view .r {text-align:left; }
.fo_line {margin:15px 0 auto; text-align:center; }

.left {text-align:right; }
.right {padding:4px 0px; }
</style>
<script language="javascript">
function check_data(oForm) {
	if (oForm.huifang.value == '') {
		if (!confirm("您还没有输入回访内容，确定要提交吗？")) {
			oForm.huifang.focus();
			return false;
		}
	}
	return true;
}
</script>
</head>

<body>
<form name="mainform" action="" method="POST" onSubmit="return check_data(this)">
<table width="100%" align="center" class="view">
	<tr>
		<td colspan="4" class="h">基本资料</td>
	</tr>
	<tr>
		<td class="l">姓名：</td>
		<td class="r" colspan="3"><b><?php echo $line["name"]; ?></b></td>
	</tr>
	<tr>
		<td class="l" width="15%">性别：</td>
		<td class="r" width="30%"><?php echo $line["sex"]; ?></td>
		<td class="l" width="15%">年龄：</td>
		<td class="r" width="40%"><?php echo $line["age"] > 0 ? $line["age"] : ""; ?></td>
	</tr>
	<tr>
		<td class="l">电话：</td><!-- 回访需要显示号码 这里不考虑是否设置了显示号码功能 始终显示 否则无法回访 -->
		<td class="r"><?php echo $line["tel"]; ?></td>
		<td class="l">医生：</td>
		<td class="r"><?php  echo $line["doctor"];?></td>
	</tr>
    <tr>
		<td class="l">门诊号：</td>
		<td class="r"><?php echo $line["zhuanjia_num"]; ?></td>
		<td class="l">到院时间：</td>
		<td class="r"><?php echo @date("Y-m-d H:i", $line["order_date"]); ?></td>
	</tr>
	<tr>
		<td class="l">疾病类型：</td>
		<td class="r"><?php echo $disease_id_name[$line["disease_id"]]; ?></td>
		<td class="l">媒体来源：</td>
		<td class="r"><?php echo $line["media_from"]; ?></td>
	</tr>
	<tr>
		<td class="l">备注：</td>
		<td class="r" colspan="3"><?php echo text_show($line["xf_memo"]); ?></td>
	</tr>
	<tr>
		<td colspan="4" class="h">回访</td>
	</tr>
	<tr>
		<td class="l" valign="top">历次回访：</td>
		<td class="r" colspan="3"><?php echo $line["xf_huifang"] ? text_show(strip_tags($line["xf_huifang"])) : "<font color=gray>(暂无记录)</font>"; ?></td>
	</tr>
	<tr>
		<td class="l" valign="top">本次回访：</td>
		<td class="r" colspan="3"><textarea name="xf_huifang" style="width:80%; height:60px;" class="input"></textarea> <span class="intro">回访记录</span></td>
	</tr>
	<tr>
		<td class="l" valign="top">下次回访提醒：</td>
		<td class="r" colspan="3"><input name="xf_huifang_nexttime" value="<?php echo $xf_huifang_time; ?>" class="input" style="width:150px" id="xf_huifang_nexttime"> <img src="/res/img/calendar.gif" id="xf_huifang_nexttime" onClick="picker({el:'xf_huifang_nexttime',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="选择时间">  <span class="intro">设置下次回访的提醒时间，修改为空则不提醒</span></td>
	</tr>
	<tr>
		<td class="l" valign="top">添加备注：</td>
		<td class="r" colspan="3"><input name="xf_memo" style="width:80%;" class="input"> <span class="intro">添加一条备注资料</span></td>
	</tr>
</table>

<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="crc" value="<?php echo $_GET["crc"]; ?>">

<div class="button_line">
	<input type="submit" class="buttonb" value="提交资料">
</div>

</form>
</body>
</html>