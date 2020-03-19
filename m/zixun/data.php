<?php
/*
// - 功能说明 : 咨询报表时间设置
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2013-4-20
*/
require "../../core/core.php";
require "config.inc.php";
$table = "zixun_data";

if (count($hospital_ids) == 0) {
	exit_html("管理员没有为你分配医院，不能使用此功能。");
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
		header("location: data.php");
	}
	exit;
}

$h_name = $db->query("select name from hospital where id=$hid limit 1", 1, "name");


//$kefu_id_names = $db->query("select id,realname from sys_admin where part_id in (2) and concat(',', hospitals, ',') like '%,{$hid},%'", "id", "realname");
$kefu_str = $db->query("select kefu from count_type where type='web' and hid=$hid", 1, "kefu");
$kefu_id_names = explode(",", $kefu_str);

if (!$_GET["kefu"] && $kefu_str != '') {
	$_GET["kefu"] = $kefu_id_names[0]; //自动选中第一个客服
}

// 查询当前设置:
$hour_set_arr = $db->query("select * from zixun_hour_set", "hid");
$cur_hour_set = $hour_set_arr[$hid]["h_set"];
$cur_hour_set_arr = hour_set_to_show(explode(",", $cur_hour_set));

if ($op == "add") {
	include "data.add.php";
	exit;
}

if ($op == "edit") {
	include "data.edit.php";
	exit;
}



// 读取客服数据:
if ($_GET["kefu"] != '') {
	$kefu = $_GET["kefu"];
	$pagesize = 50;

	$count = $db->query("select count(*) as c from zixun_data where hid=$hid and kefu='$kefu'", 1, "c");
	$pagecount = max(ceil($count / $pagesize), 1);
	$page = max(min($pagecount, intval($_GET["page"])), 1);
	$offset = ($page - 1) * $pagesize;

	$line_arr = $db->query("select * from zixun_data where hid=$hid and kefu='$kefu' order by id desc limit $offset, $pagesize", "id");
}


// 页面开始 ------------------------
?>
<html>
<head>
<title>咨询数据维护</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/sorttable_keep.js" language="javascript"></script>
<style type="text/css">
.column_sortable {cursor:pointer; color:blue; font-family:"微软雅黑"; }
.sorttable_nosort {font-family:"微软雅黑"; }
.hour_set_list {border:1px solid #97e6a5; }
.hour_set_list .head td {border:1px solid #e7e7e7; background:#f2f8f9; padding:4px 3px 3px 3px; font-weight:bold; }
.hour_set_list .data td {border:1px solid #e7e7e7; padding:4px 3px 3px 3px; }
.al {text-align:left; }
.ac {text-align:center; }
.yh {font-family:"微软雅黑"; }

#kefu_select * {font-family:"微软雅黑"; }
#kefu_name_tips {float:left; width:100px; }
#kefu_name_area {float:left; }
.kefu_selected {color:red; font-weight:bold; }

#kf_select {height:25px; overflow:hidden; margin-top:10px; background:url("/res/img/tab_bg.jpg") repeat-x; }

.hs_tab_cur {margin-left:5px; float:left; }
.hs_tab_cur .hs_tab_left {float:left; width:3px; height:25px; background:url("/res/img/tab_cur_left.jpg") no-repeat; }
.hs_tab_cur .hs_tab_center {float:left; height:25px; background:url("/res/img/tab_cur_center.jpg") repeat-x; }
.hs_tab_cur .hs_tab_right {float:left; width:3px; height:25px; background:url("/res/img/tab_cur_right.jpg") no-repeat; }
.hs_tab_cur a {font-weight:bold; text-decoration:none; display:block; line-height:25px; padding:0 3px; color:red; }

.hs_tab_nor {margin-left:5px; float:left; }
.hs_tab_nor .hs_tab_left {float:left; width:3px; height:25px; background:url("/res/img/tab_nor_left.jpg") no-repeat; }
.hs_tab_nor .hs_tab_center {float:left; height:25px; background:url("/res/img/tab_nor_center.jpg") repeat-x; }
.hs_tab_nor .hs_tab_right {float:left; width:3px; height:25px; background:url("/res/img/tab_nor_right.jpg") no-repeat; }
.hs_tab_nor a {font-weight:normal; text-decoration:none; display:block; line-height:25px; padding:0 3px; }
</style>

<script type="text/javascript">
function h_set(hid) {
	var link = "/m/zixun/set.php?op=edit&hid="+hid;
	parent.load_src(1, link, 700, 200);
	return false;
}

function add(link) {
	parent.load_src(1, link, 600, 400);
}

function change_hospital(link) {
	parent.load_src(1, link, 600, 570);
}
</script>
</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $h_name; ?> 咨询数据</td><td class="bar_right"></td></tr></table></div>
	<div class="header_center">
<?php if (check_power("add")) { ?>
		<button onclick="add('m/zixun/data.php?op=add&kefu=<?php echo urlencode($_GET["kefu"]); ?>'); return false;" class="button">录入</button>&nbsp;
<?php } ?>
		<button onclick="self.location.reload();return false;" class="button">刷新</button>
	</div>
	<div class="headers_oprate">
		<button onclick="change_hospital('m/chhos.php'); return false;" class="buttonb" title="切换到其他医院">切换医院</button>&nbsp;
		<button onclick="location = '/m/zixun/data.php?go=prev'; return false;" class="button" title="切换到上一家医院">上</button>&nbsp;
		<button onclick="location = '/m/zixun/data.php?go=next'; return false;" class="button" title="切换到下一家医院">下</button>
	</div>
</div>
<!-- 头部 end -->


<div id="kf_select">
<?php
foreach ($kefu_id_names as $_name) {
	$tab_class = $_name == $_GET["kefu"] ? "hs_tab_cur" : "hs_tab_nor";
	$kf_show_name = $_name;
?>
	<div class="<?php echo $tab_class; ?>">
		<div class="hs_tab_left"></div>
		<div class="hs_tab_center"><a href="?kefu=<?php echo urlencode($_name); ?>"><?php echo $_name; ?></a></div>
		<div class="hs_tab_right"></div>
		<div class="clear"></div>
	</div>
<?php
	}
?>
	<div class="clear"></div>
</div>

<div class="space"></div>
<table id="hour_set" class="round_table hour_set_list sortable" cellpadding="0" cellspacing="0" width="100%">
	<tr class="head">
		<td class="ac column_sortable" width="" title="点击可排序">日期</td>
		<td class="ac column_sortable" width="" title="点击可排序">时间段</td>
		<td class="ac column_sortable" width="" title="点击可排序">总点击</td>
		<td class="ac column_sortable" width="" title="点击可排序">商务通预约</td>
		<td class="ac column_sortable" width="" title="点击可排序">QQ预约</td>
		<td class="ac sorttable_nosort" width="">操作</td>
	</tr>

<?php
if(is_array($line_arr)){
foreach ($line_arr as $_id => $line) {
?>
	<tr class="data" onmouseover="mi(this)" onmouseout="mo(this)">
		<td class="ac"><?php echo int_date_to_date($line["date"]); ?></td>
		<td class="ac"><?php echo $line["hour"]; ?></td>
		<td class="ac"><?php echo $line["click_all"]; ?></td>
		<td class="ac"><?php echo $line["swt_order"]; ?></td>
		<td class="ac"><?php echo $line["qq_order"]; ?></td>
		<td class="ac"><button onclick="edit(<?php echo $line["id"]; ?>)" class="button">修改</button></td>
	</tr>
<?php }} ?>

</table>

<div class="footer_op" style="margin-top:10px; ">
	<div class="footer_op_left"></div>
	<div class="footer_op_right"><?php echo pagelinkc($page, $pagecount, $count, "?kefu=".$kefu, "button"); ?></div>
</div>

</body>
</html>