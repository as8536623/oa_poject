<?php
/*
// - 功能说明 : 竞价消费
// - 创建作者 : 幽兰 (weelia@126.com)
// - 创建时间 : 2011-07-23
*/
$table = "jingjia_xiaofei";
require "../../core/core.php";

if (count($hospital_ids) == 0) {
	exit_html("管理员没有为你分配医院，不能使用此功能。");
}

if (!$hid) {
	/*
	echo '<script type="text/javascript">'."\r\n";
	echo 'alert("对不起，您还没有选择医院，请点击“确定”，然后选择一家医院。");'."\r\n";
	echo 'parent.load_box(1, "src", "/m/chhos.php");'."\r\n";
	echo '</script>'."\r\n";
	exit;
	*/
}

$change_op = $_GET["go"];
if (!$hid || $change_op != '') {
	// 医院切换序列:
	$hids = implode(",", $hospital_ids);
	$h_list = $db->query("select id,name from hospital where id in ($hids) order by sort desc, name asc", "", "id");

	if (!$hid) {
		$check_hid = $h_list[0];
	}
	if ($change_op == "prev") {
		$cur_k = array_search($hid, $h_list);
		if ($cur_k > 0) {
			$check_hid = $h_list[$cur_k - 1];
		} else {
			msg_box("已经是最前一家医院了", "back", 1, 2);
		}
	}
	if ($change_op == "next") {
		$cur_k = array_search($hid, $h_list);
		if ($cur_k < count($h_list) - 1) {
			$check_hid = $h_list[$cur_k + 1];
		} else {
			msg_box("已经是最后一家医院了", "back", 1, 2);
		}
	}
	if ($check_hid > 0) {
		$_SESSION["hospital_id"] = $check_hid;
		header("location: xiaofei.php");
	}
	exit;
}

$h_name = $db->query("select name from hospital where id=$hid limit 1", 1, "name");

// 所有竞价字段:
$all_field_arr = $db->query("select fieldname, name from jingjia_field_set order by fieldname asc", "fieldname", "name");
$sub_name_arr = $db->query("select fieldname, sub_name from jingjia_field_set order by fieldname asc", "fieldname", "sub_name");

// 当前医院字段设置:
$h_field = $db->query("select fields from jingjia_hospital_set where hid=$hid limit 1", 1, "fields");
if ($h_field != '') {
	$h_field_arr = explode(",", $h_field);
} else {
	$h_field_arr = array_keys($all_field_arr); //使用全局
}



// 加载非竞价消费:
$feijingjia_m_arr = $db->query("select month,x1_per_day from jingjia_feijingjia where hid='$hid' and x1>0", "month", "x1_per_day");
//print_r($feijingjia_m_arr);



// 是否显示总消费：
$show_xiaofei_count = 0;

// 当前用户能控制的字段:
if ($debug_mode || $username == "admin" || $uinfo["part_id"] == 9 || ($uinfo["part_id"] == 202 && $uinfo["part_admin"])) {
	$user_field_arr = $h_field_arr; //array_keys($all_field_arr); //管理人员可以看到所有具体消费
	$show_xiaofei_count = 1; //管理人员可以看到总消费
} else {
	// 普通人员根据系统设置，且不能看到总消费
	$user_field = $db->query("select fields from jingjia_user_set where hid=$hid and uid=$uid limit 1", 1, "fields");
	$user_field_arr = $user_field ? explode(",", $user_field) : array();
}

// 只有设置了权限 才能操作
if (count($user_field_arr) > 0) {

	if ($op) {
		include "xiaofei.op.php";
	}

	if ($_GET["btime"]) {
		$_GET["begin_time"] = strtotime($_GET["btime"]);
	}
	if ($_GET["etime"]) {
		$_GET["end_time"] = strtotime($_GET["etime"]);
	}

	// 定义当前页需要用到的调用参数:
	$aLinkInfo = array(
		"page" => "page",
		"sort" => "sort",
		"order" => "order",
		"searchword" => "searchword",
		"begin_time" => "begin_time",
		"end_time" => "end_time",
	);

	// 读取页面调用参数:
	foreach ($aLinkInfo as $local_var_name => $call_var_name) {
		$$local_var_name = $_GET[$call_var_name];
	}

	// 定义单元格格式:
	$aOrderType = array("asc", "desc");

	// 定义字段
	$aTdFormat = array();
	//$aTdFormat['chk'] = array("title"=>"选", "width"=>"32", "align"=>"center");
	$aTdFormat['id'] = array("title"=>"ID", "width"=>"60", "align"=>"center", "sort"=>1);
	$aTdFormat['date'] = array("title"=>"日期", "width"=>"", "align"=>"center", "sort"=>1);
	if ($show_xiaofei_count) {
		$aTdFormat['xiaofei'] = array("title"=>"总消费额", "width"=>"", "align"=>"center", "sort"=>1);
		$aTdFormat['ex_baidu'] = array("title"=>"除百度外消费", "width"=>"", "align"=>"center");
	}
	foreach ($user_field_arr as $v) {
		$_n = $all_field_arr[$v].($sub_name_arr[$v] ? ('<br><font color="silver">('.$sub_name_arr[$v].')</font>') : "");
		$aTdFormat[$v] = array("title"=>$_n, "align"=>"center", "sort"=>1);
	}
	if ($debug_mode || $uinfo["part_id"] == 9) {
		$aTdFormat['feijingjia'] = array("title"=>"非竞价渠道", "width"=>"", "align"=>"center");
	}
	//$aTdFormat['addtime'] = array("title"=>"添加时间", "width"=>"", "align"=>"center", "sort"=>1);
	$aTdFormat['u_name'] = array("title"=>"提交人", "width"=>"", "align"=>"center", "sort"=>1);
	$aTdFormat['op'] = array("title"=>"操作", "width"=>"", "align"=>"center");

	// 默认排序方式:
	$defaultsort = 'date';
	$defaultorder = 'desc';

	// 查询条件:
	$where = array();
	$where[] = "hid=$hid";
	if ($searchword) {
		$where[] = "(binary u_name like '%{$searchword}%')";
	}

	$sqlwhere = count($where) > 0 ? ("where ".implode(" and ", $where)) : "";

	if ($sort && array_key_exists($sort, $aTdFormat)) {
		$sqlsort = "order by ".$sort." ".($order ? (in_array($order, $aOrderType) ? $order : "asc") : "asc");
	} else {
		$sqlsort = "order by ".$defaultsort." ".$defaultorder;
	}

	// 分页数据:
	$count = $db->query("select count(*) as count from $table $sqlwhere", 1, "count");
	$pagecount = max(ceil($count / $pagesize), 1);
	$page = max(min($pagecount, intval($page)), 1);
	$offset = ($page - 1) * $pagesize;

	// 查询:
	$data = $db->query("select * from $table $sqlwhere $sqlsort limit $offset,$pagesize");

}

// 页面开始 ------------------------
?>
<html>
<head>
<title>竞价消费记录</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style>
.head, .head a {font-family:"微软雅黑","Verdana"; }
.item {font-family:"Tahoma"; padding:8px 3px 6px 3px !important; }
.footer_op_left {font-family:"Tahoma"; }
</style>
<script language="javascript">
function set_date(s) {
	byid('date_input').value = s;
}
function load_url(s) {
	parent.load_box(1, 'src', s);
}
function del_confirm() {
	return confirm("严重警告：删除不能恢复，确定不确定啊？          ");
}
function feijingjia() {
	parent.load_src(1, "m/jingjia/feijingjia.php", 800, 500);
}
</script>
</head>

<body>
<!-- 头部 begin -->
<table width="100%">
	<tr>
	<td class="headers_title" style="width:200px;"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $h_name; ?> 竞价消费记录</td><td class="bar_right"></td></tr></table></td>
	<td class="header_center">
		<button onClick="load_url('m/chhos.php'); return false;" class="buttonb" title="切换到其他医院">切换医院</button>&nbsp;
		<button onClick="location = 'xiaofei.php?go=prev'; return false;" class="button" title="切换到上一家医院">上</button>&nbsp;
		<button onClick="location = 'xiaofei.php?go=next'; return false;" class="button" title="切换到下一家医院">下</button>&nbsp;
		&nbsp;
<?php if (check_power("add") && !empty($user_field_arr)) { ?>
		<button onClick="load_url('m/jingjia/xiaofei.php?op=add'); return false;" class="buttonb" title="录入当日或昨日消费">录入数据</button>&nbsp;
<?php } ?>

<?php if ($debug_mode || $uinfo["part_id"] == 9) { ?>
		<a href="javascript:;" onClick="feijingjia();">非竞价数据管理</a>&nbsp;
<?php } ?>

	</td>
	<td class="headers_oprate" style="width:300px; text-align:right;"><form name="topform" method="GET">模糊搜索：<input name="searchword" value="<?php echo $_GET["searchword"]; ?>" class="input" size="12">&nbsp;<input type="submit" class="search" value="搜索" style="font-weight:bold" title="点击搜索">&nbsp;<button onClick="location='?'" class="search" title="退出条件查询">退出</button></form></td>
	</tr>
</table>
<!-- 头部 end -->

<div class="space"></div>

<?php
if (count($user_field_arr) > 0) {
?>

<!-- 数据列表 begin -->
<form name="mainform">
<table width="100%" align="center" class="list">
	<!-- 表头定义 begin -->
	<tr>
<?php
// 表头处理:
foreach ($aTdFormat as $fn => $fa) {
	$_align = $fa["align"] ? $fa["align"] : "center";
	$_width = $fa["width"];
	if ($fa["sort"]) {
		$_link = make_link_info($aLinkInfo, "sort order");
		$_order = ($sort == $fn ? ($order == "asc" ? "desc" : "asc") : "asc");
		$_link .= "&sort=".$fn."&order=".$_order;
		if (empty($sort)) {
			$_arrow = $defaultsort == $fn ? ($defaultorder == "asc" ? "↑" : "↓") : "";
		} else {
			$_arrow = $sort == $fn ? ($_order == "asc" ? "↓" : "↑") : "";
		}
		$_title = '<a href="'.$_link.'">'.$fa["title"].$_arrow.'</a>';
	} else {
		$_title = $fa["title"];
	}
?>
		<td class="head" align="<?php echo $_align; ?>" width="<?php echo $_width; ?>"><?php echo $_title; ?></td>
<? } ?>
	</tr>
	<!-- 表头定义 end -->

	<!-- 主要列表数据 begin -->
<?php
$xiaofei_count = 0;
if (count($data) > 0) {
	foreach ($data as $line) {
		$id = $line["id"];
		$xiaofei_count += floatval($line["xiaofei"]);
		$line["ex_baidu"] = round($line["x5"] + $line["x6"] + $line["x7"], 1);
		if ($id == 0) {
?>
	<tr>
		<td colspan="<?php echo count($aTdFormat); ?>" align="left" class="group"><?php echo $line["name"]; ?></td>
	</tr>
<?php
		} else {

		$op = array();
		if (check_power("edit") && !empty($user_field_arr)) {
			$op[] = "<a href='javascript:void(0);' onclick='load_url(\"m/jingjia/xiaofei.php?op=edit&id=$id\");' class='op'>修改</a>";
		}
		if ($debug_mode || $username == "admin" || $uinfo["part_id"] == 9) {
			$op[] = "<a href='javascript:void(0);' onclick='load_url(\"m/jingjia/xiaofei.php?op=log&id=$id\");' class='op' title='查看修改日志'>日志</a>";
		}
		if ($debug_mode) {
			//$op[] = "<a href='?op=delete&id=$id' onclick='return del_confirm()' class='op'>删除</a>";
		}
		$op_button = implode('&nbsp;<font color=silver>|</font>&nbsp;', $op);

		$hide_line = ($pinfo && $pinfo["ishide"] && $line["isshow"] != 1) ? 1 : 0;

?>
	<tr<?php echo $hide_line ? " class='hide'" : ""; ?>>
<?php
	// 输出字段内容:
	foreach ($aTdFormat as $fn => $fa) {
		$int_m = date("Ym", strtotime(int_date_to_date($line["date"])));

		$_align = $fa["align"] ? $fa["align"] : "center";
		if ($fn == "chk") {
			$s = '<input name="delcheck" type="checkbox" value="'.$id.'" onpropertychange="set_item_color(this)">';
		} else if ($fn == "date") {
			$s = int_date_to_date($line["date"]);
		} else if ($fn == "op") {
			$s = $op_button;
		} else if ($fn == "addtime") {
			$s = str_replace(" ", "<br>", date("Y-m-d H:i", $line["addtime"]));
		} else if ($fn == "feijingjia") {
			$s = $feijingjia_m_arr[$int_m] ? $feijingjia_m_arr[$int_m] : "-";
		} else {
			$s = array_key_exists($fn, $line) ? $line[$fn] : "-";
		}
?>
		<td align="<?php echo $_align; ?>" class="item"><?php echo $s; ?></td>
<?php } ?>
	</tr>
<?php
		}
	}
} else {
?>
	<tr>
		<td colspan="<?php echo count($aTdFormat); ?>" align="center" class="nodata">(暂无数据...)</td>
	</tr>
<?php } ?>
	<!-- 主要列表数据 end -->

</table>
</form>
<!-- 数据列表 end -->

<!-- 分页链接 begin -->
<div class="space"></div>
<div class="footer_op">
	<div class="footer_op_left">
<?php if ($show_xiaofei_count) { ?>
	&nbsp;本页总计消费额(<b><?php echo $xiaofei_count; ?></b>) / 天数(<b><?php echo count($data); ?></b>) = 日均消费(<b><?php echo @round($xiaofei_count / count($data), 1); ?></b>)
<?php } ?>
	</div>
	<div class="footer_op_right"><?php echo pagelinkc($page, $pagecount, $count, make_link_info($aLinkInfo, "page"), "button"); ?></div>
</div>
<!-- 分页链接 end -->
</form>

<?php } else { ?>

	<div class="nodata" style="border:2px solid silver; text-align:center; padding:30px 0px;">对不起，您不具备此医院录入权限，请切换其他医院试试。</div>

<?php } ?>

</body>
</html>