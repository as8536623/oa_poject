<?php
// --------------------------------------------------------
// - 功能说明 : 统计 项目 管理
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2010-10-13 11:34
// --------------------------------------------------------
require "../../core/core.php";
$table = "count_web_type";

// 操作的处理:
if ($op) {

	if ($op == "edit") {
		$hid = intval($_REQUEST["hid"]);
		include "web_type.edit.php";
		exit;
	}

	if ($op == "show_log") {
		include "web_type.show_log.php";
		exit;
	}

	if ($op == "delete") {
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
	}
}

// 定义当前页需要用到的调用参数:
$link_param = array("page","sort","order","key");
$param = array();
foreach ($link_param as $s) {
	$param[$s] = $_GET[$s];
}
extract($param);



$sort_by = "name asc,id asc";

if ($debug_mode || in_array($uinfo["part_id"], array(9))) {
	$hid_list = $db->query("select id,name,sort from hospital order by $sort_by", "id");
} else {
	$hids = implode(",", $hospital_ids);
	$hid_list = $db->query("select id,name,sort from hospital where id in ($hids) order by $sort_by", "id");
}


// 查询条件:
$where = array();
if ($key) {
	$where[] = "(binary name like '%{$key}%')";
}
$sqlwhere = $db->make_where($where);

// 查询:
$list = $db->query("select * from $table", "hid");

$admin_id_name = $db->query("select id,realname from sys_admin where isshow=1", "id", "realname");


// 页面开始 ------------------------
?>
<html>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/sorttable_keep.js" language="javascript"></script>
<style>
.column_sortable {color:blue !important; cursor:pointer; font-family:"微软雅黑"; }
.sorttable_nosort {color:gray; font-family:"微软雅黑"; }
</style>
<script type="text/javascript">
function edit(hid, obj) {
	set_high_light(obj);
	parent.load_src(1, '/m/count/web_type.php?op=edit&hid='+hid, 900, 500);
	return false;
}

function show_log(hid, obj) {
	set_high_light(obj);
	parent.load_src(1, '/m/count/web_type.php?op=show_log&hid='+hid, 900, 500);
	return false;
}
</script>
</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title" style="width:50%"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><nobr>客服设置</nobr></td><td class="bar_right"></td></tr></table></div>
	<div class="header_center"></div>
	<div class="headers_oprate"><form name="topform" method="GET"><nobr>搜索：<input name="key" value="<?php echo $_GET["key"]; ?>" class="input" size="12">&nbsp;<input type="submit" class="search" value="搜索" style="font-weight:bold" title="点击搜索">&nbsp;<button onclick="location='?'" class="search" title="退出条件查询">重置</button></nobr></form></div>
</div>
<!-- 头部 end -->

<div class="space"></div>

<!-- 数据列表 begin -->
<form name="mainform">
<table class="new_list sortable" width="100%">
	<tr class="head">
		<td class="sorttable_nosort" align="center" width="32">选</td>
		<td class="column_sortable" title="点击可排序" align="left" width="100">医院</td>
		<td class="column_sortable" title="点击可排序" align="left">人员编制</td>
		<td class="column_sortable" title="点击可排序" align="center" width="90">添加时间</td>
		<td class="column_sortable" title="点击可排序" align="center" width="60">优先度</td>
		<td class="sorttable_nosort" align="center" width="120">操作</td>
	</tr>

<?php
foreach ($hid_list as $_hid => $_hinfo) {
	$li = $list[$_hid];
	$r = array();

	$r["选"] = '<input name="delcheck" type="checkbox" value="'.$li["id"].'" onclick="set_item_color(this)">';
	$r["医院"] = $_hinfo["name"];

	$uids = explode(",", $li["uids"]);
	$u_names = array();
	foreach ($uids as $v) {
		if (array_key_exists($v, $admin_id_name)) {
			$u_names[] = $admin_id_name[$v];
		}
	}

	$r["人员编制"] = "<font color=blue><b>客服</b>：".$li["kefu"]."</font><br>"."<font color=red><b>管理员</b>：".implode("、", $u_names)."</font>";

	$r["添加时间"] = str_replace(" ", "<br>", date("Y-m-d H:i", $li["addtime"]));
	$r["排序"] = intval($_hinfo["sort"]);

	$op = array();
	if (check_power("edit")) {
		$op[] = "<a href='#edit:".$_hid."' onclick='edit(".$_hid.", this);return false;' class='op' title='修改内容'>修改</a>";
	}
	if ($username == "admin" || $debug_mode) {
		$op[] = "<a href='#log:".$_hid."' onclick='show_log(".$_hid.", this);return false;' class='op' title='查看修改日志'>日志</a>";
	}
	if (check_power("delete")) {
		//$op[] = "<a href='?op=delete&hid=".$_hid."' onclick='return isdel()' class='op'>删除</a>";
	}
	$r["操作"] = implode($GLOBALS["button_split"], $op);

?>
	<tr class="line" onmouseover="mi(this)" onmouseout="mo(this)">
		<td align="center"><?php echo $r["选"]; ?></td>
		<td align="left"><?php echo $r["医院"]; ?></td>
		<td align="left"><?php echo $r["人员编制"]; ?></td>
		<td align="center"><?php echo $r["添加时间"]; ?></td>
		<td align="center"><?php echo $r["排序"]; ?></td>
		<td align="center"><?php echo $r["操作"]; ?></td>
	</tr>
<?php
}
?>
</table>
</form>
<!-- 数据列表 end -->

<div class="space"></div>

<!-- 分页链接 begin -->
<div class="footer_op">
	<div class="footer_op_left"><button onclick="select_all()" class="button">全选</button>&nbsp;<button onclick="unselect()" class="button">反选</button></div>
	<div class="footer_op_right">共 <b><?php echo count($list); ?></b> 条</div>
</div>
<!-- 分页链接 end -->

</body>
</html>