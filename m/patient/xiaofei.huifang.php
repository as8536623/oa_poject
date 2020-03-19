<?php
// --------------------------------------------------------
// - ����˵�� : ���õ�Ժ
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2011-09-14
// --------------------------------------------------------
$status_array = array(0 => '�ȴ�', 1 => '�ѵ�', 2 => 'δ��');

if (!$id) {
	exit("��������.");
}

if ($_POST) {
	$r = array();
	
	// �´λط�ʱ��:
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
			echo "�ύ��������ϵ������Ա������<br>".$error;
			exit;
		}
		if ($rs) {
			$str = "�����ύ�ɹ���";
		} else {
			echo "�ύ��������ϵ������Ա������<br>".$db->sql;
			exit;
		}
	} else {
		$str = "�����ޱ䶯";
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
<title><?php echo $line["name"]; ?> - �ط�</title>
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
		if (!confirm("����û������ط����ݣ�ȷ��Ҫ�ύ��")) {
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
		<td colspan="4" class="h">��������</td>
	</tr>
	<tr>
		<td class="l">������</td>
		<td class="r" colspan="3"><b><?php echo $line["name"]; ?></b></td>
	</tr>
	<tr>
		<td class="l" width="15%">�Ա�</td>
		<td class="r" width="30%"><?php echo $line["sex"]; ?></td>
		<td class="l" width="15%">���䣺</td>
		<td class="r" width="40%"><?php echo $line["age"] > 0 ? $line["age"] : ""; ?></td>
	</tr>
	<tr>
		<td class="l">�绰��</td><!-- �ط���Ҫ��ʾ���� ���ﲻ�����Ƿ���������ʾ���빦�� ʼ����ʾ �����޷��ط� -->
		<td class="r"><?php echo $line["tel"]; ?></td>
		<td class="l">ҽ����</td>
		<td class="r"><?php  echo $line["doctor"];?></td>
	</tr>
    <tr>
		<td class="l">����ţ�</td>
		<td class="r"><?php echo $line["zhuanjia_num"]; ?></td>
		<td class="l">��Ժʱ�䣺</td>
		<td class="r"><?php echo @date("Y-m-d H:i", $line["order_date"]); ?></td>
	</tr>
	<tr>
		<td class="l">�������ͣ�</td>
		<td class="r"><?php echo $disease_id_name[$line["disease_id"]]; ?></td>
		<td class="l">ý����Դ��</td>
		<td class="r"><?php echo $line["media_from"]; ?></td>
	</tr>
	<tr>
		<td class="l">��ע��</td>
		<td class="r" colspan="3"><?php echo text_show($line["xf_memo"]); ?></td>
	</tr>
	<tr>
		<td colspan="4" class="h">�ط�</td>
	</tr>
	<tr>
		<td class="l" valign="top">���λطã�</td>
		<td class="r" colspan="3"><?php echo $line["xf_huifang"] ? text_show(strip_tags($line["xf_huifang"])) : "<font color=gray>(���޼�¼)</font>"; ?></td>
	</tr>
	<tr>
		<td class="l" valign="top">���λطã�</td>
		<td class="r" colspan="3"><textarea name="xf_huifang" style="width:80%; height:60px;" class="input"></textarea> <span class="intro">�طü�¼</span></td>
	</tr>
	<tr>
		<td class="l" valign="top">�´λط����ѣ�</td>
		<td class="r" colspan="3"><input name="xf_huifang_nexttime" value="<?php echo $xf_huifang_time; ?>" class="input" style="width:150px" id="xf_huifang_nexttime"> <img src="/res/img/calendar.gif" id="xf_huifang_nexttime" onClick="picker({el:'xf_huifang_nexttime',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��">  <span class="intro">�����´λطõ�����ʱ�䣬�޸�Ϊ��������</span></td>
	</tr>
	<tr>
		<td class="l" valign="top">��ӱ�ע��</td>
		<td class="r" colspan="3"><input name="xf_memo" style="width:80%;" class="input"> <span class="intro">���һ����ע����</span></td>
	</tr>
</table>

<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="crc" value="<?php echo $_GET["crc"]; ?>">

<div class="button_line">
	<input type="submit" class="buttonb" value="�ύ����">
</div>

</form>
</body>
</html>