<?php
/*
// 说明: log_function.php
// 作者: 幽兰 (weelia@126.com)
// 时间: 2014-3-18
*/

//error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", "On");
ini_set("log_errors", 0);

function _wee_log($from, $log_type, $str='') {
	$log = dirname(__FILE__)."/request.log";
	if (@filesize($log) > 10 * 1024 * 1024) {
		@rename($log, $log.".".date("Ymd_His"));
	}

	$s2 = '';
	if (substr_count($log_type, "GET") > 0) {
		if (!empty($_GET)) {
			$s2 .= "\r\nGET=".serialize($_GET);
		}
	}
	if (substr_count($log_type, "POST") > 0) {
		if (!empty($_POST)) {
			$s2 .= "\r\nPOST=".serialize($_POST);
		}
	}

	$ip = _get_ip();
	$s = date("Y-m-d H:i:s ")."[".$from."] ".$ip." ".$str."\r\n";
	if (trim($s2) != '') {
		$s .= trim($s2)."\r\n";
	}
	$s .= "\r\n";

	@file_put_contents($log, $s, FILE_APPEND);

}


// 获取当前用户的ip地址:
function _get_ip() {
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