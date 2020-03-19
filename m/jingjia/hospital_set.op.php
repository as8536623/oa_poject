<?php
/*
// 说明: 操作
// 作者: 幽兰 (weelia@126.com)
// 时间: 2011-07-25
*/

if ($op == "setfield") {
	// 所有可用字段:
	$all_field_arr = $db->query("select fieldname, name from jingjia_field_set order by fieldname asc", "fieldname", "name");

	// 读取当前设置:
	$set_hid = intval($_GET["hid"]);
	if ($set_hid > 0) {
		$cur_field = $db->query("select fields from jingjia_hospital_set where hid=$set_hid limit 1", 1, "fields");
		$cur_field_arr = array();
		if ($cur_field != '') {
			$cur_field_arr = explode(",", $cur_field);
		}
	}
	include "hospital_set.setfield.php";
	exit;
}

if ($op == "setfield_submit") {
	// 要设置的医院ID:
	$set_hid = intval($_POST["set_hid"]);

	// 设置字段(表单提交为数组):
	if (!is_array($_POST["field_set"]) || count($_POST["field_set"]) == 0) {
		$new_fields = array();
	} else {
		$new_fields = $_POST["field_set"];
	}

	// 删除可能的空字段
	foreach ($new_fields as $k => $v) {
		if ($v == '') {
			unset($new_fields[$k]);
		}
	}
	$new_field = implode(",", $new_fields);

	// 查询是否已经有记录，有则更新，无则插入
	$set_id = $db->query("select id from jingjia_hospital_set where hid=$set_hid limit 1", 1, "id");
	if ($set_id > 0) {
		$db->query("update jingjia_hospital_set set fields='".$new_field."' where id=$set_id limit 1");
	} else {
		$h_name = $db->query("select name from hospital where id=$set_hid limit 1", 1, "name");
		$db->query("insert into jingjia_hospital_set set hid=$set_hid, h_name='".$h_name."', fields='".$new_field."', addtime=".time().", author='".$realname."'");
	}

	// 弹出框输出处理:
	echo '<script type="text/javascript">';
	echo 'parent.update_content();';
	echo 'parent.load_box(0);';
	echo 'parent.msg_box("数据保存成功");';
	echo '</script>';
	exit;
}

if ($op == "setuser") {
	// 医院
	$set_hid = intval($_GET["hid"]);

	// 查询当前录入人员:
	$cur_users = $db->query("select uid,u_name,fields from jingjia_user_set where hid=$set_hid order by u_name asc", "uid");

	// 查询所有可能的录入人员(系统：人员管理，里面添加为竞价部门，且医院是勾了这家医院的)
	$all_users = $db->query("select id, realname from sys_admin where part_id=202 and concat(',',hospitals,',') like '%,".$set_hid.",%' order by realname asc", "id", "realname");

	// 系统所有可用搜索引擎:
	$field_name_arr = $db->query("select fieldname, name from jingjia_field_set order by fieldname asc", "fieldname", "name");

	// 当前医院所有可用搜索引擎:
	$hospital_field = $db->query("select fields from jingjia_hospital_set where hid=$set_hid limit 1", 1, "fields");
	if (!empty($hospital_field)) {
		$hospital_field_arr = explode(",", $hospital_field);
	} else {
		$hospital_field_arr = array_keys($field_name_arr); //未设置，使用系统所有可用搜索引擎
	}

	include "hospital_set.setuser.php";
	exit;
}


if ($op == "setuser_submit") {
	// 处理提交数据:
	$set_hid = intval($_POST["set_hid"]);
	if ($set_hid == 0) exit_html("参数错误...");

	// 将该医院所有人员字段设置为空:
	$db->query("update jingjia_user_set set fields='' where hid=$set_hid ");

	// 然后依据提交表格重新设置：
	foreach ($_POST["user_field_set"] as $_uid => $_arr) {
		$_uname = $db->query("select realname from sys_admin where id=$_uid limit 1", 1, "realname");
		$field_str = implode(",", $_arr);
		// 有则修改，无则添加:
		$set_id = $db->query("select id from jingjia_user_set where hid=$set_hid and uid=$_uid limit 1", 1, "id");
		if ($set_id > 0) {
			$db->query("update jingjia_user_set set fields='".$field_str."' where id=$set_id limit 1");
		} else {
			$db->query("insert into jingjia_user_set set hid=$set_hid, uid=$_uid, u_name='".$_uname."', fields='".$field_str."', addtime=".time().", author='".$realname."'");
		}
	}

	// 删除没有记录的同志：
	$db->query("delete from jingjia_user_set where hid=$set_hid and fields=''");

	// 弹出框输出处理:
	echo '<script type="text/javascript">';
	echo 'parent.update_content();';
	echo 'parent.load_box(0);';
	echo 'parent.msg_box("录入人员设置成功");';
	echo '</script>';
	exit;
}


?>