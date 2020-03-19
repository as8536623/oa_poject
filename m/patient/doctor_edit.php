<?php
/*
// - 功能说明 : 医生新增、修改
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2009-05-02 16:44
*/

if ($_POST) {
	$r = array();
	$r["doctor_num"] = $_POST["doctor_num"];
	$r["name"] = $_POST["name"];
	$r["intro"] = $_POST["intro"];

	if ($op == "add") {
		$r["hospital_id"] = $hid;
		$r["addtime"] = time();
		$r["author"] = $username;
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
		if ($op == "add") {
			echo '<script> parent.update_content(); </script>';
		}
		echo '<script> parent.msg_box("资料提交成功", 2); </script>';
		echo '<script> parent.load_src(0); </script>';
	} else {
		exit_html("提交出错：".$error);
	}

	exit;
}

$title = $editmode ? "修改" : "添加";

$hospital_list = $db->query("select id,name from hospital");
?>
<html>
<head>
<title>医生管理</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script language="javascript">
function check() {
	var oForm = document.mainform;
	if (oForm.name.value == "") {
		alert("请输入“医生名字”！"); oForm.name.focus(); return false;
	}
	return true;
}
</script>
</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $title; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"></div>
</div>
<!-- 头部 end -->

<div class="space"></div>
<form name="mainform" action="" method="POST" onsubmit="return check()">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">医生资料</td>
	</tr>
	<tr>
		<td class="left">医生名字：</td>
		<td class="right"><input name="name" value="<?php echo $line["name"]; ?>" class="input" size="30" style="width:200px"> <span class="intro">* 医生名字必须填写</span></td>
	</tr>
	<tr>
		<td class="left">医生编号：</td>
		<td class="right"><input name="doctor_num" value="<?php echo $line["doctor_num"]; ?>" class="input" size="30" style="width:200px"> <span class="intro">选填</span></td>
	</tr>
	<tr>
		<td class="left">医生简介：</td>
		<td class="right"><textarea name="intro" class="input" style="width:60%; height:80px; overflow:visible;"><?php echo $line["intro"]; ?></textarea> <span class="intro">选填</span></td>
	</tr>
</table>
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="op" value="<?php echo $op; ?>">

<div class="button_line"><input type="submit" class="submit" value="提交资料"></div>
</form>
</body>
</html>