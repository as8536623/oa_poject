<?php
// --------------------------------------------------------
// - ����˵�� : ����������ϸ
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2013-9-10
// --------------------------------------------------------
require "../../core/core.php";
include "web_config.php";

if ($_GET["dt"] == "") {
	$_GET["dt"] = date("Y-m-d");
}
$dt = str_replace("-", "", $_GET["dt"]);

if ($_GET["sid"] == '') {
	$_GET["sid"] = 1;
}
$sid = intval($_GET["sid"]);

$list = $db->query("select * from count_web where hid='$hid' and date='$dt' and sub_id='$sid' order by kefu asc, sub_id asc", "id");


// �����Ĵ���:
if ($op) {
	if ($op == "delete") {
		$opid = intval($_GET["id"]);
		$crc = intval($_GET["crc"]);
		if ($opid > 0) {
			$tmp_data = $db->query_first("select * from $table where id='$opid' limit 1");
			if ($tmp_data["addtime"] != $crc) {
				exit("�Բ��� crc У����� �޷�ɾ�� ");
			}
			if ($db->query("delete from $table where id='$opid' limit 1")) {
				$op_data[] = $tmp_data;
				$log->add("delete", "ɾ������", serialize($op_data));
				msg_box("ɾ���ɹ�", "back", 1);
			}
		} else {
			exit("��������...");
		}
	}
}





// ҳ�濪ʼ ------------------------
?>
<html>
<head>
<title><?php echo $h_name; ?> - ������ϸ</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<style>
* {font-family:"Tahoma"; }
</style>
<script type="text/javascript">
function del_confirm() {
	return confirm("ɾ�����ָܻ���ȷ��Ҫɾ������������");
}
</script>
</head>

<body>
<table style="margin:10px 0 0 0px;" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="300" align="left">
		</td>

		<td width="" align="center">
			<form method="GET">
				<input name="dt" value="<?php echo $_GET["dt"]; ?>" class="input" style="width:150px" id="dt"> <img src="/res/img/calendar.gif" id="dt" onClick="picker({el:'dt',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="ѡ������">&nbsp;
				<select name="sid" class="combo">
					<?php echo list_option($sub_type_arr, "_key_", "_value_", $_GET["sid"]); ?>
				</select>&nbsp;
				<input type="submit" class="button" value="ȷ��">
			</form>
		</td>

		<td width="300" align="right">
		</td>
	</tr>
</table>

<div class="space"></div>

<table width="100%" align="center" class="list">
	<tr>
		<td class="head" align="center">�ͷ�</td>
		<td class="head" align="center">����</td>

		<td class="head" align="center" style="color:red">�ܵ��</td>
		<td class="head" align="center">����</td>
		<td class="head" align="center">���</td>
		<td class="head" align="center" style="color:red">����Ч</td>
		<td class="head" align="center">����</td>
		<td class="head" align="center">���</td>

		<td class="head" align="center" style="color:red">����Լ</td>
		<td class="head" align="center">����</td>
		<td class="head" align="center">���</td>
		<td class="head" align="center" style="color:red">Ԥ�Ƶ�Ժ</td>
		<td class="head" align="center">����</td>
		<td class="head" align="center">���</td>
		<td class="head" align="center" style="color:red">ʵ�ʵ�Ժ</td>
		<td class="head" align="center">����</td>
		<td class="head" align="center">���</td>

		<td class="head" align="center">�����</td>
		<td class="head" align="center">����</td>
	</tr>

<?php
foreach ($list as $li) {
?>

	<tr>
		<td class="item" align="center"><?php echo $li["kefu"]; ?></td>
		<td class="item" align="center"><?php echo $sub_type_arr[$li["sub_id"]]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo $li["click"]; ?></td>
		<td class="item" align="center"><?php echo $li["click_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["click_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["ok_click"]; ?></td>
		<td class="item" align="center"><?php echo $li["ok_click_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["ok_click_other"]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo $li["talk"]; ?></td>
		<td class="item" align="center"><?php echo $li["talk_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["talk_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["orders"]; ?></td>
		<td class="item" align="center"><?php echo $li["order_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["order_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["come"]; ?></td>
		<td class="item" align="center"><?php echo $li["come_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["come_other"]; ?></td>

		<td class="item" align="center" onclick="alert(this.title)" title="<?php echo trim($li["log"]); ?>"><?php echo $li["u_realname"]; ?></td>
		<td class="item" align="center">
			<a href="?op=delete&id=<?php echo $li["id"]; ?>&crc=<?php echo $li["addtime"]; ?>" onclick="return del_confirm();">ɾ��</a>
		</td>
	</tr>

<?php } ?>

</table>

</body>
</html>