<?php
/* --------------------------------------------------------
// ˵��:
// ����: ���� (weelia@126.com)
// ʱ��:
// ----------------------------------------------------- */
$table = "jingjia_field_set";
require "../../core/core.php";
$max_field_num = 10;

if ($debug_mode || $username == "admin" || $uinfo["part_id"] == 9) {
	// �����޸�
} else {
	exit_html("�Բ�����û�в���Ȩ�ޣ�");
}

// ��ȡ��ǰ�б�
$f_info = $db->query("select fieldname, name, sub_name from $table order by fieldname", "fieldname");
$x = $sub_name = array();
foreach ($f_info as $k => $v) {
	$x[$k] = $v["name"];
	$sub_name[$k] = $v["sub_name"];
}


if ($_POST) {

	for ($i = 1; $i <= $max_field_num; $i++) {
		if (empty($x["x".$i]) && $_POST["x"][$i] != '') {
			if (array_key_exists("x".$i, $x)) {
				// ���ܽ����޸�ģʽ (��Ҫ�Ļ�ֱ�Ӳ������ݿ�ɣ�ǣ�������ܶ�)
				//$db->query("update $table set name='".$_POST["x"][$i]."' where fieldname='x".$i."' limit 1");
			} else {
				$db->query("insert into $table set fieldname='x".$i."', name='".$_POST["x"][$i]."', addtime=".time().", author='".$realname."'");
			}
		}
	}

	// ���渱����
	for ($i = 1; $i <= $max_field_num; $i++) {
		if ($x["x".$i] != '') {
			$sub = $_POST["sub_name"][$i];
			$db->query("update $table set sub_name='$sub' where fieldname='x".$i."' limit 1");
		}
	}

	msg_box("����ɹ�", "jingjia_set.php", 1, 3);
}


$can_edit = array();
for ($i = 1; $i <= $max_field_num; $i++) {
	if (!empty($x["x".$i])) {
		$can_edit["x".$i] = 'readoly="true" disabled="true"';
	} else {
		$can_edit["x".$i] = '';
	}
}

?>
<html>
<head>
<title>����</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script type="text/javascript">
function check_data(oForm) {
	return confirm("�ύ֮����޷��޸��ˣ�ȷ����          ");
}
</script>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center">����������������</td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"><button onclick="history.back()" class="button">����</button></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>
<div class="description">
	<div class="d_item">���ú����޸ģ�����������������ȷʵ��Ҫ�޸ģ�����ϵ������Ա��</div>
</div>

<div class="space"></div>
<form method="POST" onsubmit="return check_data(this)">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">����������<?php echo $max_field_num; ?>������Ϊȫ�����ã�����ҽԺֻ�ܴ���Щ������ѡȡ�ֶ�ʹ�ã�</td>
	</tr>

<?php for ($i = 1; $i <= $max_field_num; $i++) { ?>
	<tr>
		<td class="left">�ֶ�<?php echo $i; ?>��</td>
		<td class="right">
			<input class="input" name="x[<?php echo $i; ?>]" value="<?php echo $x["x".$i]; ?>" <?php echo $can_edit["x".$i]; ?> style="size:200px;"> <span class="intro">����Ҫ������⣬�Ҿ�����</span>
			&nbsp;&nbsp;&nbsp;&nbsp;�����⣺<input class="input" name="sub_name[<?php echo $i; ?>]" value="<?php echo $sub_name["x".$i]; ?>" style="size:200px;"> <span class="intro">�����⣬��д���ڱ�ͷ���·���ʾ</span>
		</td>
	</tr>
<?php } ?>

</table>

<div class="button_line"><input type="submit" class="submit" value="�ύ����"></div>

</form>


</body>
</html>