<?php
/*
// - ����˵�� : ý������
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2009-05-03 14:47
*/
require "../../core/core.php";
$table = "swt_account";

if ($_GET["date"] == '') {
	$date_int = date("Ymd"); //Ĭ�Ͻ���
	$_GET["date"] = date("Y-m-d");
} else {
	$date_int = date("Ymd", strtotime($_GET["date"]));
}

// �����Ĵ���:
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
	"view" => "view",
	"hospital" => "hospital",
	"date" => "date",
);

// ��ȡҳ����ò���:
foreach ($aLinkInfo as $local_var_name => $call_var_name) {
	$$local_var_name = $_GET[$call_var_name];
}

// ���嵥Ԫ���ʽ:
$aOrderType = array(0 => "", 1 => "asc", 2 => "desc");
$aTdFormat = array(
	0=>array("title"=>"ѡ", "width"=>"32", "align"=>"center"),
	1=>array("title"=>"UID", "width"=>"10%", "align"=>"left", "sort"=>"uid", "defaultorder"=>1),
	2=>array("title"=>"�û���", "width"=>"10%", "align"=>"left", "sort"=>"uname", "defaultorder"=>1),
	5=>array("title"=>"�ͻ�������ͨ", "align"=>"left", "sort"=>"content", "defaultorder"=>1),
	3=>array("title"=>"�ռ�ʱ��", "width"=>"130", "align"=>"center", "sort"=>"addtime", "defaultorder"=>2),
	4=>array("title"=>"����", "width"=>"100", "align"=>"center"),
);

// Ĭ������ʽ:
$defaultsort = 3;
$defaultorder = 2;

// ��ѯ����:
$where = array();
$where[] = "date=$date_int";

// ֻ���ͷ�:
if ($_GET["view"] != '') {
	$kefu_uids = $db->query("select id from sys_admin where part_id=2 or part_id=3", "", "id");
	$where[] = "uid in (".implode(",", $kefu_uids).")";
}

// ֻ��ĳ��ҽԺ��
$hospital = intval($hospital);
if ($hospital > 0) {
	$kefu_uids = $db->query("select id from sys_admin where concat(',', hospitals, ',') like '%,{$hospital},%'", "", "id");
	$where[] = "uid in (".implode(",", $kefu_uids).")";
}


if ($searchword) {
	$where[] = "(binary uname like '%{$searchword}%')";
}
$sqlwhere = count($where) > 0 ? ("where ".implode(" and ", $where)) : "";

// ������Ĵ���
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

// ��ҳ����:
$pagesize = 50;
$count = $db->query_count("select count(*) from $table $sqlwhere");
$pagecount = max(ceil($count / $pagesize), 1);
$page = max(min($pagecount, intval($page)), 1);
$offset = ($page - 1) * $pagesize;

// ��ѯ:
$data = $db->query("select * from $table $sqlwhere $sqlsort limit $offset,$pagesize");

// ��ҽԺ��Ӧ������ͨ�ʺţ�
$swt_to_hospital_arr = $db->query("select swt_ids, name from hospital where swt_ids!=''", "swt_ids", "name");

$hospital_arr = $db->query("select id,name from hospital order by name asc", "id", "name");

// ҳ�濪ʼ ------------------------
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


// �޸� page_param ����ĳ���������
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

// ɾ��ĳ���������
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
			parent.msg_box("�Ѿ�����ǰ��", 3);
		}
	}
	if (dir == "down") {
		if (obj.selectedIndex < obj.options.length-1) {
			obj.selectedIndex = obj.selectedIndex + 1;
			obj.onchange();
			o.disabled = true;
		} else {
			parent.msg_box("�Ѿ������һ����", 3);
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

<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title" style="width:50%"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center">����ͨ�����ʺŲ鿴</td><td class="bar_right"></td></tr></table></div>
	<div class="header_center">
<?php if (check_power("add")) { ?>
		<button onclick="add()" class="button">���</button>
<?php } ?>
	</div>
	<div class="headers_oprate"><form name="topform" method="GET">����������<input name="searchword" value="<?php echo $_GET["searchword"]; ?>" class="input" size="12">&nbsp;<input type="submit" class="search" value="����" style="font-weight:bold" title="�������">&nbsp;<button onclick="location='?'" class="search" title="�˳�������ѯ">����</button></form></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>
<div style="text-align:center;">
	��ʾ���ڣ�<b><?php echo $_GET["date"]; ?></b> &nbsp; �鿴�������ڣ�<input class="input" size="10" onchange="page_param_update('date',this.value,1)" name="date" id="date_to_set" value="<?php echo $_GET["date"]; ?>"> <img src="/res/img/calendar.gif" onClick="picker({el:'date_to_set',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="ѡ������"> &nbsp; &nbsp;
	<button onclick="page_param_update('view','kefu',1)" class="buttonb"><?php echo $_GET["view"] == "kefu" ? ("<b style='color:red'>ֻ����ѯ</b>") : "ֻ����ѯ"; ?></button>&nbsp;
	<button onclick="page_param_update('view','',1)" class="buttonb"><?php echo $_GET["view"] == "" ? ("<b style='color:red'>��ȫ��</b>") : "��ȫ��"; ?></button> &nbsp; &nbsp;
	<select name="hospital" id="hospital_list" class="combo" onchange="page_param_update('hospital',this.value,1)">
		<option value="" style="color:gray">(����ҽԺ)</option>
		<?php echo list_option($hospital_arr, "_key_", "_value_", $_GET["hospital"]); ?>
	</select>&nbsp;
	<button class="button" onclick="hgo('up',this);">��</button>&nbsp;
	<button class="button" onclick="hgo('down',this);">��</button>
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
<?php } ?>
	</tr>
	<!-- ��ͷ���� end -->

	<!-- ��Ҫ�б����� begin -->
<?php
if (count($data) > 0) {
	foreach ($data as $line) {
		$id = $line["id"];

		$content = $line["content"];
		if ($content != '') {
			$c_arr = explode("|", $content);
			$pa = $pb = array();
			foreach ($c_arr as $v) {
				if (in_array($v, explode(" ", "Menu Agent_Records �½��ļ���"))) {
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
			$op[] = "<a href='#edit' onclick='edit(".$id.", this)' class='op'>�޸�</a>";
		}
		if (check_power("delete")) {
			$op[] = "<a href='?op=delete&id=$id' onclick='return isdel()' class='op'>ɾ��</a>";
		}
		$op_button = $op ? implode("&nbsp;", $op) : '<font color="gray">(��)</font>';

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
	<div class="footer_op_left"><button onclick="select_all()" class="button">ȫѡ</button>&nbsp;<button onclick="unselect()" class="button">��ѡ</button>&nbsp;<?php echo $power->show_button("hide,delete"); ?></div>
	<div class="footer_op_right"><?php echo pagelinkc($page, $pagecount, $count, make_link_info($aLinkInfo, "page"), "button"); ?></div>
</div>
<!-- ��ҳ���� end -->

</body>
</html>