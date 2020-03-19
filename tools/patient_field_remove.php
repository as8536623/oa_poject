<?php
//
// patient表移除字段工具 @ 2012-07-16
//
header("Content-Type:text/html;charset=gbk");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1970 05:00:00 GMT");

require "../core/core.php";
if (!$debug_mode) {
	exit_html("无执行权限...");
}

set_time_limit(0);

// 要移除的字段
$remove = array();
$remove[] = "order_date_changes";
$remove[] = "order_date_log";
$remove[] = "come_date";
$remove[] = "xiaofei";
$remove[] = "rechecktime";
$remove[] = "fee";


// 读取需更新的每家医院:
$hids = $db->query("select id from hospital", "", "id");

echo "正在处理，请稍候...".str_repeat("&nbsp;", 50)."<br>";
flush();
ob_flush();
ob_end_flush();

// 要处理的表:
$table_names = array();
$table_names[] = "patient";
foreach ($hids as $hid) {
	$table_names[] = "patient_".$hid;
	$table_names[] = "patient_".$hid."_history";
}

// 处理:
foreach ($table_names as $table_name) {
	if (table_exists($table_name, $db->dblink)) {
		foreach ($remove as $f) {
			if (!empty($f)) {
				if (field_exists($f, $table_name, $db->dblink)) {
					$sql = "ALTER TABLE `".$table_name."` DROP `".$f."`";
					$db->query($sql);
					echo "表 ".$table_name." 移除字段 ".$f."<br>";
				}
			}
		}
	}

	flush();
	ob_flush();
	ob_end_flush();
	usleep(500*1000); //0.5s
}

echo "<br>全部完成。";
exit;


?>