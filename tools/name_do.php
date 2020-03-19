<?php
/* --------------------------------------------------------
// 说明: 处理导数据的接待客服名字问题
// 作者: 幽兰 (weelia@126.com)
// 时间: 2009-05-12 22:06
// ----------------------------------------------------- */
include "lib/mysql.php";
$db = new mysql();

$table = "patient_6";  //表名

$admins = $db->query("select name,realname from sys_admin", "name", "realname");

$list = $db->query("select id, jiedai, author from $table");
foreach ($list as $li) {
	$id = $li["id"];
	if (!array_key_exists($li["author"], $admins)) {
		$newname = $li["jiedai"];
		$db->query("update $table set jiedai='' where id=$id limit 1");
	} else {
		$newname = $admins[$li["author"]];
	}
	if ($newname != '') {
		$db->query("update $table set author='$newname' where id=$id limit 1");
	}
}

echo "done...".count($list);

?>