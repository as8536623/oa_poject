<?php
/*
// 说明: sys_admin.op.php
// 作者: 幽兰 (weelia@126.com)
// 时间: 2010-10-16 13:39
*/
if (!defined("ROOT")) exit("Error.");

if (!$debug_mode) {
	//exit("功能调整中。。。");
}

// 添加和修改:
if ($op == "add" || $op == "edit") {

	if ($id > 0) {
		$line = $db->query("select * from sys_admin where id='$id' limit 1", 1);
	} else {
		$line = array();
	}

	// post:
	if ($_POST) {
		// 用户名和密码:
		$r = array();
		$name = $_POST["name"];
		if ($op == "add" && $db->query("select count(*) as count from $table where name='$name'", 1, "count") > 0) {
			exit("该帐户名称($name)已经有人使用，请尝试其他名称！");
		}

		if ($op == "add") {
			$r["name"] = $name;
		}

		if ($op == "add") {
			if (!$_POST["realname"]) {
				exit("真实姓名不能为空，请填写！");
			}
			$r["realname"] = $_POST["realname"];
		}

		if ($_POST["pass"]) {
			$r["pass"] = md5($_POST["pass"]);
		}

		// 用户权限:
		$ch_id = intval($_POST["character_id"]);
		if ($ch_id == 0) {
			exit_html("用户权限必须设置！");
		}
		if ($ch_id == -1) {
			if (empty($_SESSION["global_user_menu"])) {
				exit("对不起，“自定义权限”必须设置，否则该帐号不能正常使用。");
			}
			$r["powermode"] = 1;
			$r["menu"] = $_SESSION["global_user_menu"];
			$r["character_id"] = 0;
		} else {
			$r["powermode"] = 2;
			$r["menu"] = "";
			$r["character_id"] = $ch_id;
		}

		$r["guahao_config"] = @implode(",", $_POST["guahao_config"]);

		// 医院和部门:
		if (isset($_POST["hospital_ids"])) {
			asort($_POST["hospital_ids"]);
			$r["hospitals"] = implode(",", $_POST["hospital_ids"]);
		} else {
			//$r["hospitals"] = $uinfo["hospitals"];
			$r["hospitals"] = '';
		}

		$r["part_id"] = $_POST["part_id"];
		$r["part_admin"] = $_POST["part_admin"] ? 1 : 0; //2009-05-19 10:29 是否部门管理员
		//$r["part_manage"] = implode(",", $_POST["part_manage"]);
		$r["data_power"] = @implode(",", $_POST["data_power"]);

		if ($debug_mode || $username == "admin" || $realname == "黄开章" || $realname == "陈文滔") {
			$r["jiuzhen_view"] = intval($_POST["jiuzhen_view"]) ? 1 : 0;
			$r["jiuzhen_edit"] = intval($_POST["jiuzhen_edit"]) ? 1 : 0;

			$r["show_tel"] = intval($_POST["show_tel"]);
			$r["show_list"] = intval($_POST["show_list"]);
			//$r["show_weixin"] = intval($_POST["show_weixin"]);
			//$r["show_shouji"] = intval($_POST["show_shouji"]);

			// 2011-06-23 ukey
			$r["use_ukey"] = $_POST["use_ukey"] ? "1" : "0";
			$r["ukey_sn"] = $_POST["ukey_sn"];
			$r["ukey_no"] = $_POST["ukey_no"];

			// 2012-04-10 手机挂号系统
			$r["allow_mobile_login"] = $_POST["allow_mobile_login"] ? 1 : 0;

		} else {
			// 非管理员开帐号，uk默认选中状态
			if ($op == "add") {
				$r["use_ukey"] = "1";
			}
		}

		if ($debug_mode || $username == "admin" || $realname == "黄开章" || $uinfo["character_id"] == 61) {
			// 2013-7-23 显示聊天记录
			$r["show_talk"] = $_POST["show_talk"] ? 1 : 0;

			// 2013-11-8 工作日志
			$r["worklog"] = @implode(",", $_POST["worklog"]);
		}


		if ($op == "add") {
			$r["addtime"] = time();
			$r["author"] = $username;
		}


		$_GET["back_url"] = base64_decode($_POST["back_url"]);

		$sqldata = $db->sqljoin($r);
		if ($op == "edit") {
			$sql = "update $table set $sqldata where id='$id' limit 1";
		} else {
			$sql = "insert into $table set $sqldata";
		}

		if ($db->query($sql)) {

			// 记录医院调整日志 @ 2012-11-24:
			if ($op == "edit" && $r["hospitals"] != $line["hospitals"]) {
				$h_id_name = $db->query("select id,name from hospital", "id", "name");
				$h_from = explode(",", $line["hospitals"]);
				$h_to = explode(",", $r["hospitals"]);
				// 判断增加的医院:
				$h_add = $h_remove = array();
				foreach ($h_to as $v) {
					if (!@in_array($v, $h_from)) {
						$h_add[] = @array_key_exists($v, $h_id_name) ? $h_id_name[$v] : $v;
					}
				}
				// 移去的:
				foreach ($h_from as $v) {
					if (!@in_array($v, $h_to)) {
						$h_remove[] = @array_key_exists($v, $h_id_name) ? $h_id_name[$v] : $v;
					}
				}
				$log = '';
				if (count($h_add) > 0) {
					$log .= '增加医院['.implode("、", $h_add)."]\n";
				}
				if (count($h_remove) > 0) {
					$log .= '移去医院['.implode("、", $h_remove).']\n';
				}
				$log = trim($log);
				$user_id = $line["id"];
				$user_name = $line["realname"];
				$author = $realname;
				$addtime = time();
				$db->query("insert into sys_hospital_log set user_id='$user_id', user_name='$user_name', logs='$log', author='$author', addtime='$addtime'");
			}

			if ($op == "add" || ($op == "edit" && $line["part_id"] != $_POST["part_id"]) ) {
				echo '<script> parent.update_content(); </script>';
			}
			echo '<script> parent.msg_box("资料提交成功", 2); </script>';
			echo '<script> parent.load_src(0); </script>';
		} else {
			echo "资料提交失败，系统繁忙，请稍后再试。";
		}
		exit;
	}
	// end of post

	$hospital_list = $db->query("select id,name from hospital where id in ($hospitals)", "id");
	$power->init_ch_data();
	$ch_data = $power->ch_data;
	foreach ($ch_data as $k => $v) {
		if ($power->compare_power($v["menu"], $usermenu) > 0) {
			unset($ch_data[$k]);
		}
	}


	if ($op == "add") {
		$title = "添加人员";
	} else {
		$title = "修改资料";

		// 检查修改权限:
		// 1. 检查医院修改权限:
		$uh = explode(",", trim($line["hospitals"], ","));
		foreach ($uh as $uhid) {
			if ($uhid > 0 && !in_array($uhid, $hospital_ids)) {
				exit("修改权限不够，医院超出您的管理范围。");
			}
		}
		// 2. 检查部门:
		$my_parts = array_keys($part->get_sub_part(intval($uinfo["part_id"]), 1));
		if ($line["part_id"] > 0 && !in_array($line["part_id"], $my_parts)) {
			exit("修改权限不够，部门超出您的管理范围。");
		}

		// 3. 检查管理部门:
		/*
		$part_manages = explode(",", trim($line["part_manage"], ','));
		if ($part_manages) {
			foreach ($part_manages as $_pid) {
				if ($_pid > 0 && !in_array($_pid, $my_parts)) {
					exit("修改权限不够，管理部门超出您的管理范围。");
				}
			}
		}
		*/

		// 4. 功能权限:
		if ($line["power_mode"] == 1) {
			$line_power = $line["menu"];
		} else {
			$line_power = $db->query("select menu from sys_character where id=".$line["character_id"]." limit 1", 1, "menu");
		}
		if ($power->compare_power($line_power, $usermenu) > 0) {
			exit("修改权限不够，权限超出您的管理范围。");
		}

	}

	include "admin.edit.php";
	exit;
}


if ($op == "view" || $op == "viewweb") {
	if ($nID = $_GET["id"]) {
		$line = $db->query("select * from $table where id='$nID' limit 1", 1);
	} else {
		if ($admin_name = $_GET["name"]) {
			if (!$line = $db->query("select * from $table where name='$admin_name' limit 1", 1)) {
				msg_box("系统无此用户: {$admin_name}", "back", 1);
			}
		} else {
			msg_box("参数错误...", "back", 1);
		}
	}

	$hospital_id_name = $db->query("select id,name from hospital", "id", "name");

	if ($line["name"] == "admin") {
		$line["hs_str"] = '<font color="gray">(所有医院)</font>';
	} else {
		$hs = explode(",", $line["hospitals"]);
		$show_hs = array();
		foreach ($hs as $v) {
			$show_hs[] = $hospital_id_name[$v];
		}
		$line["hs_str"] = implode(" | ", $show_hs);
	}

	$title = "查看管理员资料";
	if ($op == "view") {
		include "admin.view.php";
	} else {
		include "admin.view.web.php";
	}
	exit;
}

if ($op == "delete") {
	$ids = $_POST["uid"];
	$del_ok = $del_bad = 0; $op_data = array();
	foreach ($ids as $opid) {
		if (($opid = intval($opid)) > 0) {
			$tmp_data = $db->query("select * from $table where id='$opid' limit 1", 1);
			if ($db->query("delete from $table where id='$opid' limit 1")) {
				$del_ok++;
				$op_data[] = $tmp_data;
			} else {
				$del_bad++;
			}
		}
	}

	if ($del_ok > 0) {
		$log->add("delete", "删除数据", serialize($op_data));
	}

	if ($del_bad > 0) {
		msg_box("删除成功 $del_ok 条资料，删除失败 $del_bad 条资料。", "back", 1);
	} else {
		msg_box("删除成功", "back", 1);
	}
}

if ($op == "open" || $op == "close") {
	$isshow_value = ($op == "open" ? 1 : 0);
	$ids = $_POST["uid"];
	if (count($ids) > 0) {
		$ok_ids = array();
		foreach ($ids as $opid) {
			$opid = intval($opid);
			if ($opid > 0) {
				$ok_ids[] = $opid;
			}
		}
		if (count($ok_ids) > 0) {
			$db->query("update sys_admin set isshow='$isshow_value' where id in (".@implode(",", $ok_ids).")");

			if ($op == "close") {
				// 解除uk绑定 @ 2012-11-27:
				$db->query("update sys_admin set ukey_sn='' where id in (".@implode(",", $ok_ids).")");

				$_name_arr = $db->query("select realname from sys_admin where id in (".@implode(",", $ok_ids).")", "", "realname");
				$log->add("close", "禁用帐号[".@implode(",", $_name_arr)."]", "");
			}
			msg_box("操作成功", "back", 1, 1);
		} else {
			exit("提交数据异常，请稍后再试...");
		}
	} else {
		exit("没有选择人员");
	}
}

if ($op == "change_group_type") {
	$cur_group = $_SESSION["admin_group_type"] = intval($_GET["group"]);
}

if ($op == "set_ch") {
	$uids = $_POST["uid"];
	$new_ch = intval($_POST["ch_id"]);
	if (@count($uids) > 0 && $new_ch > 0) {
		foreach ($uids as $v) {
			$db->query("update sys_admin set powermode=2, character_id='$new_ch' where id='$v' limit 1");
		}
		msg_box("设置成功", "?refresh", 1);
	} else {
		exit("参数错误");
	}
}

?>