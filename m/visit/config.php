<?php
require "../../core/core.php";

if (!$hid) {
	exit_html("�Բ���û��ѡ��ҽԺ������ִ�иò�����");
}

$h_config_str = $db->query("select config from hospital where id=$hid limit 1", 1, "config");
$h_config = @unserialize($h_config_str);

if ($_POST) {
	$h_config["engine"] = str_replace("\n", "|", str_replace("\r", "", $_POST["engine"]));

	// ȥ���ظ�����Ч��:
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

	msg_box("ѡ�����óɹ�", "back", 1);
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
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $pinfo["title"]; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"><input type="button" value="����" onclick="history.back()" class="button"></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>

<div class="description">
	<div class="d_title">��ʾ��</div>
	<li class="d_item">������ʾ</li>
</div>

<div class="space"></div>

<form name="mainform" method="POST" onsubmit="return check_data()">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">��������</td>
	</tr>

	<!-- <tr>
		<td class="left">�������棺</td>
		<td class="right"><textarea name="engine" class="input" style="width:200px;height:120px;vertical-align:middle;"><?php echo str_replace("|", "\r\n", $h_config["engine"]); ?></textarea> <span class="intro">��д�����������ƣ�ÿ��һ��</span></td>
	</tr> -->


</table>

<div class="space"></div>

<input type="hidden" name="op" value="<?php echo $op; ?>">
<input type="hidden" name="back_url" value="<?php echo $_GET["back_url"]; ?>">
<!-- <div class="button_line"><input type="submit" class="submit" value="�ύ����"></div> -->


</form>

<div class="space"></div>
</body>
</html>