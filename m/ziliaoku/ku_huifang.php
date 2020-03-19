<?php
// --------------------------------------------------------
// - 功能说明 : 回访
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2013-7-12
// --------------------------------------------------------
require "../../core/core.php";
$table = "ku_list";

$id = intval($_REQUEST["id"]);
if (!$id) {
	exit("参数错误.");
}

$line = $db->query("select * from $table where id='$id' limit 1", 1);


if ($_POST) {
	$r = array();
	$yyb = array(); 
	if (isset($_POST["huifang"]) && trim($_POST["huifang"]) != '') {
		$hf_log = $line["hf_log"].date("Y-m-d H:i")." ".$realname.": ".trim(strip_tags($_POST["huifang"]))."\n";
		$huifang_time = $_POST["huifang_nexttime"];
		$huifang_nowtime = time();
		$yyb_huifang_time = $_POST["huifang_nexttime"] ? intval(str_replace("-", "", $_POST["huifang_nexttime"])) : 0;
		$sql = "update $table set hf_log='$hf_log' ,huifang_nexttime = '$huifang_time' ,huifang_nowtime = '$huifang_nowtime' where id='$id' limit 1";
		
		//更新预约表单(回访内容，下次回访时间)
		if ($line["is_yuyue"]) {
			$patient_hid = "patient_".$line["hid"];
			$yyb_sql = "update $patient_hid set huifang='$hf_log' ,huifang_nexttime = '$yyb_huifang_time' where lid='$id' limit 1";
			$db->query($yyb_sql);
		}
		
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


// page begin ----------------------------------------------------
?>
<html>
<head>
<title>回访</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<style>
.view {border:2px solid #ddf2e2; }
.view td {padding:5px 3px 3px 8px; border:1px solid #ddf2e2; }
.view .h {font-weight:bold; background:#e9f8ec; text-align:left; padding-left:15px; }
.view .l {text-align:right; color:#000000; background:#f8fcf9; }
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
//回访时间快捷
function input_date(id, value) {
	var cv = byid(id).value;
	//var time = cv.split(" ")[1];

	if (byid(id).disabled != true) {
		byid(id).value = value;
	}
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
		<td class="r"><b><?php echo $line["name"]; ?></b></td>
		<td class="l">所属医院：</td>
		<td class="r"><?php echo $line["h_name"]; ?></td>
	</tr>
	<tr>
		<td class="l">咨询内容：</td>
		<td class="r" colspan="3"><?php echo text_show(rtrim($line["zx_content"])); ?></td>
	</tr>
	<tr>
		<td class="l" width="15%">性别：</td>
		<td class="r" width="30%"><?php echo $line["sex"]; ?></td>
		<td class="l" width="15%">年龄：</td>
		<td class="r" width="40%"><?php echo $line["age"] > 0 ? $line["age"] : ""; ?></td>
	</tr>
	<tr>
		<td class="l">手机：</td>
		<td class="r"><?php echo $line["mobile"]; ?></td>
		<td class="l">资料来源：</td>
		<td class="r"><?php echo $line["laiyuan"]; ?></td>
	</tr>
	<tr>
		<td class="l">患者QQ：</td>
		<td class="r"><?php echo $line["qq"]; ?></td>
		<td class="l">患者微信：</td>
		<td class="r"><?php echo $line["weixin"]; ?></td>
	</tr>
	<tr>
		<td colspan="4" class="h">回访</td>
	</tr>
	<tr>
		<td class="l" valign="top">以往回访：</td>
		<td class="r" colspan="3"><?php echo $line["hf_log"] ? text_show($line["hf_log"]) : "<font color=gray>(无记录)</font>"; ?></td>
	</tr>
	<tr>
		<td class="l" valign="top">本次回访：</td>
		<td class="r" colspan="3"><textarea name="huifang" style="width:80%; height:60px;" class="input"></textarea></td>
	</tr>
	<tr>
		<td class="l" valign="top">下次回访提醒：</td>
		<td class="r" colspan="3"><input name="huifang_nexttime" value="<?php echo $huifang_nexttime; ?>" class="input" style="width:150px" id="huifang_nexttime"> <img src="/res/img/calendar.gif" id="huifang_nexttime" onClick="picker({el:'huifang_nexttime',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="选择时间">
        <?php
		$show_days = array(
		
			"今" => $today = date("Y-m-d"), //今天
			"明" => date("Y-m-d", strtotime("+1 day")), //明天
			"后" => date("Y-m-d", strtotime("+2 days")), //后天
			"7天后" => date("Y-m-d", strtotime("+7 days")), 
			"15天后" => date("Y-m-d", strtotime("+15 days")), 
			"1个月后" => date("Y-m-d", strtotime("next Month")), 
		);
		echo '<div style="padding-top:6px;">日期: ';
		foreach ($show_days as $name => $value) {
			echo '<a href="javascript:input_date(\'huifang_nexttime\', \''.$value.'\')">['.$name.']</a>&nbsp;';
		}

?>
        </td>
	</tr>
</table>

<div class="button_line">
	<input type="submit" class="buttonb" value="提交资料">
</div>

<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
</form>

</body>
</html>