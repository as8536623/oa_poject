<?php defined("ROOT") or exit("Error."); ?>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $title; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"><input type="button" value="����" onclick="history.back()" class="button"></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>

<table width="100%" class="edit">
	<tr>
		<td class="head" colspan="2">��ϸ����</td>
	</tr>
	<tr>
		<td class="left">��¼����</td>
		<td class="right"><b><?php echo $line["name"]; ?></b></td>
	</tr>
	<tr>
		<td class="left">��ʵ������</td>
		<td class="right"><?php echo $line["realname"]; ?></td>
	</tr>
	<tr>
		<td class="left">����վ�㣺</td>
		<td class="right"><?php echo $line["hospitals_str"]; ?></td>
	</tr>
	<tr>
		<td class="left">��Ȩ��ʽ��</td>
		<td class="right"><?php
if ($line["powermode"] == 1) {
	echo "ֱ����Ȩ";
} else if ($line["powermode"] == 2) {
	echo "ͨ����ɫ��Ȩ";
} else {
	echo "(δ��Ȩ)";
}
?>
		</td>
	</tr>

	<tr>
		<td class="left">��ɫ���ƣ�</td>
		<td class="right"><?php
if ($line["powermode"] == 2) {
	$ch_data = $db->query_first("select * from sys_character where id='".$line["character_id"]."' limit 1");
	echo $ch_data["name"];
} else {
	echo "<font color='gray'>(δʹ�ý�ɫϵͳ)</font>";
}
?>
		</td>
	</tr>
	<tr>
		<td class="head" colspan="2">��������</td>
	</tr>
	<tr>
		<td class="left">�绰��</td>
		<td class="right"><?php echo $line["phone"]; ?></td>
	</tr>
	<tr>
		<td class="left">�ֻ���</td>
		<td class="right"><?php echo $line["mobile"]; ?></td>
	</tr>
	<tr>
		<td class="left">QQ��</td>
		<td class="right"><?php echo $line["qq"]; ?></td>
	</tr>
	<tr>
		<td class="left">E-Mail��</td>
		<td class="right"><?php echo $line["email"]; ?></td>
	</tr>
	<tr>
		<td class="left">���˼�飺</td>
		<td class="right"><?php echo $line["intro"]; ?></td>
	</tr>


</table>

<div class="button_line"><button onclick="history.back()" class="buttonb">����</button></div>
<div class="space"></div>
</body>
</html>