<?php
defined("ROOT") or exit;

// ���嵱ǰҳ��Ҫ�õ��ĵ��ò���:
$aLinkInfo = array("page", "sortid", "sorttype", "key", "s_ty", "s_con");

// ��ȡҳ����ò���:
foreach ($aLinkInfo as $local_var_name) {
	$$local_var_name = $_GET[$local_var_name];
}

// ���嵥Ԫ���ʽ:
$aOrderType = array(0 => "", 1 => "asc", 2 => "desc");
$aTdFormat = array(
	0=>array("title"=>"ѡ", "width"=>"32", "align"=>"center"),
	4=>array("title"=>"ID", "width"=>"50", "align"=>"center"),
	1=>array("title"=>"ҽԺ����", "width"=>"150", "align"=>"left"),
	9=>array("title"=>"����", "width"=>"80", "align"=>"center"),
	6=>array("title"=>"�¹���վ", "width"=>"", "align"=>"left"),
	7=>array("title"=>"SWT ID", "width"=>"100", "align"=>"center"),
	8=>array("title"=>"ָ���ط�", "width"=>"60", "align"=>"center"),
	2=>array("title"=>"���ʱ��", "width"=>"80", "align"=>"center"),
	5=>array("title"=>"���ȶ�", "width"=>"60", "align"=>"center"),
	3=>array("title"=>"����", "width"=>"80", "align"=>"center"),
);


// ��ѯ����:
$where = array();
if ($key) {
	$where[] = "(name like '%{$key}%' or area like '%{$key}%' or sname like '%{$key}%' or depart like '%{$key}%' or full_name like '%{$key}%' )";
}

if ($s_ty != '') {
	if ($s_ty == "area") {
		$where[] = "area='".$s_con."'";
	}
	if ($s_ty == "depart") {
		$where[] = "depart='".$s_con."'";
	}
}

$sqlwhere = count($where) > 0 ? ("where ".implode(" and ", $where)) : "";

// ����
$sqlsort = "order by sort desc, id asc";

// ��ҳ����:
$pagesize = 50;
$count = $db->query_count("select count(*) from $table $sqlwhere");
$pagecount = max(ceil($count / $pagesize), 1);
$page = max(min($pagecount, intval($page)), 1);
$offset = ($page - 1) * $pagesize;

// ��ѯ:
$data = $db->query("select * from $table $sqlwhere $sqlsort limit $offset,$pagesize");

// ��ѯ�¹���վ:
$hids = array();
foreach ($data as $k => $v) {
	$hids[] = $v["id"];
}

$hid_str = implode(",", $hids);
$hid_site = array();
if ($hid_str) {
	$urls = $db->query("select id,hid,url from sites where hid in ($hid_str)");
	foreach ($urls as $v) {
		$hid_site[$v["hid"]][] = '<a href="#edit_site" onclick="edit_site('.$v["hid"].', '.$v["id"].'); return false;" class="op">'.$v["url"].'</a>';
	}

	foreach ($data as $k => $v) {
		$data[$k]["sites"] = array();
		if (count($hid_site[$v["id"]]) > 0) {
			$data[$k]["sites"] = $hid_site[$v["id"]];
		}
		$data[$k]["sites"][] = '<a href="#add_site" onclick="add_site('.$v["id"].'); return false;" class="op">���</a>';
	}
}

include $mod.".list.tpl.php";

?>