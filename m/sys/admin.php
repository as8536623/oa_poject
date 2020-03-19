<?php
/*
// - ����˵�� : admin.php
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2009-05-11 23:16
*/
require "../../core/core.php";
$table = "sys_admin";

if (!$debug_mode && !$uinfo["part_admin"]) {
	exit_html("û�д�Ȩ��..."); //�����ǲ��Ź���Ա
}

// �����Ĵ���:
$op = $_REQUEST["op"];
if ($op) {
	include "admin.op.php";
}

$sqlwhere = "1";

// ��Ա��ȡ����:
if (!$debug_mode && $username != "admin" && $uinfo["character_id"] != 73) {
	$hd_s = array();
	foreach ($hospital_ids as $v) {
		$hd_s[] = ','.$v.',';
	}
	$hd = implode("", $hd_s);
	$sqlwhere .= " and ('$hd' like concat('%,',replace(hospitals,',',',%,'),',%') or hospitals='')";
}

// ����:
if ($key = $_GET["key"]) {
	$sqlwhere .= " and (name like '%{$key}%' or realname like '%{$key}%' or ukey_sn='%{$key}%')";
}

// �ų�
$sqlwhere .= " and name!='$username'";


$group_type = array(1 => "����", 2 => "Ȩ��", 3 => "ҽԺ", 4 => "��������", 5 => "���õ��˺�", 6 => "�����û�", 7 => "uKey�û�");
$cur_group = intval($_SESSION["admin_group_type"]);
if (!$cur_group) {
	$cur_group = $_SESSION["admin_group_type"] = 1;
}


// ��������
$users_count = $db->query("select count(*) as c from sys_admin", 1, "c"); //������
$users_count_close = $db->query("select count(*) as c from sys_admin where isshow=0", 1, "c"); //�ܹر�����
$users_count_open = intval($users_count - $users_count_close); //�ܿ�ͨ����
$users_online = $db->query("select count(*) as c from sys_admin where online=1", 1, "c"); //����
$users_ukey = $db->query("select count(*) as c from sys_admin where isshow=1 and ukey_sn!=''", 1, "c"); //ukey


// ------------- ҳ�濪ʼ ---------------
?>
<html>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style>
.admin_list {margin-left:10px; margin-top:10px; }
#rec_part, #rec_user {margin-top:6px; }
.rub {width:180px; float:left; }
.rub input {float:left; }
.rub a {display:block; float:left; padding-top:2px; }
.rgp {clear:both; margin:10px 0 5px 0; font-weight:bold; }
.group_select {margin-top:10px; margin-bottom:0px; text-align:center; }
</style>

<script language="javascript">
function ucc(o) {
	o.parentNode.getElementsByTagName("a")[0].style.color = o.checked ? "red" : "";
}
function sd(id) {
	var ss = byid("g_"+id).getElementsByTagName("INPUT");
	for (var i=0; i<ss.length; i++) {
		ss[i].checked = !ss[i].checked;
	}
	return false;
}

function add() {
	set_high_light('');
	parent.load_src(1, 'm/sys/admin.php?op=add');
	return false;
}

function ld(id) {
	parent.load_src(1,'m/sys/admin.php?op=edit&id='+id);
	return false;
}

function del() {
	if (confirm("���ȷ��Ҫɾ����Щ��Ա������ؽ���������")) {
		byid("op_value").value = "delete";
		byid("mainform").submit();
	}
}

function close_account() {
	byid("op_value").value = "close";
	byid("mainform").submit();
}

function open_account() {
	byid("op_value").value = "open";
	byid("mainform").submit();
}

function set_ch() {
	byid("new_ch").style.display = byid("new_ch").style.display == "none" ? "inline" : "none";
}

function submit_ch() {
	if (byid("ch_id").value > 0) {
		byid("op_value").value = "set_ch";
		byid("mainform").submit();
	} else {
		alert("��ѡ��Ҫ���õ�Ȩ�ޣ�");
		byid("ch_id").focus();
		return false;
	}
}
</script>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title" width="30%"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center">ϵͳ��Ա����</td><td class="bar_right"></td></tr></table></div>
	<div class="header_center" width="40%">
<?php if (check_power("add")) { ?>
		<button onclick="add();return false;" class="button">����</button>&nbsp;
<?php } ?>
<?php if ($debug_mode || $username == "admin") { ?>
		<button onclick="piliang(); return false;" class="buttonb">����Ȩ��</button>&nbsp;
		<script type="text/javascript">
		function piliang() {
			parent.load_src(1, '/m/sys/admin_piliang.php', 700, 400);
			return false;
		}
		</script>
<?php } ?>
	</div>
	<div class="headers_oprate"><button onclick="history.back()" class="button" title="������һҳ">����</button></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>
<div class="group_select">
	<b>���з�ʽ��</b>
	<form method="GET" style="display:inline;">
		<select name="group" class="combo" onchange="this.form.submit()">
			<?php echo list_option($group_type, "_key_", "_value_", $cur_group); ?>
		</select>
		<input type="hidden" name="op" value="change_group_type">
		<input type="hidden" name="key" value="<?php echo $_GET["key"]; ?>">
	</form>&nbsp;&nbsp;

	<b>�������֣�</b>
	<form method="GET" style="display:inline;">
		<input name="key" value="<?php echo $_GET["key"]; ?>" class="input" size="12">
		<input type="submit" class="button" value="����" style="font-weight:bold;">
		<input type="submit" class="button" onclick="this.form.key.value=''" value="����">
	</form>
</div>

<div style="padding:10px; padding-top:20px; text-align:center; "><b>����ͳ������</b> ��������<b style="color:blue"><?php echo $users_count; ?></b>����ͨ�ʺţ�<b style="color:blue"><?php echo $users_count_open; ?></b>���ر��ʺţ�<b style="color:blue"><?php echo $users_count_close; ?></b>�������ߣ�<b style="color:blue"><?php echo $users_online; ?></b>��ʹ��uKey�û�����<b style="color:blue"><?php echo $users_ukey; ?></b></div>

<form method="POST" name="mainform" id="mainform" action="?">
<div class="admin_list">
	<div id="rec_user">
<?php
if ($cur_group == 1) { //����
	$id_name = $db->query("select id,name,if(id=9,0,id) as sort from sys_part order by sort", "id", "name");
	foreach ($id_name as $k => $v) {
		$all_admin = $db->query("select id,name,realname from sys_admin where $sqlwhere and isshow=1 and id!='$uid' and part_id='$k' order by realname", "id");
		echo '<div class="rgp">'.$v.'('.count($all_admin).')'.' <a href="#" onclick="sd('.$k.');return false;">ȫѡ</a></div>';
		echo '<div id="g_'.$k.'">';
		foreach ($all_admin as $a => $b) {
			echo '<div class="rub"><input type="checkbox" name="uid[]" value="'.$a.'" onclick="ucc(this)"><a href="#" onclick="return ld('.$b["id"].')">'.$b["realname"].($b["realname"] != $b["name"] ? (" (".$b["name"].") ") : "").'</a></div>';
		}
		echo '</div>';
	}
} else if ($cur_group == 2) { //��ɫ
	$id_name = $db->query("select id,concat(name,' (',author,')') as name from sys_character", "id", "name");
	foreach ($id_name as $k => $v) {
		$all_admin = $db->query("select id,name,realname from sys_admin where $sqlwhere and isshow=1 and id!='$uid' and character_id='$k' order by realname", "id");
		echo '<div class="rgp">'.$v.'('.count($all_admin).')'.' <a href="#" onclick="sd('.$k.');return false;">ȫѡ</a></div>';
		echo '<div id="g_'.$k.'">';
		foreach ($all_admin as $a => $b) {
			echo '<div class="rub"><input type="checkbox" name="uid[]" value="'.$a.'" onclick="ucc(this)"><a href="#" onclick="return ld('.$b["id"].')">'.$b["realname"].($b["realname"] != $b["name"] ? (" (".$b["name"].") ") : "").'</a></div>';
		}
		echo '</div>';
	}
} else if ($cur_group == 3) { //ҽԺ
	$allow_ids = implode(",", $hospital_ids);
	$id_name = $db->query("select id,name from hospital where id in ($allow_ids) order by sort desc,id asc", "id", "name");
	foreach ($id_name as $k => $v) {
		$all_admin = $db->query("select id,name,realname from sys_admin where $sqlwhere and isshow=1 and id!='$uid' and concat(',',hospitals,',') like '%,".$k.",%' order by realname", "id");
		echo '<div class="rgp">'.$v.'('.count($all_admin).')'.' <a href="#" onclick="sd('.$k.');return false;">ȫѡ</a></div>';
		echo '<div id="g_'.$k.'">';
		foreach ($all_admin as $a => $b) {
			echo '<div class="rub"><input type="checkbox" name="uid[]" value="'.$a.'" onclick="ucc(this)"><a href="#" onclick="return ld('.$b["id"].')">'.$b["realname"].($b["realname"] != $b["name"] ? (" (".$b["name"].") ") : "").'</a></div>';
		}
		echo '</div>';
	}
} else if ($cur_group == 4) { //����
	$id_name = array(1 => "��������", 0 => "��ͨ��Ա(������)");
	foreach ($id_name as $k => $v) {
		$all_admin = $db->query("select id,name,realname from sys_admin where $sqlwhere and isshow=1 and id!='$uid' and part_admin='$k' order by realname", "id");
		echo '<div class="rgp">'.$v.'('.count($all_admin).')'.' <a href="#" onclick="sd('.$k.');return false;">ȫѡ</a></div>';
		echo '<div id="g_'.$k.'">';
		foreach ($all_admin as $a => $b) {
			echo '<div class="rub"><input type="checkbox" name="uid[]" value="'.$a.'" onclick="ucc(this)"><a href="#" onclick="return ld('.$b["id"].')">'.$b["realname"].($b["realname"] != $b["name"] ? (" (".$b["name"].") ") : "").'</a></div>';
		}
		echo '</div>';
	}
} else if ($cur_group == 5) { //����
	$id_name = array(0 => "���õ��˺�", 1 => "��ͨ���˺�");
	foreach ($id_name as $k => $v) {
		$all_admin = $db->query("select id,name,realname,isshow from sys_admin where $sqlwhere and isshow='$k' and id!='$uid' order by realname", "id");
		echo '<div class="rgp">'.$v.'('.count($all_admin).')'.' <a href="#" onclick="sd('.$k.');return false;">ȫѡ</a></div>';
		echo '<div id="g_'.$k.'">';
		foreach ($all_admin as $a => $b) {
			echo '<div class="rub"><input type="checkbox" name="uid[]" value="'.$a.'" onclick="ucc(this)"><a href="#" onclick="return ld('.$b["id"].')">'.$b["realname"].($b["realname"] != $b["name"] ? (" (".$b["name"].") ") : "").($b["isshow"]!=1 ? ' <font color="red">��</font>' : '').'</a></div>';
		}
		echo '</div>';
	}
} else if ($cur_group == 6) { //����
	$id_name = array(1 => "����", 0 => "������");
	foreach ($id_name as $k => $v) {
		$all_admin = $db->query("select id,name,realname,isshow from sys_admin where $sqlwhere and isshow=1 and online='$k' and id!='$uid' order by realname", "id");
		echo '<div class="rgp">'.$v.'('.count($all_admin).')'.' <a href="#" onclick="sd('.$k.');return false;">ȫѡ</a></div>';
		echo '<div id="g_'.$k.'">';
		foreach ($all_admin as $a => $b) {
			echo '<div class="rub"><input type="checkbox" name="uid[]" value="'.$a.'" onclick="ucc(this)"><a href="#" onclick="return ld('.$b["id"].')">'.$b["realname"].($b["realname"] != $b["name"] ? (" (".$b["name"].") ") : "").'</a></div>';
		}
		echo '</div>';
	}
} else if ($cur_group == 7) { //ukey
	$id_name = array(1 => "��ʹ��uKey", 0 => "δʹ��uKey");
	foreach ($id_name as $k => $v) {
		if ($k > 0) {
			$all_admin = $db->query("select id,name,realname,isshow from sys_admin where $sqlwhere and isshow=1 and ukey_sn!='' and id!='$uid' order by realname", "id");
		} else {
			$all_admin = $db->query("select id,name,realname,isshow from sys_admin where $sqlwhere and isshow=1 and ukey_sn='' and id!='$uid' order by realname", "id");
		}
		echo '<div class="rgp">'.$v.'('.count($all_admin).')'.' <a href="#" onclick="sd('.$k.');return false;">ȫѡ</a></div>';
		echo '<div id="g_'.$k.'">';
		foreach ($all_admin as $a => $b) {
			echo '<div class="rub"><input type="checkbox" name="uid[]" value="'.$a.'" onclick="ucc(this)"><a href="#" onclick="return ld('.$b["id"].')">'.$b["realname"].($b["realname"] != $b["name"] ? (" (".$b["name"].") ") : "").'</a></div>';
		}
		echo '</div>';
	}
}
?>
		<div class="clear"></div>
	</div>
</div>
<input type="hidden" name="op" id="op_value" value="">

<div class="space" style="height:20px;"></div>

<b>&nbsp;&nbsp;������</b>
<button onclick="select_all(); return false;" class="button">ȫѡ</button>&nbsp;
<button onclick="unselect(); return false;" class="button">��ѡ</button>&nbsp;

<b>&nbsp;&nbsp;��ѡ��Ա��</b>
<?php if ($debug_mode) { ?>
�����ܣ�<button onclick="del(); return false;" class="button">ɾ��</button>��&nbsp;
<?php } ?>
<button onclick="close_account(); return false;" class="buttonb">�ر��ʻ�</button>&nbsp;
<button onclick="open_account(); return false;" class="buttonb">��ͨ�ʻ�</button>&nbsp;
<button onclick="set_ch(); return false;" class="buttonb">����Ȩ��</button>&nbsp;
<span id="new_ch" style="display:none;">
	<select name="ch_id" id="ch_id" class="combo">
		<option value="" style="color:gray">-��ѡ����Ȩ��-</option>
<?php
$id_name = $db->query("select id,concat(name,' (',author,')') as name from sys_character", "id", "name");
echo list_option($id_name, "_key_", "_value_");
?>
	</select>&nbsp;
	<button onclick="submit_ch(); return false;" class="button">ȷ��</button>
</span>

<div class="space" style="height:20px;"></div>
</form>

</body>
</html>