<?php
/*
// ˵��: ͳ��������ҳ
// ����: ���� (weelia@126.com)
// ʱ��: 2010-07-10
*/
$back_url = base64_encode($_SERVER["PHP_SELF"]);

$date = $_GET["date"] ? $_GET["date"] : date("Ymd");
$date_time = strtotime(substr($date,0,4)."-".substr($date,4,2)."-".substr($date,6,2)." 0:0:0");

$y_array = $m_array = $d_array = array();
for ($i = date("Y"); $i >= (date("Y") - 2); $i--) $y_array[] = $i;
for ($i = 1; $i <= 12; $i++) $m_array[] = $i;

//$max_days = cal_days_in_month(CAL_GREGORIAN, date("n", $date_time), date("Y", $date_time)); //�ú���δ����...
for ($i = 1; $i <= 31; $i++) {
	if ($i <= 28 || checkdate(date("n", $date_time), $i, date("Y", $date_time))) {
		$d_array[] = $i;
	}
}


// ҽԺ��Ϣ:
$h_info = $db->query("select * from hospital where id=$hid limit 1", 1);
$h_name = $h_info["name"];

// �����¹�վ��:
$h_sites = $db->query("select * from sites where hid=$hid", "id");

/*
// -----------   ��ѯͳ������ ---------------
*/
// ���嵥Ԫ���ʽ:
$list_heads = array(
	"�¹�վ��" => array("width"=>"150", "align"=>"center"),
	"IP" => array("width"=>"", "align"=>"center"),
	"PV" => array("width"=>"", "align"=>"center"),
	"���" => array("width"=>"", "align"=>"center"),
	"��Ч���" => array("width"=>"", "align"=>"center"),
	"�����" => array("width"=>"", "align"=>"center"),
	"����" => array("width"=>"150", "align"=>"center"),
);

// �б���ʾ��:
$t = load_class("table");
$t->set_head($list_heads, '', '');
$t->table_class = "new_list";

$h_site_ids = implode(",", array_keys($h_sites));
if ($h_site_ids) {
	$list = $db->query("select * from $table where date='$date' and site_id in ($h_site_ids)", "site_id");
}

$sum = array();
foreach ($h_sites as $h_sid => $v) {
	$li = $list[$h_sid];

	$r = $op = array();
	$r["�¹�վ��"] = '<span class="site_name"><a href="http://'.$v["url"].'" target="_blank" title="�������վ">'.$v["url"].'</a></span>';
	if ($li) {

		// sum����:
		foreach ($li as $a => $b) {
			if (is_numeric($b)) {
				$sum[$a] = floatval($sum[$a]) + $b;
			}
		}

		$r["IP"] = "<font color=red>".$li["ip"]."</font> | <font color=green>".$li["ip_local"]."</font> | ".$li["ip_other"];
		$r["PV"] = "<font color=red>".$li["pv"]."</font> | <font color=green>".$li["pv_local"]."</font> | ".$li["pv_other"];
		$r["���"] = "<font color=red>".$li["click"]."</font> | <font color=green>".$li["click_local"]."</font> | ".$li["click_other"];
		$r["��Ч���"] = "<font color=red>".$li["ok_click"]."</font> | <font color=green>".$li["ok_click_local"]."</font> | ".$li["ok_click_other"];
		$r["�����"] = $li["u_realname"];

		if (check_power("edit")) {
			$op[] = "<a href='?op=edit&id=".$li["id"]."&back_url=".$back_url."' class='op' title='�޸�'>�޸�</a>";
		}
		if (check_power("delete")) {
			$op[] = "<a href='?op=delete&id=".$li["id"]."' onclick='return isdel()' class='op'>ɾ��</a>";
		}
		$r["����"] = implode(" ", $op);
	} else {
		$r["IP"] = $r["PV"] = $r["���"] = $r["��Ч���"] = $r["�����"] = "";
		if (check_power("add")) {
			$op[] = "<a href='?op=add&date=$date&site_id=$h_sid&back_url=".$back_url."' class='op' title='�������'>���</a>";
		}
		$r["����"] = implode(" ", $op);
	}
	$t->add($r);
}

$t->add_tip_line("");
$sum["�¹�վ��"] = '<span class="site_name">����</span>';
if (count($list) > 0) {
	$sum["IP"] = "<font color=red>".$sum["ip"]."</font> | <font color=green>".$sum["ip_local"]."</font> | ".$sum["ip_other"];
	$sum["PV"] = "<font color=red>".$sum["pv"]."</font> | <font color=green>".$sum["pv_local"]."</font> | ".$sum["pv_other"];
	$sum["���"] = "<font color=red>".$sum["click"]."</font> | <font color=green>".$sum["click_local"]."</font> | ".$sum["click_other"];
	$sum["��Ч���"] = "<font color=red>".$sum["ok_click"]."</font> | <font color=green>".$sum["ok_click_local"]."</font> | ".$sum["ok_click_other"];
	$sum["�����"] = '-';
	$sum["����"] = '-';
}
$t->add($sum);



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