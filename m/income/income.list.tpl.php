<?php defined("ROOT") or exit("Error."); ?>
<html>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title" style="width:45%"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $pinfo["title"]; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="header_center">
		<?php //echo $power->show_button("add", "&sid=".$sid."&back_url=".$back_url); ?>
	</div>
	<div class="headers_oprate"><form name="topform" method="GET"><input type="hidden" name="op" value="<?php echo $op; ?>"><input type="hidden" name="sid" value="<?php echo $sid; ?>">&nbsp;&nbsp;<nobr>模糊搜索：<input name="key" value="<?php echo $_GET["key"]; ?>" class="input" size="12">&nbsp;<input type="submit" class="search" value="搜索" style="font-weight:bold" title="点击搜索">&nbsp;<input type="button" value="重置" onclick="location='?op=list&sid=<?php echo $sid; ?>'" class="search" title="退出条件查询"><input type="hidden" name="op" value="list"></nobr></form></div>
</div>
<!-- 头部 end -->

<div class="space"></div>

<!-- 数据列表 begin -->
<form name="mainform">
<?php echo $t->show(); ?>
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
	<div class="footer_op_right"><?php echo $pagelink; ?></div>
</div>
<!-- 分页链接 end -->

<div class="space"></div>
</body>
</html>