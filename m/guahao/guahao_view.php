<?php
/*
// - 功能说明 : 挂号资料查看
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2009-05-22 13:03
*/
require "../../core/core.php";
$table = "guahao";

if (!$hid) {
	exit_html("对不起，没有选择医院，不能执行该操作！");
}

if ($id = $_GET["id"]) {
	$line = $db->query("select * from $table where hospital_id=$hid and id='$id' limit 1", 1);
} else {
	msg_box("参数错误...", "back", 1);
}

check_power("v", $pinfo, $pagepower) or msg_box("对不起，您没有查看权限!", "back", 1);

$title = "查看挂号资料";

// 数据:
$viewdata = array(
	array("姓名", $line["name"]),
	array("性别", $line["sex"]),
	array("电话", $line["tel"]),
	array("E-Mail", $line["email"]),
	array("城市", $line["city"]),
	array("预约时间", $line["order_date"] > 0 ? date("Y-m-d H:i", $line["order_date"]) : '-'),
	array("预约科室", $line["depart"]),
	array("预约内容", text_show($line["content"])),
	array("预约医生", $line["doctor"]),
	array("备注", text_show($line["memo"])),
	array("发布者IP", $line["ip"]),
	array("IP对应地址", $line["ip_address"]),
	array("提交时间", date("Y-m-d H:i", $line["addtime"])),
	array("来源站点", $line["site"]),
	array("POST数据(供参考)", $line["postdata"]),
);

if ($debug_mode) {
	$viewdata = array_merge($viewdata, array(
		array("GET数据", $line["getdata"]),
		array("SERVER数据", $line["serverdata"])
	));
}

?>
<html>
<head>
<title>查看数据</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $title; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"><button onclick="history.back()" class="button">返回</button></div>
</div>
<!-- 头部 end -->

<div class="space"></div>

<table width="100%" class="edit">
<?php foreach ($viewdata as $k => $v) { ?>
	<tr>
		<td class="left"><?php echo $v[0]; ?>：</td>
		<td class="right"><?php echo $v[1]; ?></td>
	</tr>
<?php } ?>
</table>

<div class="button_line">
	<input type="button" class="submit" onclick="history.back()" value="返回">
</div>

</body>
</html>