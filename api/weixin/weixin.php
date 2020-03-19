<?php
/*
// 说明: 微信到院数据
// 作者: 幽兰 (weelia@126.com)
// 时间: 2013-4-13
*/
include "../core/db.php";

// 记录日志:
set_visit_log();

if ($_GET["t"] == "today") {
	$type = "今日";
} else if ($_GET["t"] == "yesterday") {
	$type = "昨日";
} else if ($_GET["t"] == "month") {
	$type = "本月";
} else {
	$type = "本月";
}

// 医院列表:
$hospital_arr = $db->query("select id,name from hospital order by id asc ", "id", "name");

// 读取缓存数据:
$cache_data = $db->query("select hid,data from patient_data", "hid", "data");
foreach ($cache_data as $k => $v) {
	$arr = @unserialize($v);
	$weixin_yuyue[$k] = intval($arr["微信"]["预约"][$type]);
	$weixin_come[$k] = intval($arr["微信"]["实到"][$type]);
}

// 排序结果:
arsort($weixin_yuyue);
arsort($weixin_come);

// 有用处的医院id:
$k1 = array_keys($weixin_come);
$k2 = array_keys($weixin_yuyue);
$keys = array_unique(array_merge($k1, $k2));

// 输出:
echo "<pre>\r\n";
echo "医院\t".$type."预约\t".$type."到院\r\n"; //表头
foreach ($keys as $k) {
	// 有数据的才显示
	if (intval($weixin_yuyue[$k]) + intval($weixin_come[$k]) > 0) {
		echo $hospital_arr[$k]."\t".intval($weixin_yuyue[$k])."\t".intval($weixin_come[$k])."\r\n";
	}
}
echo "</pre>";


function set_visit_log() {
	$ip = get_ip();
	$str = date("Y-m-d H:i:s")." [".$ip."] 访问\r\n";
	@file_put_contents("weixin_logs_wee.txt", $str, FILE_APPEND);
}

// 获取当前用户的ip地址:
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