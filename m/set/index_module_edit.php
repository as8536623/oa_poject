<?php
/*
//  ����˵�� : �������޸�
*/

if ($_POST) {
	$_POST["name"] = trim($_POST["name"]);
	$_POST["type"] = trim($_POST["type"]);
	$_POST["sum_condition"] = trim($_POST["sum_condition"]);

	if ($_POST["name"] == '' || $_POST["type"] == '' || $_POST["sum_condition"] == "") {
		exit("�ύ���ݲ��������뷵��������д��");
	}

	$sys_names_arr = array("��", "����", "�绰");
	if (in_array($_POST["name"], $sys_names_arr)) {
		exit("���Ʋ����ǡ�".implode("��", $sys_names_arr)."���е��κ�һ����");
	}

	$r = array();
	$r["name"] = $_POST["name"];
	$r["type"] = $_POST["type"];
	$r["sum_condition"] = $_POST["sum_condition"];
	$r["sort"] = intval($_POST["sort"]);

	if ($op == "add") {
		$r["author"] = $username;
		$r["addtime"] = time();
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


?>
<html>
<head>
<title>��ҳģ������</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script language="javascript">
function Check() {
	var oForm = document.mainform;
	if (oForm.name.value == "") {
		alert("�����롰���ơ���");
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
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center">ģ������</td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>
<form name="mainform" action="" method="POST" onsubmit="return Check()">
<table width="100%" class="edit">
	<tr>
		<td class="left">��ʾ���ƣ�</td>
		<td class="right"><input name="name" value="<?php echo $line["name"]; ?>" class="input" style="width:200px"> <span class="intro">���Ʊ�����д</span></td>
	</tr>
	<tr>
		<td class="left">���ͣ�</td>
		<td class="right">
			<select name="type" class="combo">
				<?php echo list_option($index_module_type_list, '_key_', '_value_', $line["type"]); ?>
			</select>&nbsp;
			<span class="intro">����ѡ��</span>
		</td>
	</tr>
	<tr>
		<td class="left">���ܷ�����</td>
		<td class="right"><input name="sum_condition" value="<?php echo $line["sum_condition"]; ?>" class="input" style="width:400px"><br><font color="green">�������+���ӣ��硰�ֻ�+΢�š����޸ĺ���ҳ���ݸ�����Ҫ10����ʱ�䡣</font></td>
	</tr>
	<tr>
		<td class="left">���ȶȣ�</td>
		<td class="right"><input name="sort" value="<?php echo $line["sort"]; ?>" class="input" style="width:100px"> <span class="intro">Խ��Խ����</span></td>
	</tr>
</table>
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="op" value="<?php echo $op; ?>">

<div class="button_line"><input type="submit" class="submit" value="�ύ����"></div>
</form>
</body>
</html>