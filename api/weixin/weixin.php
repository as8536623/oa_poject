<?php
/*
// ˵��: ΢�ŵ�Ժ����
// ����: ���� (weelia@126.com)
// ʱ��: 2013-4-13
*/
include "../core/db.php";

// ��¼��־:
set_visit_log();

if ($_GET["t"] == "today") {
	$type = "����";
} else if ($_GET["t"] == "yesterday") {
	$type = "����";
} else if ($_GET["t"] == "month") {
	$type = "����";
} else {
	$type = "����";
}

// ҽԺ�б�:
$hospital_arr = $db->query("select id,name from hospital order by id asc ", "id", "name");

// ��ȡ��������:
$cache_data = $db->query("select hid,data from patient_data", "hid", "data");
foreach ($cache_data as $k => $v) {
	$arr = @unserialize($v);
	$weixin_yuyue[$k] = intval($arr["΢��"]["ԤԼ"][$type]);
	$weixin_come[$k] = intval($arr["΢��"]["ʵ��"][$type]);
}

// ������:
arsort($weixin_yuyue);
arsort($weixin_come);

// ���ô���ҽԺid:
$k1 = array_keys($weixin_come);
$k2 = array_keys($weixin_yuyue);
$keys = array_unique(array_merge($k1, $k2));

// ���:
echo "<pre>\r\n";
echo "ҽԺ\t".$type."ԤԼ\t".$type."��Ժ\r\n"; //��ͷ
foreach ($keys as $k) {
	// �����ݵĲ���ʾ
	if (intval($weixin_yuyue[$k]) + intval($weixin_come[$k]) > 0) {
		echo $hospital_arr[$k]."\t".intval($weixin_yuyue[$k])."\t".intval($weixin_come[$k])."\r\n";
	}
}
echo "</pre>";


function set_visit_log() {
	$ip = get_ip();
	$str = date("Y-m-d H:i:s")." [".$ip."] ����\r\n";
	@file_put_contents("weixin_logs_wee.txt", $str, FILE_APPEND);
}

// ��ȡ��ǰ�û���ip��ַ:
function get_ip() {
	$long_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	if ($long_ip != "") {
		foreach (explode(",", $long_ip) as $cur_ip) {
			list($ip1, $ip2) = explode(".", $cur_ip, 2);
			if ($ip1 <> "10") {
				return $cur_ip;
			}
		}
	}
	return $_SERVER["REMOTE_ADDR"];
}

?>