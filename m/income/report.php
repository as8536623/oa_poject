<?php
/*
// ˵��: ����ͳ��ϵͳ ���ڶα���
// ����: ���� (weelia@126.com)
// ʱ��: 2010-10-11
*/
$mod = $table = "income";
require "../../core/core.php";

// ���ݶ���
$back_url = base64_encode($_SERVER["PHP_SELF"]);

$h_name = $db->query("select * from hospital where id=$hid limit 1", 1, "name");

// ����
$yesterday_begin = strtotime("-1 day");
// ����
$weekday = date("w");
if ($weekday == 0) $weekday = 7; //ÿ�ܵĿ�ʼΪ��һ, ����������
$this_week_begin = mktime(0, 0, 0, date("m"), (date("d") - $weekday + 1));
$this_week_end = strtotime("+6 days", $this_week_begin);
// ����
$last_week_begin = strtotime("-7 days", $this_week_begin);
$last_week_end = strtotime("-1 days", $this_week_begin);
// ����
$this_month_begin = mktime(0,0,0,date("m"), 1);
$this_month_end = strtotime("+1 month", $this_month_begin) - 1;
// �ϸ���
$last_month_end = $this_month_begin - 1;
$last_month_begin = strtotime("-1 month", $this_month_begin);
//����
$this_year_begin = mktime(0,0,0,1,1);
$this_year_end = strtotime("+1 year", $this_year_begin) - 1;
// ���һ����
$near_1_month_begin = strtotime("-1 month");
// ���������
$near_3_month_begin = strtotime("-3 month");
// ���һ��
$near_1_year_begin = strtotime("-12 month");


$patient_all = $yingyee_all = 0;


if ($_GET["btime"] && $_GET["etime"]) {

	$btime = date("Ymd", strtotime($_GET["btime"]." 0:0:0"));
	$etime = date("Ymd", strtotime($_GET["etime"]." 23:59:59"));


	/*
	  ---------------------   ����   ------------------------
	*/
	$res1 = array();

	// ���嵥Ԫ���ʽ:
	$list_heads = array(
		"ҽ��" => array("width"=>"", "align"=>"center"),
		"��������" => array("width"=>"", "align"=>"center"),
		"��������" => array("width"=>"", "align"=>"center"),
		"������" => array("width"=>"", "align"=>"center"),
		"��ʧ����" => array("width"=>"", "align"=>"center"),
		"��ʧ��" => array("width"=>"", "align"=>"center"),
	);

	$shoufei_array = explode("|", $hconfig["�����շ���Ŀ"]);
	foreach ($shoufei_array as $k) {
		if ($k) {
			$list_heads[$k] = array("width"=>"", "align"=>"center");
			if (in_array($k, array("ҩƷ��", "���Ʒ�", "������"))) {
				$list_heads[$k."��"] = array("width"=>"", "align"=>"center");
			}
		}
	}

	$list_heads["Ӫҵ��"] = array("width"=>"", "align"=>"center");
	$list_heads["�˾�����"] = array("width"=>"", "align"=>"center");


	// �б���ʾ��:
	$t = load_class("table");
	$t->set_head($list_heads, '', '');
	$t->table_class = "print_list";

	$list = $db->query("select * from $table where hid=$hid and fee_type=0 and date>=$btime and date<=$etime order by doctor_id", "id");
	if (!is_array($list)) {
		exit_html("Error: ".$db->sql);
	}

	$sum_list = array();
	$logs1 = $logs2 = array();

	foreach ($list as $id => $li) {
		$r = array();
		$r["ҽ��"] = $li["doctor_name"];
		$r["��������"] = $li["chuzhen"];
		$r["��������"] = $li["fuzhen"];
		$r["��ʧ����"] = $li["liushi"];

		$tmp = unserialize($li["detail"]);
		$r = array_merge($r, $tmp);

		$r["Ӫҵ��"] = $li["yingyee"];
		$r["�˾�����"] = $li["renjun"];

		foreach ($r as $k => $v) {
			if (is_numeric($v)) {
				$sum_list[$k] = floatval($sum_list[$k]) + $v;
			}
		}

		$res1[] = $r;
	}

	// �ϲ�����:
	$list1 = array();
	foreach ($res1 as $v) {
		if (!array_key_exists($v["ҽ��"], $list1)) {
			$list1[$v["ҽ��"]] = $v;
		} else {
			foreach ($v as $x => $y) {
				if ($y && is_numeric($y)) {
					$list1[$v["ҽ��"]][$x] = floatval($list1[$v["ҽ��"]][$x]) + $y;
				}
			}
		}
	}

	foreach ($list1 as $v) {
		$v["������"] = floatval(@round($v["��������"] / $v["��������"], 2));
		$v["��ʧ��"] = floatval(@round($v["��ʧ����"] / $v["��������"] * 100, 2))."%";

		$v["ҩƷ����"] = floatval(@round($v["ҩƷ��"] / $v["Ӫҵ��"] * 100, 2))."%";
		$v["���Ʒ���"] = floatval(@round($v["���Ʒ�"] / $v["Ӫҵ��"] * 100, 2))."%";
		$v["��������"] = floatval(@round($v["������"] / $v["Ӫҵ��"] * 100, 2))."%";

		$v["�˾�����"] = @round($v["Ӫҵ��"] / $v["��������"], 2);
		$t->add($v);
	}

	if (count($list1) > 0) {
		$t->add_tip_line("");
		$sum_list["ҽ��"] = "����";

		$sum_list["������"] = floatval(@round($sum_list["��������"] / $sum_list["��������"], 2));
		$sum_list["��ʧ��"] = floatval(@round($sum_list["��ʧ����"] / $sum_list["��������"] * 100, 2))."%";

		$sum_list["ҩƷ����"] = floatval(@round($sum_list["ҩƷ��"] / $sum_list["Ӫҵ��"] * 100, 2))."%";
		$sum_list["���Ʒ���"] = floatval(@round($sum_list["���Ʒ�"] / $sum_list["Ӫҵ��"] * 100, 2))."%";
		$sum_list["��������"] = floatval(@round($sum_list["������"] / $sum_list["Ӫҵ��"] * 100, 2))."%";

		if ($sum_list["�˾�����"] > 0) {
			$sum_list["�˾�����"] = round($sum_list["Ӫҵ��"] / $sum_list["��������"], 2);
		}
		$sum_list["����"] = "-";
		$t->add($sum_list);

		$patient_all += $sum_list["��������"];
		$yingyee_all += $sum_list["Ӫҵ��"];
	}


	/*
	  --------------------   סԺ   -----------------------
	*/

	if ($hconfig["סԺ�շ���Ŀ"] != '') {

		$res2 = array();

		// ���嵥Ԫ���ʽ:
		$list_heads = array(
			"ҽ��" => array("width"=>"", "align"=>"center"),
			"סԺ����" => array("width"=>"", "align"=>"center"),
		);

		$shoufei_array = explode("|", $hconfig["סԺ�շ���Ŀ"]);
		foreach ($shoufei_array as $k) {
			if ($k) {
				$list_heads[$k] = array("width"=>"", "align"=>"center");
			}
		}

		$list_heads["Ӫҵ��"] = array("width"=>"", "align"=>"center");
		$list_heads["�˾�����"] = array("width"=>"", "align"=>"center");

		// �б���ʾ��:
		$t2 = load_class("table");
		$t2->set_head($list_heads, '', '');
		$t2->table_class = "print_list";

		$list = $db->query("select * from $table where hid=$hid and fee_type=1 and date>=$btime and date<=$etime order by doctor_id", "id");
		if (!is_array($list)) {
			exit_html("Error: ".$db->sql);
		}

		$sum_list = array();

		foreach ($list as $id => $li) {
			$r = array();
			$r["ҽ��"] = $li["doctor_name"];
			$r["סԺ����"] = $li["zhuyuan"];

			$tmp = unserialize($li["detail"]);
			$r = array_merge($r, $tmp);

			$r["Ӫҵ��"] = $li["yingyee"];
			$r["�˾�����"] = $li["renjun"];

			foreach ($r as $k => $v) {
				if (is_numeric($v)) {
					$sum_list[$k] = floatval($sum_list[$k]) + $v;
				}
			}

			$res2[] = array();
		}

		// �ϲ�����:
		$list2 = array();
		foreach ($res2 as $v) {
			if (!array_key_exists($v["ҽ��"], $list2)) {
				$list2[$v["ҽ��"]] = $v;
			} else {
				foreach ($v as $x => $y) {
					if ($y && is_numeric($y)) {
						$list2[$v["ҽ��"]][$x] = floatval($list2[$v["ҽ��"]][$x]) + $y;
					}
				}
			}
		}

		foreach ($list2 as $v) {
			$v["�˾�����"] = round($v["Ӫҵ��"] / $v["סԺ����"], 2);
			$t2->add($v);
		}

		if (count($list2) > 0) {
			$t2->add_tip_line("");
			$sum_list["ҽ��"] = "����";
			if ($sum_list["�˾�����"] > 0) {
				$sum_list["�˾�����"] = round($sum_list["Ӫҵ��"] / $sum_list["סԺ����"], 2);
			}
			$sum_list["����"] = "-";
			$t2->add($sum_list);

			$yingyee_all += $sum_list["Ӫҵ��"];
		}
	}
}



include "report.tpl.php";


function my_show($arr, $default_value='', $click='') {
	$s = '';
	foreach ($arr as $v) {
		if ($v == $default_value) {
			$s .= '<b>'.$v.'</b>';
		} else {
			$s .= '<a href="#" onclick="'.$click.'">'.$v.'</a>';
		}
	}
	return $s;
}

?>