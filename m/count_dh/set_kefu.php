<?php
// --------------------------------------------------------
// - ����˵�� : ��Ŀ�������޸�
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2010-10-13 11:40
// --------------------------------------------------------
require "../../core/core.php";

if ($_POST) {

	// ����ͷ�
	$kefu_arr = explode(",", str_replace("��", ",", str_replace("��", ",", $_POST["kefu"])));
	$new_arr = array();
	foreach ($kefu_arr as $v) {
		$v = trim($v);
		if ($v) $new_arr[] = $v;
	}
	$kefu = @implode(",", $new_arr);

	// ��ѯ�Ƿ��Ѿ��м�¼�ˣ�
	$line = $db->query("select * from count_dh_type where hid=$hid limit 1", 1);
	if ($line["hid"] > 0) {
		$sql = "update count_dh_type set kefu='$kefu' where hid='$hid' limit 1";
	} else {
		$sql = "insert into count_dh_type set hid='$hid', kefu='$kefu'";
	}

	ob_start();
	$db->query($sql);
	$error = ob_get_clean();

	if (empty($error)) {
		echo '<script> parent.update_content(); </script>';
		echo '<script> parent.msg_box("�����ύ�ɹ�", 2); </script>';
		echo '<script> parent.load_src(0); </script>';
	} else {
		exit_html("�ύ����".$error);
	}

	exit;
}

$line = $db->query("select * from count_dh_type where hid=$hid limit 1", 1);


?>
<html>
<head>
<title>���ÿͷ�</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>

<style>
#rec_part, #rec_user {margin-top:6px; }
.rec_user_b {width:140px; float:left; }
.rec_group_part {clear:both; margin:10px 0 5px 0; font-weight:bold; }

.hide_scroll {overflow-y:hidden; overflow-x:hidden; }
.show_scroll, html {overflow-y:auto; overflow-x:hidden; }

.left {padding:10px 5px !important; }
.right {padding:10px 5px !important; }
</style>

<script language="javascript">
</script>

</head>

<body>

<div class="space"></div>

<form name="mainform" action="" method="POST">
<table width="100%" class="edit">
	<tr>
		<td class="left" valign="top">�ͷ�������</td>
		<td class="right">
			<input name="kefu" value="<?php echo $line["kefu"]; ?>" class="input" style="width:90%"><br>
			<b>��д˵����</b><br>
			����1. ��ע�����ֲ�Ҫ���Ҫ��ϵͳ�ѵǼǵ���Ա��ʵ����һ�¡�<br>
			����2. �����д��������ϵͳ��鲻���������޷����й�����ѯ�����絽Ժ�����ȣ�<br>
			����3. �������ö��ţ���Сд���ž��ɣ�������
		</td>
	</tr>
</table>

<input type="hidden" name="op" value="submit">

<br>

<div class="button_line">
	<input type="submit" class="submit" value="�ύ����">
</div>

</form>

</body>
</html>