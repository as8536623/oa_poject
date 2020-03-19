<?php
// --------------------------------------------------------
// - 功能说明 : 检查重复数据
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2011-03-22 15:09
// --------------------------------------------------------
exit_html("该功能正在调整中...");

if (!($debug_mode || $uinfo["part_id"] == 9)) {
	exit("没有权限");
}

if (!($cur_type > 0)) {
	exit("医院项目没有选择");
}


set_time_limit(120);

// 更新重复数据字典字段:
$db->query("update count_web set repeatcheck=concat(type_id,'_',date,'_',kefu) where repeatcheck='' ");

// 检查重复数据:
$list = $db->query("select * from (select type_name,date,kefu,repeatcheck,count(repeatcheck) as c from `count_web` where repeatcheck!='' group by repeatcheck order by c desc) as t where t.c>1");

if (count($list) == 0) {
	exit_html("在所有项目、所有日期的网络数据中均未发现重复数据。");
}


// 页面开始 ------------------------
?>
<html>
<head>
<title>重复数据检查</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<style>
body {padding:5px 8px; }
form {display:inline; }
</style>
<script type="text/javascript">
function do_u_confirm() {
	return confirm("是否确认要将重复的数据删除？");
}
</script>
</head>

<body>

<div class="headers">
	<div class="headers_title" style="width:40%"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center">重复数据检查</td><td class="bar_right"></td></tr></table></div>
	<div class="header_center"></div>
	<div class="headers_oprate"><button onclick="history.back()" class="button" title="返回上一页">返回</button></div>
	<div class="clear"></div>
</div>

<div class="space"></div>
<table width="100%" align="center" class="list">
	<tr>
		<td class="head" align="center">重复次数</td>
		<td class="head" align="center">项目名称</td>
		<td class="head" align="center">日期</td>
		<td class="head" align="center">客服</td>
		<td class="head" align="center">操作</td>
	</tr>
<?php foreach ($list as $v) { ?>
	<tr>
		<td class="item" align="center"><?php echo $v["c"]; ?></td>
		<td class="item" align="center"><?php echo $v["type_name"]; ?></td>
		<td class="item" align="center"><?php echo $v["date"]; ?></td>
		<td class="item" align="center"><?php echo $v["kefu"]; ?></td>
		<td class="item" align="center"><a href="?op=repeat_del&str=<?php echo $v["repeatcheck"]; ?>" onclick="return do_u_confirm()">处理</a></td>
	</tr>
<?php } ?>

</table>

<br>

<div style="text-align:right;">点“处理”，将自动把多余的重复数据删除。&nbsp;</div>

<br>

</body>
</html>
