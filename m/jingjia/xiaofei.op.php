<?php
/*
// 作者: 幽兰 (weelia@126.com)
// 时间: 2011-07-23 23:26
*/
if (!defined("ROOT")) {
	exit("无权限操作..."); //检测是否为包含页面
}

if ($op == "add") {
	if ($_GET["date"]) {
		$date = str_replace("-", "", $_GET["date"]);
	} else {
		$date = date("Ymd");
	}
	$line = $db->query("select * from $table where hid=$hid and date=".$date." limit 1", 1);
	include "xiaofei.add.php";
	exit;
}

if ($op == "add_submit") {

	$date = str_replace("-", "", $_POST["date"]);

	// 检查数据是否已经添加过了:
	$old = $db->query("select * from $table where hid=$hid and date=$date limit 1", 1);
	if ($old["id"] > 0) {
		$op_id = $old["id"];
		// 更新:
		$r = array();
		$log_arr = array();
		foreach ($_POST["xiaofei"] as $k => $v) {
			if (in_array($k, $h_field_arr)) { //必须是当前医院允许的字段(未设置采用全局)
				if ($old[$k] != $v) { //数据是否有修改
					$r[$k] = floatval($v);
					$log_arr[] = $all_field_arr[$k]." 由 ".$old[$k]." 修改为 ".floatval($v);
				}
			}
		}
		if (count($log_arr) > 0) {
			$r["log"] = $old["log"].date("Y-m-d H:i:s")." ".$realname.": ".implode("、", $log_arr)."<br>";
		}
		if (count($r) > 0) {
			$sqldata = $db->sqljoin($r);
			$db->query("update $table set $sqldata where id=$op_id limit 1");
		} else {
			// 数据无修改，不处理
		}

	} else {
		$r = array();
		$r["hid"] = $hid;
		$r["h_name"] = $h_name;
		$r["date"] = $date;
		foreach ($_POST["xiaofei"] as $k => $v) {
			if (in_array($k, $h_field_arr)) { //必须是当前医院允许的字段(未设置采用全局)
				$r[$k] = $v;
			}
		}
		$r["uid"] = $uid;
		$r["u_name"] = $realname;
		$r["addtime"] = time();
		$sqldata = $db->sqljoin($r);
		$op_id = $db->query("insert into $table set $sqldata");
	}

	// 更新统计数据:
	$db->query("update $table set xiaofei=x1+x2+x3+x4+x5+x6+x7 where id=$op_id limit 1");

	echo '<script type="text/javascript">';
	echo 'parent.update_content();';
	echo 'parent.load_box(0);';
	echo 'parent.msg_box("数据保存成功");';
	echo '</script>';
	exit;
}

if ($op == "edit") {
	$line = $db->query("select * from $table where id=$id limit 1", 1);
	include "xiaofei.edit.php";
	exit;
}

if ($op == "edit_submit") {
	if (empty($id)) exit_html("参数错误!");
	$old = $db->query("select * from $table where id=$id limit 1", 1);
	if ($old) {
		$r = array();
		$log_arr = array();
		foreach ($_POST["xiaofei"] as $k => $v) {
			if (in_array($k, $h_field_arr)) { //必须是当前医院允许的字段(未设置采用全局)
				if ($old[$k] != $v) { //数据是否有修改
					$r[$k] = floatval($v);
					$log_arr[] = $all_field_arr[$k]." 由 ".$old[$k]." 修改为 ".floatval($v);
				}
			}
		}
		if (count($log_arr) > 0) {
			$r["log"] = $old["log"].date("Y-m-d H:i:s")." ".$realname.": ".implode("、", $log_arr)."<br>";
		}
		if (count($r) > 0) {
			$sqldata = $db->sqljoin($r);
			$db->query("update $table set $sqldata where id=$id limit 1");

			// 更新统计数据:
			$db->query("update $table set xiaofei=x1+x2+x3+x4+x5+x6+x7 where id=$id limit 1");

			echo '<script type="text/javascript">';
			echo 'parent.update_content();';
			echo 'parent.load_box(0);';
			echo 'parent.msg_box("数据保存成功");';
			echo '</script>';
		} else {
			// 数据无修改，不处理
			echo '<script type="text/javascript">';
			echo 'parent.load_box(0);';
			echo 'parent.msg_box("数据无改动");';
			echo '</script>';
		}
	} else {
		exit_html("读取数据出错.请联系开发人员.");
	}
	exit;
}

if ($op == "log") {
	if (empty($id)) exit_html("参数错误!");
	$line = $db->query("select * from $table where id=$id limit 1", 1);
	include "xiaofei.log.php";
	exit;
}

if ($op == "delete") {
	exit_html("暂时不能删除.");
}


?>