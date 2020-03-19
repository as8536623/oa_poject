<?php
/*
// - ����˵�� : ��������
// - �������� : ���� (weelia@126.com)
// - ����ʱ�� : 2011-07-23
*/
$table = "jingjia_xiaofei";
require "../../core/core.php";

if (count($hospital_ids) == 0) {
	exit_html("����Աû��Ϊ�����ҽԺ������ʹ�ô˹��ܡ�");
}

if (!$hid) {
	/*
	echo '<script type="text/javascript">'."\r\n";
	echo 'alert("�Բ�������û��ѡ��ҽԺ��������ȷ������Ȼ��ѡ��һ��ҽԺ��");'."\r\n";
	echo 'parent.load_box(1, "src", "/m/chhos.php");'."\r\n";
	echo '</script>'."\r\n";
	exit;
	*/
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
		header("location: xiaofei.php");
	}
	exit;
}

$h_name = $db->query("select name from hospital where id=$hid limit 1", 1, "name");

// ���о����ֶ�:
$all_field_arr = $db->query("select fieldname, name from jingjia_field_set order by fieldname asc", "fieldname", "name");
$sub_name_arr = $db->query("select fieldname, sub_name from jingjia_field_set order by fieldname asc", "fieldname", "sub_name");

// ��ǰҽԺ�ֶ�����:
$h_field = $db->query("select fields from jingjia_hospital_set where hid=$hid limit 1", 1, "fields");
if ($h_field != '') {
	$h_field_arr = explode(",", $h_field);
} else {
	$h_field_arr = array_keys($all_field_arr); //ʹ��ȫ��
}



// ���طǾ�������:
$feijingjia_m_arr = $db->query("select month,x1_per_day from jingjia_feijingjia where hid='$hid' and x1>0", "month", "x1_per_day");
//print_r($feijingjia_m_arr);



// �Ƿ���ʾ�����ѣ�
$show_xiaofei_count = 0;

// ��ǰ�û��ܿ��Ƶ��ֶ�:
if ($debug_mode || $username == "admin" || $uinfo["part_id"] == 9 || ($uinfo["part_id"] == 202 && $uinfo["part_admin"])) {
	$user_field_arr = $h_field_arr; //array_keys($all_field_arr); //������Ա���Կ������о�������
	$show_xiaofei_count = 1; //������Ա���Կ���������
} else {
	// ��ͨ��Ա����ϵͳ���ã��Ҳ��ܿ���������
	$user_field = $db->query("select fields from jingjia_user_set where hid=$hid and uid=$uid limit 1", 1, "fields");
	$user_field_arr = $user_field ? explode(",", $user_field) : array();
}

// ֻ��������Ȩ�� ���ܲ���
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

	// ���嵱ǰҳ��Ҫ�õ��ĵ��ò���:
	$aLinkInfo = array(
		"page" => "page",
		"sort" => "sort",
		"order" => "order",
		"searchword" => "searchword",
		"begin_time" => "begin_time",
		"end_time" => "end_time",
	);

	// ��ȡҳ����ò���:
	foreach ($aLinkInfo as $local_var_name => $call_var_name) {
		$$local_var_name = $_GET[$call_var_name];
	}

	// ���嵥Ԫ���ʽ:
	$aOrderType = array("asc", "desc");

	// �����ֶ�
	$aTdFormat = array();
	//$aTdFormat['chk'] = array("title"=>"ѡ", "width"=>"32", "align"=>"center");
	$aTdFormat['id'] = array("title"=>"ID", "width"=>"60", "align"=>"center", "sort"=>1);
	$aTdFormat['date'] = array("title"=>"����", "width"=>"", "align"=>"center", "sort"=>1);
	if ($show_xiaofei_count) {
		$aTdFormat['xiaofei'] = array("title"=>"�����Ѷ�", "width"=>"", "align"=>"center", "sort"=>1);
		$aTdFormat['ex_baidu'] = array("title"=>"���ٶ�������", "width"=>"", "align"=>"center");
	}
	foreach ($user_field_arr as $v) {
		$_n = $all_field_arr[$v].($sub_name_arr[$v] ? ('<br><font color="silver">('.$sub_name_arr[$v].')</font>') : "");
		$aTdFormat[$v] = array("title"=>$_n, "align"=>"center", "sort"=>1);
	}
	if ($debug_mode || $uinfo["part_id"] == 9) {
		$aTdFormat['feijingjia'] = array("title"=>"�Ǿ�������", "width"=>"", "align"=>"center");
	}
	//$aTdFormat['addtime'] = array("title"=>"���ʱ��", "width"=>"", "align"=>"center", "sort"=>1);
	$aTdFormat['u_name'] = array("title"=>"�ύ��", "width"=>"", "align"=>"center", "sort"=>1);
	$aTdFormat['op'] = array("title"=>"����", "width"=>"", "align"=>"center");

	// Ĭ������ʽ:
	$defaultsort = 'date';
	$defaultorder = 'desc';

	// ��ѯ����:
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

	// ��ҳ����:
	$count = $db->query("select count(*) as count from $table $sqlwhere", 1, "count");
	$pagecount = max(ceil($count / $pagesize), 1);
	$page = max(min($pagecount, intval($page)), 1);
	$offset = ($page - 1) * $pagesize;

	// ��ѯ:
	$data = $db->query("select * from $table $sqlwhere $sqlsort limit $offset,$pagesize");

}

// ҳ�濪ʼ ------------------------
?>
<html>
<head>
<title>�������Ѽ�¼</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style>
.head, .head a {font-family:"΢���ź�","Verdana"; }
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
	return confirm("���ؾ��棺ɾ�����ָܻ���ȷ����ȷ������          ");
}
function feijingjia() {
	parent.load_src(1, "m/jingjia/feijingjia.php", 800, 500);
}
</script>
</head>

<body>
<!-- ͷ�� begin -->
<table width="100%">
	<tr>
	<td class="headers_title" style="width:200px;"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $h_name; ?> �������Ѽ�¼</td><td class="bar_right"></td></tr></table></td>
	<td class="header_center">
		<button onClick="load_url('m/chhos.php'); return false;" class="buttonb" title="�л�������ҽԺ">�л�ҽԺ</button>&nbsp;
		<button onClick="location = 'xiaofei.php?go=prev'; return false;" class="button" title="�л�����һ��ҽԺ">��</button>&nbsp;
		<button onClick="location = 'xiaofei.php?go=next'; return false;" class="button" title="�л�����һ��ҽԺ">��</button>&nbsp;
		&nbsp;
<?php if (check_power("add") && !empty($user_field_arr)) { ?>
		<button onClick="load_url('m/jingjia/xiaofei.php?op=add'); return false;" class="buttonb" title="¼�뵱�ջ���������">¼������</button>&nbsp;
<?php } ?>

<?php if ($debug_mode || $uinfo["part_id"] == 9) { ?>
		<a href="javascript:;" onClick="feijingjia();">�Ǿ������ݹ���</a>&nbsp;
<?php } ?>

	</td>
	<td class="headers_oprate" style="width:300px; text-align:right;"><form name="topform" method="GET">ģ��������<input name="searchword" value="<?php echo $_GET["searchword"]; ?>" class="input" size="12">&nbsp;<input type="submit" class="search" value="����" style="font-weight:bold" title="�������">&nbsp;<button onClick="location='?'" class="search" title="�˳�������ѯ">�˳�</button></form></td>
	</tr>
</table>
<!-- ͷ�� end -->

<div class="space"></div>

<?php
if (count($user_field_arr) > 0) {
?>

<!-- �����б� begin -->
<form name="mainform">
<table width="100%" align="center" class="list">
	<!-- ��ͷ���� begin -->
	<tr>
<?php
// ��ͷ����:
foreach ($aTdFormat as $fn => $fa) {
	$_align = $fa["align"] ? $fa["align"] : "center";
	$_width = $fa["width"];
	if ($fa["sort"]) {
		$_link = make_link_info($aLinkInfo, "sort order");
		$_order = ($sort == $fn ? ($order == "asc" ? "desc" : "asc") : "asc");
		$_link .= "&sort=".$fn."&order=".$_order;
		if (empty($sort)) {
			$_arrow = $defaultsort == $fn ? ($defaultorder == "asc" ? "��" : "��") : "";
		} else {
			$_arrow = $sort == $fn ? ($_order == "asc" ? "��" : "��") : "";
		}
		$_title = '<a href="'.$_link.'">'.$fa["title"].$_arrow.'</a>';
	} else {
		$_title = $fa["title"];
	}
?>
		<td class="head" align="<?php echo $_align; ?>" width="<?php echo $_width; ?>"><?php echo $_title; ?></td>
<? } ?>
	</tr>
	<!-- ��ͷ���� end -->

	<!-- ��Ҫ�б����� begin -->
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
			$op[] = "<a href='javascript:void(0);' onclick='load_url(\"m/jingjia/xiaofei.php?op=edit&id=$id\");' class='op'>�޸�</a>";
		}
		if ($debug_mode || $username == "admin" || $uinfo["part_id"] == 9) {
			$op[] = "<a href='javascript:void(0);' onclick='load_url(\"m/jingjia/xiaofei.php?op=log&id=$id\");' class='op' title='�鿴�޸���־'>��־</a>";
		}
		if ($debug_mode) {
			//$op[] = "<a href='?op=delete&id=$id' onclick='return del_confirm()' class='op'>ɾ��</a>";
		}
		$op_button = implode('&nbsp;<font color=silver>|</font>&nbsp;', $op);

		$hide_line = ($pinfo && $pinfo["ishide"] && $line["isshow"] != 1) ? 1 : 0;

?>
	<tr<?php echo $hide_line ? " class='hide'" : ""; ?>>
<?php
	// ����ֶ�����:
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
		<td colspan="<?php echo count($aTdFormat); ?>" align="center" class="nodata">(��������...)</td>
	</tr>
<?php } ?>
	<!-- ��Ҫ�б����� end -->

</table>
</form>
<!-- �����б� end -->

<!-- ��ҳ���� begin -->
<div class="space"></div>
<div class="footer_op">
	<div class="footer_op_left">
<?php if ($show_xiaofei_count) { ?>
	&nbsp;��ҳ�ܼ����Ѷ�(<b><?php echo $xiaofei_count; ?></b>) / ����(<b><?php echo count($data); ?></b>) = �վ�����(<b><?php echo @round($xiaofei_count / count($data), 1); ?></b>)
<?php } ?>
	</div>
	<div class="footer_op_right"><?php echo pagelinkc($page, $pagecount, $count, make_link_info($aLinkInfo, "page"), "button"); ?></div>
</div>
<!-- ��ҳ���� end -->
</form>

<?php } else { ?>

	<div class="nodata" style="border:2px solid silver; text-align:center; padding:30px 0px;">�Բ��������߱���ҽԺ¼��Ȩ�ޣ����л�����ҽԺ���ԡ�</div>

<?php } ?>

</body>
</html>