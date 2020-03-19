<?php
require "../../core/core.php";

if (!$hid) {
	exit_html("对不起，没有选择医院，不能执行该操作！");
}

$h_config_str = $db->query("select config from hospital where id=$hid limit 1", 1, "config");
$h_config = @unserialize($h_config_str);

if ($_POST) {
	$h_config["engine"] = str_replace("\n", "|", str_replace("\r", "", $_POST["engine"]));

	// 去除重复和无效的:
	$tmp = array();
	$ss = explode("|", $h_config["engine"]);
	foreach ($ss as $s) {
		if ($s = trim($s)) {
			$tmp[$s] = 1;
		}
	}
	$h_config["engine"] = implode("|", array_keys($tmp));

	$h_config_str = serialize($h_config);
	$db->query("update hospital set config='$h_config_str' where id='$hid' limit 1");

	msg_box("选项设置成功", "back", 1);
}

?>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style>
</style>
<script language="javascript">

</script>
</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $pinfo["title"]; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"><input type="button" value="返回" onclick="history.back()" class="button"></div>
</div>
<!-- 头部 end -->

<div class="space"></div>

<div class="description">
	<div class="d_title">提示：</div>
	<li class="d_item">暂无提示</li>
</div>

<div class="space"></div>

<form name="mainform" method="POST" onsubmit="return check_data()">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">基本数据</td>
	</tr>

	<!-- <tr>
		<td class="left">搜索引擎：</td>
		<td class="right"><textarea name="engine" class="input" style="width:200px;height:120px;vertical-align:middle;"><?php echo str_replace("|", "\r\n", $h_config["engine"]); ?></textarea> <span class="intro">填写搜索引擎名称，每行一个</span></td>
	</tr> -->


</table>

<div class="space"></div>

<input type="hidden" name="op" value="<?php echo $op; ?>">
<input type="hidden" name="back_url" value="<?php echo $_GET["back_url"]; ?>">
<!-- <div class="button_line"><input type="submit" class="submit" value="提交资料"></div> -->


</form>

<div class="space"></div>
</body>
</html>