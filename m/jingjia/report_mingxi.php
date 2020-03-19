<?php
/*
// - 功能说明 : 竞价消费报表 (仅查看功能)
// - 创建作者 : 幽兰 (weelia@126.com)
// - 创建时间 : 2011-11-02
*/
$table = "jingjia_xiaofei";
require "../../core/core.php";

if (!$hid) {
	echo '<script type="text/javascript">'."\r\n";
	echo 'alert("对不起，您还没有选择医院，请点击“确定”，然后选择一家医院。");'."\r\n";
	echo 'parent.load_box(1, "src", "/m/chhos.php");'."\r\n";
	echo '</script>'."\r\n";
	exit;
}

$h_name = $db->query("select name from hospital where id=$hid limit 1", 1, "name");

// 所有竞价字段:
$all_field_arr = $db->query("select fieldname, name from jingjia_field_set order by fieldname asc", "fieldname", "name");

// 当前医院字段设置:
$h_field = $db->query("select fields from jingjia_hospital_set where hid=$hid limit 1", 1, "fields");
if ($h_field != '') {
	$h_field_arr = explode(",", $h_field);
} else {
	$h_field_arr = array_keys($all_field_arr); //使用全局
}

// 是否显示总消费：
$show_xiaofei_count = 1;


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
$aTdFormat['date'] = array("title"=>"日期", "width"=>"100", "align"=>"center", "sort"=>1);
if ($show_xiaofei_count) {
	$aTdFormat['xiaofei'] = array("title"=>"总消费额", "width"=>"", "align"=>"center", "sort"=>1);
}
foreach ($h_field_arr as $v) {
	$aTdFormat[$v] = array("title"=>$all_field_arr[$v], "align"=>"center", "sort"=>1);
}
$aTdFormat['u_name'] = array("title"=>"提交人", "width"=>"", "align"=>"center", "sort"=>1);

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
</script>
</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $h_name; ?> 竞价消费明细报表</td><td class="bar_right"></td></tr></table></div>
	<div class="header_center">
		<button onclick="load_url('m/chhos.php'); return false;" class="buttonb" title="切换到其他医院">切换医院</button>&nbsp;&nbsp;
	</div>
	<div class="headers_oprate"><button onclick="history.back()" class="button" title="返回上一页">返回</button></div>
</div>
<!-- 头部 end -->

<div class="space"></div>


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
		if ($id == 0) {
?>
	<tr>
		<td colspan="<?php echo count($aTdFormat); ?>" align="left" class="group"><?php echo $line["name"]; ?></td>
	</tr>
<?php
		} else {
?>
	<tr>
<?php
	// 输出字段内容:
	foreach ($aTdFormat as $fn => $fa) {
		$_align = $fa["align"] ? $fa["align"] : "center";
		if ($fn == "chk") {
			$s = '<input name="delcheck" type="checkbox" value="'.$id.'" onpropertychange="set_item_color(this)">';
		} else if ($fn == "date") {
			$s = int_date_to_date($line["date"]);
			if (date("Y", strtotime($s)) == date("Y")) {
				$s = date("m-d", strtotime($s));
			}
		} else if ($fn == "op") {
			$s = $op_button;
		} else if ($fn == "addtime") {
			$s = str_replace(" ", "<br>", date("Y-m-d H:i", $line["addtime"]));
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

</body>
</html>