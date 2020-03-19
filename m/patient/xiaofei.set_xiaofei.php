<?php
// --------------------------------------------------------
// - 功能说明 : 现场客服咨询结果及设置
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2011-09-16
// --------------------------------------------------------
$id = intval($_REQUEST["id"]);
if (!$id) {
	exit("参数错误.");
}

$line = $db->query_first("select * from $table where id='$id' limit 1");

if ($_POST) {
	$p = $_POST;
	$r = array();

	//$r["is_zhiliao"] = $p["is_zhiliao"];
	$r["is_xiaofei"] = $p["is_xiaofei"];

	if ($p["xiaofei"] > 0) {
		$r["xiaofei_count"] = floatval($line["xiaofei_count"]) + floatval($p["xiaofei"]);
		$r["xiaofei_log"] = trim($line["xiaofei_log"]."\n".date("Y-m-d")." 消费 ".$p["xiaofei"]." 元 ".$p["xiaofei_str"]);
	}

	if (trim($p["xf_memo"]) != '') {
		$r["xf_memo"] = trim($line["xf_memo"]."\n".date("Y-m-d H:i ").$realname.": ".$_POST["xf_memo"]);
	}
	
	// 要监视的修改字段:
	$log_field_str = "xiaofei_count xiaofei_log memo";

	if (count($r) > 0) {
		
		$s2 = patient_modify_log_s($r, $line, $log_field_str);
		if ($s2 != '') {
			$log->add("修改到诊病人：".$line["name"], $s2, $line, $table);
		}
		
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
			echo "提交出错，请稍后再试。";
			exit;
		}
	} else {
		$str = "资料无变动";
	}
	echo '<script type="text/javascript">'."\r\n";
	echo 'parent.load_src(0);'."\r\n";
	echo 'parent.msg_box("'.$str.'");'."\r\n";
	echo '</script>'."\r\n";
	exit;
}



// page begin ----------------------------------------------------
?>
<html>
<head>
<title><?php echo $line["name"]; ?> - 消费记录</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<style>
.l {text-align:right; border-bottom:1px dotted #D8D8D8; padding:8px 0px 4px 0; }
.r {text-align:left; border-bottom:1px dotted #D8D8D8; padding:6px 0px 6px 0; }
.foot_button {margin-top:15px; text-align:center; }
</style>
<script language="javascript">
function check_data(oForm) {
	return true;
}
</script>
</head>

<body>
<form name="mainform" action="" method="POST" onSubmit="return check_data(this)">
<table width="100%" style="margin-top:10px;">
	<!--<tr>
		<td class="l" width="120">是否治疗：</td>
		<td class="r">
			<input type="radio" name="is_zhiliao" value="1" <?php echo $line["is_zhiliao"] == 1 ? "checked" : ""; ?> id="is_zhiliao_1"><label for="is_zhiliao_1">已治疗</label>
			<input type="radio" name="is_zhiliao" value="0" <?php echo $line["is_zhiliao"] == 0 ? "checked" : ""; ?> id="is_zhiliao_0" disabled="true"><label for="is_zhiliao_0">未治疗</label>
		</td>
	</tr>-->
	<tr>
		<td class="l" width="120">是否消费：</td>
		<td class="r">
			<input type="radio" name="is_xiaofei" value="1" <?php echo $line["is_xiaofei"] == 1 ? "checked" : ""; ?> id="is_xiaofei_1"><label for="is_xiaofei_1">已消费</label>
			<input type="radio" name="is_xiaofei" value="0" <?php echo $line["is_xiaofei"] == 0 ? "checked" : ""; ?> id="is_xiaofei_0" disabled="true"><label for="is_xiaofei_0">未消费</label>
		</td>
	</tr>
	<tr>
		<td class="l" width="120" valign="top">本次消费：</td>
		<td class="r">
			<input name="xiaofei" value="" class="input" style="width:100px;"> &nbsp;消费备注：<input name="xiaofei_str" value="" class="input" style="width:200px;">
		</td>
	</tr>
	<tr>
		<td class="l" width="120" valign="top">历史总消费：</td>
		<td class="r"><?php echo round($line["xiaofei_count"], 1); ?></td>
	</tr>
	<tr>
		<td class="l" width="120" valign="top">消费记录：</td>
		<td class="r"><?php echo $line["xiaofei_log"] ? text_show($line["xiaofei_log"]) : '<font color=gray>(无)</font>'; ?></td>
	</tr>
	<tr>
		<td class="l" width="120" valign="top">添加备注：</td>
		<td class="r"><textarea name="xf_memo" style="width:75%; height:48px;" class="input"></textarea> (所有人可见)</td>
	</tr>
</table>

<div class="foot_button">
	<input type="submit" class="buttonb" value="提交资料">
</div>

<input type="hidden" name="id" value="<?php echo $id; ?>">
</form>
</body>
</html>