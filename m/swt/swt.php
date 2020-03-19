<?php
/*
// - 功能说明 : 媒体类型
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2009-05-03 14:47
*/
require "../../core/core.php";
$table = "swt_account";

if ($_GET["date"] == '') {
	$date_int = date("Ymd"); //默认今日
	$_GET["date"] = date("Y-m-d");
} else {
	$date_int = date("Ymd", strtotime($_GET["date"]));
}

// 操作的处理:
if ($op = $_GET["op"]) {
	switch ($op) {
		case "add":
			include "swt_edit.php";
			exit;

		case "edit":
			$line = $db->query_first("select * from $table where id='$id' limit 1");
			include "swt_edit.php";
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
	"view" => "view",
	"hospital" => "hospital",
	"date" => "date",
);

// 读取页面调用参数:
foreach ($aLinkInfo as $local_var_name => $call_var_name) {
	$$local_var_name = $_GET[$call_var_name];
}

// 定义单元格格式:
$aOrderType = array(0 => "", 1 => "asc", 2 => "desc");
$aTdFormat = array(
	0=>array("title"=>"选", "width"=>"32", "align"=>"center"),
	1=>array("title"=>"UID", "width"=>"10%", "align"=>"left", "sort"=>"uid", "defaultorder"=>1),
	2=>array("title"=>"用户名", "width"=>"10%", "align"=>"left", "sort"=>"uname", "defaultorder"=>1),
	5=>array("title"=>"客户端商务通", "align"=>"left", "sort"=>"content", "defaultorder"=>1),
	3=>array("title"=>"收集时间", "width"=>"130", "align"=>"center", "sort"=>"addtime", "defaultorder"=>2),
	4=>array("title"=>"操作", "width"=>"100", "align"=>"center"),
);

// 默认排序方式:
$defaultsort = 3;
$defaultorder = 2;

// 查询条件:
$where = array();
$where[] = "date=$date_int";

// 只看客服:
if ($_GET["view"] != '') {
	$kefu_uids = $db->query("select id from sys_admin where part_id=2 or part_id=3", "", "id");
	$where[] = "uid in (".implode(",", $kefu_uids).")";
}

// 只看某家医院：
$hospital = intval($hospital);
if ($hospital > 0) {
	$kefu_uids = $db->query("select id from sys_admin where concat(',', hospitals, ',') like '%,{$hospital},%'", "", "id");
	$where[] = "uid in (".implode(",", $kefu_uids).")";
}


if ($searchword) {
	$where[] = "(binary uname like '%{$searchword}%')";
}
$sqlwhere = count($where) > 0 ? ("where ".implode(" and ", $where)) : "";

// 对排序的处理：
if ($sortid > 0) {
	$sqlsort = "order by ".$aTdFormat[$sortid]["sort"]." ";
	if ($sorttype > 0) {
		$sqlsort .= $aOrderType[$sorttype];
	} else {
		$sqlsort .= $aOrderType[$aTdFormat[$sortid]["defaultorder"]];
	}
} else {
	if ($defaultsort > 0 && array_key_exists($defaultsort, $aTdFormat)) {
		$sqlsort = "order by ".$aTdFormat[$defaultsort]["sort"]." ".$aOrderType[$defaultorder];
	} else {
		$sqlsort = "";
	}
}
//$sqlsort = "order by hospital, id asc";

// 分页数据:
$pagesize = 50;
$count = $db->query_count("select count(*) from $table $sqlwhere");
$pagecount = max(ceil($count / $pagesize), 1);
$page = max(min($pagecount, intval($page)), 1);
$offset = ($page - 1) * $pagesize;

// 查询:
$data = $db->query("select * from $table $sqlwhere $sqlsort limit $offset,$pagesize");

// 各医院对应的商务通帐号：
$swt_to_hospital_arr = $db->query("select swt_ids, name from hospital where swt_ids!=''", "swt_ids", "name");

$hospital_arr = $db->query("select id,name from hospital order by name asc", "id", "name");

// 页面开始 ------------------------
?>
<html>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<script language="javascript">
var base_src = "m/swt/swt.php";
function add(hid) {
	set_high_light('');
	parent.load_src(1, base_src+'?op=add', 600, 300);
	return false;
}

function edit(id, obj) {
	set_high_light(obj);
	parent.load_src(1, base_src+'?op=edit&id='+id, 600, 300);
	return false;
}

if (!document.all) {
	HTMLElement.prototype.insertAdjacentHTML = function(where, html) {
		var e = this.ownerDocument.createRange();
		e.setStartBefore(this);
		e = e.createContextualFragment(html);
		switch (where) {
			case 'beforeBegin': this.parentNode.insertBefore(e, this);break;
			case 'afterBegin': this.insertBefore(e, this.firstChild); break;
			case 'beforeEnd': this.appendChild(e); break;
			case 'afterEnd':
				if(!this.nextSibling) this.parentNode.appendChild(e);
				else this.parentNode.insertBefore(e, this.nextSibling); break;
		}
	};
}


// 修改 page_param 表单的某个请求参数
function page_param_update(name, value, is_submit) {
	var is_found = 0;
	var el = byid("page_param").getElementsByTagName("INPUT");
	for (var i = 0; i < el.length; i++) {
		if (el[i].name == name) {
			el[i].value = value;
			is_found = 1;
			break;
		}
	}
	if (!is_found) {
		var s = '<input type="hidden" name="'+name+'" value="'+value+'" />';
		byid("page_param").insertAdjacentHTML("beforeEnd", s);
	}
	if (is_submit) {
		page_param_submit();
	}
}

// 删除某个请求参数
function page_param_del(name) {
	var el = byid("page_param").getElementsByTagName("INPUT");
	for (var i = 0; i < el.length; i++) {
		if (el[i].name == name) {
			el[i].value = '';
			el[i].parentNode.removeChild(el[i]);
			return true;
		}
	}
}

function page_param_submit() {
	byid("page_param").submit();
}

function hgo(dir, o) {
	var obj = byid("hospital_list");
	if (dir == "up") {
		if (obj.selectedIndex > 1) {
			obj.selectedIndex = obj.selectedIndex - 1;
			obj.onchange();
			o.disabled = true;
		} else {
			parent.msg_box("已经是最前了", 3);
		}
	}
	if (dir == "down") {
		if (obj.selectedIndex < obj.options.length-1) {
			obj.selectedIndex = obj.selectedIndex + 1;
			obj.onchange();
			o.disabled = true;
		} else {
			parent.msg_box("已经是最后一个了", 3);
		}
	}
}
</script>
</head>

<body>
<form id="page_param" method="GET" action="" style="display:none;">
	<input type="hidden" name="date" value="<?php echo $_GET["date"]; ?>" />
	<input type="hidden" name="view" value="<?php echo $_GET["view"]; ?>" />
	<input type="hidden" name="hospital" value="<?php echo $_GET["hospital"]; ?>" />
</form>

<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title" style="width:50%"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center">商务通关联帐号查看</td><td class="bar_right"></td></tr></table></div>
	<div class="header_center">
<?php if (check_power("add")) { ?>
		<button onclick="add()" class="button">添加</button>
<?php } ?>
	</div>
	<div class="headers_oprate"><form name="topform" method="GET">搜索姓名：<input name="searchword" value="<?php echo $_GET["searchword"]; ?>" class="input" size="12">&nbsp;<input type="submit" class="search" value="搜索" style="font-weight:bold" title="点击搜索">&nbsp;<button onclick="location='?'" class="search" title="退出条件查询">重置</button></form></div>
</div>
<!-- 头部 end -->

<div class="space"></div>
<div style="text-align:center;">
	显示日期：<b><?php echo $_GET["date"]; ?></b> &nbsp; 查看其它日期：<input class="input" size="10" onchange="page_param_update('date',this.value,1)" name="date" id="date_to_set" value="<?php echo $_GET["date"]; ?>"> <img src="/res/img/calendar.gif" onClick="picker({el:'date_to_set',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="选择日期"> &nbsp; &nbsp;
	<button onclick="page_param_update('view','kefu',1)" class="buttonb"><?php echo $_GET["view"] == "kefu" ? ("<b style='color:red'>只看咨询</b>") : "只看咨询"; ?></button>&nbsp;
	<button onclick="page_param_update('view','',1)" class="buttonb"><?php echo $_GET["view"] == "" ? ("<b style='color:red'>看全部</b>") : "看全部"; ?></button> &nbsp; &nbsp;
	<select name="hospital" id="hospital_list" class="combo" onchange="page_param_update('hospital',this.value,1)">
		<option value="" style="color:gray">(所有医院)</option>
		<?php echo list_option($hospital_arr, "_key_", "_value_", $_GET["hospital"]); ?>
	</select>&nbsp;
	<button class="button" onclick="hgo('up',this);">上</button>&nbsp;
	<button class="button" onclick="hgo('down',this);">下</button>
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
<?php } ?>
	</tr>
	<!-- 表头定义 end -->

	<!-- 主要列表数据 begin -->
<?php
if (count($data) > 0) {
	foreach ($data as $line) {
		$id = $line["id"];

		$content = $line["content"];
		if ($content != '') {
			$c_arr = explode("|", $content);
			$pa = $pb = array();
			foreach ($c_arr as $v) {
				if (in_array($v, explode(" ", "Menu Agent_Records 新建文件夹"))) {
					continue;
				}
				$v = strtoupper($v);
				if (array_key_exists($v, $swt_to_hospital_arr)) {
					$pa[] = '<font color="red">'.$v."(".$swt_to_hospital_arr[$v].")</font>";
				} else {
					$pb[] = $v;
				}
			}
			if (count($pb) > 0) {
				foreach ($pb as $v) {
					$pa[] = $v;
				}
			}
			$content = implode(" <font color=gray>|</font> ", $pa);
		}

		$op = array();
		if (check_power("edit")) {
			$op[] = "<a href='#edit' onclick='edit(".$id.", this)' class='op'>修改</a>";
		}
		if (check_power("delete")) {
			$op[] = "<a href='?op=delete&id=$id' onclick='return isdel()' class='op'>删除</a>";
		}
		$op_button = $op ? implode("&nbsp;", $op) : '<font color="gray">(无)</font>';

		$hide_line = ($pinfo && $pinfo["ishide"] && $line["isshow"] != 1) ? 1 : 0;
?>
	<tr<?php echo $hide_line ? " class='hide'" : ""; ?>>
		<td align="center" class="item"><input name="delcheck" type="checkbox" value="<?php echo $id; ?>" onpropertychange="set_item_color(this)"></td>
		<td align="left" class="item"><?php echo $line["uid"]; ?></td>
		<td align="left" class="item"><?php echo $line["uname"]; ?></td>
		<td align="left" class="item"><?php echo $content; ?></td>
		<td align="center" class="item"><?php echo date("Y-m-d H:i", $line["addtime"]); ?></td>
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
	<div class="footer_op_left"><button onclick="select_all()" class="button">全选</button>&nbsp;<button onclick="unselect()" class="button">反选</button>&nbsp;<?php echo $power->show_button("hide,delete"); ?></div>
	<div class="footer_op_right"><?php echo pagelinkc($page, $pagecount, $count, make_link_info($aLinkInfo, "page"), "button"); ?></div>
</div>
<!-- 分页链接 end -->

</body>
</html>