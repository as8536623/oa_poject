<?php
// --------------------------------------------------------
// - ����˵�� : �ط�
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2013-7-12
// --------------------------------------------------------
require "../../core/core.php";
$table = "ku_list";

$id = intval($_REQUEST["id"]);
if (!$id) {
	exit("��������.");
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
		
		//����ԤԼ��(�ط����ݣ��´λط�ʱ��)
		if ($line["is_yuyue"]) {
			$patient_hid = "patient_".$line["hid"];
			$yyb_sql = "update $patient_hid set huifang='$hf_log' ,huifang_nexttime = '$yyb_huifang_time' where lid='$id' limit 1";
			$db->query($yyb_sql);
		}
		
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


// page begin ----------------------------------------------------
?>
<html>
<head>
<title>�ط�</title>
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
		if (!confirm("����û������ط����ݣ�ȷ��Ҫ�ύ��")) {
			oForm.huifang.focus();
			return false;
		}
	}
	return true;
}
//�ط�ʱ����
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
		<td colspan="4" class="h">��������</td>
	</tr>
	<tr>
		<td class="l">������</td>
		<td class="r"><b><?php echo $line["name"]; ?></b></td>
		<td class="l">����ҽԺ��</td>
		<td class="r"><?php echo $line["h_name"]; ?></td>
	</tr>
	<tr>
		<td class="l">��ѯ���ݣ�</td>
		<td class="r" colspan="3"><?php echo text_show(rtrim($line["zx_content"])); ?></td>
	</tr>
	<tr>
		<td class="l" width="15%">�Ա�</td>
		<td class="r" width="30%"><?php echo $line["sex"]; ?></td>
		<td class="l" width="15%">���䣺</td>
		<td class="r" width="40%"><?php echo $line["age"] > 0 ? $line["age"] : ""; ?></td>
	</tr>
	<tr>
		<td class="l">�ֻ���</td>
		<td class="r"><?php echo $line["mobile"]; ?></td>
		<td class="l">������Դ��</td>
		<td class="r"><?php echo $line["laiyuan"]; ?></td>
	</tr>
	<tr>
		<td class="l">����QQ��</td>
		<td class="r"><?php echo $line["qq"]; ?></td>
		<td class="l">����΢�ţ�</td>
		<td class="r"><?php echo $line["weixin"]; ?></td>
	</tr>
	<tr>
		<td colspan="4" class="h">�ط�</td>
	</tr>
	<tr>
		<td class="l" valign="top">�����طã�</td>
		<td class="r" colspan="3"><?php echo $line["hf_log"] ? text_show($line["hf_log"]) : "<font color=gray>(�޼�¼)</font>"; ?></td>
	</tr>
	<tr>
		<td class="l" valign="top">���λطã�</td>
		<td class="r" colspan="3"><textarea name="huifang" style="width:80%; height:60px;" class="input"></textarea></td>
	</tr>
	<tr>
		<td class="l" valign="top">�´λط����ѣ�</td>
		<td class="r" colspan="3"><input name="huifang_nexttime" value="<?php echo $huifang_nexttime; ?>" class="input" style="width:150px" id="huifang_nexttime"> <img src="/res/img/calendar.gif" id="huifang_nexttime" onClick="picker({el:'huifang_nexttime',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��">
        <?php
		$show_days = array(
		
			"��" => $today = date("Y-m-d"), //����
			"��" => date("Y-m-d", strtotime("+1 day")), //����
			"��" => date("Y-m-d", strtotime("+2 days")), //����
			"7���" => date("Y-m-d", strtotime("+7 days")), 
			"15���" => date("Y-m-d", strtotime("+15 days")), 
			"1���º�" => date("Y-m-d", strtotime("next Month")), 
		);
		echo '<div style="padding-top:6px;">����: ';
		foreach ($show_days as $name => $value) {
			echo '<a href="javascript:input_date(\'huifang_nexttime\', \''.$value.'\')">['.$name.']</a>&nbsp;';
		}

?>
        </td>
	</tr>
</table>

<div class="button_line">
	<input type="submit" class="buttonb" value="�ύ����">
</div>

<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
</form>

</body>
</html>