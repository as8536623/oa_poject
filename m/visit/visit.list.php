<?php
/*
// 说明: visit.list
// 作者: 幽兰 (weelia@126.com)
// 时间: 2010-07-07
*/

// 定义当前页需要用到的调用参数:
$link_param = array("op", "sid", "page","sort","sorttype","searchword");
foreach ($link_param as $v) {
	if ($v != '') $$v = $_GET[$v];
}

$sid = intval($sid);
if (!$sid) {
	exit_html("参数错误");
}

// 定义单元格格式:
$list_heads = array(
	"选" => array("width"=>"32", "align"=>"center"),
	"时间" => array("width"=>"80", "align"=>"center", "sort"=>"date", "order"=>"asc"),
	"IP" => array("width"=>"", "align"=>"center", "sort"=>"ip", "order"=>"desc"),
	"PV" => array("width"=>"", "align"=>"center", "sort"=>"pv", "order"=>"desc"),
	"点击" => array("width"=>"", "align"=>"center", "sort"=>"click", "order"=>"desc"),
	"有效点击" => array("width"=>"", "align"=>"center", "sort"=>"ok_click", "order"=>"desc"),
	"添加人" => array("width"=>"", "align"=>"center", "sort"=>"u_realname", "order"=>"desc"),
	"操作" => array("width"=>"150", "align"=>"center"),
);

function show_data($t, $li) {
	switch ($t) {
		case "选":
			return '<input name="delcheck" type="checkbox" value="'.$li["id"].'" onclick="set_item_color(this)">';
		case "时间":
			return $li["date"];
		case "IP":
			return "<font color=red>".$li["ip"]."</font> | <font color=green>".$li["ip_local"]."</font> | ".$li["ip_other"];
		case "PV":
			return "<font color=red>".$li["pv"]."</font> | <font color=green>".$li["pv_local"]."</font> | ".$li["pv_other"];
		case "点击":
			return "<font color=red>".$li["click"]."</font> | <font color=green>".$li["click_local"]."</font> | ".$li["click_other"];
		case "有效点击":
			return "<font color=red>".$li["ok_click"]."</font> | <font color=green>".$li["ok_click_local"]."</font> | ".$li["ok_click_other"];
		case "添加人":
			return $li["u_realname"];
		case "操作":
			$op = array();
			if (check_power("view")) $op[] = "<a href='?op=view&id=".$li["id"]."' class='op'>查看</a>";
			if (check_power("edit")) {
				$op[] = "<a href='?op=edit&id=".$li["id"]."&back_url=".$GLOBALS["back_url"]."' class='op' title='修改内容'>修改</a>";
			}
			if (check_power("delete")) {
				$op[] = "<a href='?op=delete&id=".$li["id"]."' onclick='return isdel()' class='op'>删除</a>";
			}
			return implode($GLOBALS["button_split"], $op);
		default:
			return '';
	}
}

// 默认排序方式:
$defaultsort = "时间";
$defaultorder = "desc";


// 查询条件:
$where = array();

//$where[] = "hid=$hid";
$where[] = "site_id=$sid";

if ($searchword) {
	$where[] = "date='$searchword' or u_realname='$searchword'";
}
$sqlwhere = count($where) > 0 ? ("where ".implode(" and ", $where)) : "";

// 对排序的处理：
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

// 分页数据:
$count = $db->query("select count(*) as c from $table $sqlwhere", 1, "c");
$pagecount = max(ceil($count / $pagesize), 1);
$page = max(min($pagecount, intval($page)), 1);
$offset = ($page - 1) * $pagesize;

// 筛选:
$list = $db->query("select * from $table $sqlwhere $sqlsort limit $offset,$pagesize", "id");
if (!is_array($list)) {
	exit("Error: ".$db->sql);
}


$back_url = make_back_url();

// 表格头部:
$table_header = '<tr>';
foreach ($list_heads as $k => $v) {
	list($tdalign, $tdwidth, $tdtitle) = build_table_head($k, $v);
	$table_header .= '<td class="head" align="'.$tdalign.'" width="'.$tdwidth.'">'.$tdtitle.'</td>';
}
$table_header .= '</tr>';

// 表格数据:
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