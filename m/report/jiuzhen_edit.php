<?php
/*
// ˵��: �޸ı�������
// ����: ���� (weelia@126.com)
// ʱ��: 2013-08-30
*/
require "../../core/core.php";
include "config.php";
$table = "jiuzhen_report";


$_hid = intval($_REQUEST["hid"]);
$_month = intval(str_replace("-", "", $_REQUEST["month"]));
$_fname = $_REQUEST["fname"];

if ($_hid > 0) {
	$h_name = $db->query("select name from hospital where id={$_hid} limit 1", 1, "name");
}


if ($_hid > 0 && $_month > 0) {
	$res = $db->query("select * from jiuzhen_report where hid={$_hid} and month={$_month} and sub_id=$sub_id limit 1", 1);
	$line = @unserialize($res["config"]);
} else {
	exit("����������...");
}

$fname_arr = array(
	"fuzeren" => "������",
	"h_jiuzhen" => "������",
	"h_wangcha" => "����",
	"h_renjun" => "�˾��ɱ�",
	"dabiaozhishu1" => "���ָ��",
	"dabiaozhishu2" => "���ָ��2",
	"dabiaozhishu3" => "���ָ��3",
	"jianglijishu1" => "��������",
	"jianglijishu2" => "��������2",
	"jianglijishu3" => "��������3",
	"jianglizhibiao1" => "����ָ��",
	"jianglizhibiao2" => "����ָ��2",
	"jianglizhibiao3" => "����ָ��3",
	"mubiao1" => "Ŀ��",
	"mubiao2" => "Ŀ��2",
	"mubiao3" => "Ŀ��3",
);

if (!array_key_exists($_fname, $fname_arr)) {
	exit("��֧�ֱ༭���ֶ�: $_fname ");
}

$title = $h_name." - ".$_REQUEST["month"]." - ".$fname_arr[$_fname]." - ".$sub_name;


if ($_POST) {
	ob_start();

	$line[$_fname] = $value = _safe_word($_POST["value"]);
	$str = serialize($line);

	if ($res["hid"] > 0) {
		$db->query("update $table set config='{$str}' where hid='{$_hid}' and month='{$_month}' and sub_id='{$sub_id}' limit 1");
	} else {
		$db->query("insert into $table set hid='{$_hid}', month='{$_month}', sub_id='{$sub_id}', config='{$str}'");
	}

	$err = ob_get_clean();
	if ($err == '') {
		$id = $_hid."_".str_replace("-", "", $_month)."_".$_fname;
		if ($value == '') {
			$value = "���";
		}
		echo '<script> parent.update_content_byid("'.$id.'", "'.$value.'", "innerHTML"); </script>';
		echo '<script> parent.load_src(0); </script>';
		echo '<script> parent.msg_box("�����ύ�ɹ�", 2); </script>';
	} else {
		echo "�ύʧ�ܣ�����ϵ����Ա��飺<br>".$db->sql."<br><br><br>";
	}
	exit;
}




?>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script type="text/javascript">
function check_data() {
	return true;
}

function write_s_data(s) {
	byid(s).value = byid("s_"+s).innerHTML;
}
</script>
</head>

<body>
<form name="mainform" action="" method="POST" onsubmit="return check_data()">
<table width="100%" class="edit">
	<tr>
		<td class="left" style="width:35%;"><?php echo $fname_arr[$_fname]; ?>��</td>
		<td class="right"><input name="value" id="input1" value="<?php echo $line[$_fname]; ?>" class="input" style="width:100px"></td>
	</tr>
</table>

<input type="hidden" name="hid" value="<?php echo $_hid; ?>">
<input type="hidden" name="month" value="<?php echo $_month; ?>">
<input type="hidden" name="fname" value="<?php echo $_fname; ?>">
<div class="button_line"><input id="submit_button" type="submit" class="submit" value="�ύ����"></div>

</form>

<script type="text/javascript">
byid("input1").focus();
</script>

</body>
</html>