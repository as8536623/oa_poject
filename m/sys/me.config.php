<?php
/*
// - ����˵�� : ѡ������
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2007-07-19 09:46
*/
require "../../core/core.php";
$table = "sys_admin";

if (!$uid) {
	exit_html("�����޸���������...");
}

if ($_POST) {
	$new_config = array();
	if ($uinfo["config"] != '') {
		$new_config = @unserialize($uinfo["config"]);
	}

	unset($new_config["jsmenu"]);
	unset($new_config["shortcut"]);
	unset($new_config["submenu_pos"]);
	$new_config["close_left_menu"] = intval($_POST["close_left_menu"]); // �Ƿ�ر����

	$new_str = serialize($new_config);

	if ($uid > 0) {
		$sql = "update $table set config='$new_str' where id=$uid limit 1";

		if ($db->query($sql)) {
			//msg_box("ѡ���޸ĳɹ�", "", 0);
			update_main_frame();
			exit;
		} else {
			msg_box("�����ύʧ�ܣ�ϵͳ��æ�����Ժ����ԡ�", "back", 1, 5);
		}
	}
}

?>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
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
	<li class="d_item">���������ĸ���ϲ�����ý��棻��ҳѡ���ύ�󽫻�����������ʾ</li>
</div>

<div class="space"></div>

<form name="mainform" action="" method="POST">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">ѡ������</td>
	</tr>
	<tr>
		<td class="left">�������</td>
		<td class="right">
			<select name="close_left_menu" class="combo">
				<option value="0" <?php if (!$config["close_left_menu"]) echo "selected"; ?>>��ʾ<?php if (!$config["close_left_menu"]) echo " *"; ?></option>
				<option value="1" <?php if ($config["close_left_menu"]) echo "selected"; ?>>�ر�<?php if ($config["close_left_menu"]) echo " *"; ?></option>
			</select>&nbsp; <span class="intro">��ѡ���Ϊ�������ʼ״̬������Ҫ��ʱ�����ͨ��������չ����������ť��ʱ����</span>
		</td>
	</tr>
</table>

<div class="button_line"><input type="submit" class="submit" value="�ύ����"></div>
<input type="hidden" name="back_url" value="<?php echo $_GET["back_url"]; ?>">
</form>

<div class="space"></div>
</body>
</html>