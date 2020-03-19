<?php

// 定义当前页需要用到的调用参数:
$link_param = array("op", "sid", "page","sort","order","key","btime","etime","doctor_id");

// 定义单元格格式:
$list_heads = array(
	"选" => array("width"=>"32", "align"=>"center"),
	"类型" => array("width"=>"80", "align"=>"center", "sort"=>"fee_type", "order"=>"asc"),
	"时间" => array("width"=>"", "align"=>"center", "sort"=>"date", "order"=>"desc"),
	"医生" => array("width"=>"", "align"=>"center", "sort"=>"doctor_id", "order"=>"desc"),
	"有效人数" => array("width"=>"", "align"=>"center", "sort"=>"", "order"=>"desc"),
	"营业额" => array("width"=>"", "align"=>"center", "sort"=>"yingyee", "order"=>"desc"),
	"人均消费" => array("width"=>"", "align"=>"center", "sort"=>"renjun", "order"=>"desc"),
	"添加人" => array("width"=>"", "align"=>"center", "sort"=>"uid", "order"=>"desc"),
	"操作" => array("width"=>"150", "align"=>"center"),
);

// 默认排序
$default_sort = "时间";
$default_order = "desc";


$param = array();
foreach ($link_param as $s) {
	$param[$s] = $_GET[$s];
}
extract($param);

// 列表显示类:
$t = load_class("table");
$t->set_head($list_heads, $default_sort, $default_order);
$t->set_sort($_GET["sort"], $_GET["order"]);
$t->param = $param;
$t->table_class = "new_list";


// 查询条件:
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

// 对排序的处理：
$sqlsort = $db->make_sort($list_heads, $sort, $order, $default_sort, $default_order);

// 分页数据:
$count = $db->query("select count(*) as c from $table $sqlwhere", 1, "c");
$pagecount = max(ceil($count / $pagesize), 1);
$page = max(min($pagecount, intval($page)), 1);
$offset = ($page - 1) * $pagesize;

// 筛选:
$list = $db->query("select * from $table $sqlwhere $sqlsort limit $offset,$pagesize", "id");
if (!is_array($list)) {
	exit_html("Error: ".$db->sql);
}

$back_url = make_back_url();

foreach ($list as $id => $li) {
	$r = array();
	$r["选"] = '<input name="delcheck" type="checkbox" value="'.$li["id"].'" onclick="set_item_color(this)">';
	$r["类型"] = $li["fee_typename"];
	$r["时间"] = $li["date"];
	$r["医生"] = $li["doctor_name"];
	$r["有效人数"] = $li["fee_type"] == 0 ? $li["chuzhen"] : $li["zhuyuan"];
	$r["营业额"] = $li["yingyee"];
	$r["人均消费"] = $li["renjun"];
	$r["添加人"] = $li["u_realname"];

	$op = array();
	if (check_power("view")) $op[] = "<a href='?op=view&id=".$id."' class='op'>查看</a>";
	if (check_power("edit")) {
		$op[] = "<a href='?op=edit&id=".$li["id"]."&back_url=".$back_url."' class='op' title='修改内容'>修改</a>";
	}
	if (check_power("delete")) {
		$op[] = "<a href='?op=delete&id=".$li["id"]."' onclick='return isdel()' class='op'>删除</a>";
	}
	$r["操作"] = implode($GLOBALS["button_split"], $op);

	$t->add($r);
}

$pagelink = pagelinkc($page, $pagecount, $count, make_link_info($link_param, "page"), "button");

include $mod.".list.tpl.php";

?>