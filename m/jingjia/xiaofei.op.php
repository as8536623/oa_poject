<?php
/*
// ����: ���� (weelia@126.com)
// ʱ��: 2011-07-23 23:26
*/
if (!defined("ROOT")) {
	exit("��Ȩ�޲���..."); //����Ƿ�Ϊ����ҳ��
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

	// ��������Ƿ��Ѿ���ӹ���:
	$old = $db->query("select * from $table where hid=$hid and date=$date limit 1", 1);
	if ($old["id"] > 0) {
		$op_id = $old["id"];
		// ����:
		$r = array();
		$log_arr = array();
		foreach ($_POST["xiaofei"] as $k => $v) {
			if (in_array($k, $h_field_arr)) { //�����ǵ�ǰҽԺ������ֶ�(δ���ò���ȫ��)
				if ($old[$k] != $v) { //�����Ƿ����޸�
					$r[$k] = floatval($v);
					$log_arr[] = $all_field_arr[$k]." �� ".$old[$k]." �޸�Ϊ ".floatval($v);
				}
			}
		}
		if (count($log_arr) > 0) {
			$r["log"] = $old["log"].date("Y-m-d H:i:s")." ".$realname.": ".implode("��", $log_arr)."<br>";
		}
		if (count($r) > 0) {
			$sqldata = $db->sqljoin($r);
			$db->query("update $table set $sqldata where id=$op_id limit 1");
		} else {
			// �������޸ģ�������
		}

	} else {
		$r = array();
		$r["hid"] = $hid;
		$r["h_name"] = $h_name;
		$r["date"] = $date;
		foreach ($_POST["xiaofei"] as $k => $v) {
			if (in_array($k, $h_field_arr)) { //�����ǵ�ǰҽԺ������ֶ�(δ���ò���ȫ��)
				$r[$k] = $v;
			}
		}
		$r["uid"] = $uid;
		$r["u_name"] = $realname;
		$r["addtime"] = time();
		$sqldata = $db->sqljoin($r);
		$op_id = $db->query("insert into $table set $sqldata");
	}

	// ����ͳ������:
	$db->query("update $table set xiaofei=x1+x2+x3+x4+x5+x6+x7 where id=$op_id limit 1");

	echo '<script type="text/javascript">';
	echo 'parent.update_content();';
	echo 'parent.load_box(0);';
	echo 'parent.msg_box("���ݱ���ɹ�");';
	echo '</script>';
	exit;
}

if ($op == "edit") {
	$line = $db->query("select * from $table where id=$id limit 1", 1);
	include "xiaofei.edit.php";
	exit;
}

if ($op == "edit_submit") {
	if (empty($id)) exit_html("��������!");
	$old = $db->query("select * from $table where id=$id limit 1", 1);
	if ($old) {
		$r = array();
		$log_arr = array();
		foreach ($_POST["xiaofei"] as $k => $v) {
			if (in_array($k, $h_field_arr)) { //�����ǵ�ǰҽԺ������ֶ�(δ���ò���ȫ��)
				if ($old[$k] != $v) { //�����Ƿ����޸�
					$r[$k] = floatval($v);
					$log_arr[] = $all_field_arr[$k]." �� ".$old[$k]." �޸�Ϊ ".floatval($v);
				}
			}
		}
		if (count($log_arr) > 0) {
			$r["log"] = $old["log"].date("Y-m-d H:i:s")." ".$realname.": ".implode("��", $log_arr)."<br>";
		}
		if (count($r) > 0) {
			$sqldata = $db->sqljoin($r);
			$db->query("update $table set $sqldata where id=$id limit 1");

			// ����ͳ������:
			$db->query("update $table set xiaofei=x1+x2+x3+x4+x5+x6+x7 where id=$id limit 1");

			echo '<script type="text/javascript">';
			echo 'parent.update_content();';
			echo 'parent.load_box(0);';
			echo 'parent.msg_box("���ݱ���ɹ�");';
			echo '</script>';
		} else {
			// �������޸ģ�������
			echo '<script type="text/javascript">';
			echo 'parent.load_box(0);';
			echo 'parent.msg_box("�����޸Ķ�");';
			echo '</script>';
		}
	} else {
		exit_html("��ȡ���ݳ���.����ϵ������Ա.");
	}
	exit;
}

if ($op == "log") {
	if (empty($id)) exit_html("��������!");
	$line = $db->query("select * from $table where id=$id limit 1", 1);
	include "xiaofei.log.php";
	exit;
}

if ($op == "delete") {
	exit_html("��ʱ����ɾ��.");
}


?>