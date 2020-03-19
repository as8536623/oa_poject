<?php
/*
// ˵��: ����
// ����: ���� (weelia@126.com)
// ʱ��: 2011-07-25
*/

if ($op == "setfield") {
	// ���п����ֶ�:
	$all_field_arr = $db->query("select fieldname, name from jingjia_field_set order by fieldname asc", "fieldname", "name");

	// ��ȡ��ǰ����:
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
	// Ҫ���õ�ҽԺID:
	$set_hid = intval($_POST["set_hid"]);

	// �����ֶ�(���ύΪ����):
	if (!is_array($_POST["field_set"]) || count($_POST["field_set"]) == 0) {
		$new_fields = array();
	} else {
		$new_fields = $_POST["field_set"];
	}

	// ɾ�����ܵĿ��ֶ�
	foreach ($new_fields as $k => $v) {
		if ($v == '') {
			unset($new_fields[$k]);
		}
	}
	$new_field = implode(",", $new_fields);

	// ��ѯ�Ƿ��Ѿ��м�¼��������£��������
	$set_id = $db->query("select id from jingjia_hospital_set where hid=$set_hid limit 1", 1, "id");
	if ($set_id > 0) {
		$db->query("update jingjia_hospital_set set fields='".$new_field."' where id=$set_id limit 1");
	} else {
		$h_name = $db->query("select name from hospital where id=$set_hid limit 1", 1, "name");
		$db->query("insert into jingjia_hospital_set set hid=$set_hid, h_name='".$h_name."', fields='".$new_field."', addtime=".time().", author='".$realname."'");
	}

	// �������������:
	echo '<script type="text/javascript">';
	echo 'parent.update_content();';
	echo 'parent.load_box(0);';
	echo 'parent.msg_box("���ݱ���ɹ�");';
	echo '</script>';
	exit;
}

if ($op == "setuser") {
	// ҽԺ
	$set_hid = intval($_GET["hid"]);

	// ��ѯ��ǰ¼����Ա:
	$cur_users = $db->query("select uid,u_name,fields from jingjia_user_set where hid=$set_hid order by u_name asc", "uid");

	// ��ѯ���п��ܵ�¼����Ա(ϵͳ����Ա�����������Ϊ���۲��ţ���ҽԺ�ǹ������ҽԺ��)
	$all_users = $db->query("select id, realname from sys_admin where part_id=202 and concat(',',hospitals,',') like '%,".$set_hid.",%' order by realname asc", "id", "realname");

	// ϵͳ���п�����������:
	$field_name_arr = $db->query("select fieldname, name from jingjia_field_set order by fieldname asc", "fieldname", "name");

	// ��ǰҽԺ���п�����������:
	$hospital_field = $db->query("select fields from jingjia_hospital_set where hid=$set_hid limit 1", 1, "fields");
	if (!empty($hospital_field)) {
		$hospital_field_arr = explode(",", $hospital_field);
	} else {
		$hospital_field_arr = array_keys($field_name_arr); //δ���ã�ʹ��ϵͳ���п�����������
	}

	include "hospital_set.setuser.php";
	exit;
}


if ($op == "setuser_submit") {
	// �����ύ����:
	$set_hid = intval($_POST["set_hid"]);
	if ($set_hid == 0) exit_html("��������...");

	// ����ҽԺ������Ա�ֶ�����Ϊ��:
	$db->query("update jingjia_user_set set fields='' where hid=$set_hid ");

	// Ȼ�������ύ����������ã�
	foreach ($_POST["user_field_set"] as $_uid => $_arr) {
		$_uname = $db->query("select realname from sys_admin where id=$_uid limit 1", 1, "realname");
		$field_str = implode(",", $_arr);
		// �����޸ģ��������:
		$set_id = $db->query("select id from jingjia_user_set where hid=$set_hid and uid=$_uid limit 1", 1, "id");
		if ($set_id > 0) {
			$db->query("update jingjia_user_set set fields='".$field_str."' where id=$set_id limit 1");
		} else {
			$db->query("insert into jingjia_user_set set hid=$set_hid, uid=$_uid, u_name='".$_uname."', fields='".$field_str."', addtime=".time().", author='".$realname."'");
		}
	}

	// ɾ��û�м�¼��ͬ־��
	$db->query("delete from jingjia_user_set where hid=$set_hid and fields=''");

	// �������������:
	echo '<script type="text/javascript">';
	echo 'parent.update_content();';
	echo 'parent.load_box(0);';
	echo 'parent.msg_box("¼����Ա���óɹ�");';
	echo '</script>';
	exit;
}


?>