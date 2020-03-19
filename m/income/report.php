<?php
/*
// 说明: 收益统计系统 日期段报表
// 作者: 幽兰 (weelia@126.com)
// 时间: 2010-10-11
*/
$mod = $table = "income";
require "../../core/core.php";

// 数据定义
$back_url = base64_encode($_SERVER["PHP_SELF"]);

$h_name = $db->query("select * from hospital where id=$hid limit 1", 1, "name");

// 昨天
$yesterday_begin = strtotime("-1 day");
// 本周
$weekday = date("w");
if ($weekday == 0) $weekday = 7; //每周的开始为周一, 而不是周日
$this_week_begin = mktime(0, 0, 0, date("m"), (date("d") - $weekday + 1));
$this_week_end = strtotime("+6 days", $this_week_begin);
// 上周
$last_week_begin = strtotime("-7 days", $this_week_begin);
$last_week_end = strtotime("-1 days", $this_week_begin);
// 本月
$this_month_begin = mktime(0,0,0,date("m"), 1);
$this_month_end = strtotime("+1 month", $this_month_begin) - 1;
// 上个月
$last_month_end = $this_month_begin - 1;
$last_month_begin = strtotime("-1 month", $this_month_begin);
//今年
$this_year_begin = mktime(0,0,0,1,1);
$this_year_end = strtotime("+1 year", $this_year_begin) - 1;
// 最近一个月
$near_1_month_begin = strtotime("-1 month");
// 最近三个月
$near_3_month_begin = strtotime("-3 month");
// 最近一年
$near_1_year_begin = strtotime("-12 month");


$patient_all = $yingyee_all = 0;


if ($_GET["btime"] && $_GET["etime"]) {

	$btime = date("Ymd", strtotime($_GET["btime"]." 0:0:0"));
	$etime = date("Ymd", strtotime($_GET["etime"]." 23:59:59"));


	/*
	  ---------------------   门诊   ------------------------
	*/
	$res1 = array();

	// 定义单元格格式:
	$list_heads = array(
		"医生" => array("width"=>"", "align"=>"center"),
		"初诊人数" => array("width"=>"", "align"=>"center"),
		"复诊人数" => array("width"=>"", "align"=>"center"),
		"复诊率" => array("width"=>"", "align"=>"center"),
		"流失人数" => array("width"=>"", "align"=>"center"),
		"流失率" => array("width"=>"", "align"=>"center"),
	);

	$shoufei_array = explode("|", $hconfig["门诊收费项目"]);
	foreach ($shoufei_array as $k) {
		if ($k) {
			$list_heads[$k] = array("width"=>"", "align"=>"center");
			if (in_array($k, array("药品费", "治疗费", "手术费"))) {
				$list_heads[$k."率"] = array("width"=>"", "align"=>"center");
			}
		}
	}

	$list_heads["营业额"] = array("width"=>"", "align"=>"center");
	$list_heads["人均消费"] = array("width"=>"", "align"=>"center");


	// 列表显示类:
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
		$r["医生"] = $li["doctor_name"];
		$r["初诊人数"] = $li["chuzhen"];
		$r["复诊人数"] = $li["fuzhen"];
		$r["流失人数"] = $li["liushi"];

		$tmp = unserialize($li["detail"]);
		$r = array_merge($r, $tmp);

		$r["营业额"] = $li["yingyee"];
		$r["人均消费"] = $li["renjun"];

		foreach ($r as $k => $v) {
			if (is_numeric($v)) {
				$sum_list[$k] = floatval($sum_list[$k]) + $v;
			}
		}

		$res1[] = $r;
	}

	// 合并数据:
	$list1 = array();
	foreach ($res1 as $v) {
		if (!array_key_exists($v["医生"], $list1)) {
			$list1[$v["医生"]] = $v;
		} else {
			foreach ($v as $x => $y) {
				if ($y && is_numeric($y)) {
					$list1[$v["医生"]][$x] = floatval($list1[$v["医生"]][$x]) + $y;
				}
			}
		}
	}

	foreach ($list1 as $v) {
		$v["复诊率"] = floatval(@round($v["复诊人数"] / $v["初诊人数"], 2));
		$v["流失率"] = floatval(@round($v["流失人数"] / $v["初诊人数"] * 100, 2))."%";

		$v["药品费率"] = floatval(@round($v["药品费"] / $v["营业额"] * 100, 2))."%";
		$v["治疗费率"] = floatval(@round($v["治疗费"] / $v["营业额"] * 100, 2))."%";
		$v["手术费率"] = floatval(@round($v["手术费"] / $v["营业额"] * 100, 2))."%";

		$v["人均消费"] = @round($v["营业额"] / $v["初诊人数"], 2);
		$t->add($v);
	}

	if (count($list1) > 0) {
		$t->add_tip_line("");
		$sum_list["医生"] = "汇总";

		$sum_list["复诊率"] = floatval(@round($sum_list["复诊人数"] / $sum_list["初诊人数"], 2));
		$sum_list["流失率"] = floatval(@round($sum_list["流失人数"] / $sum_list["初诊人数"] * 100, 2))."%";

		$sum_list["药品费率"] = floatval(@round($sum_list["药品费"] / $sum_list["营业额"] * 100, 2))."%";
		$sum_list["治疗费率"] = floatval(@round($sum_list["治疗费"] / $sum_list["营业额"] * 100, 2))."%";
		$sum_list["手术费率"] = floatval(@round($sum_list["手术费"] / $sum_list["营业额"] * 100, 2))."%";

		if ($sum_list["人均消费"] > 0) {
			$sum_list["人均消费"] = round($sum_list["营业额"] / $sum_list["初诊人数"], 2);
		}
		$sum_list["操作"] = "-";
		$t->add($sum_list);

		$patient_all += $sum_list["初诊人数"];
		$yingyee_all += $sum_list["营业额"];
	}


	/*
	  --------------------   住院   -----------------------
	*/

	if ($hconfig["住院收费项目"] != '') {

		$res2 = array();

		// 定义单元格格式:
		$list_heads = array(
			"医生" => array("width"=>"", "align"=>"center"),
			"住院人数" => array("width"=>"", "align"=>"center"),
		);

		$shoufei_array = explode("|", $hconfig["住院收费项目"]);
		foreach ($shoufei_array as $k) {
			if ($k) {
				$list_heads[$k] = array("width"=>"", "align"=>"center");
			}
		}

		$list_heads["营业额"] = array("width"=>"", "align"=>"center");
		$list_heads["人均消费"] = array("width"=>"", "align"=>"center");

		// 列表显示类:
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
			$r["医生"] = $li["doctor_name"];
			$r["住院人数"] = $li["zhuyuan"];

			$tmp = unserialize($li["detail"]);
			$r = array_merge($r, $tmp);

			$r["营业额"] = $li["yingyee"];
			$r["人均消费"] = $li["renjun"];

			foreach ($r as $k => $v) {
				if (is_numeric($v)) {
					$sum_list[$k] = floatval($sum_list[$k]) + $v;
				}
			}

			$res2[] = array();
		}

		// 合并数据:
		$list2 = array();
		foreach ($res2 as $v) {
			if (!array_key_exists($v["医生"], $list2)) {
				$list2[$v["医生"]] = $v;
			} else {
				foreach ($v as $x => $y) {
					if ($y && is_numeric($y)) {
						$list2[$v["医生"]][$x] = floatval($list2[$v["医生"]][$x]) + $y;
					}
				}
			}
		}

		foreach ($list2 as $v) {
			$v["人均消费"] = round($v["营业额"] / $v["住院人数"], 2);
			$t2->add($v);
		}

		if (count($list2) > 0) {
			$t2->add_tip_line("");
			$sum_list["医生"] = "汇总";
			if ($sum_list["人均消费"] > 0) {
				$sum_list["人均消费"] = round($sum_list["营业额"] / $sum_list["住院人数"], 2);
			}
			$sum_list["操作"] = "-";
			$t2->add($sum_list);

			$yingyee_all += $sum_list["营业额"];
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