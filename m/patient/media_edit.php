<?php
/*
// - 功能说明 : 媒体类型新增、修改
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2009-05-03 14:48
*/

if ($_POST) {
	$r = array();
	$r["name"] = $_POST["name"];

	if ($r["name"] == "网络" || $r["name"] == "电话") {
		exit("“网络”和“电话”为系统内置媒体来源，不需要在此添加。");
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
		echo '<script> parent.msg_box("资料提交成功", 2); </script>';
		echo '<script> parent.load_src(0); </script>';
	} else {
		exit_html("提交出错：".$error);
	}

	exit;
}

$title = ($op == "edit") ? "修改" : "添加";

$_hid = ($mode == "edit") ? $line["hospital_id"] : intval($_GET["hid"]);

// 下拉选项:
$shid = $_hid ? $_hid : $hid;
$h_name = $db->query("select name from hospital where id=$shid limit 1", 1, "name");

$media_type_arr = array("0" => "全局（每家医院都有）", $shid => "“".$h_name."”独有");

?>
<html>
<head>
<title>媒体类型管理</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script language="javascript">
function check() {
	var oForm = document.mainform;
	if (oForm.name.value == "") {
		alert("请输入“名称”！"); oForm.name.focus(); return false;
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
		<td colspan="2" class="head">媒体类型资料</td>
	</tr>
	<tr>
		<td class="left">类型：</td>
		<td class="right">
			<select name="media_type" class="combo">
				<?php echo list_option($media_type_arr, "_key_", "_value_", ($op == "edit") ? $line["hospital_id"] : $_GET["hid"]); ?>
			</select>
			<span class="intro">媒体类型必须填写</span>
		</td>
	</tr>
	<tr>
		<td class="left">名称：</td>
		<td class="right"><input name="name" value="<?php echo $line["name"]; ?>" class="input" size="30" style="width:200px"> <span class="intro">名称必须填写</span></td>
	</tr>
	<tr>
		<td class="left">排序：</td>
		<td class="right"><input name="sort" value="<?php echo $line["sort"]; ?>" class="input" size="30" style="width:200px"> <span class="intro">越大越靠前；可以为负值，排最后</span></td>
	</tr>
</table>
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="op" value="<?php echo $op; ?>">

<div class="button_line"><input type="submit" class="submit" value="提交资料"></div>
</form>
</body>
</html>