<?php
/*
// - ����˵�� : core.php
// - �������� : weelia.zhu (weelia@126.com)
// - ����ʱ�� : 2009-10-19 09:31
*/
ini_set('date.timezone','Asia/Shanghai'); 
error_reporting(E_ALL ^ E_NOTICE);
ob_start();
define("ROOT", str_replace("\\", "/", dirname(dirname(__FILE__)))."/");

ini_set("display_errors", "Off");
ini_set("log_errors", 0);
ini_set("error_log", ROOT."data/php_error.log");

function now() {
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}
$pagebegintime = now();

$time = $timestamp = time();
$islocal = @file_exists("D:/Server/") ? true : false;

//�ű����ִ��ʱ��
set_time_limit(15);

// ���˵����� (���ȷʵ��Ҫ�ύ�������ŵ� �����ö�ά������������)
foreach ($_POST as $k => $v) {
	if (!is_array($v)) {
		$_POST[$k] = str_replace("'", "", $v);
	}
}

require ROOT."core/config.php";
require ROOT."core/db.php";

// session ����:
require ROOT."core/session.php";

// ���غ����ļ�
require ROOT."core/function.php";
require_once ROOT."core/function.mem_cache.php";

$log = load_class("log");
$power = load_class("power", $db);
$part = load_class("part", $db);

// ��ʼ������
$username = $_SESSION["username"];
$debug_mode = $_SESSION["debug"] ? 1 : 0;
$config = array();
if (!isset($nochecklogin) || !$nochecklogin) {
	if (empty($username)) {
		if ($_POST) {
			include ROOT."core/offline.tips.php";
			exit;
		}
		exit("<script> top.location = '/m/login.php'; </script>");
	} else {
		$uinfo = load_user_info($username);
		$uid = $uinfo["id"];
		$usermenu = $uinfo["menu"];
		$shortcut = $uinfo["shortcut"];
		$realname = $uinfo["realname"];
		if ($uinfo["config"] != '') {
			$config = @unserialize($uinfo["config"]);
		}
	}
}
$uid = intval($uid);

// ҳ����Ϣ:
$pinfo = load_page_info();
$pagesize = $global_default_pagesize;
if ($pinfo) {
	$pagesize = $pinfo["pagesize"] > 0 ? $pinfo["pagesize"] : $global_default_pagesize;
	$pagepower = $pinfo["pagepower"];
}

$id = $op = ''; //��ʼ�������ؼ�����
if (isset($_REQUEST["op"])) $op = $_REQUEST["op"];
if (isset($_REQUEST["id"])) $id = intval($_REQUEST["id"]);

// 2009-05-19 ��ʼ��ҽԺ:
if ($debug_mode || $username == 'admin') {
	$hospital_ids = $db->query("select id from hospital", '', 'id');
} else {
	if ($uinfo["hospitals"] != '') {
		$hospital_ids = explode(",", $uinfo["hospitals"]);
	} else {
		$hospital_ids = array();
	}
}
if (count($hospital_ids) == 1) {
	$_SESSION["hospital_id"] = intval($hospital_ids[0]);
}
$hospitals = implode(",", $hospital_ids);

// �Զ�ѡ�񵽵�һ��ҽԺ
/*
if (count($hospital_ids) > 0 && empty($_SESSION["hospital_id"])) {
	$_SESSION["hospital_id"] = intval($hospital_ids[0]);
}
*/

// �л�ҽԺ @ 2012-07-15
if ($_GET["tohid"]) {
	$_hid_ = intval($_GET["tohid"]);
	if (in_array($_hid_, $hospital_ids)) {
		$_SESSION["hospital_id"] = $_hid_;
	}
}

$hid = intval($_SESSION["hospital_id"]);


if ($_GET["main_hid_to"] != '') {
	if ($debug_mode) {
		//echo "<pre>";
		//print_r($_SERVER);
		//exit;
	}

	$_ty = trim($_GET["main_hid_to"]);

	// ��ȡ����
	$_hids = $db->query("select id from hospital where id in ($hospitals) order by sort desc, id asc", "", "id");

	$new_hid = 0;
	if ($hid == 0 || !in_array($hid, $_hids)) {
		$new_hid = $_hids[0];
	} else {
		if ($_ty == "next") {
			$k = array_search($hid, $_hids);
			if ($k < (count($_hids) - 1)) {
				$new_hid = $_hids[$k + 1];
			} else {
				echo "<script> parent.msg_box('�������һ��ҽԺ'); </script>";
			}
		} else if ($_ty == "pre") {
			$k = array_search($hid, $_hids);
			if ($k > 0) {
				$new_hid = $_hids[$k - 1];
			} else {
				echo "<script> parent.msg_box('������ǰһ��ҽԺ'); </script>";
			}
		} else {
			exit("main_hid_to��������ȷ...");
		}
	}
	if ($new_hid > 0) {
		$_SESSION["hospital_id"] = $new_hid;
		$url = $_SERVER["REQUEST_URI"];
		$url = str_replace("main_hid_to=pre", "", $url);
		$url = str_replace("main_hid_to=next", "", $url);
		$url = rtrim($url, "&?");
		echo '<script> self.location = "'.$url.'"; </script>';
		exit;
	}
}


$hinfo = $hconfig = array();
if ($hid > 0) {
	$hinfo = $db->query("select * from hospital where id='$hid' limit 1", 1);
	if ($hinfo["config"]) {
		$hconfig = unserialize($hinfo["config"]);
	}
}


// �Һź�������:
if ($username == "admin" || $debug_mode) {
	$guahao_config = array_keys($guahao_config_arr);
} else {
	$guahao_config = explode(",", $uinfo["guahao_config"]);
	// ����Ƿ���ʧЧ��:
	foreach ($guahao_config as $k => $v) {
		if (!array_key_exists($v, $guahao_config_arr)) {
			unset($guahao_config[$k]);
		}
	}
}


if ($username == "admin") {
	$_names = $db->query("select name from index_module_set where isshow=1 order by sort desc, id asc", "", "name");
	$uinfo["data_power"] = "all,web,tel,".implode(",", $_names);
}


unset($_SESSION["history"]);
/*
// ҳ����ʷ��¼:
if (!$_POST && $_SERVER["REQUEST_URI"] != '') {
	if (empty($_SESSION["history"]) || (count($_SESSION["history"]) && $_SESSION["history"][count($_SESSION["history"]) - 1] != $_SERVER["REQUEST_URI"])) {
		if (substr_count($_SERVER["REQUEST_URI"], "/http/") == 0 && $_SERVER["REQUEST_URI"] != "/") {
			$_SESSION["history"][] = $_SERVER["REQUEST_URI"];
			if (count($_SESSION["history"]) > 20) {
				array_shift($_SESSION["history"]);
			}
		}
	}
}
*/


// ɾ��session���� @ 2012-12-09
$gc_interval = 30; //gc���ʱ��
$tmp = @wee_mem_get_cache("ses_gc_last_time"); // ��ȡmemcache�е����ݣ��ϴ�gcʱ��
$gc_last_time = is_array($tmp) ? $tmp["data"] : 0;
if (time() - $gc_last_time > $gc_interval) {
	wee_mem_set_cache("ses_gc_last_time", time(), $gc_interval*2); //��������gc��ʱ�� ��ֹ������ͬʱgc
	ses_gc_by_core(); //��������
}


$power->check_power() or msg_box("û��Ȩ��", "back", 1);
?>