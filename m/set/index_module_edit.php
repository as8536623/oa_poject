<?php
/*
//  功能说明 : 新增、修改
*/

if ($_POST) {
	$_POST["name"] = trim($_POST["name"]);
	$_POST["type"] = trim($_POST["type"]);
	$_POST["sum_condition"] = trim($_POST["sum_condition"]);

	if ($_POST["name"] == '' || $_POST["type"] == '' || $_POST["sum_condition"] == "") {
		exit("提交数据不完整，请返回重新填写！");
	}

	$sys_names_arr = array("总", "网络", "电话");
	if (in_array($_POST["name"], $sys_names_arr)) {
		exit("名称不能是“".implode("、", $sys_names_arr)."”中的任何一个。");
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
		echo '<script> parent.msg_box("资料提交成功", 2); </script>';
		echo '<script> parent.load_src(0); </script>';
	} else {
		exit_html("提交出错：".$error);
	}

	exit;

}


?>
<html>
<head>
<title>首页模块设置</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script language="javascript">
function Check() {
	var oForm = document.mainform;
	if (oForm.name.value == "") {
		alert("请输入“名称”！");
		oForm.name.focus();
		return false;
	}
	return true;
}
</script>
</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center">模块详情</td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"></div>
</div>
<!-- 头部 end -->

<div class="space"></div>
<form name="mainform" action="" method="POST" onsubmit="return Check()">
<table width="100%" class="edit">
	<tr>
		<td class="left">显示名称：</td>
		<td class="right"><input name="name" value="<?php echo $line["name"]; ?>" class="input" style="width:200px"> <span class="intro">名称必须填写</span></td>
	</tr>
	<tr>
		<td class="left">类型：</td>
		<td class="right">
			<select name="type" class="combo">
				<?php echo list_option($index_module_type_list, '_key_', '_value_', $line["type"]); ?>
			</select>&nbsp;
			<span class="intro">必须选择</span>
		</td>
	</tr>
	<tr>
		<td class="left">汇总方法：</td>
		<td class="right"><input name="sum_condition" value="<?php echo $line["sum_condition"]; ?>" class="input" style="width:400px"><br><font color="green">多个请用+连接，如“手机+微信”。修改后，首页数据更新需要10分钟时间。</font></td>
	</tr>
	<tr>
		<td class="left">优先度：</td>
		<td class="right"><input name="sort" value="<?php echo $line["sort"]; ?>" class="input" style="width:100px"> <span class="intro">越大越优先</span></td>
	</tr>
</table>
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="op" value="<?php echo $op; ?>">

<div class="button_line"><input type="submit" class="submit" value="提交资料"></div>
</form>
</body>
</html>