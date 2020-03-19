<?php
/*
// ���µ�Ժ���ݣ�����ժҪ��ʾ
// ����: ���� (weelia@126.com)
*/
header("Content-Type:text/html;charset=gb2312");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
set_time_limit(0);
ignore_user_abort(true);

// ������̸���Ƶ��:
$update_interval = 3*60;


include "db.php";

function _get_now() {
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}
$page_begintime = _get_now();

function flush_echo($s = '') {
	echo $s."<br>\r\n";
	flush();
	ob_flush();
	ob_end_flush();
}

function _get_month_days($month = '') {
	if ($month == '') $month = date("Y-m");
	return date("j", strtotime("+1 month", strtotime($month."-1 0:0:0")) - 1);
}

// ʱ�䶨�� 2011-12-28:
// ʱ�����ʼ�㶼�� YYYY-MM-DD 00:00:00 �������� YYYY-MM-DD 23:59:59
$today_tb = mktime(0,0,0); //���쿪ʼ
$today_te = strtotime("+1 day", $today_tb) - 1; //�������

$yesterday_tb = strtotime("-1 day", $today_tb); //���쿪ʼ
$yesterday_te = $today_tb - 1; //�������

$month_tb = mktime(0, 0, 0, date("m"), 1); //���¿�ʼ
$month_te = strtotime("+1 month", $month_tb) - 1; //���½���

$lastmonth_tb = strtotime("-1 month", $month_tb); //���¿�ʼ
$lastmonth_te = $month_tb - 1; //���½���

$tb_tb = strtotime("-1 month", $month_tb); //ͬ��ʱ�俪ʼ
$tb_te = strtotime("-1 month", time()); //ͬ��ʱ�����
if (date("d", $tb_te) != date("d")) {
	$tb_te = $month_tb - 1;
}

// �±�:
$yuebi_tb = strtotime("-1 month", $today_tb);
if (date("d", $yuebi_tb) != date("d", $today_tb)) {
	$yuebi_tb = $yuebi_te = -1;
} else {
	$yuebi_te = $yuebi_tb + 24*3600;
}
// �ܱ�:
$zhoubi_tb = strtotime("-7 day", $today_tb);
$zhoubi_te = $zhoubi_tb + 24*3600;

// ȥ��ͬ�±�
$yb_tb = strtotime("-1 year", $month_tb);
$_days = _get_month_days(date("Y-m", $yb_tb));
if (date("j") > $_days) { //��ǰ�����Ѿ�����ȥ��ͬ�µ�������(����29�պ�ȥ���28��)
	$yb_te = strtotime(date("Y-m-", $yb_tb).$_days.date(" 23:59:59")); //�Ա�Ϊȥ��ͬ�µ�����
} else {
	$yb_te = strtotime(date("Y-m-", $yb_tb).date("d H:i:s"));
}

// ���ݲ�ѯ���մ����鶨��
$time_arr = array(
	"����" => array($today_tb, $today_te),
	"�±�" => array($yuebi_tb, $yuebi_te),
	"�ܱ�" => array($zhoubi_tb, $zhoubi_te),
	"����" => array($yesterday_tb, $yesterday_te),
	"����" => array($month_tb, $today_te),
	"ͬ��" => array($tb_tb, $tb_te),
	"����" => array($lastmonth_tb, $lastmonth_te),
);

$tb = $lastmonth_tb; //��Сʱ��
$te = $today_te; //���ʱ��


// ��ҳģ������:
$index_module = $db->query("select name,type,sum_condition from index_module_set where isshow='1'");


$time_file = "../data/update_data.txt";

$last_update = @intval(file_get_contents($time_file));

if (time() - $last_update > $update_interval) {
	flush_echo("���ڸ��£����Ժ�...".str_repeat("&nbsp; ", 100) );

	// �����ļ�ʱ��:
	file_put_contents($time_file, time());

	// Ҫ���µ�ҽԺ����:
	$_hlist = $db->query("select id, name from hospital", "id", "name");

	// ��ǰ���������:
	$cur_cache_data = $db->query("select hid,data from patient_data", "hid", "data");
	$cur_cache_hid = array_keys($cur_cache_data);

	foreach ($_hlist as $_id => $_name) {
		$table = "patient_{$_id}";

		$field_add = "";

		$q = mysql_query("SELECT id,part_id,disease_id,depart,media_from,status,from_account,author,order_date,addtime{$field_add} FROM $table WHERE (addtime>=$tb and addtime<=$te) or (order_date>=$tb and order_date<=$te)", $db->dblink);
		$data = array();
		while ($li = mysql_fetch_assoc($q)) {
			$data[] = $li;
		}

		// ���ݷ��� (����֤������η������򽫺�ʱ�϶࣬���ԶԽ�����л��棬�ӿ��ٶ�)
		$res = array();
		foreach ($data as $li) {
			$ad = $li["addtime"]; //adΪaddtime
			$od = $li["order_date"]; //odΪorder_date
			foreach ($time_arr as $tn => $d) {
				// ��
				if ($ad >= $d[0] && $ad <= $d[1]) {
					$res["��"]["ԤԼ"][$tn] += 1;
				}
				if ($od >= $d[0] && $od <= $d[1]) {
					$res["��"]["Ԥ��"][$tn] += 1;
					if ($li["status"] == 1) $res["��"]["ʵ��"][$tn] += 1;
				}

				// ����
				if ($li["part_id"] == 2 && $ad >= $d[0] && $ad <= $d[1]) {
					$res["����"]["ԤԼ"][$tn] += 1;
				}
				if ($li["part_id"] == 2 && $od >= $d[0] && $od <= $d[1]) {
					$res["����"]["Ԥ��"][$tn] += 1;
					if ($li["status"] == 1) $res["����"]["ʵ��"][$tn] += 1;
				}

				// �绰
				if ($li["part_id"] == 3 && $ad >= $d[0] && $ad <= $d[1]) {
					$res["�绰"]["ԤԼ"][$tn] += 1;
				}
				if ($li["part_id"] == 3 && $od >= $d[0] && $od <= $d[1]) {
					$res["�绰"]["Ԥ��"][$tn] += 1;
					if ($li["status"] == 1) $res["�绰"]["ʵ��"][$tn] += 1;
				}

				// ����
				$li["author"] = trim($li["author"]);
				if ($li["author"] != '' && $ad >= $d[0] && $ad <= $d[1]) {
					$res["����_".$li["author"]]["ԤԼ"][$tn] += 1;
				}
				if ($li["author"] != '' && $od >= $d[0] && $od <= $d[1]) {
					$res["����_".$li["author"]]["Ԥ��"][$tn] += 1;
					if ($li["status"] == 1) $res["����_".$li["author"]]["ʵ��"][$tn] += 1;
				}

				// ����
				if ($li["media_from"] == "����" && $li["part_id"] == 3 && $li["status"] == 1 && $od >= $d[0] && $od <= $d[1]) {
					$res["����"]["ʵ��"][$tn] += 1;
				}

				// 2013-5-13
				foreach ($index_module as $z) {
					$zv = explode("+", $z["sum_condition"]);
					if (in_array($li[$z["type"]], $zv)) {
						if ($ad >= $d[0] && $ad <= $d[1]) {
							$res[$z["name"]]["ԤԼ"][$tn] += 1;
						}
						if ($od >= $d[0] && $od <= $d[1]) {
							$res[$z["name"]]["Ԥ��"][$tn] += 1;
							if ($li["status"] == 1) $res[$z["name"]]["ʵ��"][$tn] += 1;
						}
					}
				}


			}
		}
		unset($data); //�ͷ����������

		$s = addslashes(serialize($res));

		if (!in_array($_id, $cur_cache_hid)) {
			$db->query("insert into patient_data set hid=$_id, data='$s'");
		} else {
			$db->query("update patient_data set data='$s' where hid=$_id limit 1");
		}

		// �ӳ�һ�� �ٽ�����һ��
		usleep(100000);
	}

	//$db->query("optimize table patient_data");

	$status = "�����Ѹ���";
} else {
	$status = "���ݸ�������δ�������Ժ�����";
}

// ��¼��־��
$time_used = round(_get_now() - $page_begintime, 4);
$log_str = date("Y-m-d H:i:s")." [".$time_used."s] ".$status."\r\n";
$log_file = "../data/update_data_log.txt";
@file_put_contents($log_file, $log_str, FILE_APPEND);

flush_echo();
flush_echo($status." @ ".$time_used."s");


// ���� sys_admin ��ʼ��
$t_begin = _get_now();
$q = mysql_query("show create table `sys_admin`");
$res = mysql_fetch_assoc($q);
$sql = $res["Create Table"];

if ($sql != '') {
	$sql = str_replace("`sys_admin`", "`sys_admin_back`", $sql);

	$db->query("drop table if exists `sys_admin_back`");
	$db->query($sql);
	$db->query("insert into `sys_admin_back` select * from `sys_admin`");

	$t_end = _get_now();
	flush_echo("`sys_admin` ���ݳɹ�����ʱ ".round($t_end - $t_begin, 4)."s");
} else {
	flush_echo("��ȡsqlʧ�� �޷�����`sys_admin`���ݱ�ṹ");
}

?>