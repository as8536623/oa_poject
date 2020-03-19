<?php
/*
// - ����˵�� : ý������
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2009-05-03 14:47
*/
require "../../core/core.php";
$table = "media";

if ($hid == 0) {
	exit_html("�Բ���û��ѡ��ҽԺ������ִ�иò�����");
}

// �����Ĵ���:
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
				$log->add("delete", "ɾ������", serialize($op_data));
			}

			if ($del_bad > 0) {
				msg_box("ɾ���ɹ� $del_ok �����ϣ�ɾ��ʧ�� $del_bad �����ϡ�", "back", 1);
			} else {
				msg_box("ɾ���ɹ�", "back", 1);
			}

		default:
			msg_box("����δ����...", "back", 1);
	}
}

// ���嵱ǰҳ��Ҫ�õ��ĵ��ò���:
$aLinkInfo = array(
	"page" => "page",
	"sortid" => "sort",
	"sorttype" => "sorttype",
	"searchword" => "searchword",
);

// ��ȡҳ����ò���:
foreach ($aLinkInfo as $local_var_name => $call_var_name) {
	$$local_var_name = $_GET[$call_var_name];
}

// ���嵥Ԫ���ʽ:
$aOrderType = array(0 => "", 1 => "asc", 2 => "desc");
$aTdFormat = array(
	0=>array("title"=>"ѡ", "width"=>"32", "align"=>"center"),
	1=>array("title"=>"����", "width"=>"100", "align"=>"center"),
	5=>array("title"=>"���ȶ�", "width"=>"80", "align"=>"center"),
	2=>array("title"=>"����", "width"=>"", "align"=>"left"),
	3=>array("title"=>"���ʱ��", "width"=>"120", "align"=>"center"),
	9=>array("title"=>"�����", "width"=>"100", "align"=>"center"),
	4=>array("title"=>"����", "width"=>"100", "align"=>"center"),
);

// Ĭ������ʽ:
$defaultsort = 5;
$defaultorder = 2;

$sqlsort = "order by sort desc, id asc";

// ��ѯ����:
$where = array();
$where[] = "(hospital_id=0 or hospital_id=$hid)";
if ($searchword) {
	$where[] = "(binary name like '%{$searchword}%')";
}
$sqlwhere = count($where) > 0 ? ("where ".implode(" and ", $where)) : "";


// ��ҳ����:
$pagesize = 9999;
$count = $db->query_count("select count(*) from $table $sqlwhere");
$pagecount = max(ceil($count / $pagesize), 1);
$page = max(min($pagecount, intval($page)), 1);
$offset = ($page - 1) * $pagesize;

// ��ѯ:
$data = $db->query("select * from $table $sqlwhere $sqlsort limit $offset,$pagesize");

$hospital_id_name = $db->query("select id,name from hospital", 'id', 'name');


// ҳ�濪ʼ ------------------------
?>
<html>
<head>
<title>ý����Դ����</title>
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
<!-- ͷ�� begin -->
<table class="headers" width="100%">
	<tr>
		<td class="headers_title" style="width:280px;"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center">ý����Դ (ȫ��)</td><td class="bar_right"></td></tr></table></td>
		<td class="header_cneter" align="center">
<?php
if (check_power("add")) {
	echo '<a href="javascript:void(0);" onclick="add(0)"><b>���ȫ��ý����Դ</b></a>&nbsp;|&nbsp;<a href="javascript:void(0);" onclick="add('.$hid.')"><b>��ӡ�'.$hinfo["name"].'��ý����Դ(˽��)</b></a>';
}
?>
		</td>
		<td class="headers_oprate" style="width:280px; text-align:right;">
			<form name="topform" method="GET"><nobr>ģ��������<input name="searchword" value="<?php echo $_GET["searchword"]; ?>" class="input" size="12">&nbsp;<input type="submit" class="search" value="����" style="font-weight:bold" title="�������">&nbsp;<button onclick="location='?'" class="search" title="�˳�������ѯ">����</button></nobr></form>
		</td>
	</tr>
</table>
<!-- ͷ�� end -->

<div class="space"></div>
<div class="description">
	<div class="d_item">��ע��: �����硱�����绰�� ��ϵͳ����ý����Դ�����ﲻ��Ҫ��ӡ�</div>
</div>

<div class="space"></div>

<!-- �����б� begin -->
<form name="mainform">
<table width="100%" align="center" class="list">
	<!-- ��ͷ���� begin -->
	<tr>
<?php
// ��ͷ����:
foreach ($aTdFormat as $tdid => $tdinfo) {
	list($tdalign, $tdwidth, $tdtitle) = make_td_head($tdid, $tdinfo);
?>
		<td class="head" align="<?php echo $tdalign; ?>" width="<?php echo $tdwidth; ?>"><?php echo $tdtitle; ?></td>
<? } ?>
	</tr>
	<!-- ��ͷ���� end -->

	<!-- ��Ҫ�б����� begin -->
<?php
if (count($data) > 0) {
	foreach ($data as $line) {
		$id = $line["id"];

		$op = array();
		//if (check_power("edit")) {
		if ($username == "admin" || $debug_mode) {
			$op[] = "<a href='#edit' onclick='edit(".$id.", this)' class='op'>�޸�</a>";
		}
		//if (check_power("delete") && $line["hospital_id"] > 0) {
		if ($username == "admin" || $debug_mode) {
			$op[] = "<a href='?op=delete&id=$id' onclick='return isdel()' class='op'>ɾ��</a>";
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
		<td align="center" class="item"><?php echo $line["hospital_id"] == 0 ? "ȫ��" : "˽��"; ?></td>
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
		<td colspan="<?php echo count($aTdFormat); ?>" align="center" class="nodata">(û������...)</td>
	</tr>
<?php } ?>
	<!-- ��Ҫ�б����� end -->
</table>
</form>
<!-- �����б� end -->

<div class="space"></div>

<!-- ��ҳ���� begin -->
<div class="footer_op">
	<div class="footer_op_left"><button onclick="select_all()" class="button">ȫѡ</button>&nbsp;<button onclick="unselect()" class="button">��ѡ</button>&nbsp;<?php //echo $power->show_button("hide,delete"); ?></div>
	<div class="footer_op_right"><?php echo pagelinkc($page, $pagecount, $count, make_link_info($aLinkInfo, "page"), "button"); ?></div>
</div>
<!-- ��ҳ���� end -->

</body>
</html>