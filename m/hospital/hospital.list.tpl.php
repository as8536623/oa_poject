<?php
defined("ROOT") or exit;
?>
<html>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style type="text/css">
.num {font-size:10px; font-family:"Tahoma"; color:gray; }
</style>
<script language="javascript">
var base_src = "m/hospital/hospital.php";
function add(hid) {
	set_high_light('');
	parent.load_src(1, base_src+'?op=add', 900, 650);
	return false;
}

function edit(id, obj) {
	set_high_light(obj);
	parent.load_src(1, base_src+'?op=edit&id='+id, 900, 650);
	return false;
}

function add_site(hid) {
	parent.load_src(1, base_src+'?op=add_site&hid='+hid, 700, 450);
	return false;
}

function edit_site(hid, id) {
	parent.load_src(1, base_src+'?op=edit_site&hid='+hid+"&id="+id, 700, 450);
	return false;
}
</script>
</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><nobr><?php echo $pinfo["title"]; ?></nobr></td><td class="bar_right"></td></tr></table></div>
	<div class="header_center">
<?php if (check_power("add")) { ?>
		<button onclick="add()" class="button">添加</button>
<?php } ?>
	</div>
	<div class="headers_oprate"><form name="topform" method="GET"><nobr>模糊搜索：<input name="key" value="<?php echo $_GET["key"]; ?>" class="input" size="12">&nbsp;<input type="submit" class="search" value="搜索" style="font-weight:bold" title="点击搜索">&nbsp;<button onclick="location='?'" class="search" title="退出条件查询">重置</button></form></nobr></div>
</div>
<!-- 头部 end -->

<div class="space"></div>

<?php
// 查询
$h_arr = $db->query("select id,name,area,depart from hospital where id in ($hospitals)", "id");
$area_arr = $depart_arr = array();
$area_qita = $depart_qita = 0;
foreach ($h_arr as $_hid => $h) {
	$hid_name[$_hid] = $h["name"];
	$h["area"] = trim($h["area"]);
	$h["depart"] = trim($h["depart"]);
	if ($h["area"] == '' || $h["area"] == "其它") {
		$area_qita++;
	} else {
		$area_arr[$h["area"]] = intval($area_arr[$h["area"]]) + 1;
	}
	if ($h["depart"] == '' || $h["depart"] == "其它") {
		$depart_qita++;
	} else {
		$depart_arr[$h["depart"]] = intval($depart_arr[$h["depart"]]) + 1;
	}
}

arsort($area_arr);
arsort($depart_arr);

if ($area_qita > 0) {
	$area_arr["其它"] = $area_qita;
}
if ($depart_qita > 0) {
	$depart_arr["其它"] = $depart_qita;
}
?>

<div style="margin-top:4px;">
	<b>地区：</b>
<?php foreach ($area_arr as $k => $v) { ?>
	<a href="?s_ty=area&s_con=<?php echo urlencode($k); ?>"><?php echo ($s_ty == "area" && $s_con == $k) ? ('<font color="red"><b>'.$k.'</b></font>') : $k; ?><span class="num">(<?php echo $v; ?>)</span></a>&nbsp;
<?php } ?>
</div>

<div style="margin-top:4px;">
	<b>科室：</b>
<?php foreach ($depart_arr as $k => $v) { ?>
	<a href="?s_ty=depart&s_con=<?php echo urlencode($k); ?>&"><?php echo ($s_ty == "depart" && $s_con == $k) ? ('<font color="red"><b>'.$k.'</b></font>') : $k; ?><span class="num">(<?php echo $v; ?>)</span></a>&nbsp;
<?php } ?>
</div>


<!-- 数据列表 begin -->
<div class="space"></div>
<form name="mainform" method="GET">
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
		if (check_power("edit")) {
			$op[] = "<a href='#edit' onclick='edit(".$id.", this); return false;' class='op'>修改</a>";
		}

		if (check_power("delete")) {
			if ($username == "admin" || $debug_mode) {
				$op[] = "<a href='?op=delete&id=$id' onclick='return isdel()' class='op'>删除</a>";
			} else {
				$op[] = "<i class='op' onclick='alert(this.title)' title='对不起，该数据的删除牵涉较多问题，如果确实需要删除，请联系管理员确认'>删除</i>";
			}
		}
		$op_button = implode("&nbsp;", $op);

		$hide_line = ($pinfo && $pinfo["ishide"] && $line["isshow"] != 1) ? 1 : 0;
?>
	<tr id="#<?php echo $id; ?>" <?php echo $hide_line ? " class='hide'" : ""; ?>>
		<td align="center" class="item"><input name="ids[]" type="checkbox" value="<?php echo $id; ?>" onclick="set_item_color(this)"></td>
		<td align="center" class="item"><?php echo $line["id"]; ?></td>
		<td align="left" class="item"><nobr><b><?php echo $line["name"]; ?></b></nobr></td>
		<td align="center" class="item"><?php echo $line["area"]; ?></td>
		<td align="left" class="item"><?php echo implode(" | ", $line["sites"]); ?></td>
		<td align="center" class="item"><nobr><?php echo $line["swt_ids"]; ?></nobr></td>
		<td align="center" class="item"><?php echo $line["set_huifang_kf"] ? "是" : "-"; ?></td>
		<td align="center" class="item"><?php echo date("Y-m-d", $line["addtime"]); ?></td>
		<td align="center" class="item"><nobr><?php echo $line["sort"]; ?></nobr></td>
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
	<div class="footer_op_left"><button onclick="select('all')" class="button">全选</button>&nbsp;<button onclick="select('reverse')" class="button">反选</button>&nbsp;<?php echo $power->show_button("hdie"); ?></div>
	<div class="footer_op_right"><?php echo pagelinkc($page, $pagecount, $count, make_link_info($aLinkInfo, "page"), "button"); ?></div>
</div>
<!-- 分页链接 end -->

</body>
</html>