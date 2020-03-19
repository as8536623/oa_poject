<?php
/*
// ˵��: main.hospital.php1
// ����: ���� (weelia@126.com)
// ʱ��: 2013-4-13
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
		
		$d["������ѯ"]["data"] = intval($data_arr["��"]["��ѯ"]["����"]);
		$d["����ԤԼ"]["data"] = intval($data_arr["��"]["ԤԼ"]["����"]);
		$d["����Ԥ��"]["data"] = intval($data_arr["��"]["Ԥ��"]["����"]);
		$d["����ʵ��"]["data"] = intval($data_arr["��"]["ʵ��"]["����"]);
		$d["����δ��"]["data"] = intval($d["����Ԥ��"]["data"] - $d["����ʵ��"]["data"]);
		$d["���ո���"]["data"] = intval($data_arr["��"]["����"]["����"]);
		
		$d["������ѯ"]["link"] = "/m/ziliaoku/ku_list1.php?show=today";
		$d["����ԤԼ"]["link"] = "/m/patient/patient.php?show=today&time_type=addtime";
		$d["����Ԥ��"]["link"] = "/m/patient/patient.php?show=today";
		$d["����ʵ��"]["link"] = "/m/patient/patient.php?show=today&come=1";
		$d["����δ��"]["link"] = "/m/patient/patient.php?show=today&come=0";
        $d["���ո���"]["link"] = "/m/patient/patient.php?show=today&come=4";

		// �±�:
		if ($yuebi_tb > 0) {
			$d["�±�ԤԼ"]["data"] = intval($data_arr["��"]["ԤԼ"]["�±�"]);
			$d["�±�Ԥ��"]["data"] = intval($data_arr["��"]["Ԥ��"]["�±�"]);
			$d["�±�ʵ��"]["data"] = intval($data_arr["��"]["ʵ��"]["�±�"]);
			$d["�±�δ��"]["data"] = $d["�±�Ԥ��"]["data"] - $d["�±�ʵ��"]["data"];
		} else {
			$d["�±�ԤԼ"]["data"] = $d["�±�Ԥ��"]["data"] = $d["�±�ʵ��"]["data"] = $d["�±�δ��"]["data"] = "-";
		}


		// �ܱ�:
		$d["�ܱ�ԤԼ"]["data"] = intval($data_arr["��"]["ԤԼ"]["�ܱ�"]);
		$d["�ܱ�Ԥ��"]["data"] = intval($data_arr["��"]["Ԥ��"]["�ܱ�"]);
		$d["�ܱ�ʵ��"]["data"] = intval($data_arr["��"]["ʵ��"]["�ܱ�"]);
		$d["�ܱ�δ��"]["data"] = $d["�ܱ�Ԥ��"]["data"] - $d["�ܱ�ʵ��"]["data"];


		// ����:
		$d["������ѯ"]["data"] = intval($data_arr["��"]["��ѯ"]["����"]);
		$d["����ԤԼ"]["data"] = intval($data_arr["��"]["ԤԼ"]["����"]);
		$d["����Ԥ��"]["data"] = intval($data_arr["��"]["Ԥ��"]["����"]);
		$d["����ʵ��"]["data"] = intval($data_arr["��"]["ʵ��"]["����"]);
		$d["����δ��"]["data"] = $d["����Ԥ��"]["data"] - $d["����ʵ��"]["data"];
        $d["���ո���"]["data"] = intval($data_arr["��"]["����"]["����"]);
		
		$d["������ѯ"]["link"] = "/m/ziliaoku/ku_list1.php?show=yesterday";
		$d["����ԤԼ"]["link"] = "/m/patient/patient.php?show=yesterday&time_type=addtime";
		$d["����Ԥ��"]["link"] = "/m/patient/patient.php?show=yesterday";
		$d["����ʵ��"]["link"] = "/m/patient/patient.php?show=yesterday&come=1";
		$d["����δ��"]["link"] = "/m/patient/patient.php?show=yesterday&come=0";


		// ���£�
		$d["������ѯ"]["data"] = intval($data_arr["��"]["��ѯ"]["����"]);
		$d["����ԤԼ"]["data"] = intval($data_arr["��"]["ԤԼ"]["����"]);
		$d["����Ԥ��"]["data"] = intval($data_arr["��"]["Ԥ��"]["����"]);
		$d["����ʵ��"]["data"] = intval($data_arr["��"]["ʵ��"]["����"]);
		$d["����δ��"]["data"] = $d["����Ԥ��"]["data"] - $d["����ʵ��"]["data"];
        $d["���¸���"]["data"] = intval($data_arr["��"]["����"]["����"]);
		
		$d["������ѯ"]["link"] = "/m/ziliaoku/ku_list1.php?show=thismonth";
		$d["����ԤԼ"]["link"] = "/m/patient/patient.php?show=thismonth&time_type=addtime";
		$d["����Ԥ��"]["link"] = "/m/patient/patient.php?show=thismonth";
		$d["����ʵ��"]["link"] = "/m/patient/patient.php?show=thismonth&come=1";
		$d["����δ��"]["link"] = "/m/patient/patient.php?show=thismonth&come=0";


		// ����:
		$d["������ѯ"]["data"] = intval($data_arr["��"]["��ѯ"]["����"]);
		$d["����ԤԼ"]["data"] = intval($data_arr["��"]["ԤԼ"]["����"]);
		$d["����Ԥ��"]["data"] = intval($data_arr["��"]["Ԥ��"]["����"]);
		$d["����ʵ��"]["data"] = intval($data_arr["��"]["ʵ��"]["����"]);
		$d["����δ��"]["data"] = $d["����Ԥ��"]["data"] - $d["����ʵ��"]["data"];
        $d["������Ч"]["data"] = $d["����ԤԼ"]["data"] - $d["����Ԥ��"]["data"];
		$d["���¸���"]["data"] = intval($data_arr["��"]["����"]["����"]);
		$d["������ѯ"]["link"] = "/m/ziliaoku/ku_list1.php?show=lastmonth";
		$d["����ԤԼ"]["link"] = "/m/patient/patient.php?show=lastmonth&time_type=addtime";
		$d["����Ԥ��"]["link"] = "/m/patient/patient.php?show=lastmonth";
		$d["����ʵ��"]["link"] = "/m/patient/patient.php?show=lastmonth&come=1";
		$d["����δ��"]["link"] = "/m/patient/patient.php?show=lastmonth&come=0";


		// ͬ��:
		$d["ͬ����ѯ"]["data"] = intval($data_arr["��"]["��ѯ"]["ͬ��"]);
		$d["ͬ��ԤԼ"]["data"] = intval($data_arr["��"]["ԤԼ"]["ͬ��"]);
		$d["ͬ��Ԥ��"]["data"] = intval($data_arr["��"]["Ԥ��"]["ͬ��"]);
		$d["ͬ��ʵ��"]["data"] = intval($data_arr["��"]["ʵ��"]["ͬ��"]);
		$d["ͬ��δ��"]["data"] = $d["ͬ��Ԥ��"]["data"] - $d["ͬ��ʵ��"]["data"];
		
		
		//���ˣ�
		$author_arr = $db->query("select realname from sys_admin where isshow = 1 and (part_id = 2 or part_id = 209) and hospitals like '%".$_hid."%'");
		if($author_arr){
			foreach($author_arr as $n){
				$d[$n['realname']."��ѯ"]["data"] = intval($data_arr["����_".$n['realname']]["��ѯ"]["����"]);
				$d[$n['realname']."ԤԼ"]["data"] = intval($data_arr["����_".$n['realname']]["ԤԼ"]["����"]);
				$d[$n['realname']."Ԥ��"]["data"] = intval($data_arr["����_".$n['realname']]["Ԥ��"]["����"]);
				$d[$n['realname']."ʵ��"]["data"] = intval($data_arr["����_".$n['realname']]["ʵ��"]["����"]);
				$d[$n['realname']."��ѯ"]["link"] = "/m/ziliaoku/ku_list1.php?show=thismonth&kefu_23_name=".$n['realname'];
				$d[$n['realname']."ԤԼ"]["link"] = "/m/patient/patient.php?show=thismonth&time_type=addtime&kefu_23_name=".$n['realname'];
				$d[$n['realname']."Ԥ��"]["link"] = "/m/patient/patient.php?show=thismonth&kefu_23_name=".$n['realname'];
				$d[$n['realname']."ʵ��"]["link"] = "/m/patient/patient.php?show=thismonth&come=1&kefu_23_name=".$n['realname'];
			}
		}
		
		
		
		

		$index_data["��"] = $d;
	}


	// ����
	if (in_array("web", $data_power) || $debug_mode) {
		$d = array();

		$d["����ԤԼ"]["data"] = intval($data_arr["����"]["ԤԼ"]["����"]);
		$d["����Ԥ��"]["data"] = intval($data_arr["����"]["Ԥ��"]["����"]);
		$d["����ʵ��"]["data"] = intval($data_arr["����"]["ʵ��"]["����"]);
		$d["����δ��"]["data"] = $d["����Ԥ��"]["data"] - $d["����ʵ��"]["data"];

		$d["����ԤԼ"]["link"] = "/m/patient/patient.php?part_id=2&show=today&time_type=addtime";
		$d["����Ԥ��"]["link"] = "/m/patient/patient.php?part_id=2&show=today";
		$d["����ʵ��"]["link"] = "/m/patient/patient.php?part_id=2&show=today&come=1";
		$d["����δ��"]["link"] = "/m/patient/patient.php?part_id=2&show=today&come=0";


		// �±�:
		if ($yuebi_tb > 0) {
			$d["�±�ԤԼ"]["data"] = intval($data_arr["����"]["ԤԼ"]["�±�"]);
			$d["�±�Ԥ��"]["data"] = intval($data_arr["����"]["Ԥ��"]["�±�"]);
			$d["�±�ʵ��"]["data"] = intval($data_arr["����"]["ʵ��"]["�±�"]);
			$d["�±�δ��"]["data"] = $d["�±�Ԥ��"]["data"] - $d["�±�ʵ��"]["data"];
		} else {
			$d["�±�ԤԼ"]["data"] = $d["�±�Ԥ��"]["data"] = $d["�±�ʵ��"]["data"] = $d["�±�δ��"]["data"] = "-";
		}


		// �ܱ�:
		$d["�ܱ�ԤԼ"]["data"] = intval($data_arr["����"]["ԤԼ"]["�ܱ�"]);
		$d["�ܱ�Ԥ��"]["data"] = intval($data_arr["����"]["Ԥ��"]["�ܱ�"]);
		$d["�ܱ�ʵ��"]["data"] = intval($data_arr["����"]["ʵ��"]["�ܱ�"]);
		$d["�ܱ�δ��"]["data"] = $d["�ܱ�Ԥ��"]["data"] - $d["�ܱ�ʵ��"]["data"];


		// ����:
		$d["����ԤԼ"]["data"] = intval($data_arr["����"]["ԤԼ"]["����"]);
		$d["����Ԥ��"]["data"] = intval($data_arr["����"]["Ԥ��"]["����"]);
		$d["����ʵ��"]["data"] = intval($data_arr["����"]["ʵ��"]["����"]);
		$d["����δ��"]["data"] = $d["����Ԥ��"]["data"] - $d["����ʵ��"]["data"];

		$d["����ԤԼ"]["link"] = "/m/patient/patient.php?part_id=2&show=yesterday&time_type=addtime";
		$d["����Ԥ��"]["link"] = "/m/patient/patient.php?part_id=2&show=yesterday";
		$d["����ʵ��"]["link"] = "/m/patient/patient.php?part_id=2&show=yesterday&come=1";
		$d["����δ��"]["link"] = "/m/patient/patient.php?part_id=2&show=yesterday&come=0";


		// ���£�
		$d["����ԤԼ"]["data"] = intval($data_arr["����"]["ԤԼ"]["����"]);
		$d["����Ԥ��"]["data"] = intval($data_arr["����"]["Ԥ��"]["����"]);
		$d["����ʵ��"]["data"] = intval($data_arr["����"]["ʵ��"]["����"]);
		$d["����δ��"]["data"] = $d["����Ԥ��"]["data"] - $d["����ʵ��"]["data"];

		$d["����ԤԼ"]["link"] = "/m/patient/patient.php?part_id=2&show=thismonth&time_type=addtime";
		$d["����Ԥ��"]["link"] = "/m/patient/patient.php?part_id=2&show=thismonth";
		$d["����ʵ��"]["link"] = "/m/patient/patient.php?part_id=2&show=thismonth&come=1";
		$d["����δ��"]["link"] = "/m/patient/patient.php?part_id=2&show=thismonth&come=0";


		// ����:
		$d["����ԤԼ"]["data"] = intval($data_arr["����"]["ԤԼ"]["����"]);
		$d["����Ԥ��"]["data"] = intval($data_arr["����"]["Ԥ��"]["����"]);
		$d["����ʵ��"]["data"] = intval($data_arr["����"]["ʵ��"]["����"]);
		$d["����δ��"]["data"] = $d["����Ԥ��"]["data"] - $d["����ʵ��"]["data"];

		$d["����ԤԼ"]["link"] = "/m/patient/patient.php?part_id=2&show=lastmonth&time_type=addtime";
		$d["����Ԥ��"]["link"] = "/m/patient/patient.php?part_id=2&show=lastmonth";
		$d["����ʵ��"]["link"] = "/m/patient/patient.php?part_id=2&show=lastmonth&come=1";
		$d["����δ��"]["link"] = "/m/patient/patient.php?part_id=2&show=lastmonth&come=0";


		// ͬ��:
		$d["ͬ��ԤԼ"]["data"] = intval($data_arr["����"]["ԤԼ"]["ͬ��"]);
		$d["ͬ��Ԥ��"]["data"] = intval($data_arr["����"]["Ԥ��"]["ͬ��"]);
		$d["ͬ��ʵ��"]["data"] = intval($data_arr["����"]["ʵ��"]["ͬ��"]);
		$d["ͬ��δ��"]["data"] = $d["ͬ��Ԥ��"]["data"] - $d["ͬ��ʵ��"]["data"];

		$index_data["����"] = $d;
	}



	// �绰
	if (in_array("tel", $data_power) || $debug_mode) {
		$d = array();

		$d["����ԤԼ"]["data"] = intval($data_arr["�绰"]["ԤԼ"]["����"]);
		$d["����Ԥ��"]["data"] = intval($data_arr["�绰"]["Ԥ��"]["����"]);
		$d["����ʵ��"]["data"] = intval($data_arr["�绰"]["ʵ��"]["����"]);
		$d["����δ��"]["data"] = $d["����Ԥ��"]["data"] - $d["����ʵ��"]["data"];

		$d["����ԤԼ"]["link"] = "/m/patient/patient.php?part_id=3&show=today&time_type=addtime";
		$d["����Ԥ��"]["link"] = "/m/patient/patient.php?part_id=3&show=today";
		$d["����ʵ��"]["link"] = "/m/patient/patient.php?part_id=3&show=today&come=1";
		$d["����δ��"]["link"] = "/m/patient/patient.php?part_id=3&show=today&come=0";


		// �±�:
		if ($yuebi_tb > 0) {
			$d["�±�ԤԼ"]["data"] = intval($data_arr["�绰"]["ԤԼ"]["�±�"]);
			$d["�±�Ԥ��"]["data"] = intval($data_arr["�绰"]["Ԥ��"]["�±�"]);
			$d["�±�ʵ��"]["data"] = intval($data_arr["�绰"]["ʵ��"]["�±�"]);
			$d["�±�δ��"]["data"] = $d["�±�Ԥ��"]["data"] - $d["�±�ʵ��"]["data"];
		} else {
			$d["�±�ԤԼ"]["data"] = $d["�±�Ԥ��"]["data"] = $d["�±�ʵ��"]["data"] = $d["�±�δ��"]["data"] = "-";
		}


		// �ܱ�:
		$d["�ܱ�ԤԼ"]["data"] = intval($data_arr["�绰"]["ԤԼ"]["�ܱ�"]);
		$d["�ܱ�Ԥ��"]["data"] = intval($data_arr["�绰"]["Ԥ��"]["�ܱ�"]);
		$d["�ܱ�ʵ��"]["data"] = intval($data_arr["�绰"]["ʵ��"]["�ܱ�"]);
		$d["�ܱ�δ��"]["data"] = $d["�ܱ�Ԥ��"]["data"] - $d["�ܱ�ʵ��"]["data"];


		// ����:
		$d["����ԤԼ"]["data"] = intval($data_arr["�绰"]["ԤԼ"]["����"]);
		$d["����Ԥ��"]["data"] = intval($data_arr["�绰"]["Ԥ��"]["����"]);
		$d["����ʵ��"]["data"] = intval($data_arr["�绰"]["ʵ��"]["����"]);
		$d["����δ��"]["data"] = $d["����Ԥ��"]["data"] - $d["����ʵ��"]["data"];

		$d["����ԤԼ"]["link"] = "/m/patient/patient.php?part_id=3&show=yesterday&time_type=addtime";
		$d["����Ԥ��"]["link"] = "/m/patient/patient.php?part_id=3&show=yesterday";
		$d["����ʵ��"]["link"] = "/m/patient/patient.php?part_id=3&show=yesterday&come=1";
		$d["����δ��"]["link"] = "/m/patient/patient.php?part_id=3&show=yesterday&come=0";


		// ���£�
		$d["����ԤԼ"]["data"] = intval($data_arr["�绰"]["ԤԼ"]["����"]);
		$d["����Ԥ��"]["data"] = intval($data_arr["�绰"]["Ԥ��"]["����"]);
		$d["����ʵ��"]["data"] = intval($data_arr["�绰"]["ʵ��"]["����"]);
		$d["����δ��"]["data"] = $d["����Ԥ��"]["data"] - $d["����ʵ��"]["data"];

		$d["����ԤԼ"]["link"] = "/m/patient/patient.php?part_id=3&show=thismonth&time_type=addtime";
		$d["����Ԥ��"]["link"] = "/m/patient/patient.php?part_id=3&show=thismonth";
		$d["����ʵ��"]["link"] = "/m/patient/patient.php?part_id=3&show=thismonth&come=1";
		$d["����δ��"]["link"] = "/m/patient/patient.php?part_id=3&show=thismonth&come=0";


		// ����:
		$d["����ԤԼ"]["data"] = intval($data_arr["�绰"]["ԤԼ"]["����"]);
		$d["����Ԥ��"]["data"] = intval($data_arr["�绰"]["Ԥ��"]["����"]);
		$d["����ʵ��"]["data"] = intval($data_arr["�绰"]["ʵ��"]["����"]);
		$d["����δ��"]["data"] = $d["����Ԥ��"]["data"] - $d["����ʵ��"]["data"];

		$d["����ԤԼ"]["link"] = "/m/patient/patient.php?part_id=3&show=lastmonth&time_type=addtime";
		$d["����Ԥ��"]["link"] = "/m/patient/patient.php?part_id=3&show=lastmonth";
		$d["����ʵ��"]["link"] = "/m/patient/patient.php?part_id=3&show=lastmonth&come=1";
		$d["����δ��"]["link"] = "/m/patient/patient.php?part_id=3&show=lastmonth&come=0";


		// ͬ��:
		$d["ͬ��ԤԼ"]["data"] = intval($data_arr["�绰"]["ԤԼ"]["ͬ��"]);
		$d["ͬ��Ԥ��"]["data"] = intval($data_arr["�绰"]["Ԥ��"]["ͬ��"]);
		$d["ͬ��ʵ��"]["data"] = intval($data_arr["�绰"]["ʵ��"]["ͬ��"]);
		$d["ͬ��δ��"]["data"] = $d["ͬ��Ԥ��"]["data"] - $d["ͬ��ʵ��"]["data"];

		$index_data["�绰"] = $d;
	}


	$index_module_arr = $db->query("select name from index_module_set where isshow=1 order by sort desc, id asc");
	foreach ($index_module_arr as $z) {
		if ($debug_mode || in_array($z["name"], $data_power)) {
			$d = array();

			$d["����ԤԼ"]["data"] = intval($data_arr[$z["name"]]["ԤԼ"]["����"]);
			$d["����ԤԼ"]["data"] = intval($data_arr[$z["name"]]["ԤԼ"]["����"]);
			$d["����ԤԼ"]["data"] = intval($data_arr[$z["name"]]["ԤԼ"]["����"]);
			$d["����ԤԼ"]["data"] = intval($data_arr[$z["name"]]["ԤԼ"]["����"]);

			$d["����ԤԼ"]["link"] = "/m/patient/patient.php?index_module=".urlencode($z["name"])."&show=today&time_type=addtime";
			$d["����ԤԼ"]["link"] = "/m/patient/patient.php?index_module=".urlencode($z["name"])."&show=yesterday&time_type=addtime";
			$d["����ԤԼ"]["link"] = "/m/patient/patient.php?index_module=".urlencode($z["name"])."&show=thismonth&time_type=addtime";
			$d["����ԤԼ"]["link"] = "/m/patient/patient.php?index_module=".urlencode($z["name"])."&show=lastmonth&time_type=addtime";

			$d["����Ԥ��"]["data"] = intval($data_arr[$z["name"]]["Ԥ��"]["����"]);
			$d["����Ԥ��"]["data"] = intval($data_arr[$z["name"]]["Ԥ��"]["����"]);
			$d["����Ԥ��"]["data"] = intval($data_arr[$z["name"]]["Ԥ��"]["����"]);
			$d["����Ԥ��"]["data"] = intval($data_arr[$z["name"]]["Ԥ��"]["����"]);

			$d["����Ԥ��"]["link"] = "/m/patient/patient.php?index_module=".urlencode($z["name"])."&show=today&time_type=order_date";
			$d["����Ԥ��"]["link"] = "/m/patient/patient.php?index_module=".urlencode($z["name"])."&show=yesterday&time_type=order_date";
			$d["����Ԥ��"]["link"] = "/m/patient/patient.php?index_module=".urlencode($z["name"])."&show=thismonth&time_type=order_date";
			$d["����Ԥ��"]["link"] = "/m/patient/patient.php?index_module=".urlencode($z["name"])."&show=lastmonth&time_type=order_date";

			$d["����ʵ��"]["data"] = intval($data_arr[$z["name"]]["ʵ��"]["����"]);
			$d["����ʵ��"]["data"] = intval($data_arr[$z["name"]]["ʵ��"]["����"]);
			$d["����ʵ��"]["data"] = intval($data_arr[$z["name"]]["ʵ��"]["����"]);
			$d["����ʵ��"]["data"] = intval($data_arr[$z["name"]]["ʵ��"]["����"]);

			$d["����ʵ��"]["link"] = "/m/patient/patient.php?index_module=".urlencode($z["name"])."&come=1&show=today&time_type=order_date";
			$d["����ʵ��"]["link"] = "/m/patient/patient.php?index_module=".urlencode($z["name"])."&come=1&show=yesterday&time_type=order_date";
			$d["����ʵ��"]["link"] = "/m/patient/patient.php?index_module=".urlencode($z["name"])."&come=1&show=thismonth&time_type=order_date";
			$d["����ʵ��"]["link"] = "/m/patient/patient.php?index_module=".urlencode($z["name"])."&come=1&show=lastmonth&time_type=order_date";

			$index_data[$z["name"]] = $d;
		}
	}

	return $index_data;
}

?>