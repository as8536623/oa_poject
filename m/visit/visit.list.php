<?php
/*
// ˵��: visit.list
// ����: ���� (weelia@126.com)
// ʱ��: 2010-07-07
*/

// ���嵱ǰҳ��Ҫ�õ��ĵ��ò���:
$link_param = array("op", "sid", "page","sort","sorttype","searchword");
foreach ($link_param as $v) {
	if ($v != '') $$v = $_GET[$v];
}

$sid = intval($sid);
if (!$sid) {
	exit_html("��������");
}

// ���嵥Ԫ���ʽ:
$list_heads = array(
	"ѡ" => array("width"=>"32", "align"=>"center"),
	"ʱ��" => array("width"=>"80", "align"=>"center", "sort"=>"date", "order"=>"asc"),
	"IP" => array("width"=>"", "align"=>"center", "sort"=>"ip", "order"=>"desc"),
	"PV" => array("width"=>"", "align"=>"center", "sort"=>"pv", "order"=>"desc"),
	"���" => array("width"=>"", "align"=>"center", "sort"=>"click", "order"=>"desc"),
	"��Ч���" => array("width"=>"", "align"=>"center", "sort"=>"ok_click", "order"=>"desc"),
	"�����" => array("width"=>"", "align"=>"center", "sort"=>"u_realname", "order"=>"desc"),
	"����" => array("width"=>"150", "align"=>"center"),
);

function show_data($t, $li) {
	switch ($t) {
		case "ѡ":
			return '<input name="delcheck" type="checkbox" value="'.$li["id"].'" onclick="set_item_color(this)">';
		case "ʱ��":
			return $li["date"];
		case "IP":
			return "<font color=red>".$li["ip"]."</font> | <font color=green>".$li["ip_local"]."</font> | ".$li["ip_other"];
		case "PV":
			return "<font color=red>".$li["pv"]."</font> | <font color=green>".$li["pv_local"]."</font> | ".$li["pv_other"];
		case "���":
			return "<font color=red>".$li["click"]."</font> | <font color=green>".$li["click_local"]."</font> | ".$li["click_other"];
		case "��Ч���":
			return "<font color=red>".$li["ok_click"]."</font> | <font color=green>".$li["ok_click_local"]."</font> | ".$li["ok_click_other"];
		case "�����":
			return $li["u_realname"];
		case "����":
			$op = array();
			if (check_power("view")) $op[] = "<a href='?op=view&id=".$li["id"]."' class='op'>�鿴</a>";
			if (check_power("edit")) {
				$op[] = "<a href='?op=edit&id=".$li["id"]."&back_url=".$GLOBALS["back_url"]."' class='op' title='�޸�����'>�޸�</a>";
			}
			if (check_power("delete")) {
				$op[] = "<a href='?op=delete&id=".$li["id"]."' onclick='return isdel()' class='op'>ɾ��</a>";
			}
			return implode($GLOBALS["button_split"], $op);
		default:
			return '';
	}
}

// Ĭ������ʽ:
$defaultsort = "ʱ��";
$defaultorder = "desc";


// ��ѯ����:
$where = array();

//$where[] = "hid=$hid";
$where[] = "site_id=$sid";

if ($searchword) {
	$where[] = "date='$searchword' or u_realname='$searchword'";
}
$sqlwhere = count($where) > 0 ? ("where ".implode(" and ", $where)) : "";

// ������Ĵ���
$sqlsort = "";
if (!in_array($sorttype, array("", "asc", "desc"))) {
	$sorttype = "asc";
}
if ($sort) {
	$sqlsort = "order by ".$list_heads[$sort]["sort"]." ";
	$sqlsort .= $sorttype ? $sorttype : $list_heads[$sort]["order"];
} else {
	if ($defaultsort && array_key_exists($defaultsort, $list_heads)) {
		$sqlsort = "order by ".$list_heads[$defaultsort]["sort"]." ".$defaultorder;
	}
}

// ��ҳ����:
$count = $db->query("select count(*) as c from $table $sqlwhere", 1, "c");
$pagecount = max(ceil($count / $pagesize), 1);
$page = max(min($pagecount, intval($page)), 1);
$offset = ($page - 1) * $pagesize;

// ɸѡ:
$list = $db->query("select * from $table $sqlwhere $sqlsort limit $offset,$pagesize", "id");
if (!is_array($list)) {
	exit("Error: ".$db->sql);
}


$back_url = make_back_url();

// ���ͷ��:
$table_header = '<tr>';
foreach ($list_heads as $k => $v) {
	list($tdalign, $tdwidth, $tdtitle) = build_table_head($k, $v);
	$table_header .= '<td class="head" align="'.$tdalign.'" width="'.$tdwidth.'">'.$tdtitle.'</td>';
}
$table_header .= '</tr>';

// �������:
$table_items = array();
foreach ($list as $li) {
	//if ($li["id"] > 0) {
		$show_line = get_line_show($li, $pinfo);
		$item_data = '<tr id="#'.$li["id"].'"'.($show_line ? '' : ' class="hide"').' onmouseover="mi(this)" onmouseout="mo(this)">';
		foreach ($list_heads as $k => $v) {
			$tdalign = $v["align"];
			$item_data .= '<td class="item"'.($tdalign ? ' align="'.$tdalign.'"' : '').'>';
			$item_data .= show_data($k, $li);
			$item_data .= '</td>';
		}
		$item_data .= '</tr>';
	//} else {
	//	$item_data = '<tr class="line_tips"><td colspan="'.count($list_heads).'">'.$li["name"].'</td></tr>';
	//}

	$table_items[] = $item_data;
}

$pagelink = pagelinkc($page, $pagecount, $count, make_link_info($link_param, "page"), "button");

include $mod.".list.tpl.php";

?>