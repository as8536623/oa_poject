<?php
/*
// ˵��: config
// ����: ���� (weelia@126.com)
// ʱ��: 2012-05-07
*/
require "../../core/core.php";
$table = "count_config";

$sort_type_arr = array("1" => "�������ȶ�", "2" => "��ѯ���ȶ�", "3" => "����");

$old = $db->query("select id,name,value,intro from $table", "name");

if ($_POST) {

	ob_start();
	foreach ($_POST["config"] as $_name => $_v) {
		if ($_name != '') {
			if (array_key_exists($_name, $old)) {
				$db->query("update $table set value='$_v' where name='$_name' limit 1");
			} else {
				$db->query("insert into $table set name='$_name', value='$_v'");
			}
		}
	}
	$error = ob_get_clean();

	if (empty($error)) {
		echo '<script>';
		echo 'parent.update_content();';
		echo 'parent.msg_box("�޸ĳɹ�", 2);';
		echo 'parent.load_src(0);';
		echo '</script>';
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
	<div class="d_item">ѡ������ʽ������ύ����</div>
</div>

<div class="space"></div>
<form name="mainform" action="" method="POST" onsubmit="return check_data()">
<table width="100%" class="edit">
	<tr>
		<td class="left" style="width:20%">����ʽ��</td>
		<td class="right" style="width:80%">
			<select name="config[����ʽ_<?php echo $uid; ?>]" class="combo">
				<option value="" style="color:silver;">--��ѡ��--</option>
				<?php echo list_option($sort_type_arr, "_key_", "_value_", $old["����ʽ_".$uid]["value"]); ?>
			</select>
		</td>
	</tr>
</table>

<div class="button_line"><input id="submit_button" type="submit" class="submit" value="�ύ����"></div>

</form>

</body>
</html>