<?php
/*
// - ����˵�� : ý�������������޸�
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2009-05-03 14:48
*/

if ($_POST) {
	$r = array();
	$r["name"] = $_POST["name"];

	if ($r["name"] == "����" || $r["name"] == "�绰") {
		exit("�����硱�͡��绰��Ϊϵͳ����ý����Դ������Ҫ�ڴ���ӡ�");
	}

	$r["hospital_id"] = intval($_POST["media_type"]);

	$r["sort"] = intval($_POST["sort"]);

	if ($op == "add") {
		$r["addtime"] = time();
		$r["author"] = $realname;
	}

	$sqldata = $db->sqljoin($r);
	if ($op == "edit") {
		$sql = "update $table set $sqldata where id='$id' limit 1";
	} else {
		$sql = "insert into $table set $sqldata";
	}

	ob_start();
	$db->query($sql);
	$error = ob_get_clean();

	if (empty($error)) {
		echo '<script> parent.update_content(); </script>';
		echo '<script> parent.msg_box("�����ύ�ɹ�", 2); </script>';
		echo '<script> parent.load_src(0); </script>';
	} else {
		exit_html("�ύ����".$error);
	}

	exit;
}

$title = ($op == "edit") ? "�޸�" : "���";

$_hid = ($mode == "edit") ? $line["hospital_id"] : intval($_GET["hid"]);

// ����ѡ��:
$shid = $_hid ? $_hid : $hid;
$h_name = $db->query("select name from hospital where id=$shid limit 1", 1, "name");

$media_type_arr = array("0" => "ȫ�֣�ÿ��ҽԺ���У�", $shid => "��".$h_name."������");

?>
<html>
<head>
<title>ý�����͹���</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script language="javascript">
function check() {
	var oForm = document.mainform;
	if (oForm.name.value == "") {
		alert("�����롰���ơ���"); oForm.name.focus(); return false;
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
		<td colspan="2" class="head">ý����������</td>
	</tr>
	<tr>
		<td class="left">���ͣ�</td>
		<td class="right">
			<select name="media_type" class="combo">
				<?php echo list_option($media_type_arr, "_key_", "_value_", ($op == "edit") ? $line["hospital_id"] : $_GET["hid"]); ?>
			</select>
			<span class="intro">ý�����ͱ�����д</span>
		</td>
	</tr>
	<tr>
		<td class="left">���ƣ�</td>
		<td class="right"><input name="name" value="<?php echo $line["name"]; ?>" class="input" size="30" style="width:200px"> <span class="intro">���Ʊ�����д</span></td>
	</tr>
	<tr>
		<td class="left">����</td>
		<td class="right"><input name="sort" value="<?php echo $line["sort"]; ?>" class="input" size="30" style="width:200px"> <span class="intro">Խ��Խ��ǰ������Ϊ��ֵ�������</span></td>
	</tr>
</table>
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="op" value="<?php echo $op; ?>">

<div class="button_line"><input type="submit" class="submit" value="�ύ����"></div>
</form>
</body>
</html>