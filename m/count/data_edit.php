<?php
/*
// ˵��: ajax �ύ����
// ����: ���� (weelia@126.com)
// ʱ��: 2010-11-24 16:53
*/
require "../../core/core.php";
$table = "count_web_day";

// ���Ȩ�� @ 2012-03-21
$can_edit = array();
if ($uinfo["character_id"] == 16 || $debug_mode || (check_power("fangke") && check_power("yuyue")) ) { //������Ա
	$can_edit = explode(" ", "click_all zero_talk wangcha");
} else if ($uinfo["character_id"] == 23 || check_power("yuyue")) { //��ѯ�鳤
	$can_edit = explode(" ", "wangcha");
} else if ($uinfo["character_id"] == 28 || check_power("fangke")) { //ͳ��
	$can_edit = explode(" ", "click_all zero_talk");
}

$hid = $_SESSION["hospital_id"];
$sub_id = $_SESSION["sub_id"];

$date = intval($_REQUEST["date"]);
$type = $_REQUEST["type"];
$type_arr = array("click_all" => "�ܵ��", "zero_talk" => "��Ի�", "wangcha" => "����");
$typename = $type_arr[$type];

// ���Ȩ�� @ 2012-03-21
//if (!in_array($type, $can_edit)) {
//	exit_html("�Բ�����û��Ȩ���޸Ĵ�����...");
//}


$chk_date = date("Y-m-d", strtotime(substr($date,0,4)."-".substr($date,4,2)."-".substr($date,6,2)));
$allow_day = $db->query("select value from count_config where name='�����޸�����' limit 1", 1, "value");
$allow_day = intval($allow_day);
if ($allow_day > 0) {
	$allow = 0;
	if ($chk_date == date("Y-m-d")) {
		$allow = 1; //�������������޸�
	} else {
		for ($i = 1; $i < $allow_day; $i++) {
			if ($chk_date == date("Y-m-d", strtotime("-".$i." day"))) {
				$allow = 1;
			}
		}
	}

	if (!$allow) {
		exit_html("�Բ���ֻ���޸����".$allow_day."������ݡ�");
	}
}

$old = $db->query("select * from $table where hid='$hid' and sub_id='$sub_id' and date='$date' limit 1", 1);

if ($_POST) {

	// �ظ��ύ��⣬���ͨ��������������token
	if ($_SESSION["data_edit_token"] != $_POST["token"]) {
		exit("�벻Ҫ�ظ��ύ...");
	}
	$_SESSION["data_edit_token"] = time();

	$data = floatval($_REQUEST["data"]);

	// �ж��Ƿ��Ѿ����

	$r = array();

	$mode = "add";
	if ($old) {
		$r[$type] = $data;
		$mode = "edit";
	} else {
		$r["hid"] = $hid;
		$r["sub_id"] = $sub_id;
		$r["date"] = $date;
		$r["repeatcheck"] = $hid."_".$sub_id."_".$date;
		$r[$type] = $data;
		$r["addtime"] = time();
		$r["uid"] = $uid;
		$r["u_name"] = $realname;
	}

	// ������־:
	if ($mode == "add") {
		$r["log"] = date("Y-m-d H:i")." ".$realname." ���: ".$type.":".$r[$type]."\r\n";
	} else {
		$r["log"] = $old["log"].date("Y-m-d H:i")." ".$realname." �޸�: ".$type.":".$old[$type]."=>".$r[$type]."\r\n";
	}

	$sqldata = $db->sqljoin($r);

	if ($mode == "add") {
		$rs = $db->query("insert into $table set $sqldata");
	} else {
		$rs = $db->query("update $table set $sqldata where hid='$hid' and sub_id='$sub_id' and date='$date' limit 1");
	}

	if ($rs) {
		//if ($mode == "add") {
			echo '<script> parent.update_content(); </script>';
		//}
		echo '<script> parent.msg_box("�����ύ�ɹ�", 2); </script>';
		echo '<script> parent.load_src(0); </script>';
	} else {
		echo "�ύʧ�ܣ����Ժ����ԣ�";
	}
	exit;
}

$token = $_SESSION["data_edit_token"] = time();


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
<div class="space"></div>
<form name="mainform" action="" method="POST" onsubmit="return check_data()">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">����������</td>
	</tr>

	<tr>
		<td class="left"><?php echo $typename; ?>��</td>
		<td class="right">
			<input name="data" value="<?php echo $old[$type]; ?>" class="input" style="width:100px">
		</td>
	</tr>
</table>

<input type="hidden" name="type" value="<?php echo $type; ?>">
<input type="hidden" name="date" value="<?php echo $date; ?>">
<input type="hidden" name="token" value="<?php echo $token; ?>">

<div class="button_line">
	<input id="submit_button" type="submit" class="submit" value="�ύ����">
</div>

</form>

</body>
</html>