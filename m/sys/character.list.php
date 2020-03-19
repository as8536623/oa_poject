<?php defined("ROOT") or exit("Error."); ?>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script language="javascript">
var base_src = "m/sys/character.php";
function add(hid) {
	set_high_light('');
	parent.load_src(1, base_src+'?op=add', 1000, 600);
	return false;
}

function edit(id, obj) {
	set_high_light(obj);
	parent.load_src(1, base_src+'?op=edit&id='+id, 1000, 600);
	return false;
}
</script>
</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center">权限列表</td><td class="bar_right"></td></tr></table></div>
	<div class="header_center">
<?php if (check_power("add")) { ?>
		<button onclick="add()" class="button">添加</button>
<?php } ?>
	</div>
	<div class="headers_oprate"><form name="topform" method="GET">模糊搜索：<input name="searchword" value="<?php echo $_GET["searchword"]; ?>" class="input" size="12">&nbsp;<input type="submit" class="search" value="搜索" style="font-weight:bold" title="点击搜索">&nbsp;<input type="button" value="重置" onclick="location='?'" class="search" title="退出条件查询"></form></div>
</div>
<!-- 头部 end -->

<div class="space"></div>

<!-- 数据列表 begin -->
<form name="mainform">
<table width="100%" align="center" class="list">
<?php
echo $table_header."\r\n";
if (count($table_items) > 0) {
	echo implode("\r\n", $table_items);
} else {
?>
	<tr>
		<td colspan="<?php echo count($list_heads); ?>" align="center" class="nodata">(没有数据...)</td>
	</tr>
<?php
}
?>
</table>
</form>
<!-- 数据列表 end -->

<div class="space"></div>

<!-- 分页链接 begin -->
<div class="footer_op">
	<div class="footer_op_left"><button onclick="select_all()" class="button">全选</button>&nbsp;<button onclick="unselect()" class="button">反选</button>&nbsp;
	<?php
		if ($username == "admin" || $debug_mode) {
			echo $power->show_button("close,delete");
		}
	?>
	</div>
	<div class="footer_op_right"><?php echo pagelinkc($page, $pagecount, $count, make_link_info($link_param, "page"), "button"); ?></div>
</div>
<!-- 分页链接 end -->

<div class="space"></div>
</body>
</html>