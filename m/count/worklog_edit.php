<?php
/*
// ˵��: ajax �ύ����
// ����: ���� (weelia@126.com)
// ʱ��: 2013-11-8
*/
require "../../core/core.php";
$table = "count_worklog";

$type_arr = array(
	"zixun" => "��ѯ����",
	"zhixing" => "ִ�з���",
	"zhuguan" => "���ܷ���",
	"zhuren" => "���η���",
);

$date = intval($_REQUEST["date"]);
$type = $_REQUEST["type"];
$data_id = $_REQUEST["data_id"];

if (!array_key_exists($type, $type_arr)) {
	exit("�����ֶβ���ȷ...");
}
$type_name = $type_arr[$type];

$line = $db->query("select * from $table where hid='$hid' and date='$date' and type='$type' limit 1", 1);

if ($_POST) {
	$data = trim($_REQUEST["content"]);

	// �ж��Ƿ��Ѿ����

	$r = array();

	$mode = "add";
	if ($line) {
		$r["content"] = $data;
		$mode = "edit";
	} else {
		if ($data == '') {
			exit("��ӵ����ݲ���Ϊ�գ�");
		}
		$r["hid"] = $hid;
		$r["date"] = $date;
		$r["type"] = $type;
		$r["content"] = $data;
		$r["addtime"] = time();
		$r["author"] = $realname;
	}

	$sqldata = $db->sqljoin($r);

	if ($mode == "add") {
		$rs = $db->query("insert into $table set $sqldata");
	} else {
		$rs = $db->query("update $table set $sqldata where hid='$hid' and date='$date' and type='$type' limit 1");
	}


	if ($rs) {
		$update_str = text_show($data)." &nbsp;<font color=red>".$realname."@".date("m-d H:i").'</font>';

		if ($data == '') {
			$db->query("delete from $table where content=''");
			$update_str = '';
		}

		echo '<script> parent.update_content_byid("'.$data_id.'", "'.$update_str.'", "innerHTML"); </script>';
		echo '<script> parent.msg_box("�����ύ�ɹ�", 2); </script>';
		echo '<script> parent.load_src(0); </script>';
	} else {
		echo "�ύʧ�ܣ����Ժ����ԣ�";
	}
	exit;
}


?>
<html>
<head>
<title>�޸����� - <?php echo $date; ?></title>
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
<form name="mainform" action="" method="POST" onsubmit="return check_data()">
<table width="100%" class="edit">
	<tr>
		<td class="left" style="width:20%;"><?php echo $type_name; ?>��</td>
		<td class="right">
			<textarea name="content" class="input" style="width:92%; height:116px;"><?php echo $line["content"]; ?></textarea>
		</td>
	</tr>
</table>

<input type="hidden" name="date" value="<?php echo $date; ?>">
<input type="hidden" name="type" value="<?php echo $type; ?>">
<input type="hidden" name="data_id" value="<?php echo $data_id; ?>">
<div class="button_line">
	<input id="submit_button" type="submit" class="submit" value="�ύ����">
</div>

</form>

</body>
</html>