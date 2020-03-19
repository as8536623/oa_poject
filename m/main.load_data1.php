<?php
/*
// 说明: main.hospital.php
// 作者: 幽兰 (weelia@126.com)
// 时间: 2013-4-13
*/
if (WEE_MAIN != 1) exit;

if ($sum_hids) {
	$_tmp_str = str_replace("[", "", str_replace("]", "", $sum_hids));
	$_tmp_arr = explode(",", $_tmp_str);
	$index_data = array();
	foreach ($_tmp_arr as $v) {
		$v = intval($v);
		if ($v > 0) {
			$arr = load_hospital_data($v);
			foreach ($arr as $_a => $_b) {
				foreach ($_b as $_dt => $_dd) {
					$index_data[$_a][$_dt]["data"] = intval($index_data[$_a][$_dt]["data"]) + intval($_dd["data"]);
				}
			}
		}
	}
} else {
	$index_data = load_hospital_data($hid);
}




function load_hospital_data($_hid) {
	global $db, $debug_mode, $uinfo, $yuebi_tb;

	$index_data = array();

	$_tmp = $db->query("select data from patient_data where hid=$_hid limit 1", 1, "data");
	$data_arr = (array) @unserialize($_tmp);

	$data_power = @explode(",", $uinfo["data_power"]);

	if (in_array("all", $data_power) || $debug_mode) {
		$d = array();

		$d["今日预约"]["data"] = intval($data_arr["总"]["预约"]["今日"]);
		$d["今日预到"]["data"] = intval($data_arr["总"]["预到"]["今日"]);
		$d["今日实到"]["data"] = intval($data_arr["总"]["实到"]["今日"]);
		$d["今日未到"]["data"] = intval($d["今日预到"]["data"] - $d["今日实到"]["data"]);

		$d["今日预约"]["link"] = "/m/patient/patient.php?show=today&time_type=addtime";
		$d["今日预到"]["link"] = "/m/patient/patient.php?show=today";
		$d["今日实到"]["link"] = "/m/patient/patient.php?show=today&come=1";
		$d["今日未到"]["link"] = "/m/patient/patient.php?show=today&come=0";


		// 月比:
		if ($yuebi_tb > 0) {
			$d["月比预约"]["data"] = intval($data_arr["总"]["预约"]["月比"]);
			$d["月比预到"]["data"] = intval($data_arr["总"]["预到"]["月比"]);
			$d["月比实到"]["data"] = intval($data_arr["总"]["实到"]["月比"]);
			$d["月比未到"]["data"] = $d["月比预到"]["data"] - $d["月比实到"]["data"];
		} else {
			$d["月比预约"]["data"] = $d["月比预到"]["data"] = $d["月比实到"]["data"] = $d["月比未到"]["data"] = "-";
		}


		// 周比:
		$d["周比预约"]["data"] = intval($data_arr["总"]["预约"]["周比"]);
		$d["周比预到"]["data"] = intval($data_arr["总"]["预到"]["周比"]);
		$d["周比实到"]["data"] = intval($data_arr["总"]["实到"]["周比"]);
		$d["周比未到"]["data"] = $d["周比预到"]["data"] - $d["周比实到"]["data"];


		// 昨日:
		$d["昨日预约"]["data"] = intval($data_arr["总"]["预约"]["昨日"]);
		$d["昨日预到"]["data"] = intval($data_arr["总"]["预到"]["昨日"]);
		$d["昨日实到"]["data"] = intval($data_arr["总"]["实到"]["昨日"]);
		$d["昨日未到"]["data"] = $d["昨日预到"]["data"] - $d["昨日实到"]["data"];

		$d["昨日预约"]["link"] = "/m/patient/patient.php?show=yesterday&time_type=addtime";
		$d["昨日预到"]["link"] = "/m/patient/patient.php?show=yesterday";
		$d["昨日实到"]["link"] = "/m/patient/patient.php?show=yesterday&come=1";
		$d["昨日未到"]["link"] = "/m/patient/patient.php?show=yesterday&come=0";


		// 本月：
		$d["本月预约"]["data"] = intval($data_arr["总"]["预约"]["本月"]);
		$d["本月预到"]["data"] = intval($data_arr["总"]["预到"]["本月"]);
		$d["本月实到"]["data"] = intval($data_arr["总"]["实到"]["本月"]);
		$d["本月未到"]["data"] = $d["本月预到"]["data"] - $d["本月实到"]["data"];

		$d["上月预约"]["link"] = "/m/patient/patient.php?show=thismonth&time_type=addtime";
		$d["本月预到"]["link"] = "/m/patient/patient.php?show=thismonth";
		$d["本月实到"]["link"] = "/m/patient/patient.php?show=thismonth&come=1";
		$d["本月未到"]["link"] = "/m/patient/patient.php?show=thismonth&come=0";


		// 上月:
		$d["上月预约"]["data"] = intval($data_arr["总"]["预约"]["上月"]);
		$d["上月预到"]["data"] = intval($data_arr["总"]["预到"]["上月"]);
		$d["上月实到"]["data"] = intval($data_arr["总"]["实到"]["上月"]);
		$d["上月未到"]["data"] = $d["上月预到"]["data"] - $d["上月实到"]["data"];

		$d["上月预约"]["link"] = "/m/patient/patient.php?show=lastmonth&time_type=addtime";
		$d["上月预到"]["link"] = "/m/patient/patient.php?show=lastmonth";
		$d["上月实到"]["link"] = "/m/patient/patient.php?show=lastmonth&come=1";
		$d["上月未到"]["link"] = "/m/patient/patient.php?show=lastmonth&come=0";


		// 同比:
		$d["同比预约"]["data"] = intval($data_arr["总"]["预约"]["同比"]);
		$d["同比预到"]["data"] = intval($data_arr["总"]["预到"]["同比"]);
		$d["同比实到"]["data"] = intval($data_arr["总"]["实到"]["同比"]);
		$d["同比未到"]["data"] = $d["同比预到"]["data"] - $d["同比实到"]["data"];

		$index_data["总"] = $d;
	}


	// 网络
	if (in_array("web", $data_power) || $debug_mode) {
		$d = array();

		$d["今日预约"]["data"] = intval($data_arr["网络"]["预约"]["今日"]);
		$d["今日预到"]["data"] = intval($data_arr["网络"]["预到"]["今日"]);
		$d["今日实到"]["data"] = intval($data_arr["网络"]["实到"]["今日"]);
		$d["今日未到"]["data"] = $d["今日预到"]["data"] - $d["今日实到"]["data"];

		$d["今日预约"]["link"] = "/m/patient/patient.php?part_id=2&show=today&time_type=addtime";
		$d["今日预到"]["link"] = "/m/patient/patient.php?part_id=2&show=today";
		$d["今日实到"]["link"] = "/m/patient/patient.php?part_id=2&show=today&come=1";
		$d["今日未到"]["link"] = "/m/patient/patient.php?part_id=2&show=today&come=0";


		// 月比:
		if ($yuebi_tb > 0) {
			$d["月比预约"]["data"] = intval($data_arr["网络"]["预约"]["月比"]);
			$d["月比预到"]["data"] = intval($data_arr["网络"]["预到"]["月比"]);
			$d["月比实到"]["data"] = intval($data_arr["网络"]["实到"]["月比"]);
			$d["月比未到"]["data"] = $d["月比预到"]["data"] - $d["月比实到"]["data"];
		} else {
			$d["月比预约"]["data"] = $d["月比预到"]["data"] = $d["月比实到"]["data"] = $d["月比未到"]["data"] = "-";
		}


		// 周比:
		$d["周比预约"]["data"] = intval($data_arr["网络"]["预约"]["周比"]);
		$d["周比预到"]["data"] = intval($data_arr["网络"]["预到"]["周比"]);
		$d["周比实到"]["data"] = intval($data_arr["网络"]["实到"]["周比"]);
		$d["周比未到"]["data"] = $d["周比预到"]["data"] - $d["周比实到"]["data"];


		// 昨日:
		$d["昨日预约"]["data"] = intval($data_arr["网络"]["预约"]["昨日"]);
		$d["昨日预到"]["data"] = intval($data_arr["网络"]["预到"]["昨日"]);
		$d["昨日实到"]["data"] = intval($data_arr["网络"]["实到"]["昨日"]);
		$d["昨日未到"]["data"] = $d["昨日预到"]["data"] - $d["昨日实到"]["data"];

		$d["昨日预约"]["link"] = "/m/patient/patient.php?part_id=2&show=yesterday&time_type=addtime";
		$d["昨日预到"]["link"] = "/m/patient/patient.php?part_id=2&show=yesterday";
		$d["昨日实到"]["link"] = "/m/patient/patient.php?part_id=2&show=yesterday&come=1";
		$d["昨日未到"]["link"] = "/m/patient/patient.php?part_id=2&show=yesterday&come=0";


		// 本月：
		$d["本月预约"]["data"] = intval($data_arr["网络"]["预约"]["本月"]);
		$d["本月预到"]["data"] = intval($data_arr["网络"]["预到"]["本月"]);
		$d["本月实到"]["data"] = intval($data_arr["网络"]["实到"]["本月"]);
		$d["本月未到"]["data"] = $d["本月预到"]["data"] - $d["本月实到"]["data"];

		$d["上月预约"]["link"] = "/m/patient/patient.php?part_id=2&show=thismonth&time_type=addtime";
		$d["本月预到"]["link"] = "/m/patient/patient.php?part_id=2&show=thismonth";
		$d["本月实到"]["link"] = "/m/patient/patient.php?part_id=2&show=thismonth&come=1";
		$d["本月未到"]["link"] = "/m/patient/patient.php?part_id=2&show=thismonth&come=0";


		// 上月:
		$d["上月预约"]["data"] = intval($data_arr["网络"]["预约"]["上月"]);
		$d["上月预到"]["data"] = intval($data_arr["网络"]["预到"]["上月"]);
		$d["上月实到"]["data"] = intval($data_arr["网络"]["实到"]["上月"]);
		$d["上月未到"]["data"] = $d["上月预到"]["data"] - $d["上月实到"]["data"];

		$d["上月预约"]["link"] = "/m/patient/patient.php?part_id=2&show=lastmonth&time_type=addtime";
		$d["上月预到"]["link"] = "/m/patient/patient.php?part_id=2&show=lastmonth";
		$d["上月实到"]["link"] = "/m/patient/patient.php?part_id=2&show=lastmonth&come=1";
		$d["上月未到"]["link"] = "/m/patient/patient.php?part_id=2&show=lastmonth&come=0";


		// 同比:
		$d["同比预约"]["data"] = intval($data_arr["网络"]["预约"]["同比"]);
		$d["同比预到"]["data"] = intval($data_arr["网络"]["预到"]["同比"]);
		$d["同比实到"]["data"] = intval($data_arr["网络"]["实到"]["同比"]);
		$d["同比未到"]["data"] = $d["同比预到"]["data"] - $d["同比实到"]["data"];

		$index_data["网络"] = $d;
	}



	// 电话
	if (in_array("tel", $data_power) || $debug_mode) {
		$d = array();

		$d["今日预约"]["data"] = intval($data_arr["电话"]["预约"]["今日"]);
		$d["今日预到"]["data"] = intval($data_arr["电话"]["预到"]["今日"]);
		$d["今日实到"]["data"] = intval($data_arr["电话"]["实到"]["今日"]);
		$d["今日未到"]["data"] = $d["今日预到"]["data"] - $d["今日实到"]["data"];

		$d["今日预约"]["link"] = "/m/patient/patient.php?part_id=3&show=today&time_type=addtime";
		$d["今日预到"]["link"] = "/m/patient/patient.php?part_id=3&show=today";
		$d["今日实到"]["link"] = "/m/patient/patient.php?part_id=3&show=today&come=1";
		$d["今日未到"]["link"] = "/m/patient/patient.php?part_id=3&show=today&come=0";


		// 月比:
		if ($yuebi_tb > 0) {
			$d["月比预约"]["data"] = intval($data_arr["电话"]["预约"]["月比"]);
			$d["月比预到"]["data"] = intval($data_arr["电话"]["预到"]["月比"]);
			$d["月比实到"]["data"] = intval($data_arr["电话"]["实到"]["月比"]);
			$d["月比未到"]["data"] = $d["月比预到"]["data"] - $d["月比实到"]["data"];
		} else {
			$d["月比预约"]["data"] = $d["月比预到"]["data"] = $d["月比实到"]["data"] = $d["月比未到"]["data"] = "-";
		}


		// 周比:
		$d["周比预约"]["data"] = intval($data_arr["电话"]["预约"]["周比"]);
		$d["周比预到"]["data"] = intval($data_arr["电话"]["预到"]["周比"]);
		$d["周比实到"]["data"] = intval($data_arr["电话"]["实到"]["周比"]);
		$d["周比未到"]["data"] = $d["周比预到"]["data"] - $d["周比实到"]["data"];


		// 昨日:
		$d["昨日预约"]["data"] = intval($data_arr["电话"]["预约"]["昨日"]);
		$d["昨日预到"]["data"] = intval($data_arr["电话"]["预到"]["昨日"]);
		$d["昨日实到"]["data"] = intval($data_arr["电话"]["实到"]["昨日"]);
		$d["昨日未到"]["data"] = $d["昨日预到"]["data"] - $d["昨日实到"]["data"];

		$d["昨日预约"]["link"] = "/m/patient/patient.php?part_id=3&show=yesterday&time_type=addtime";
		$d["昨日预到"]["link"] = "/m/patient/patient.php?part_id=3&show=yesterday";
		$d["昨日实到"]["link"] = "/m/patient/patient.php?part_id=3&show=yesterday&come=1";
		$d["昨日未到"]["link"] = "/m/patient/patient.php?part_id=3&show=yesterday&come=0";


		// 本月：
		$d["本月预约"]["data"] = intval($data_arr["电话"]["预约"]["本月"]);
		$d["本月预到"]["data"] = intval($data_arr["电话"]["预到"]["本月"]);
		$d["本月实到"]["data"] = intval($data_arr["电话"]["实到"]["本月"]);
		$d["本月未到"]["data"] = $d["本月预到"]["data"] - $d["本月实到"]["data"];

		$d["上月预约"]["link"] = "/m/patient/patient.php?part_id=3&show=thismonth&time_type=addtime";
		$d["本月预到"]["link"] = "/m/patient/patient.php?part_id=3&show=thismonth";
		$d["本月实到"]["link"] = "/m/patient/patient.php?part_id=3&show=thismonth&come=1";
		$d["本月未到"]["link"] = "/m/patient/patient.php?part_id=3&show=thismonth&come=0";


		// 上月:
		$d["上月预约"]["data"] = intval($data_arr["电话"]["预约"]["上月"]);
		$d["上月预到"]["data"] = intval($data_arr["电话"]["预到"]["上月"]);
		$d["上月实到"]["data"] = intval($data_arr["电话"]["实到"]["上月"]);
		$d["上月未到"]["data"] = $d["上月预到"]["data"] - $d["上月实到"]["data"];

		$d["上月预约"]["link"] = "/m/patient/patient.php?part_id=3&show=lastmonth&time_type=addtime";
		$d["上月预到"]["link"] = "/m/patient/patient.php?part_id=3&show=lastmonth";
		$d["上月实到"]["link"] = "/m/patient/patient.php?part_id=3&show=lastmonth&come=1";
		$d["上月未到"]["link"] = "/m/patient/patient.php?part_id=3&show=lastmonth&come=0";


		// 同比:
		$d["同比预约"]["data"] = intval($data_arr["电话"]["预约"]["同比"]);
		$d["同比预到"]["data"] = intval($data_arr["电话"]["预到"]["同比"]);
		$d["同比实到"]["data"] = intval($data_arr["电话"]["实到"]["同比"]);
		$d["同比未到"]["data"] = $d["同比预到"]["data"] - $d["同比实到"]["data"];

		$index_data["电话"] = $d;
	}


	$index_module_arr = $db->query("select name from index_module_set where isshow=1 order by sort desc, id asc");
	foreach ($index_module_arr as $z) {
		if ($debug_mode || in_array($z["name"], $data_power)) {
			$d = array();

			$d["今日预约"]["data"] = intval($data_arr[$z["name"]]["预约"]["今日"]);
			$d["昨日预约"]["data"] = intval($data_arr[$z["name"]]["预约"]["昨日"]);
			$d["本月预约"]["data"] = intval($data_arr[$z["name"]]["预约"]["本月"]);
			$d["上月预约"]["data"] = intval($data_arr[$z["name"]]["预约"]["上月"]);

			$d["今日预约"]["link"] = "/m/patient/patient.php?index_module=".urlencode($z["name"])."&show=today&time_type=addtime";
			$d["昨日预约"]["link"] = "/m/patient/patient.php?index_module=".urlencode($z["name"])."&show=yesterday&time_type=addtime";
			$d["本月预约"]["link"] = "/m/patient/patient.php?index_module=".urlencode($z["name"])."&show=thismonth&time_type=addtime";
			$d["上月预约"]["link"] = "/m/patient/patient.php?index_module=".urlencode($z["name"])."&show=lastmonth&time_type=addtime";

			$d["今日预到"]["data"] = intval($data_arr[$z["name"]]["预到"]["今日"]);
			$d["昨日预到"]["data"] = intval($data_arr[$z["name"]]["预到"]["昨日"]);
			$d["本月预到"]["data"] = intval($data_arr[$z["name"]]["预到"]["本月"]);
			$d["上月预到"]["data"] = intval($data_arr[$z["name"]]["预到"]["上月"]);

			$d["今日预到"]["link"] = "/m/patient/patient.php?index_module=".urlencode($z["name"])."&show=today&time_type=order_date";
			$d["昨日预到"]["link"] = "/m/patient/patient.php?index_module=".urlencode($z["name"])."&show=yesterday&time_type=order_date";
			$d["本月预到"]["link"] = "/m/patient/patient.php?index_module=".urlencode($z["name"])."&show=thismonth&time_type=order_date";
			$d["上月预到"]["link"] = "/m/patient/patient.php?index_module=".urlencode($z["name"])."&show=lastmonth&time_type=order_date";

			$d["今日实到"]["data"] = intval($data_arr[$z["name"]]["实到"]["今日"]);
			$d["昨日实到"]["data"] = intval($data_arr[$z["name"]]["实到"]["昨日"]);
			$d["本月实到"]["data"] = intval($data_arr[$z["name"]]["实到"]["本月"]);
			$d["上月实到"]["data"] = intval($data_arr[$z["name"]]["实到"]["上月"]);

			$d["今日实到"]["link"] = "/m/patient/patient.php?index_module=".urlencode($z["name"])."&come=1&show=today&time_type=order_date";
			$d["昨日实到"]["link"] = "/m/patient/patient.php?index_module=".urlencode($z["name"])."&come=1&show=yesterday&time_type=order_date";
			$d["本月实到"]["link"] = "/m/patient/patient.php?index_module=".urlencode($z["name"])."&come=1&show=thismonth&time_type=order_date";
			$d["上月实到"]["link"] = "/m/patient/patient.php?index_module=".urlencode($z["name"])."&come=1&show=lastmonth&time_type=order_date";

			$index_data[$z["name"]] = $d;
		}
	}

	return $index_data;
}

?>