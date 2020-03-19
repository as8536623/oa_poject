<?php
// --------------------------------------------------------
// - ����˵�� : ͳ�� ��Ŀ ����
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2010-10-13 11:34
// --------------------------------------------------------
require "../../core/core.php";
$table = "count_web_type";

// �����Ĵ���:
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
			$log->add("delete", "ɾ������", serialize($op_data));
		}

		if ($del_bad > 0) {
			msg_box("ɾ���ɹ� $del_ok �����ϣ�ɾ��ʧ�� $del_bad �����ϡ�", "back", 1);
		} else {
			msg_box("ɾ���ɹ�", "back", 1);
		}
	}
}

// ���嵱ǰҳ��Ҫ�õ��ĵ��ò���:
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


// ��ѯ����:
$where = array();
if ($key) {
	$where[] = "(binary name like '%{$key}%')";
}
$sqlwhere = $db->make_where($where);

// ��ѯ:
$list = $db->query("select * from $table", "hid");

$admin_id_name = $db->query("select id,realname from sys_admin where isshow=1", "id", "realname");


// ҳ�濪ʼ ------------------------
?>
<html>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/sorttable_keep.js" language="javascript"></script>
<style>
.column_sortable {color:blue !important; cursor:pointer; font-family:"΢���ź�"; }
.sorttable_nosort {color:gray; font-family:"΢���ź�"; }
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
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title" style="width:50%"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><nobr>�ͷ�����</nobr></td><td class="bar_right"></td></tr></table></div>
	<div class="header_center"></div>
	<div class="headers_oprate"><form name="topform" method="GET"><nobr>������<input name="key" value="<?php echo $_GET["key"]; ?>" class="input" size="12">&nbsp;<input type="submit" class="search" value="����" style="font-weight:bold" title="�������">&nbsp;<button onclick="location='?'" class="search" title="�˳�������ѯ">����</button></nobr></form></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>

<!-- �����б� begin -->
<form name="mainform">
<table class="new_list sortable" width="100%">
	<tr class="head">
		<td class="sorttable_nosort" align="center" width="32">ѡ</td>
		<td class="column_sortable" title="���������" align="left" width="100">ҽԺ</td>
		<td class="column_sortable" title="���������" align="left">��Ա����</td>
		<td class="column_sortable" title="���������" align="center" width="90">���ʱ��</td>
		<td class="column_sortable" title="���������" align="center" width="60">���ȶ�</td>
		<td class="sorttable_nosort" align="center" width="120">����</td>
	</tr>

<?php
foreach ($hid_list as $_hid => $_hinfo) {
	$li = $list[$_hid];
	$r = array();

	$r["ѡ"] = '<input name="delcheck" type="checkbox" value="'.$li["id"].'" onclick="set_item_color(this)">';
	$r["ҽԺ"] = $_hinfo["name"];

	$uids = explode(",", $li["uids"]);
	$u_names = array();
	foreach ($uids as $v) {
		if (array_key_exists($v, $admin_id_name)) {
			$u_names[] = $admin_id_name[$v];
		}
	}

	$r["��Ա����"] = "<font color=blue><b>�ͷ�</b>��".$li["kefu"]."</font><br>"."<font color=red><b>����Ա</b>��".implode("��", $u_names)."</font>";

	$r["���ʱ��"] = str_replace(" ", "<br>", date("Y-m-d H:i", $li["addtime"]));
	$r["����"] = intval($_hinfo["sort"]);

	$op = array();
	if (check_power("edit")) {
		$op[] = "<a href='#edit:".$_hid."' onclick='edit(".$_hid.", this);return false;' class='op' title='�޸�����'>�޸�</a>";
	}
	if ($username == "admin" || $debug_mode) {
		$op[] = "<a href='#log:".$_hid."' onclick='show_log(".$_hid.", this);return false;' class='op' title='�鿴�޸���־'>��־</a>";
	}
	if (check_power("delete")) {
		//$op[] = "<a href='?op=delete&hid=".$_hid."' onclick='return isdel()' class='op'>ɾ��</a>";
	}
	$r["����"] = implode($GLOBALS["button_split"], $op);

?>
	<tr class="line" onmouseover="mi(this)" onmouseout="mo(this)">
		<td align="center"><?php echo $r["ѡ"]; ?></td>
		<td align="left"><?php echo $r["ҽԺ"]; ?></td>
		<td align="left"><?php echo $r["��Ա����"]; ?></td>
		<td align="center"><?php echo $r["���ʱ��"]; ?></td>
		<td align="center"><?php echo $r["����"]; ?></td>
		<td align="center"><?php echo $r["����"]; ?></td>
	</tr>
<?php
}
?>
</table>
</form>
<!-- �����б� end -->

<div class="space"></div>

<!-- ��ҳ���� begin -->
<div class="footer_op">
	<div class="footer_op_left"><button onclick="select_all()" class="button">ȫѡ</button>&nbsp;<button onclick="unselect()" class="button">��ѡ</button></div>
	<div class="footer_op_right">�� <b><?php echo count($list); ?></b> ��</div>
</div>
<!-- ��ҳ���� end -->

</body>
</html>