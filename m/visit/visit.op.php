<?php
/*
// 说明: visit.do.php
// 作者: 幽兰 (weelia@126.com)
// 时间: 2010-07-07
*/

if ($op == "list") {
	include $mod.".list.php";
	exit;
}


// 添加和修改:
if ($op == "add" || $op == "edit") {

	// post:
	if ($_POST) {
		$r = array();

		if ($op == "add") {
			$r["hid"] = $hid;
			$r["h_name"] = $hinfo["name"];
			$r["date"] = $date = $_POST["date"];
			$r["site_id"] = $_POST["site_id"];
			$r["site_url"] = $db->query("select url from sites where id=".intval($_POST["site_id"])." limit 1", 1, "url");
		}

		// 检查日期是否重复
		if (strlen($date) == 8) {
			if ($op == "add") {
				$repeat = $db->query("select count(*) as c from $table where hid=$hid and site_id=".$r["site_id"]." and date='$date'", 1, "c");
			} else {
				$repeat = $db->query("select count(*) as c from $table where id!=$id and date='$date'", 1, "c");
			}
			if ($repeat > 0) {
				msg_box("对不起，日期有重复，请修改", "back", 1);
			}
		} else {
			msg_box("日期格式不正确", "back", 1);
		}


		$r["ip"] = $_POST["ip"];
		$r["ip_local"] = $_POST["ip_local"];
		$r["ip_other"] = $_POST["ip_other"];

		$r["pv"] = $_POST["pv"];
		$r["pv_local"] = $_POST["pv_local"];
		$r["pv_other"] = $_POST["pv_other"];

		$r["click"] = $_POST["click"];
		$r["click_local"] = $_POST["click_local"];
		$r["click_other"] = $_POST["click_other"];

		$r["ok_click"] = $_POST["ok_click"];
		$r["ok_click_local"] = $_POST["ok_click_local"];
		$r["ok_click_other"] = $_POST["ok_click_other"];

		$r["zero_talk"] = $_POST["zero_talk"];

		$engine_names = explode("|", $_POST["engine_names"]);
		if (count($engine_names) > 0) {
			$eng = array();
			foreach ($engine_names as $n) {
				if ($_POST["engine_".$n."_all"] || $_POST["engine_".$n."_local"] || $_POST["engine_".$n."_other"]) {
					$eng[$n]["all"] = $_POST["engine_".$n."_all"];
					$eng[$n]["local"] = $_POST["engine_".$n."_local"];
					$eng[$n]["other"] = $_POST["engine_".$n."_other"];
				}
			}
			$r["engine"] = serialize($eng);
		}

		if ($op == "add") {
			$r["addtime"] = time();
			$r["uid"] = $uid;
			$r["u_realname"] = $realname;
		}


		$_GET["back_url"] = base64_decode($_POST["back_url"]);

		$sqldata = $db->sqljoin($r);
		if ($op == "add") {
			$sql = "insert into $table set $sqldata";
		} else {
			$sql = "update $table set $sqldata where id='$id' limit 1";
		}

		if ($db->query($sql)) {
			msg_box("资料提交成功！", "?date=".$date, 1);
		} else {
			msg_box("资料提交失败，系统繁忙，请稍后再试。", "back", 1, 5);
		}
	}
	// end of post

	// load page to edit:
	if ($op == "edit") {
		$line = $db->query("select * from $table where id=$id limit 1", 1);
	}

	if ($op == "add") {
		$date = $_GET["date"];
		$site_id = intval($_GET["site_id"]);
		if (!$site_id || !$date) {
			exit_html("参数错误");
		}
	}

	$sinfo = $db->query("select * from sites where id=$site_id limit 1", 1);
	$sconfig = (array) @unserialize($sinfo["config"]);

	include $mod.".edit.php";
	exit;
}


if ($op == "view") {
	$title = "查看资料";
	include $mod.".view.php";

	exit;
}


if ($op == "delete") {
	$ids = explode(",", $_GET["id"]);
	$del_ok = $del_bad = 0; $op_data = array();
	foreach ($ids as $opid) {
		if (($opid = intval($opid)) > 0) {
			$tmp_data = $db->query_first("select * from $table where id='$opid' limit 1");
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

if ($op == "setshow") {
	$isshow_value = intval($_GET["value"]) > 0 ? 1 : 0;
	$ids = explode(",", $_GET["id"]);
	$set_ok = $set_bad = 0;
	foreach ($ids as $opid) {
		if (($opid = intval($opid)) > 0) {
			if ($db->query("update $table set isshow='$isshow_value' where id='$opid' limit 1")) {
				$set_ok++;
			} else {
				$set_bad++;
			}
		}
	}

	if ($set_bad > 0) {
		msg_box("操作成功完成 $set_ok 条，失败 $del_bad 条。", "back", 1);
	} else {
		msg_box("操作成功", "back", 1, 1);
	}
}

?>