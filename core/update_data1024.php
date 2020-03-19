<?php
/*
// 更新到院数据，用于摘要显示
// 作者: 幽兰 (weelia@126.com)
*/
header("Content-Type:text/html;charset=gb2312");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
set_time_limit(0);
ignore_user_abort(true);

// 数据最短更新频率:
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

// 时间定义 2011-12-28:
// 时间的起始点都是 YYYY-MM-DD 00:00:00 结束则是 YYYY-MM-DD 23:59:59
$today_tb = mktime(0,0,0); //今天开始
$today_te = strtotime("+1 day", $today_tb) - 1; //今天结束

$yesterday_tb = strtotime("-1 day", $today_tb); //昨天开始
$yesterday_te = $today_tb - 1; //昨天结束

$month_tb = mktime(0, 0, 0, date("m"), 1); //本月开始
$month_te = strtotime("+1 month", $month_tb) - 1; //本月结束

$lastmonth_tb = strtotime("-1 month", $month_tb); //上月开始
$lastmonth_te = $month_tb - 1; //上月结束

$tb_tb = strtotime("-1 month", $month_tb); //同比时间开始
$tb_te = strtotime("-1 month", time()); //同比时间结束
if (date("d", $tb_te) != date("d")) {
	$tb_te = $month_tb - 1;
}

// 月比:
$yuebi_tb = strtotime("-1 month", $today_tb);
if (date("d", $yuebi_tb) != date("d", $today_tb)) {
	$yuebi_tb = $yuebi_te = -1;
} else {
	$yuebi_te = $yuebi_tb + 24*3600;
}
// 周比:
$zhoubi_tb = strtotime("-7 day", $today_tb);
$zhoubi_te = $zhoubi_tb + 24*3600;

// 去年同月比
$yb_tb = strtotime("-1 year", $month_tb);
$_days = _get_month_days(date("Y-m", $yb_tb));
if (date("j") > $_days) { //当前的日已经大于去年同月的天数了(比如29日和去年的28日)
	$yb_te = strtotime(date("Y-m-", $yb_tb).$_days.date(" 23:59:59")); //对比为去年同月的整月
} else {
	$yb_te = strtotime(date("Y-m-", $yb_tb).date("d H:i:s"));
}

// 数据查询依照此数组定义
$time_arr = array(
	"今日" => array($today_tb, $today_te),
	"月比" => array($yuebi_tb, $yuebi_te),
	"周比" => array($zhoubi_tb, $zhoubi_te),
	"昨日" => array($yesterday_tb, $yesterday_te),
	"本月" => array($month_tb, $today_te),
	"同比" => array($tb_tb, $tb_te),
	"上月" => array($lastmonth_tb, $lastmonth_te),
);

$tb = $lastmonth_tb; //最小时间
$te = $today_te; //最大时间


// 首页模块设置:
$index_module = $db->query("select name,type,sum_condition from index_module_set where isshow='1'");


$time_file = "../data/update_data.txt";

$last_update = @intval(file_get_contents($time_file));

if (time() - $last_update > $update_interval) {
	flush_echo("正在更新，请稍候...".str_repeat("&nbsp; ", 100) );

	// 更新文件时间:
	file_put_contents($time_file, time());

	// 要更新的医院数据:
	$_hlist = $db->query("select id, name from hospital", "id", "name");

	// 当前缓存的数据:
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

		// 数据分析 (测试证明，这段分析程序将耗时较多，所以对结果进行缓存，加快速度)
		$res = array();
		foreach ($data as $li) {
			$ad = $li["addtime"]; //ad为addtime
			$od = $li["order_date"]; //od为order_date
			foreach ($time_arr as $tn => $d) {
				// 总
				if ($ad >= $d[0] && $ad <= $d[1]) {
					$res["总"]["预约"][$tn] += 1;
				}
				if ($od >= $d[0] && $od <= $d[1]) {
					$res["总"]["预到"][$tn] += 1;
					if ($li["status"] == 1) $res["总"]["实到"][$tn] += 1;
				}

				// 网络
				if ($li["part_id"] == 2 && $ad >= $d[0] && $ad <= $d[1]) {
					$res["网络"]["预约"][$tn] += 1;
				}
				if ($li["part_id"] == 2 && $od >= $d[0] && $od <= $d[1]) {
					$res["网络"]["预到"][$tn] += 1;
					if ($li["status"] == 1) $res["网络"]["实到"][$tn] += 1;
				}

				// 电话
				if ($li["part_id"] == 3 && $ad >= $d[0] && $ad <= $d[1]) {
					$res["电话"]["预约"][$tn] += 1;
				}
				if ($li["part_id"] == 3 && $od >= $d[0] && $od <= $d[1]) {
					$res["电话"]["预到"][$tn] += 1;
					if ($li["status"] == 1) $res["电话"]["实到"][$tn] += 1;
				}

				// 个人
				$li["author"] = trim($li["author"]);
				if ($li["author"] != '' && $ad >= $d[0] && $ad <= $d[1]) {
					$res["个人_".$li["author"]]["预约"][$tn] += 1;
				}
				if ($li["author"] != '' && $od >= $d[0] && $od <= $d[1]) {
					$res["个人_".$li["author"]]["预到"][$tn] += 1;
					if ($li["status"] == 1) $res["个人_".$li["author"]]["实到"][$tn] += 1;
				}

				// 网查
				if ($li["media_from"] == "网络" && $li["part_id"] == 3 && $li["status"] == 1 && $od >= $d[0] && $od <= $d[1]) {
					$res["网查"]["实到"][$tn] += 1;
				}

				// 2013-5-13
				foreach ($index_module as $z) {
					$zv = explode("+", $z["sum_condition"]);
					if (in_array($li[$z["type"]], $zv)) {
						if ($ad >= $d[0] && $ad <= $d[1]) {
							$res[$z["name"]]["预约"][$tn] += 1;
						}
						if ($od >= $d[0] && $od <= $d[1]) {
							$res[$z["name"]]["预到"][$tn] += 1;
							if ($li["status"] == 1) $res[$z["name"]]["实到"][$tn] += 1;
						}
					}
				}


			}
		}
		unset($data); //释放这个大数组

		$s = addslashes(serialize($res));

		if (!in_array($_id, $cur_cache_hid)) {
			$db->query("insert into patient_data set hid=$_id, data='$s'");
		} else {
			$db->query("update patient_data set data='$s' where hid=$_id limit 1");
		}

		// 延迟一会 再进行下一个
		usleep(100000);
	}

	//$db->query("optimize table patient_data");

	$status = "数据已更新";
} else {
	$status = "数据更新周期未到，请稍后再试";
}

// 记录日志：
$time_used = round(_get_now() - $page_begintime, 4);
$log_str = date("Y-m-d H:i:s")." [".$time_used."s] ".$status."\r\n";
$log_file = "../data/update_data_log.txt";
@file_put_contents($log_file, $log_str, FILE_APPEND);

flush_echo();
flush_echo($status." @ ".$time_used."s");


// 备份 sys_admin 开始：
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
	flush_echo("`sys_admin` 备份成功，耗时 ".round($t_end - $t_begin, 4)."s");
} else {
	flush_echo("获取sql失败 无法创建`sys_admin`备份表结构");
}

?>