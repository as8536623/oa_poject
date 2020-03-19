<?php
/*
// 说明:
// 作者: 幽兰 (weelia@126.com)
// 时间:
*/
require "../../core/core.php";
$table = "count_memo";

$_GET["kefu"] = convert($_GET["kefu"], "UTF-8", "gbk");

$type_id = intval($_SESSION["count_type_id_web"]);
if ($type_id == 0) {
	exit("请先选择项目...");
}

$op = $_REQUEST["op"];
if ($op == "add_yuanyin") {
	$tips = "添加原因分析";
	$field = "yuanyin";
} else if ($op == "add_fangan") {
	$tips = "添加解决方案";
	$field = "fangan";
} else {
	exit("参数错误...");
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
		echo '<script> parent.msg_box("保存成功", 2); </script>';
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
		alert("请输入内容后再提交！");
		return false;
	}
	return confirm("提交后就不能再修改了，确认吗？");
}
</script>
</head>

<body>

<form method="POST" action="" onsubmit="return data_check(this)">
	<div id="tips"><?php echo $tips; ?>：</div>
	<textarea name="content" id="content" class="input" style="width:80%; height:60px; margin-top:5px;"></textarea><br>
	<input type="submit" class="button" value="提交" style="margin-top:10px;">
	<input type="hidden" name="month" value="<?php echo $_GET["month"]; ?>">
	<input type="hidden" name="week" value="<?php echo $_GET["week"]; ?>">
	<input type="hidden" name="kefu" value="<?php echo $_GET["kefu"]; ?>">
	<input type="hidden" name="op" value="<?php echo $_GET["op"]; ?>">
</form>


</body>
</html>