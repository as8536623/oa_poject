<?php
defined("ROOT") or exit;

// 定义当前页需要用到的调用参数:
$aLinkInfo = array("page", "sortid", "sorttype", "key", "s_ty", "s_con");

// 读取页面调用参数:
foreach ($aLinkInfo as $local_var_name) {
	$$local_var_name = $_GET[$local_var_name];
}

// 定义单元格格式:
$aOrderType = array(0 => "", 1 => "asc", 2 => "desc");
$aTdFormat = array(
	0=>array("title"=>"选", "width"=>"32", "align"=>"center"),
	4=>array("title"=>"ID", "width"=>"50", "align"=>"center"),
	1=>array("title"=>"医院名称", "width"=>"150", "align"=>"left"),
	9=>array("title"=>"地区", "width"=>"80", "align"=>"center"),
	6=>array("title"=>"下挂网站", "width"=>"", "align"=>"left"),
	7=>array("title"=>"SWT ID", "width"=>"100", "align"=>"center"),
	8=>array("title"=>"指定回访", "width"=>"60", "align"=>"center"),
	2=>array("title"=>"添加时间", "width"=>"80", "align"=>"center"),
	5=>array("title"=>"优先度", "width"=>"60", "align"=>"center"),
	3=>array("title"=>"操作", "width"=>"80", "align"=>"center"),
);


// 查询条件:
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

// 排序：
$sqlsort = "order by sort desc, id asc";

// 分页数据:
$pagesize = 50;
$count = $db->query_count("select count(*) from $table $sqlwhere");
$pagecount = max(ceil($count / $pagesize), 1);
$page = max(min($pagecount, intval($page)), 1);
$offset = ($page - 1) * $pagesize;

// 查询:
$data = $db->query("select * from $table $sqlwhere $sqlsort limit $offset,$pagesize");

// 查询下挂网站:
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
		$data[$k]["sites"][] = '<a href="#add_site" onclick="add_site('.$v["id"].'); return false;" class="op">添加</a>';
	}
}

include $mod.".list.tpl.php";

?>