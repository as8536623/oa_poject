<?php
defined("ROOT") or exit;

if ($_POST) {
	$r = array();
	$r["name"] = $_POST["name"];
	$r["intro"] = $_POST["intro"];
	$r["sort"] = $_POST["sort"];

	$r["area"] = $_POST["area"];
	$r["sname"] = $_POST["sname"];
	$r["depart"] = $_POST["depart"];

	$r["full_name"] = $r["area"].$r["sname"].$r["depart"];

	$r["swt_ids"] = $_POST["swt_ids"];
	$r["set_huifang_kf"] = $_POST["set_huifang_kf"] ? 1 : 0;

	// �������Ѿ����أ����壩��������ֵ���ᶪʧ
	$line["config"]["�����շ���Ŀ"] = str_replace("\n", "|", str_replace("\r", "", $_POST["menzhen_fei"]));
	$line["config"]["סԺ�շ���Ŀ"] = str_replace("\n", "|", str_replace("\r", "", $_POST["zhuyuan_fei"]));

	$r["config"] = serialize($line["config"]);

	if ($op == "add") {
		$r["addtime"] = time();
		$r["author"] = $username;
	}

	$sqldata = $db->sqljoin($r);
	if ($op == "edit") {
		$sql = "update $table set $sqldata where id='$id' limit 1";
	} else {
		$sql = "insert into $table set $sqldata";
	}

	if ($hid = $db->query($sql)) {
		$hid = ($op == "edit" ? $id : $hid);
		create_patient_table($hid);

		// �������ڵĴ���ʽ:
		if ($mode == "add") {
			echo '<script> parent.update_content(); </script>';
		}
		echo '<script> parent.msg_box("�����ύ�ɹ�", 2); </script>';
		echo '<script> parent.load_src(0); </script>';
	} else {
		echo "�ύʧ�ܣ����Ժ����ԣ�";
	}
	exit;
}

?>
<html>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script language="javascript">
function check_data(oForm) {
	if (oForm.name.value == "") {
		alert("�����롰ҽԺ���ơ���");
		oForm.name.focus();
		return false;
	}
	return true;
}
</script>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $pinfo["title"]." - ".($op == "add" ? "����" : "�޸�"); ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>
<form method="POST" onsubmit="return check_data(this)">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">ҽԺ����</td>
	</tr>
	<tr>
		<td class="left">��Ŀ���ƣ�</td>
		<td class="right"><input name="name" value="<?php echo $line["name"]; ?>" class="input" size="30" style="width:200px"> <span class="intro">���Ʊ�����д</span></td>
	</tr>
	<tr>
		<td class="left">���ڵ�����</td>
		<td class="right">
			<input name="area" value="<?php echo $line["area"]; ?>" class="input" style="width:60px">
			ҽԺ����<input name="sname" value="<?php echo $line["sname"]; ?>" class="input" style="width:150px">
			��������<input name="depart" value="<?php echo $line["depart"]; ?>" class="input" style="width:80px">
		</td>
	</tr>
	<tr>
		<td class="left">ҽԺ��飺</td>
		<td class="right"><textarea class="input" name="intro" style="width:60%; height:80px; vertical-align:middle;"><?php echo $line["intro"]; ?></textarea> <span class="intro">ҽԺ��飬ѡ��</span></td>
	</tr>
	<tr>
		<td class="left">����ͨID��</td>
		<td class="right"><input name="swt_ids" value="<?php echo $line["swt_ids"]; ?>" class="input" style="width:200px"> <span class="intro">����������ͨ�ʺ�ID</span></td>
	</tr>
	<tr>
		<td class="left">�ط��趨��</td>
		<td class="right"><input type="checkbox" name="set_huifang_kf" value="1" <?php if ($line["set_huifang_kf"]) echo "checked"; ?> id="chk_set_huifang_kf"><label for="chk_set_huifang_kf">��ѡ����ҽԺ�ط���<b>�ط�����</b>ָ���ͷ�������ѡ������ָ�����طÿͷ������ɻطã�</label></td>
	</tr>
	<tr>
		<td class="left">���ȶȣ�</td>
		<td class="right"><input name="sort" value="<?php echo $line["sort"]; ?>" class="input" style="width:80px"> <span class="intro">���ȶ�Խ��,����Խ��ǰ</span></td>
	</tr>
</table>

<div class="space"></div>

<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">ҽԺ����</td>
	</tr>
	<tr>
		<td class="left">�����շ���Ŀ��</td>
		<td class="right"><textarea class="input" name="menzhen_fei" style="width:200px; height:70px; vertical-align:middle;"><?php echo str_replace("|", "\r\n", $line["config"]["�����շ���Ŀ"]); ?></textarea> <span class="intro">�����շ���Ŀ��ÿ��һ��</span></td>
	</tr>
	<tr>
		<td class="left">סԺ�շ���Ŀ��</td>
		<td class="right"><textarea class="input" name="zhuyuan_fei" style="width:200px; height:70px; vertical-align:middle;"><?php echo str_replace("|", "\r\n", $line["config"]["סԺ�շ���Ŀ"]); ?></textarea> <span class="intro">סԺ�շ���Ŀ��ÿ��һ��</span></td>
	</tr>
</table>

<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="op" value="<?php echo $op; ?>">
<input type="hidden" name="pre_page" value="<?php echo $_SERVER["HTTP_REFERER"]; ?>">

<div class="button_line"><input type="submit" class="submit" value="�ύ����"></div>
</form>
</body>
</html>