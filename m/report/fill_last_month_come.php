<?php
/*
// ˵��: �Զ���䵽������
// ����: ���� (weelia@126.com)
// ʱ��: 2013-12-31
*/
require "../../core/core.php";
$table = "jiuzhen_report";
set_time_limit(0);

$month_arr = array();

$dt = strtotime(date("Y-m")."-01");

for ($i = 0; $i <= 10; $i++) {
	$month_arr[] = date("Y-m", strtotime("-".$i." month", $dt));
}

if ($_POST) {
	$hid_arr = $db->query("select id,name from hospital order by id asc", "id", "name");

	$is_fugai = $_POST["skip_copy_if_set"] ? 0 : 1;

	$month = intval(str_replace("-", "", $_POST["month"]));
	$tb = strtotime($_POST["month"]."-01 0:0:0");
	$te = strtotime("+1 month", $tb) - 1;

	if (strlen($month) != 6) {
		exit("�·����ô������������ã�");
	}

	foreach ($hid_arr as $_hid => $_hname) {
		$tip = $_hname." ";
		$old = $db->query("select * from jiuzhen_report where hid={$_hid} and month={$month} limit 1", 1);

		$come = $db->query("select count(*) as c from patient_{$_hid} where part_id=2 and status=1 and order_date>=$tb and order_date<=$te", 1, "c");

		$old_arr = @unserialize($old["config"]);
		$new_arr = $old_arr;

		if ($new_arr["h_jiuzhen"] != '') {
			if ($is_fugai && $old_arr["h_jiuzhen"] != '') {
				$old_value = $old_arr["h_jiuzhen"];
				$new_arr["h_jiuzhen"] = $come;
				$tip .= "��ʷ������[".$old_value."]����Ϊ[".$come."] ";
			} else {
				$tip .= "��ʷ����[".$old_arr["h_jiuzhen"]."]��Ϊ�ղ����� ";
			}
		} else {
			$new_arr["h_jiuzhen"] = $come;
			$tip .= "��ʷ�������Ϊ[".$come."] ";
		}

		$new_str = @serialize($new_arr);
		if ($old["hid"] > 0) {
			$db->query("update jiuzhen_report set config='$new_str' where hid='$_hid' and month='$month' limit 1");
		} else {
			$db->query("insert into jiuzhen_report set hid='$_hid', month='$month', sub_id=0, config='$new_str'");
		}

		echo $tip."<br><br>";
	}

	echo "<br>ȫ����ɣ�";
	exit;
}

?>
<html>
<head>
<title>��������</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
</head>

<body>
<form method="POST">
����·ݣ�
<select class="combo" name="month">
	<option value="" style="color:gray">-�·�-</option>
	<?php echo list_option($month_arr, "_value_", "_value_"); ?>
</select>
<br>
<br>
<input type="checkbox" name="skip_copy_if_set" id="skip_copy_if_set" checked><label for="skip_copy_if_set">���Ŀ���·��������ݣ��򲻸���</label>&nbsp;
<br>
<br>
<input type="submit" class="submit" value="��ʼ����">


</form>

</body>
</html>