<?php
/*
// 说明: config
// 作者: 幽兰 (weelia@126.com)
// 时间: 2012-05-07
*/
require "../../core/core.php";
$table = "count_config";

$sort_type_arr = array("1" => "竞价优先度", "2" => "咨询优先度", "3" => "名称");

$old = $db->query("select id,name,value,intro from $table", "name");

if ($_POST) {

	ob_start();
	foreach ($_POST["config"] as $_name => $_v) {
		if ($_name != '') {
			if (array_key_exists($_name, $old)) {
				$db->query("update $table set value='$_v' where name='$_name' limit 1");
			} else {
				$db->query("insert into $table set name='$_name', value='$_v'");
			}
		}
	}
	$error = ob_get_clean();

	if (empty($error)) {
		echo '<script>';
		echo 'parent.update_content();';
		echo 'parent.msg_box("修改成功", 2);';
		echo 'parent.load_src(0);';
		echo '</script>';
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
	<div class="d_item">选择排序方式，点击提交即可</div>
</div>

<div class="space"></div>
<form name="mainform" action="" method="POST" onsubmit="return check_data()">
<table width="100%" class="edit">
	<tr>
		<td class="left" style="width:20%">排序方式：</td>
		<td class="right" style="width:80%">
			<select name="config[排序方式_<?php echo $uid; ?>]" class="combo">
				<option value="" style="color:silver;">--请选择--</option>
				<?php echo list_option($sort_type_arr, "_key_", "_value_", $old["排序方式_".$uid]["value"]); ?>
			</select>
		</td>
	</tr>
</table>

<div class="button_line"><input id="submit_button" type="submit" class="submit" value="提交数据"></div>

</form>

</body>
</html>