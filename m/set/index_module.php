<?php
/*
// - 功能说明 : 首页模块设置
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2013/5/11
*/
require "../../core/core.php";
$table = "index_module_set";

$index_module_type_list = array("media_from" => "媒体来源");

// 操作的处理:
if ($op = $_GET["op"]) {
	switch ($op) {
		case "add":
			include "index_module_edit.php";
			exit;

		case "edit":
			$line = $db->query_first("select * from $table where id='$id' limit 1");
			include "index_module_edit.php";
			exit;

		case "delete":
			$id = intval($_GET["id"]);
			$db->query("delete from $table where id='$id' limit 1");
			msg_box("删除成功", "back", 1);
	}
}


$list = $db->query("select * from $table order by sort desc, id asc", "id");


?>
<html>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script type="text/javascript">
var base_src = "m/set/index_module.php";
function add(hid) {
	set_high_light('');
	parent.load_src(1, base_src+'?op=add', 600, 350);
	return false;
}

function edit(id, obj) {
	set_high_light(obj);
	parent.load_src(1, base_src+'?op=edit&id='+id, 600, 350);
	return false;
}

function delete_confirm() {
	return confirm("删除后不能恢复，您确定要删除吗？");
}
</script>
</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center">首页模块设置</td><td class="bar_right"></td></tr></table></div>
	<div class="header_center">
<?php if (check_power("add")) { ?>
		<button onclick="add()" class="button">添加</button>
<?php } ?>
	</div>
	<div class="headers_oprate"><button onclick="history.back()" class="button" title="返回上一页">返回</button></div>
</div>
<!-- 头部 end -->

<div class="space"></div>

<!-- 数据列表 begin -->
<form name="mainform">
<table width="100%" align="center" class="list">
	<tr>
		<td class="head" align="center" width="32">ID</td>
		<td class="head" align="left">名称</td>
		<td class="head" align="left">类型</td>
		<td class="head" align="left">汇总方法</td>
		<td class="head" align="center">优先度</td>
		<td class="head" align="center">添加人</td>
		<td class="head" align="center">添加时间</td>
		<td class="head" align="center">操作</td>
	</tr>

<?php foreach ($list as $li) { ?>
	<tr>
		<td align="center" class="item"><?php echo $li["id"]; ?></td>
		<td align="left" class="item"><?php echo $li["name"]; ?></td>
		<td align="left" class="item"><?php echo $index_module_type_list[$li["type"]]; ?></td>
		<td align="left" class="item"><?php echo wee_wrap($li["sum_condition"], 80); ?></td>
		<td align="center" class="item"><?php echo $li["sort"]; ?></td>
		<td align="center" class="item"><?php echo $li["author"]; ?></td>
		<td align="center" class="item"><?php echo nl2br(date("Y-m-d\nH:i", $li["addtime"])); ?></td>
		<td align="center" class="item">
			<a href='#edit' onclick='edit(<?php echo $li["id"]; ?>, this)' class='op'>修改</a>&nbsp;
			<a href='?op=delete&id=<?php echo $li["id"]; ?>' onclick='return delete_confirm();' class='op'>删除</a>
		</td>
	</tr>
<?php } ?>

</table>
</form>
<!-- 数据列表 end -->

<div class="space"></div>

</body>
</html>