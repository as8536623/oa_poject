<?php
/* --------------------------------------------------------
// 说明:
// 作者: 幽兰 (weelia@126.com)
// 时间:
// ----------------------------------------------------- */
$table = "jingjia_field_set";
require "../../core/core.php";
$max_field_num = 10;

if ($debug_mode || $username == "admin" || $uinfo["part_id"] == 9) {
	// 允许修改
} else {
	exit_html("对不起，您没有操作权限！");
}

// 读取当前列表
$f_info = $db->query("select fieldname, name, sub_name from $table order by fieldname", "fieldname");
$x = $sub_name = array();
foreach ($f_info as $k => $v) {
	$x[$k] = $v["name"];
	$sub_name[$k] = $v["sub_name"];
}


if ($_POST) {

	for ($i = 1; $i <= $max_field_num; $i++) {
		if (empty($x["x".$i]) && $_POST["x"][$i] != '') {
			if (array_key_exists("x".$i, $x)) {
				// 不能进入修改模式 (需要的话直接操作数据库吧，牵涉的问题很多)
				//$db->query("update $table set name='".$_POST["x"][$i]."' where fieldname='x".$i."' limit 1");
			} else {
				$db->query("insert into $table set fieldname='x".$i."', name='".$_POST["x"][$i]."', addtime=".time().", author='".$realname."'");
			}
		}
	}

	// 保存副标题
	for ($i = 1; $i <= $max_field_num; $i++) {
		if ($x["x".$i] != '') {
			$sub = $_POST["sub_name"][$i];
			$db->query("update $table set sub_name='$sub' where fieldname='x".$i."' limit 1");
		}
	}

	msg_box("保存成功", "jingjia_set.php", 1, 3);
}


$can_edit = array();
for ($i = 1; $i <= $max_field_num; $i++) {
	if (!empty($x["x".$i])) {
		$can_edit["x".$i] = 'readoly="true" disabled="true"';
	} else {
		$can_edit["x".$i] = '';
	}
}

?>
<html>
<head>
<title>设置</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script type="text/javascript">
function check_data(oForm) {
	return confirm("提交之后就无法修改了，确定吗？          ");
}
</script>
</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center">竞价搜索引擎设置</td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"><button onclick="history.back()" class="button">返回</button></div>
</div>
<!-- 头部 end -->

<div class="space"></div>
<div class="description">
	<div class="d_item">设置后不能修改，请谨慎操作。（如果确实需要修改，请联系开发人员）</div>
</div>

<div class="space"></div>
<form method="POST" onsubmit="return check_data(this)">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">最多可以设置<?php echo $max_field_num; ?>个，且为全局设置（各家医院只能从这些设置中选取字段使用）</td>
	</tr>

<?php for ($i = 1; $i <= $max_field_num; $i++) { ?>
	<tr>
		<td class="left">字段<?php echo $i; ?>：</td>
		<td class="right">
			<input class="input" name="x[<?php echo $i; ?>]" value="<?php echo $x["x".$i]; ?>" <?php echo $can_edit["x".$i]; ?> style="size:200px;"> <span class="intro">名称要容易理解，且尽量短</span>
			&nbsp;&nbsp;&nbsp;&nbsp;副标题：<input class="input" name="sub_name[<?php echo $i; ?>]" value="<?php echo $sub_name["x".$i]; ?>" style="size:200px;"> <span class="intro">副标题，填写后将在表头的下方显示</span>
		</td>
	</tr>
<?php } ?>

</table>

<div class="button_line"><input type="submit" class="submit" value="提交资料"></div>

</form>


</body>
</html>