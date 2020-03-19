<?php
/* --------------------------------------------------------
// 幽兰 @ 2012-07-15
// ----------------------------------------------------- */
set_time_limit(0);
include "../core/core.php";



$user_list = $db->query("select * from sys_admin", "id");

foreach ($user_list as $_uid => $_uinfo) {
	if ($uinfo["name"] == "admin") {
		continue;
	}

	if ($_uinfo["part_id"] == 3) {
		$cur_config = @explode(",", $_uinfo["guahao_config"]);
		if (!in_array("huifang", $cur_config)) {
			$cur_config[] = "huifang";
			$guahao_config_str = implode(",", $cur_config);
			$db->query("update sys_admin set guahao_config='$guahao_config_str' where id=$_uid limit 1");
			echo $_uinfo["realname"]." : ".$guahao_config_str."<br>";
		}
	}
}

echo "<br><br>全部完成！";





?>