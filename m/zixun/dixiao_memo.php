<?php
/*
// ˵��:
// ����: ���� (weelia@126.com)
// ʱ��:
*/
require "../../core/core.php";
$table = "count_memo";

$_GET["kefu"] = convert($_GET["kefu"], "UTF-8", "gbk");

$type_id = intval($_SESSION["count_type_id_web"]);
if ($type_id == 0) {
	exit("����ѡ����Ŀ...");
}

$op = $_REQUEST["op"];
if ($op == "add_yuanyin") {
	$tips = "���ԭ�����";
	$field = "yuanyin";
} else if ($op == "add_fangan") {
	$tips = "��ӽ������";
	$field = "fangan";
} else {
	exit("��������...");
}


if ($_POST) {
	$int_month = intval($_POST["month"]);
	$int_week = intval($_POST["week"]);
	$kefu = $_POST["kefu"];

	ob_start();

	$old = $db->query("select * from count_memo where type_id='$type_id' and month='$int_month' and week='$int_week' and kefu='$kefu' limit 1", 1);

	$save_data = date("Y-m-d ").$realname.": ".trim(strip_tags($_POST["content"]) );
	if (($old_id = $old["id"]) > 0) {
		$content = trim($old[$field]."\r\n".$save_data);
		$db->query("update count_memo set $field='$content' where id='$old_id' limit 1");
	} else {
		$db->query("insert into count_memo set type_id='$type_id', month='$int_month', week='$int_week', kefu='$kefu', $field='$save_data'");
	}

	$error = ob_get_clean();
	if ($error == '') {
		echo '<script> parent.update_content(); </script>';
		echo '<script> parent.msg_box("����ɹ�", 2); </script>';
		echo '<script> parent.load_src(0); </script>';
	} else {
		echo $error;
	}
	exit;
}


?>
<html>
<head>
<title><?php echo $tips." - ".$_GET["kefu"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script type="text/javascript">
function data_check(form) {
	if (form.content.value == '') {
		alert("���������ݺ����ύ��");
		return false;
	}
	return confirm("�ύ��Ͳ������޸��ˣ�ȷ����");
}
</script>
</head>

<body>

<form method="POST" action="" onsubmit="return data_check(this)">
	<div id="tips"><?php echo $tips; ?>��</div>
	<textarea name="content" id="content" class="input" style="width:80%; height:60px; margin-top:5px;"></textarea><br>
	<input type="submit" class="button" value="�ύ" style="margin-top:10px;">
	<input type="hidden" name="month" value="<?php echo $_GET["month"]; ?>">
	<input type="hidden" name="week" value="<?php echo $_GET["week"]; ?>">
	<input type="hidden" name="kefu" value="<?php echo $_GET["kefu"]; ?>">
	<input type="hidden" name="op" value="<?php echo $_GET["op"]; ?>">
</form>


</body>
</html>