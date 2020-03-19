<?php defined("ROOT") or exit("Error."); ?>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script language="javascript">
function Check() {
	var oForm = document.mainform;
	if (oForm.ch_name.value == "") {
		alert("请输入“权限名称”！");
		oForm.ch_name.focus();
		return false;
	}
	return true;
}
</script>
</head>

<body>
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $title; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"></div>
</div>

<div class="space"></div>
<form name="mainform" action="" method="POST" onsubmit="return Check()">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">权限设置</td>
	</tr>
	<tr>
		<td class="left">权限名称：</td>
		<td class="right"><input name="ch_name" value="<?php echo $cline["name"]; ?>" class="input" size="30" style="width:200px"> <span class="intro">权限名称必须填写</span></td>
	</tr>
	<tr>
		<td class="left">权限明细：</td>
		<td class="right"><?php echo $power->show_power_table($usermenu, $cline["menu"]); ?></td>
	</tr>
</table>
<input type="hidden" name="id" value="<?php echo intval($id); ?>">
<input type="hidden" name="back_url" value="<?php echo $_GET["back_url"]; ?>">
<input type="hidden" name="op" value="<?php echo $op; ?>">

<div class="button_line"><input type="submit" class="submit" value="提交资料"></div>
</form>

<div class="space"></div>
</body>
</html>