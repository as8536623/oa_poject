<?php

// ���ݶ���

$back_url = base64_encode($_SERVER["PHP_SELF"]);

$y_array = $m_array = $d_array = array();
for ($i = date("Y"); $i >= (date("Y") - 2); $i--) $y_array[] = $i;
for ($i = 1; $i <= 12; $i++) $m_array[] = $i;
for ($i = 1; $i <= 31; $i++) $d_array[] = $i;

$date = $_GET["date"] ? $_GET["date"] : date("Ymd");
$date_time = strtotime(substr($date,0,4)."-".substr($date,4,2)."-".substr($date,6,2)." 0:0:0");

$patient_all = $yingyee_all = 0;


/*
  ---------------------   ����   ------------------------
*/

// ���嵥Ԫ���ʽ:
$list_heads = array(
	"ҽ��" => array("width"=>"", "align"=>"center"),
	"��������" => array("width"=>"", "align"=>"center"),
	"��������" => array("width"=>"", "align"=>"center"),
	"��ʧ����" => array("width"=>"", "align"=>"center"),
);

$shoufei_array = explode("|", $hconfig["�����շ���Ŀ"]);
foreach ($shoufei_array as $k) {
	if ($k) {
		$list_heads[$k] = array("width"=>"", "align"=>"center");
	}
}

$list_heads["Ӫҵ��"] = array("width"=>"", "align"=>"center");
$list_heads["�˾�����"] = array("width"=>"", "align"=>"center");
$list_heads["¼��"] = array("width"=>"", "align"=>"center");
$list_heads["����"] = array("width"=>"80", "align"=>"center");


// �б���ʾ��:
$t = load_class("table");
$t->set_head($list_heads, '', '');
$t->table_class = "new_list";

$list = $db->query("select * from $table where hid=$hid and fee_type=0 and date=$date order by doctor_id", "id");
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
	$r["¼��"] = $li["u_realname"];
	if ($li["log"] != '') {
		//$r["�����"] .= ' <a href="#" title="'.rtrim(str_replace("<br>", "\r\n", $li["log"])).'">��</a>';
		$logs1[] = '<div class="m20">ҽ��: <b>'.$r["ҽ��"].'</b></div><div class="m40">'.$li["log"]."</div>";
	}

	$op = array();
	if (check_power("edit")) {
		$op[] = '<a href="?op=edit&id='.$li["id"]."&fee_type=".$li["fee_type"].'">�޸�</a>';
	}
	if (check_power("delete")) {
		$op[] = '<a href="?op=delete&id='.$li["id"].'" onclick="return confirm_delete()">ɾ��</a>';
	}
	$r["����"] = implode(" ", $op);

	foreach ($r as $k => $v) {
		if (is_numeric($v)) {
			$sum_list[$k] = floatval($sum_list[$k]) + $v;
		}
	}

	$t->add($r);
}

if (count($list) > 0) {
	$t->add_tip_line("");
	$sum_list["ҽ��"] = "����";
	if ($sum_list["�˾�����"] > 0) {
		$sum_list["�˾�����"] = round($sum_list["�˾�����"] / count($list), 2);
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
	$list_heads["¼��"] = array("width"=>"", "align"=>"center");
	$list_heads["����"] = array("width"=>"80", "align"=>"center");


	// �б���ʾ��:
	$t2 = load_class("table");
	$t2->set_head($list_heads, '', '');
	$t2->table_class = "new_list";

	$list = $db->query("select * from $table where hid=$hid and fee_type=1 and date=$date order by doctor_id", "id");
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
		$r["¼��"] = $li["u_realname"];

		if ($li["log"] != '') {
			//$r["�����"] .= ' <a href="#" title="'.rtrim(str_replace("<br>", "\r\n", $li["log"])).'">��</a>';
			$logs2[] = '<div class="m20">ҽ��: <b>'.$r["ҽ��"].'</b></div><div class="m40">'.$li["log"]."</div>";
		}

		$op = array();
		if (check_power("edit")) {
			$op[] = '<a href="?op=edit&id='.$li["id"]."&fee_type=".$li["fee_type"].'">�޸�</a>';
		}
		if (check_power("delete")) {
			$op[] = '<a href="?op=delete&id='.$li["id"].'" onclick="return confirm_delete()">ɾ��</a>';
		}
		$r["����"] = implode(" ", $op);

		foreach ($r as $k => $v) {
			if (is_numeric($v)) {
				$sum_list[$k] = floatval($sum_list[$k]) + $v;
			}
		}

		$t2->add($r);
	}

	if (count($list) > 0) {
		$t2->add_tip_line("");
		$sum_list["ҽ��"] = "����";
		if ($sum_list["�˾�����"] > 0) {
			$sum_list["�˾�����"] = round($sum_list["�˾�����"] / count($list), 2);
		}
		$sum_list["����"] = "-";
		$t2->add($sum_list);

		$yingyee_all += $sum_list["Ӫҵ��"];
	}
}

$logs_str = '';
if (count($logs1) > 0) {
	$logs_str .= '<div class="b">�����շ��޸ļ�¼</div>'.implode("", $logs1);
	$logs_str .= "<br>";
}
if (count($logs2) > 0) {
	$logs_str .= '<div class="b">סԺ�շ��޸ļ�¼</div>'.implode("", $logs2);
}

include $mod.".index.tpl.php";


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