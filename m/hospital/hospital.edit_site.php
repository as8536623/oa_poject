<?php
defined("ROOT") or exit;

$from_hid = intval($_REQUEST["hid"]);
if (!$from_hid) {
	exit("参数错误");
}
$h_line = $db->query("select * from hospital where id=$from_hid limit 1", 1);
$h_name = $h_line["name"];

if ($_POST) {
	$r = array();

	$r["url"] = str_replace("http://", "", $_POST["url"]);
	$r["site_name"] = $_POST["site_name"];

	$tm_config = array();
	$tm_config["engine"] = str_replace("\n", "|", str_replace("\r", "", $_POST["engine"]));
	$r["config"] = serialize($tm_config);

	if ($op == "add_site") {
		$r["hid"] = $from_hid;
		$r["h_name"] = $h_name;
		$r["addtime"] = time();
		$r["uid"] = $uid;
		$r["u_realname"] = $realname;
	}


	$sqldata = $db->sqljoin($r);
	if ($op == "add_site") {
		$sql = "insert into sites set $sqldata";
	} else {
		$sql = "update sites set $sqldata where id='$id' limit 1";
	}

	if ($db->query($sql)) {
		// 弹出窗口的处理方式:
		echo '<script> parent.update_content(); </script>';
		echo '<script> parent.msg_box("资料提交成功", 2); </script>';
		echo '<script> parent.load_src(0); </script>';
	} else {
		echo "提交失败，请稍后再试！";
	}
}

$title = $op == "add_site" ? "添加下挂网站" : "修改下挂网站";

?>
<html>
<head>
<title>下挂网站管理 <?php echo $h_name; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script language="javascript">
function check_data(oForm) {

	if (oForm.url.value.length == 0) {
		msg_box("请输入网址！");
		oForm.url.focus();
		return false;
	}

	return true;
}
</script>
</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $title; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"></div>
</div>
<!-- 头部 end -->

<div class="space"></div>

<form method="POST" onsubmit="return check_data(this)">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">下挂网站</td>
	</tr>
	<tr>
		<td class="left">医院名称：</td>
		<td class="right"><?php echo $h_name; ?></td>
	</tr>
	<tr>
		<td class="left">网站网址：</td>
		<td class="right"><input name="url" value="<?php echo $line["url"]; ?>" class="input" style="width:300px"> <span class="intro">网址，例如 www.021guke.com</span></td>
	</tr>
	<tr>
		<td class="left">网站名称：</td>
		<td class="right"><input name="site_name" value="<?php echo $line["site_name"]; ?>" class="input" style="width:300px"> <span class="intro">可不填</span></td>
	</tr>
	<tr>
		<td class="left">搜索引擎：</td>
		<td class="right"><textarea class="input" name="engine" style="width:300px; height:120px; vertical-align:middle;"><?php echo str_replace("|", "\r\n", $site_config["engine"]); ?></textarea> <span class="intro">一行一个</span></td>
	</tr>
</table>

<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="op" value="<?php echo $op; ?>">
<input type="hidden" name="hid" value="<?php echo $from_hid; ?>">
<input type="hidden" name="back_url" value="<?php echo $_SERVER["HTTP_REFERER"]; ?>">

<div class="button_line"><input type="submit" class="submit" value="提交资料"></div>
</form>
</body>
</html>