<?php
//
// patient���Ƴ��ֶι��� @ 2012-07-16
//
header("Content-Type:text/html;charset=gbk");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1970 05:00:00 GMT");

require "../core/core.php";
if (!$debug_mode) {
	exit_html("��ִ��Ȩ��...");
}

set_time_limit(0);

// Ҫ�Ƴ����ֶ�
$remove = array();
$remove[] = "order_date_changes";
$remove[] = "order_date_log";
$remove[] = "come_date";
$remove[] = "xiaofei";
$remove[] = "rechecktime";
$remove[] = "fee";


// ��ȡ����µ�ÿ��ҽԺ:
$hids = $db->query("select id from hospital", "", "id");

echo "���ڴ������Ժ�...".str_repeat("&nbsp;", 50)."<br>";
flush();
ob_flush();
ob_end_flush();

// Ҫ����ı�:
$table_names = array();
$table_names[] = "patient";
foreach ($hids as $hid) {
	$table_names[] = "patient_".$hid;
	$table_names[] = "patient_".$hid."_history";
}

// ����:
foreach ($table_names as $table_name) {
	if (table_exists($table_name, $db->dblink)) {
		foreach ($remove as $f) {
			if (!empty($f)) {
				if (field_exists($f, $table_name, $db->dblink)) {
					$sql = "ALTER TABLE `".$table_name."` DROP `".$f."`";
					$db->query($sql);
					echo "�� ".$table_name." �Ƴ��ֶ� ".$f."<br>";
				}
			}
		}
	}

	flush();
	ob_flush();
	ob_end_flush();
	usleep(500*1000); //0.5s
}

echo "<br>ȫ����ɡ�";
exit;


?>