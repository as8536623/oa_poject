<?php
/*
// - 功能说明 : 媒体类型
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2009-05-03 14:47
*/
require "../../core/core.php";
$table = "media";

if ($hid == 0) {
	exit_html("对不起，没有选择医院，不能执行该操作！");
}

// 操作的处理:
if ($op = $_GET["op"]) {
	switch ($op) {
		case "add":
			include "media_edit.php";
			exit;

		case "edit":
			$line = $db->query_first("select * from $table where id='$id' limit 1");
			include "media_edit.php";
			exit;

		case "delete":
			$ids = explode(",", $_GET["id"]);
			$del_ok = $del_bad = 0; $op_data = array();
			foreach ($ids as $opid) {
				if (($opid = intval($opid)) > 0) {
					$tmp_data = $db->query_first("select * from $table where id='$opid' limit 1");
					if ($db->query("delete from $table where id='$opid' limit 1")) {
						$del_ok++;
						$op_data[] = $tmp_data;
					} else {
						$del_bad++;
					}
				}
			}

			if ($del_ok > 0) {
				$log->add("delete", "删除数据", serialize($op_data));
			}

			if ($del_bad > 0) {
				msg_box("删除成功 $del_ok 条资料，删除失败 $del_bad 条资料。", "back", 1);
			} else {
				msg_box("删除成功", "back", 1);
			}

		default:
			msg_box("操作未定义...", "back", 1);
	}
}

// 定义当前页需要用到的调用参数:
$aLinkInfo = array(
	"page" => "page",
	"sortid" => "sort",
	"sorttype" => "sorttype",
	"searchword" => "searchword",
);

// 读取页面调用参数:
foreach ($aLinkInfo as $local_var_name => $call_var_name) {
	$$local_var_name = $_GET[$call_var_name];
}

// 定义单元格格式:
$aOrderType = array(0 => "", 1 => "asc", 2 => "desc");
$aTdFormat = array(
	0=>array("title"=>"选", "width"=>"32", "align"=>"center"),
	1=>array("title"=>"类型", "width"=>"100", "align"=>"center"),
	5=>array("title"=>"优先度", "width"=>"80", "align"=>"center"),
	2=>array("title"=>"名称", "width"=>"", "align"=>"left"),
	3=>array("title"=>"添加时间", "width"=>"120", "align"=>"center"),
	9=>array("title"=>"添加人", "width"=>"100", "align"=>"center"),
	4=>array("title"=>"操作", "width"=>"100", "align"=>"center"),
);

// 默认排序方式:
$defaultsort = 5;
$defaultorder = 2;

$sqlsort = "order by sort desc, id asc";

// 查询条件:
$where = array();
$where[] = "(hospital_id=0 or hospital_id=$hid)";
if ($searchword) {
	$where[] = "(binary name like '%{$searchword}%')";
}
$sqlwhere = count($where) > 0 ? ("where ".implode(" and ", $where)) : "";


// 分页数据:
$pagesize = 9999;
$count = $db->query_count("select count(*) from $table $sqlwhere");
$pagecount = max(ceil($count / $pagesize), 1);
$page = max(min($pagecount, intval($page)), 1);
$offset = ($page - 1) * $pagesize;

// 查询:
$data = $db->query("select * from $table $sqlwhere $sqlsort limit $offset,$pagesize");

$hospital_id_name = $db->query("select id,name from hospital", 'id', 'name');


// 页面开始 ------------------------
?>
<html>
<head>
<title>媒体来源设置</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script language="javascript">
var base_src = "m/patient/media.php";
function add(hid) {
	set_high_light('');
	parent.load_src(1, base_src+'?op=add&hid='+hid, 600, 300);
	return false;
}

function edit(id, obj) {
	set_high_light(obj);
	parent.load_src(1, base_src+'?op=edit&id='+id, 600, 300);
	return false;
}
</script>
</head>

<body>
<!-- 头部 begin -->
<table class="headers" width="100%">
	<tr>
		<td class="headers_title" style="width:280px;"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center">媒体来源 (全局)</td><td class="bar_right"></td></tr></table></td>
		<td class="header_cneter" align="center">
<?php
if (check_power("add")) {
	echo '<a href="javascript:void(0);" onclick="add(0)"><b>添加全局媒体来源</b></a>&nbsp;|&nbsp;<a href="javascript:void(0);" onclick="add('.$hid.')"><b>添加“'.$hinfo["name"].'”媒体来源(私有)</b></a>';
}
?>
		</td>
		<td class="headers_oprate" style="width:280px; text-align:right;">
			<form name="topform" method="GET"><nobr>模糊搜索：<input name="searchword" value="<?php echo $_GET["searchword"]; ?>" class="input" size="12">&nbsp;<input type="submit" class="search" value="搜索" style="font-weight:bold" title="点击搜索">&nbsp;<button onclick="location='?'" class="search" title="退出条件查询">重置</button></nobr></form>
		</td>
	</tr>
</table>
<!-- 头部 end -->

<div class="space"></div>
<div class="description">
	<div class="d_item">请注意: “网络”、“电话” 是系统内置媒体来源，这里不需要添加。</div>
</div>

<div class="space"></div>

<!-- 数据列表 begin -->
<form name="mainform">
<table width="100%" align="center" class="list">
	<!-- 表头定义 begin -->
	<tr>
<?php
// 表头处理:
foreach ($aTdFormat as $tdid => $tdinfo) {
	list($tdalign, $tdwidth, $tdtitle) = make_td_head($tdid, $tdinfo);
?>
		<td class="head" align="<?php echo $tdalign; ?>" width="<?php echo $tdwidth; ?>"><?php echo $tdtitle; ?></td>
<? } ?>
	</tr>
	<!-- 表头定义 end -->

	<!-- 主要列表数据 begin -->
<?php
if (count($data) > 0) {
	foreach ($data as $line) {
		$id = $line["id"];

		$op = array();
		//if (check_power("edit")) {
		if ($username == "admin" || $debug_mode) {
			$op[] = "<a href='#edit' onclick='edit(".$id.", this)' class='op'>修改</a>";
		}
		//if (check_power("delete") && $line["hospital_id"] > 0) {
		if ($username == "admin" || $debug_mode) {
			$op[] = "<a href='?op=delete&id=$id' onclick='return isdel()' class='op'>删除</a>";
		}
		$op_button = implode("&nbsp;", $op);

		if ($line["hospital_id"] != 0) {
			$tr_style = 'color:blue;';
		} else {
			$tr_style = 'color:red;';
		}
?>
	<tr style="<?php echo $tr_style; ?>">
		<td align="center" class="item"><input name="delcheck" type="checkbox" value="<?php echo $id; ?>" onpropertychange="set_item_color(this)"></td>
		<td align="center" class="item"><?php echo $line["hospital_id"] == 0 ? "全局" : "私有"; ?></td>
		<td align="center" class="item"><?php echo $line["sort"]; ?></td>
		<td align="left" class="item"><?php echo $line["name"]; ?></td>
		<td align="center" class="item"><?php echo date("Y-m-d H:i", $line["addtime"]); ?></td>
		<td align="center" class="item"><?php echo $line["author"]; ?></td>
		<td align="center" class="item"><?php echo $op_button; ?></td>
	</tr>
<?php
	}
} else {
?>
	<tr>
		<td colspan="<?php echo count($aTdFormat); ?>" align="center" class="nodata">(没有数据...)</td>
	</tr>
<?php } ?>
	<!-- 主要列表数据 end -->
</table>
</form>
<!-- 数据列表 end -->

<div class="space"></div>

<!-- 分页链接 begin -->
<div class="footer_op">
	<div class="footer_op_left"><button onclick="select_all()" class="button">全选</button>&nbsp;<button onclick="unselect()" class="button">反选</button>&nbsp;<?php //echo $power->show_button("hide,delete"); ?></div>
	<div class="footer_op_right"><?php echo pagelinkc($page, $pagecount, $count, make_link_info($aLinkInfo, "page"), "button"); ?></div>
</div>
<!-- 分页链接 end -->

</body>
</html>