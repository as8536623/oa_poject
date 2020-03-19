<?php
/*
// 说明: config
// 作者: 幽兰 (weelia@126.com)
// 时间: 2012-05-07
*/
require "../../core/core.php";
$table = "count_config";

$old = $db->query("select id,name,value,intro from $table", "name");

if ($_POST) {

	ob_start();
	foreach ($_POST["config"] as $_id => $_v) {
		$_id = intval($_id);
		if ($_id > 0) {
			$db->query("update $table set value='$_v' where id=$_id limit 1");
		}
	}
	$error = ob_get_clean();

	if (empty($error)) {
		//echo '<script> parent.update_content(); </script>';
		echo '<script> parent.msg_box("修改成功", 2); </script>';
		echo '<script> parent.load_src(0); </script>';
	} else {
		echo "提交失败，请稍后再试！";
	}
	exit;
}


?>
<html>
<head>
<title>修改配置</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script type="text/javascript">
function check_data() {
	return true;
}
</script>
</head>

<body>
<div class="description">
	<div class="d_title">提示：</div>
	<div class="d_item">按要求输入各项资料，点击提交即可</div>
</div>

<div class="space"></div>
<form name="mainform" action="" method="POST" onsubmit="return check_data()">
<table width="100%" class="edit">
	<tr>
		<td class="left" style="width:20%">允许修改天数：</td>
		<td class="right" style="width:80%">
			<input name="config[<?php echo $old["允许修改天数"]["id"]; ?>]" value="<?php echo $old["允许修改天数"]["value"]; ?>" class="input" style="width:100px"> <?php echo $old["允许修改天数"]["intro"]; ?>
		</td>
	</tr>
</table>

<div class="button_line"><input id="submit_button" type="submit" class="submit" value="提交数据"></div>

</form>

</body>
</html>