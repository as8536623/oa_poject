<?php
// --------------------------------------------------------
// - ����˵�� : ����ظ�����
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2011-03-22 15:09
// --------------------------------------------------------
exit_html("�ù������ڵ�����...");

if (!($debug_mode || $uinfo["part_id"] == 9)) {
	exit("û��Ȩ��");
}

if (!($cur_type > 0)) {
	exit("ҽԺ��Ŀû��ѡ��");
}


set_time_limit(120);

// �����ظ������ֵ��ֶ�:
$db->query("update count_web set repeatcheck=concat(type_id,'_',date,'_',kefu) where repeatcheck='' ");

// ����ظ�����:
$list = $db->query("select * from (select type_name,date,kefu,repeatcheck,count(repeatcheck) as c from `count_web` where repeatcheck!='' group by repeatcheck order by c desc) as t where t.c>1");

if (count($list) == 0) {
	exit_html("��������Ŀ���������ڵ����������о�δ�����ظ����ݡ�");
}


// ҳ�濪ʼ ------------------------
?>
<html>
<head>
<title>�ظ����ݼ��</title>
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
	return confirm("�Ƿ�ȷ��Ҫ���ظ�������ɾ����");
}
</script>
</head>

<body>

<div class="headers">
	<div class="headers_title" style="width:40%"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center">�ظ����ݼ��</td><td class="bar_right"></td></tr></table></div>
	<div class="header_center"></div>
	<div class="headers_oprate"><button onclick="history.back()" class="button" title="������һҳ">����</button></div>
	<div class="clear"></div>
</div>

<div class="space"></div>
<table width="100%" align="center" class="list">
	<tr>
		<td class="head" align="center">�ظ�����</td>
		<td class="head" align="center">��Ŀ����</td>
		<td class="head" align="center">����</td>
		<td class="head" align="center">�ͷ�</td>
		<td class="head" align="center">����</td>
	</tr>
<?php foreach ($list as $v) { ?>
	<tr>
		<td class="item" align="center"><?php echo $v["c"]; ?></td>
		<td class="item" align="center"><?php echo $v["type_name"]; ?></td>
		<td class="item" align="center"><?php echo $v["date"]; ?></td>
		<td class="item" align="center"><?php echo $v["kefu"]; ?></td>
		<td class="item" align="center"><a href="?op=repeat_del&str=<?php echo $v["repeatcheck"]; ?>" onclick="return do_u_confirm()">����</a></td>
	</tr>
<?php } ?>

</table>

<br>

<div style="text-align:right;">�㡰���������Զ��Ѷ�����ظ�����ɾ����&nbsp;</div>

<br>

</body>
</html>
