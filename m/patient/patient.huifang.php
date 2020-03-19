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
	$zlk = array(); //���µǼǱ�(�ط����ݣ��´λط�ʱ��)
	
	// �´λط�ʱ��:
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

	// �´λط����� �ͷ���
	if ($_POST["huifang"] != '' || $line["huifang_nexttime"] != intval(str_replace("-", "", $_POST["huifang_nexttime"])) ) {
		$r["huifang_kf"] = $realname;
	}
	
	//�������Ͽ���Ϣ
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


$huifang_time = $line["huifang_nexttime"] ? int_date_to_date($line["huifang_nexttime"]) : "";

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
		<td class="l">�ͷ�������</td>
		<td class="r"><?php echo $line["author"]; ?> @ <?php echo date("Y-m-d H:i", $line["addtime"]); ?> <?php echo $part_id_name[$line["part_id"]]; ?></td>
	</tr>
	<tr>
		<td class="l">��ѯ���ݣ�</td>
		<td class="r" colspan="3"><?php echo text_show(rtrim($line["content"])); ?></td>
	</tr>
	<tr>
		<td class="l">�������ͣ�</td>
		<td class="r"><?php echo $disease_id_name[$line["disease_id"]]; ?></td>
		<td class="l">ý����Դ��</td>
		<td class="r"><?php echo $line["media_from"]; ?></td>
	</tr>
	<tr>
		<td class="l">ר�Һţ�</td>
		<td class="r"><?php echo $line["zhuanjia_num"]; ?></td>
		<td class="l">ԤԼʱ�䣺</td>
		<td class="r"><?php echo @date("Y-m-d H:i", $line["order_date"]); ?></td>
	</tr>
	<tr>
		<td class="l">״̬��</td>
		<td class="r"><?php echo $status_array[$line["status"]]; ?></td>
		<td class="l">ҽ����</td>
		<td class="r">
<?php
if (in_array($uinfo["part_id"], array(2,3))) {
	echo "<font color='gray'>(����ʾ)</font>";
} else {
	if ($line["xianchang_doctor"] || $line["doctor"]) {
		echo $line["xianchang_doctor"] ? ("�ֳ�ҽ����".$line["xianchang_doctor"]."&nbsp;") : "";
		echo $line["doctor"] ? ("����ҽ����".$line["doctor"]) : "";
	} else {
		echo "<font color='gray'>(δ����)</font>";
	}
}
?>
		</td>
	</tr>
	<tr>
		<td class="l">��ע��</td>
		<td class="r" colspan="3"><?php echo text_show($line["memo"]); ?></td>
	</tr>
	<tr>
		<td colspan="4" class="h">�ط�</td>
	</tr>
	<tr>
		<td class="l" valign="top">���λطã�</td>
		<td class="r" colspan="3"><?php echo $line["huifang"] ? text_show(strip_tags($line["huifang"])) : "<font color=gray>(���޼�¼)</font>"; ?></td>
	</tr>
	<tr>
		<td class="l" valign="top">���λطã�</td>
		<td class="r" colspan="3"><textarea name="huifang" style="width:80%; height:60px;" class="input"></textarea> <span class="intro">�طü�¼</span></td>
	</tr>
	<tr>
		<td class="l" valign="top">�޸�ԤԼʱ�䣺</td>
		<td class="r" colspan="3">
<?php if ($line["status"] != 1) { ?>
			<input name="order_date" value="<?php echo date("Y-m-d H:i", $line["order_date"]); ?>" class="input" style="width:150px" id="order_date" readonly> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'order_date',dateFmt:'yyyy-MM-dd HH:mm'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��"> <span class="intro">���ޱ�Ҫ �벻Ҫ�޸�</span>
<?php } else { ?>
			<font color="red">�ѵ�Ժ�������޸�</font>
<?php } ?>
		</td>
	</tr>
	<tr>
		<td class="l" valign="top">�´λط����ѣ�</td>
		<td class="r" colspan="3"><input name="huifang_nexttime" value="<?php echo $huifang_time; ?>" class="input" style="width:150px" id="huifang_nexttime"> <img src="/res/img/calendar.gif" id="huifang_nexttime" onClick="picker({el:'huifang_nexttime',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��"> <?php if ($huifang_time && $line["huifang_kf"] != '') echo '&nbsp;��ʱ���ɡ�'.$line["huifang_kf"]."������&nbsp;"; ?> <span class="intro">�����´λطõ�����ʱ�䣬�޸�Ϊ��������</span></td>
	</tr>
	<tr>
		<td class="l" valign="top">��ӱ�ע��</td>
		<td class="r" colspan="3"><input name="memo" style="width:80%;" class="input"> <span class="intro">���һ����ע����</span></td>
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