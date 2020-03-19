<?php

// ���嵱ǰҳ��Ҫ�õ��ĵ��ò���:
$link_param = array("op", "sid", "page","sort","order","key","btime","etime","doctor_id");

// ���嵥Ԫ���ʽ:
$list_heads = array(
	"ѡ" => array("width"=>"32", "align"=>"center"),
	"����" => array("width"=>"80", "align"=>"center", "sort"=>"fee_type", "order"=>"asc"),
	"ʱ��" => array("width"=>"", "align"=>"center", "sort"=>"date", "order"=>"desc"),
	"ҽ��" => array("width"=>"", "align"=>"center", "sort"=>"doctor_id", "order"=>"desc"),
	"��Ч����" => array("width"=>"", "align"=>"center", "sort"=>"", "order"=>"desc"),
	"Ӫҵ��" => array("width"=>"", "align"=>"center", "sort"=>"yingyee", "order"=>"desc"),
	"�˾�����" => array("width"=>"", "align"=>"center", "sort"=>"renjun", "order"=>"desc"),
	"�����" => array("width"=>"", "align"=>"center", "sort"=>"uid", "order"=>"desc"),
	"����" => array("width"=>"150", "align"=>"center"),
);

// Ĭ������
$default_sort = "ʱ��";
$default_order = "desc";


$param = array();
foreach ($link_param as $s) {
	$param[$s] = $_GET[$s];
}
extract($param);

// �б���ʾ��:
$t = load_class("table");
$t->set_head($list_heads, $default_sort, $default_order);
$t->set_sort($_GET["sort"], $_GET["order"]);
$t->param = $param;
$t->table_class = "new_list";


// ��ѯ����:
$where = array();
$where[] = "hid=$hid";
if ($key) {
	$where[] = "(doctor_name like '%{$key}%' or u_realname='$key' or fee_typename='$key' or memo like '%{$key}%')";
}
if ($doctor_id) {
	$where[] = "doctor_id='".$doctor_id."'";
}
if ($btime) {
	$where[] = "date>=".intval(str_replace("-", "", $btime));
}
if ($etime) {
	$where[] = "date<=".intval(str_replace("-", "", $etime));
}

$sqlwhere = $db->make_where($where);

// ������Ĵ���
$sqlsort = $db->make_sort($list_heads, $sort, $order, $default_sort, $default_order);

// ��ҳ����:
$count = $db->query("select count(*) as c from $table $sqlwhere", 1, "c");
$pagecount = max(ceil($count / $pagesize), 1);
$page = max(min($pagecount, intval($page)), 1);
$offset = ($page - 1) * $pagesize;

// ɸѡ:
$list = $db->query("select * from $table $sqlwhere $sqlsort limit $offset,$pagesize", "id");
if (!is_array($list)) {
	exit_html("Error: ".$db->sql);
}

$back_url = make_back_url();

foreach ($list as $id => $li) {
	$r = array();
	$r["ѡ"] = '<input name="delcheck" type="checkbox" value="'.$li["id"].'" onclick="set_item_color(this)">';
	$r["����"] = $li["fee_typename"];
	$r["ʱ��"] = $li["date"];
	$r["ҽ��"] = $li["doctor_name"];
	$r["��Ч����"] = $li["fee_type"] == 0 ? $li["chuzhen"] : $li["zhuyuan"];
	$r["Ӫҵ��"] = $li["yingyee"];
	$r["�˾�����"] = $li["renjun"];
	$r["�����"] = $li["u_realname"];

	$op = array();
	if (check_power("view")) $op[] = "<a href='?op=view&id=".$id."' class='op'>�鿴</a>";
	if (check_power("edit")) {
		$op[] = "<a href='?op=edit&id=".$li["id"]."&back_url=".$back_url."' class='op' title='�޸�����'>�޸�</a>";
	}
	if (check_power("delete")) {
		$op[] = "<a href='?op=delete&id=".$li["id"]."' onclick='return isdel()' class='op'>ɾ��</a>";
	}
	$r["����"] = implode($GLOBALS["button_split"], $op);

	$t->add($r);
}

$pagelink = pagelinkc($page, $pagecount, $count, make_link_info($link_param, "page"), "button");

include $mod.".list.tpl.php";

?>