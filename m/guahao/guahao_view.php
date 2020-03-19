<?php
/*
// - ����˵�� : �Һ����ϲ鿴
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2009-05-22 13:03
*/
require "../../core/core.php";
$table = "guahao";

if (!$hid) {
	exit_html("�Բ���û��ѡ��ҽԺ������ִ�иò�����");
}

if ($id = $_GET["id"]) {
	$line = $db->query("select * from $table where hospital_id=$hid and id='$id' limit 1", 1);
} else {
	msg_box("��������...", "back", 1);
}

check_power("v", $pinfo, $pagepower) or msg_box("�Բ�����û�в鿴Ȩ��!", "back", 1);

$title = "�鿴�Һ�����";

// ����:
$viewdata = array(
	array("����", $line["name"]),
	array("�Ա�", $line["sex"]),
	array("�绰", $line["tel"]),
	array("E-Mail", $line["email"]),
	array("����", $line["city"]),
	array("ԤԼʱ��", $line["order_date"] > 0 ? date("Y-m-d H:i", $line["order_date"]) : '-'),
	array("ԤԼ����", $line["depart"]),
	array("ԤԼ����", text_show($line["content"])),
	array("ԤԼҽ��", $line["doctor"]),
	array("��ע", text_show($line["memo"])),
	array("������IP", $line["ip"]),
	array("IP��Ӧ��ַ", $line["ip_address"]),
	array("�ύʱ��", date("Y-m-d H:i", $line["addtime"])),
	array("��Դվ��", $line["site"]),
	array("POST����(���ο�)", $line["postdata"]),
);

if ($debug_mode) {
	$viewdata = array_merge($viewdata, array(
		array("GET����", $line["getdata"]),
		array("SERVER����", $line["serverdata"])
	));
}

?>
<html>
<head>
<title>�鿴����</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $title; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"><button onclick="history.back()" class="button">����</button></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>

<table width="100%" class="edit">
<?php foreach ($viewdata as $k => $v) { ?>
	<tr>
		<td class="left"><?php echo $v[0]; ?>��</td>
		<td class="right"><?php echo $v[1]; ?></td>
	</tr>
<?php } ?>
</table>

<div class="button_line">
	<input type="button" class="submit" onclick="history.back()" value="����">
</div>

</body>
</html>