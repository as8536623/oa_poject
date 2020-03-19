<?php defined("ROOT") or exit("Error."); ?>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $title; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"><input type="button" value="返回" onclick="history.back()" class="button"></div>
</div>
<!-- 头部 end -->

<div class="space"></div>

<table width="100%" class="edit">
	<tr>
		<td class="head" colspan="2">详细资料</td>
	</tr>
	<tr>
		<td class="left">登录名：</td>
		<td class="right"><b><?php echo $line["name"]; ?></b></td>
	</tr>
	<tr>
		<td class="left">真实姓名：</td>
		<td class="right"><?php echo $line["realname"]; ?></td>
	</tr>
	<tr>
		<td class="left">管理站点：</td>
		<td class="right"><?php echo $line["hospitals_str"]; ?></td>
	</tr>
	<tr>
		<td class="left">授权方式：</td>
		<td class="right"><?php
if ($line["powermode"] == 1) {
	echo "直接授权";
} else if ($line["powermode"] == 2) {
	echo "通过角色授权";
} else {
	echo "(未授权)";
}
?>
		</td>
	</tr>

	<tr>
		<td class="left">角色名称：</td>
		<td class="right"><?php
if ($line["powermode"] == 2) {
	$ch_data = $db->query_first("select * from sys_character where id='".$line["character_id"]."' limit 1");
	echo $ch_data["name"];
} else {
	echo "<font color='gray'>(未使用角色系统)</font>";
}
?>
		</td>
	</tr>
	<tr>
		<td class="head" colspan="2">个人资料</td>
	</tr>
	<tr>
		<td class="left">电话：</td>
		<td class="right"><?php echo $line["phone"]; ?></td>
	</tr>
	<tr>
		<td class="left">手机：</td>
		<td class="right"><?php echo $line["mobile"]; ?></td>
	</tr>
	<tr>
		<td class="left">QQ：</td>
		<td class="right"><?php echo $line["qq"]; ?></td>
	</tr>
	<tr>
		<td class="left">E-Mail：</td>
		<td class="right"><?php echo $line["email"]; ?></td>
	</tr>
	<tr>
		<td class="left">个人简介：</td>
		<td class="right"><?php echo $line["intro"]; ?></td>
	</tr>


</table>

<div class="button_line"><button onclick="history.back()" class="buttonb">返回</button></div>
<div class="space"></div>
</body>
</html>