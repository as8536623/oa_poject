<?php
/*
// ˵��: income.op
// ����: ���� (weelia@126.com)
// ʱ��: 2010-07-15
*/

if ($op == "list") {
	include $mod.".list.php";
	exit;
}

// ��Ӻ��޸�:
if ($op == "add" || $op == "edit") {
	$fee_type = intval($_REQUEST["fee_type"]);

	// ��ѯҪ����ļ�¼:
	if ($op == "edit") {
		$line = $db->query("select * from $table where id=$id limit 1", 1);
	}

	// post:
	if ($_POST) {
		$r = array();

		$r["hid"] = $hid;
		$r["h_name"] = $hinfo["name"];

		$r["fee_type"] = $fee_type;
		$r["fee_typename"] = $shoufei_bumen_array[$fee_type];

		$r["date"] = $_POST["date"];
		$r["doctor_id"] = $_POST["doctor_id"];
		if ($r["doctor_id"] > 0) {
			$r["doctor_name"] = $db->query("select name from doctor where id=".intval($r["doctor_id"])." limit 1", 1, "name");
		}

		if ($fee_type == 0) {
			$r["chuzhen"] = $_POST["chuzhen"];
			$r["fuzhen"] = $_POST["fuzhen"];
			$r["liushi"] = $_POST["liushi"];
		} else {
			$r["zhuyuan"] = $_POST["zhuyuan"];
		}

		$fee_names  = explode("|", $_POST["fee_names"]);
		$detail = array();
		foreach ($fee_names as $k) {
			$k = trim($k);
			if ($k && $_POST["fee_".$k]) {
				$detail[$k] = $_POST["fee_".$k];
			}
		}
		$r["detail"] = serialize($detail);

		//if (intval($_POST["yingyee"]) == 0) {
			$r["yingyee"] = array_sum($detail);
		//} else {
			//$r["yingyee"] = $_POST["yingyee"];
		//}

		// �����˾�:
		if ($fee_type == 0) {
			if ($r["chuzhen"] > 0) {
				$r["renjun"] = $r["yingyee"] / $r["chuzhen"];
			}
		} else {
			if ($r["zhuyuan"] > 0) {
				$r["renjun"] = $r["yingyee"] / $r["zhuyuan"];
			}
		}

		if ($op == "add") {
			$r["addtime"] = time();
			$r["uid"] = $uid;
			$r["u_realname"] = $realname;
		}

		if ($_POST["memo"]) {
			$r["memo"] = $line["memo"]."<b>".date("Y-m-d H:i")."  ".$realname."</b>�� ".rtrim($_POST["memo"])."\r\n";
		}

		// ��¼�޸���־:
		if ($op == "edit") {
			$old_line = array_merge($line, (array) unserialize($line["detail"]));
			$new_line = array_merge($r, $detail);
			$to_log = array("chuzhen"=>"��������", "fuzhen"=>"��������", "liushi"=>"��ʧ����", "zhuyuan"=>"סԺ����");
			foreach ($fee_names as $k) {
				$to_log[$k] = $k;
			}

			$log_data = array();
			foreach ($to_log as $k => $v) {
				if (isset($old_line[$k]) && isset($new_line[$k]) && $old_line[$k] != $new_line[$k]) {
					$log_data[] = $realname." �� ".date("Y-m-d H:i")." �� ".$v." �� ".$old_line[$k]." �޸�Ϊ ".$new_line[$k]."<br>";
				}
			}

			if (count($log_data) > 0) {
				$r["log"] = $line["log"].implode("", $log_data);
			}
		}


		$_GET["back_url"] = base64_decode($_POST["back_url"]);

		$sqldata = $db->sqljoin($r);
		if ($op == "add") {
			$sql = "insert into $table set $sqldata";
		} else {
			$sql = "update $table set $sqldata where id='$id' limit 1";
		}

		if ($db->query($sql)) {
			$go = noe(history(2, $id), ("?date=".$r["date"]));
			msg_box("�����ύ�ɹ���", $go, 1);
		} else {
			msg_box("�����ύʧ�ܣ�ϵͳ��æ�����Ժ����ԡ�", "back", 1, 5);
		}
	}
	// end of post

	include $mod.".edit.php";
	exit;
}


if ($op == "view") {
	$title = "�鿴����";
	include $mod.".view.php";

	exit;
}

if ($op == "search") {
	include $mod.".search.php";

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
		$log->add("delete", "ɾ������", serialize($op_data));
	}

	if ($del_bad > 0) {
		msg_box("ɾ���ɹ� $del_ok �����ϣ�ɾ��ʧ�� $del_bad �����ϡ�", "back", 1);
	} else {
		msg_box("ɾ���ɹ�", "back", 1);
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
		msg_box("�����ɹ���� $set_ok ����ʧ�� $del_bad ����", "back", 1);
	} else {
		msg_box("�����ɹ�", "back", 1, 1);
	}
}

?>