<?php
/*
// 说明: 设置目标
// 作者: 幽兰 (weelia@126.com)
// 时间: 2013-10-22
*/
require "../../core/core.php";

$month = intval($_REQUEST["month"]);
$field = $_REQUEST["field"];


$old = $db->query("select * from count_mubiao where hid='$hid' and month='$month' limit 1", 1);
if ($old["id"] > 0) {
	$mode = "edit";
} else {
	$mode = "add";
}

$data = @unserialize($old["config"]);

$old_data = $data[$field];

if ($_POST) {
	$d = $_REQUEST["data"];

	// 判断是否已经添加
	$data[$field] = $d;
	$str = serialize($data);

	if ($mode == "add") {
		$db->query("insert into count_mubiao set hid=$hid, month='$month', config='$str', author='$realname'");
	} else {
		$db->query("update count_mubiao set config='$str' where id=".$old["id"]." limit 1");
	}


	//echo '<script> parent.update_content(); </script>';
	$up_id = "mubiao_".$field;
	echo '<script> parent.update_content_byid("'.$up_id.'", "'.$d.'", "innerHTML"); </script>';
	echo '<script> parent.msg_box("目标设置成功", 2); </script>';
	echo '<script> parent.load_src(0); </script>';

	exit;
}


?>
<html>
<head>
<title>修改数据 - <?php echo $field; ?></title>
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
		<td colspan="2" class="head">请输入目标</td>
	</tr>

	<tr>
		<td class="left">目标：</td>
		<td class="right">
			<input name="data" value="<?php echo $old_data; ?>" class="input" style="width:100px">
		</td>
	</tr>
</table>

<input type="hidden" name="month" value="<?php echo $month; ?>">
<input type="hidden" name="field" value="<?php echo $field; ?>">
<div class="button_line">
	<input id="submit_button" type="submit" class="submit" value="提交数据">
</div>

</form>

</body>
</html>