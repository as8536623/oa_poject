<?php
// --------------------------------------------------------
// - ����˵�� : �ֳ��ͷ���ѯ���������
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2011-09-16
// --------------------------------------------------------
$id = intval($_REQUEST["id"]);
if (!$id) {
	exit("��������.");
}

$line = $db->query_first("select * from $table where id='$id' limit 1");

if ($_POST) {
	$p = $_POST;
	$r = array();

	//$r["is_zhiliao"] = $p["is_zhiliao"];
	$r["is_xiaofei"] = $p["is_xiaofei"];

	if ($p["xiaofei"] > 0) {
		$r["xiaofei_count"] = floatval($line["xiaofei_count"]) + floatval($p["xiaofei"]);
		$r["xiaofei_log"] = trim($line["xiaofei_log"]."\n".date("Y-m-d")." ���� ".$p["xiaofei"]." Ԫ ".$p["xiaofei_str"]);
	}

	if (trim($p["xf_memo"]) != '') {
		$r["xf_memo"] = trim($line["xf_memo"]."\n".date("Y-m-d H:i ").$realname.": ".$_POST["xf_memo"]);
	}
	
	// Ҫ���ӵ��޸��ֶ�:
	$log_field_str = "xiaofei_count xiaofei_log memo";

	if (count($r) > 0) {
		
		$s2 = patient_modify_log_s($r, $line, $log_field_str);
		if ($s2 != '') {
			$log->add("�޸ĵ��ﲡ�ˣ�".$line["name"], $s2, $line, $table);
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
			$str = "�����ύ�ɹ���";
		} else {
			echo "�ύ�������Ժ����ԡ�";
			exit;
		}
	} else {
		$str = "�����ޱ䶯";
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
<title><?php echo $line["name"]; ?> - ���Ѽ�¼</title>
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
		<td class="l" width="120">�Ƿ����ƣ�</td>
		<td class="r">
			<input type="radio" name="is_zhiliao" value="1" <?php echo $line["is_zhiliao"] == 1 ? "checked" : ""; ?> id="is_zhiliao_1"><label for="is_zhiliao_1">������</label>
			<input type="radio" name="is_zhiliao" value="0" <?php echo $line["is_zhiliao"] == 0 ? "checked" : ""; ?> id="is_zhiliao_0" disabled="true"><label for="is_zhiliao_0">δ����</label>
		</td>
	</tr>-->
	<tr>
		<td class="l" width="120">�Ƿ����ѣ�</td>
		<td class="r">
			<input type="radio" name="is_xiaofei" value="1" <?php echo $line["is_xiaofei"] == 1 ? "checked" : ""; ?> id="is_xiaofei_1"><label for="is_xiaofei_1">������</label>
			<input type="radio" name="is_xiaofei" value="0" <?php echo $line["is_xiaofei"] == 0 ? "checked" : ""; ?> id="is_xiaofei_0" disabled="true"><label for="is_xiaofei_0">δ����</label>
		</td>
	</tr>
	<tr>
		<td class="l" width="120" valign="top">�������ѣ�</td>
		<td class="r">
			<input name="xiaofei" value="" class="input" style="width:100px;"> &nbsp;���ѱ�ע��<input name="xiaofei_str" value="" class="input" style="width:200px;">
		</td>
	</tr>
	<tr>
		<td class="l" width="120" valign="top">��ʷ�����ѣ�</td>
		<td class="r"><?php echo round($line["xiaofei_count"], 1); ?></td>
	</tr>
	<tr>
		<td class="l" width="120" valign="top">���Ѽ�¼��</td>
		<td class="r"><?php echo $line["xiaofei_log"] ? text_show($line["xiaofei_log"]) : '<font color=gray>(��)</font>'; ?></td>
	</tr>
	<tr>
		<td class="l" width="120" valign="top">��ӱ�ע��</td>
		<td class="r"><textarea name="xf_memo" style="width:75%; height:48px;" class="input"></textarea> (�����˿ɼ�)</td>
	</tr>
</table>

<div class="foot_button">
	<input type="submit" class="buttonb" value="�ύ����">
</div>

<input type="hidden" name="id" value="<?php echo $id; ?>">
</form>
</body>
</html>