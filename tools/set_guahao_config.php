<?php
/* --------------------------------------------------------
// 幽兰 @ 2012-07-15
// ----------------------------------------------------- */
set_time_limit(0);
include "../core/core.php";


// 解析权限:
$ch_arr = $db->query("select id,menu from sys_character", "id", "menu");
foreach ($ch_arr as $k => $v) {
	$v = base64_decode($v);
	$v = unserialize($v);
	$v = $v["power"];
	$ch_arr[$k] = $v;
}

$user_list = $db->query("select * from sys_admin", "id");

foreach ($user_list as $_uid => $_uinfo) {
	if ($uinfo["name"] == "admin") {
		continue;
	}

	$ch_id = $_uinfo["character_id"];
	$menu = $ch_arr[$ch_id];

	$guahao_config = $data_power = array();

	if (array_key_exists(57, $menu)) {
		$p = $menu[57];
		if (in_array("add", $p)) {
			$guahao_config[] = "patient_add";
		}
		if (in_array("edit", $p)) {
			$guahao_config[] = "patient_edit";
		}
		if (in_array("delete", $p)) {
			$guahao_config[] = "patient_delete";
		}

		if ($_uinfo["part_id"] == 4) {
			$guahao_config[] = "set_come";
		}

		if ($_uinfo["part_id"] == 12 && $_uinfo["part_admin"] > 0) {
			$guahao_config[] = "set_huifang_kf";
		}

		if ($_uinfo["part_id"] == 12) {
			$guahao_config[] = "huifang";
		}

		if ($_uinfo["show_tel"] > 0 && $_uinfo["part_id"] != 4) { //默认导医不给showtel权限
			$guahao_config[] = "show_tel";
		}

	}


	$all = $web = $tel = 0;

	$data_manage = @explode(",", $_uinfo["part_manage"]);
	if (count($data_manage) > 2) {
		$all = 1;
		$web = 1;
		$tel = 1;
	}

	if (in_array($_uinfo["part_id"], array(1,9))  || in_array(1, $data_manage) || in_array(9, $data_manage) ) {
		$all = 1;
		$web = 1;
		$tel = 1;
	}

	if (in_array(2, $data_manage)) {
		$web = 1;
	}

	if (in_array(3, $data_manage)) {
		$tel = 1;
	}

	if ($_uinfo["part_id"] == 4) {
		$all = 1;
		$web = 1;
		$tel = 1;
	}


	if ($all == 1) {
		$data_power[] = "all";
	}

	if ($web == 1) {
		$data_power[] = "web";
	}

	if ($tel == 1) {
		$data_power[] = "tel";
	}

	$guahao_config_str = implode(",", $guahao_config);
	$data_power_str = implode(",", $data_power);

	$db->query("update sys_admin set guahao_config='$guahao_config_str', data_power='$data_power_str' where id=$_uid limit 1");

	echo $_uinfo["username"]." - ".$_uinfo["realname"]." : ".$guahao_config_str." : ".$data_power_str."<br>";

}

echo "<br><br>全部完成！";





?>