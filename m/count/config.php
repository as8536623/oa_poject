<?php
/*
// ˵��: config
// ����: ���� (weelia@126.com)
// ʱ��: 2012-05-07
*/
require "../../core/core.php";
$table = "count_config";

$old = $db->query("select id,name,value,intro from $table", "name");

if ($_POST) {

	ob_start();
	foreach ($_POST["config"] as $_id => $_v) {
		$_id = intval($_id);
		if ($_id > 0) {
			$db->query("update $table set value='$_v' where id=$_id limit 1");
		}
	}
	$error = ob_get_clean();

	if (empty($error)) {
		//echo '<script> parent.update_content(); </script>';
		echo '<script> parent.msg_box("�޸ĳɹ�", 2); </script>';
		echo '<script> parent.load_src(0); </script>';
	} else {
		echo "�ύʧ�ܣ����Ժ����ԣ�";
	}
	exit;
}


?>
<html>
<head>
<title>�޸�����</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script type="text/javascript">
function check_data() {
	return true;
}
</script>
</head>

<body>
<div class="description">
	<div class="d_title">��ʾ��</div>
	<div class="d_item">��Ҫ������������ϣ�����ύ����</div>
</div>

<div class="space"></div>
<form name="mainform" action="" method="POST" onsubmit="return check_data()">
<table width="100%" class="edit">
	<tr>
		<td class="left" style="width:20%">�����޸�������</td>
		<td class="right" style="width:80%">
			<input name="config[<?php echo $old["�����޸�����"]["id"]; ?>]" value="<?php echo $old["�����޸�����"]["value"]; ?>" class="input" style="width:100px"> <?php echo $old["�����޸�����"]["intro"]; ?>
		</td>
	</tr>
</table>

<div class="button_line"><input id="submit_button" type="submit" class="submit" value="�ύ����"></div>

</form>

</body>
</html>