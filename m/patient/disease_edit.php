<?php
/*
// - ����˵�� : ҽԺ�������޸�
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2009-05-01 00:40
*/

if ($_POST) {
	$r = array();
	$r["name"] = trim($_POST["name"]);

	$d2 = array();
	if ($_POST["disease_2"] != '') {
		$_POST["disease_2"] = str_replace("\r", "", $_POST["disease_2"]);
		$s = explode("\n", $_POST["disease_2"]);
		foreach ($s as $k) {
			if (trim($k) != '') {
				$d2[] = trim($k);
			}
		}
	}
	$r["disease_2"] = implode(",", $d2);

	$s2 = array();
	if ($_POST["xiangmu"] != '') {
		$_POST["xiangmu"] = str_replace("\r", "", $_POST["xiangmu"]);
		$s = explode("\n", $_POST["xiangmu"]);
		foreach ($s as $k) {
			if (trim($k) != '') {
				$s2[] = trim($k);
			}
		}
	}
	$r["xiangmu"] = implode(",", $s2);

	$r["intro"] = $_POST["intro"];
	$r["sort"] = $_POST["sort"];


	if ($op == "add") {
		$r["hospital_id"] = $hid;
		$r["addtime"] = time();
		$r["author"] = $username;
	}

	$sqldata = $db->sqljoin($r);
	if ($op == "add") {
		$sql = "insert into $table set $sqldata";
	} else {
		$sql = "update $table set $sqldata where id='$id' limit 1";
	}

	ob_start();
	$db->query($sql);
	$error = ob_get_clean();

	if (empty($error)) {
		if ($op == "add") {
			echo '<script> parent.update_content(); </script>';
		}
		echo '<script> parent.msg_box("�����ύ�ɹ�", 2); </script>';
		echo '<script> parent.load_src(0); </script>';
	} else {
		exit_html("�ύ����".$error);
	}

	exit;
}

$title = $op == "edit" ? "�޸�" : "���";

$hospital_list = $db->query("select id,name from hospital");
?>
<html>
<head>
<title>�������͹���</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script language="javascript">
function check() {
	var oForm = document.mainform;
	if (oForm.name.value == "") {
		alert("�����롰�������ơ���"); oForm.name.focus(); return false;
	}
	return true;
}
</script>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $title; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"></div>
</div>
<!-- ͷ�� end -->


<div class="space"></div>

<form name="mainform" action="" method="POST" onsubmit="return check()">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">��������</td>
	</tr>
	<tr>
		<td class="left">�������ƣ�</td>
		<td class="right"><input name="name" value="<?php echo $line["name"]; ?>" class="input" size="30" style="width:200px"> <span class="intro">���Ʊ�����д</span></td>
	</tr>
	<tr>
		<td class="left">����������</td>
		<td class="right"><textarea name="disease_2" class="input" style="width:60%; height:80px; overflow:visible; vertical-align:middle;"><?php echo str_replace(",", "\r\n", $line["disease_2"]); ?></textarea> <span class="intro">ÿ��һ��</span></td>
	</tr>
	<tr>
		<td class="left">������Ŀ��</td>
		<td class="right"><textarea name="xiangmu" class="input" style="width:60%; height:50px; overflow:visible; vertical-align:middle;"><?php echo str_replace(",", "\r\n", $line["xiangmu"]); ?></textarea> <span class="intro">ÿ��һ��</span></td>
	</tr>
	<tr>
		<td class="left">������飺</td>
		<td class="right"><textarea name="intro"class="input"  style="width:60%; height:50px; overflow:visible;"><?php echo $line["intro"]; ?></textarea></td>
	</tr>
	<tr>
		<td class="left">���ȶȣ�</td>
		<td class="right"><input name="sort" value="<?php echo $line["sort"]; ?>" class="input" size="30" style="width:100px"> <span class="intro">Խ��Խ���ȣ���ֵ�����</span></td>
	</tr>
</table>
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="op" value="<?php echo $op; ?>">

<div class="button_line"><input type="submit" class="submit" value="�ύ����"></div>
</form>
</body>
</html>