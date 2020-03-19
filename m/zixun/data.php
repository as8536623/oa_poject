<?php
/*
// - ����˵�� : ��ѯ����ʱ������
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2013-4-20
*/
require "../../core/core.php";
require "config.inc.php";
$table = "zixun_data";

if (count($hospital_ids) == 0) {
	exit_html("����Աû��Ϊ�����ҽԺ������ʹ�ô˹��ܡ�");
}

$change_op = $_GET["go"];
if (!$hid || $change_op != '') {
	// ҽԺ�л�����:
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
			msg_box("�Ѿ�����ǰһ��ҽԺ��", "back", 1, 2);
		}
	}
	if ($change_op == "next") {
		$cur_k = array_search($hid, $h_list);
		if ($cur_k < count($h_list) - 1) {
			$check_hid = $h_list[$cur_k + 1];
		} else {
			msg_box("�Ѿ������һ��ҽԺ��", "back", 1, 2);
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
	$_GET["kefu"] = $kefu_id_names[0]; //�Զ�ѡ�е�һ���ͷ�
}

// ��ѯ��ǰ����:
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



// ��ȡ�ͷ�����:
if ($_GET["kefu"] != '') {
	$kefu = $_GET["kefu"];
	$pagesize = 50;

	$count = $db->query("select count(*) as c from zixun_data where hid=$hid and kefu='$kefu'", 1, "c");
	$pagecount = max(ceil($count / $pagesize), 1);
	$page = max(min($pagecount, intval($_GET["page"])), 1);
	$offset = ($page - 1) * $pagesize;

	$line_arr = $db->query("select * from zixun_data where hid=$hid and kefu='$kefu' order by id desc limit $offset, $pagesize", "id");
}


// ҳ�濪ʼ ------------------------
?>
<html>
<head>
<title>��ѯ����ά��</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/sorttable_keep.js" language="javascript"></script>
<style type="text/css">
.column_sortable {cursor:pointer; color:blue; font-family:"΢���ź�"; }
.sorttable_nosort {font-family:"΢���ź�"; }
.hour_set_list {border:1px solid #97e6a5; }
.hour_set_list .head td {border:1px solid #e7e7e7; background:#f2f8f9; padding:4px 3px 3px 3px; font-weight:bold; }
.hour_set_list .data td {border:1px solid #e7e7e7; padding:4px 3px 3px 3px; }
.al {text-align:left; }
.ac {text-align:center; }
.yh {font-family:"΢���ź�"; }

#kefu_select * {font-family:"΢���ź�"; }
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
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $h_name; ?> ��ѯ����</td><td class="bar_right"></td></tr></table></div>
	<div class="header_center">
<?php if (check_power("add")) { ?>
		<button onclick="add('m/zixun/data.php?op=add&kefu=<?php echo urlencode($_GET["kefu"]); ?>'); return false;" class="button">¼��</button>&nbsp;
<?php } ?>
		<button onclick="self.location.reload();return false;" class="button">ˢ��</button>
	</div>
	<div class="headers_oprate">
		<button onclick="change_hospital('m/chhos.php'); return false;" class="buttonb" title="�л�������ҽԺ">�л�ҽԺ</button>&nbsp;
		<button onclick="location = '/m/zixun/data.php?go=prev'; return false;" class="button" title="�л�����һ��ҽԺ">��</button>&nbsp;
		<button onclick="location = '/m/zixun/data.php?go=next'; return false;" class="button" title="�л�����һ��ҽԺ">��</button>
	</div>
</div>
<!-- ͷ�� end -->


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
		<td class="ac column_sortable" width="" title="���������">����</td>
		<td class="ac column_sortable" width="" title="���������">ʱ���</td>
		<td class="ac column_sortable" width="" title="���������">�ܵ��</td>
		<td class="ac column_sortable" width="" title="���������">����ͨԤԼ</td>
		<td class="ac column_sortable" width="" title="���������">QQԤԼ</td>
		<td class="ac sorttable_nosort" width="">����</td>
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
		<td class="ac"><button onclick="edit(<?php echo $line["id"]; ?>)" class="button">�޸�</button></td>
	</tr>
<?php }} ?>

</table>

<div class="footer_op" style="margin-top:10px; ">
	<div class="footer_op_left"></div>
	<div class="footer_op_right"><?php echo pagelinkc($page, $pagecount, $count, "?kefu=".$kefu, "button"); ?></div>
</div>

</body>
</html>