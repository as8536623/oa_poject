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
	$zlk = array(); //更新登记表单(回访内容，下次回访时间)
	
	// 下次回访时间:
	$r["huifang_nexttime"] = $_POST["huifang_nexttime"] ? intval(str_replace("-", "", $_POST["huifang_nexttime"])) : 0;
	$zlk["huifang_nexttime"] = $_POST["huifang_nexttime"] ? $_POST["huifang_nexttime"] : 0;
	$zlk["huifang_nowtime"] = time();
	if (isset($_POST["huifang"]) && trim($_POST["huifang"]) != '') {
		$r["huifang"] = $line["huifang"].date("Y-m-d H:i")." ".$realname.": ".trim($_POST["huifang"])."\n";
		$zlk_id = $line["lid"];
		$zlk_line = $db->query("select * from ku_list where id='$zlk_id' limit 1", 1);
		$zlk["hf_log"] = $zlk_line["hf_log"].date("Y-m-d H:i")." ".$realname.": ".trim($_POST["huifang"])."\n";
		
	}

	if (isset($_POST["order_date"]) && date("Y-m-d H:i", $line["order_date"]) != $_POST["order_date"]) {
		$r["order_date"] = strtotime($_POST["order_date"]);
	}

	if ($_POST["memo"]) {
		$_POST["memo"] = str_replace("'", " ", $_POST["memo"]);
		$r["memo"] = (rtrim($line["memo"]) ? (rtrim($line["memo"])."\n") : "").date("Y-m-d H:i ").$realname.": ".trim($_POST["memo"]);
	}

	// 下次回访提醒 客服：
	if ($_POST["huifang"] != '' || $line["huifang_nexttime"] != intval(str_replace("-", "", $_POST["huifang_nexttime"])) ) {
		$r["huifang_kf"] = $realname;
	}
	
	//更新资料库信息
	if (count($zlk) > 0) {
		$zlk_sqldata = $db->sqljoin($zlk);
		$zlk_sql = "update ku_list set $zlk_sqldata where id='$zlk_id' limit 1";
		$db->query($zlk_sql);
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


$huifang_time = $line["huifang_nexttime"] ? int_date_to_date($line["huifang_nexttime"]) : "";

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
		<td class="l">客服姓名：</td>
		<td class="r"><?php echo $line["author"]; ?> @ <?php echo date("Y-m-d H:i", $line["addtime"]); ?> <?php echo $part_id_name[$line["part_id"]]; ?></td>
	</tr>
	<tr>
		<td class="l">咨询内容：</td>
		<td class="r" colspan="3"><?php echo text_show(rtrim($line["content"])); ?></td>
	</tr>
	<tr>
		<td class="l">疾病类型：</td>
		<td class="r"><?php echo $disease_id_name[$line["disease_id"]]; ?></td>
		<td class="l">媒体来源：</td>
		<td class="r"><?php echo $line["media_from"]; ?></td>
	</tr>
	<tr>
		<td class="l">专家号：</td>
		<td class="r"><?php echo $line["zhuanjia_num"]; ?></td>
		<td class="l">预约时间：</td>
		<td class="r"><?php echo @date("Y-m-d H:i", $line["order_date"]); ?></td>
	</tr>
	<tr>
		<td class="l">状态：</td>
		<td class="r"><?php echo $status_array[$line["status"]]; ?></td>
		<td class="l">医生：</td>
		<td class="r">
<?php
if (in_array($uinfo["part_id"], array(2,3))) {
	echo "<font color='gray'>(不显示)</font>";
} else {
	if ($line["xianchang_doctor"] || $line["doctor"]) {
		echo $line["xianchang_doctor"] ? ("现场医生：".$line["xianchang_doctor"]."&nbsp;") : "";
		echo $line["doctor"] ? ("主治医生：".$line["doctor"]) : "";
	} else {
		echo "<font color='gray'>(未设置)</font>";
	}
}
?>
		</td>
	</tr>
	<tr>
		<td class="l">备注：</td>
		<td class="r" colspan="3"><?php echo text_show($line["memo"]); ?></td>
	</tr>
	<tr>
		<td colspan="4" class="h">回访</td>
	</tr>
	<tr>
		<td class="l" valign="top">历次回访：</td>
		<td class="r" colspan="3"><?php echo $line["huifang"] ? text_show(strip_tags($line["huifang"])) : "<font color=gray>(暂无记录)</font>"; ?></td>
	</tr>
	<tr>
		<td class="l" valign="top">本次回访：</td>
		<td class="r" colspan="3"><textarea name="huifang" style="width:80%; height:60px;" class="input"></textarea> <span class="intro">回访记录</span></td>
	</tr>
	<tr>
		<td class="l" valign="top">修改预约时间：</td>
		<td class="r" colspan="3">
<?php if ($line["status"] != 1) { ?>
			<input name="order_date" value="<?php echo date("Y-m-d H:i", $line["order_date"]); ?>" class="input" style="width:150px" id="order_date" readonly> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'order_date',dateFmt:'yyyy-MM-dd HH:mm'})" align="absmiddle" style="cursor:pointer" title="选择时间"> <span class="intro">如无必要 请不要修改</span>
<?php } else { ?>
			<font color="red">已到院，不能修改</font>
<?php } ?>
		</td>
	</tr>
	<tr>
		<td class="l" valign="top">下次回访提醒：</td>
		<td class="r" colspan="3"><input name="huifang_nexttime" value="<?php echo $huifang_time; ?>" class="input" style="width:150px" id="huifang_nexttime"> <img src="/res/img/calendar.gif" id="huifang_nexttime" onClick="picker({el:'huifang_nexttime',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="选择时间"> <?php if ($huifang_time && $line["huifang_kf"] != '') echo '&nbsp;该时间由“'.$line["huifang_kf"]."”设置&nbsp;"; ?> <span class="intro">设置下次回访的提醒时间，修改为空则不提醒</span></td>
	</tr>
	<tr>
		<td class="l" valign="top">添加备注：</td>
		<td class="r" colspan="3"><input name="memo" style="width:80%;" class="input"> <span class="intro">添加一条备注资料</span></td>
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