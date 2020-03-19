<?php
defined("ROOT") or exit("Error.");

// �˵��Ĵ���
$_SESSION["global_user_menu"] = '';
if ($op == "edit" && $line["menu"] != '') {
	$_SESSION["global_user_menu"] = $line["menu"];
}

?>
<html>
<head>
<title>��Ա����</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style>
body {overflow-x:hidden; }

.man_check {float:left; width:200px; }
.HLI {border:1px solid #fea45a; font-size:12px; color:#535353;  }
.HLI td {border-bottom:1px solid #ffdfca; }
.HLI .HL {text-align:center; padding:3px 20px; }
.HLI .HR {text-align:left; padding:3px; }
.HLI b {font-family:"΢���ź�" !important; color:; }

.hide_scroll {overflow-y:hidden; overflow-x:hidden; }
.show_scroll, html {overflow-y:auto; overflow-x:hidden; }
</style>
<script language="javascript">
var op = "<?php echo $op; ?>";

function show_pass() {
	var pass = document.mainform.pass.value;
	if (pass != "") {
		alert("������������ǣ� " + pass + "��������");
	} else {
		alert("����û���������룡������������");
	}
}

function check_data() {
	oForm = document.mainform;
	if (oForm.name.value.length < 2) {
		msg_box("���������û������ҳ�������2λ");
		oForm.name.focus();
		return false;
	}
	if (oForm.realname.value == "") {
		msg_box("��������ʵ������");
		oForm.realname.focus();
		return false;
	}

	if (byid("powermode").value == "") {
		msg_box("��ѡ����Ȩ��ʽ��");
		oForm.powermode.focus();
		return false;
	}
	if (byid("powermode").value == "2" && byid("character_id").value == "0") {
		msg_box("��ѡ��Ȩ�ޣ�");
		oForm.character_id.focus();
		return false;
	}
	if (!confirm("ÿһ�����˰ɣ��������Ŷ����������ٿ�һ�£�������ȡ����")) {
		return false;
	}
	return true;
}

function show_hide_detail(o) {
	if (o.value == "-1") {
		byid("power_detail_box").style.display = "inline";
		byid("powermode").value = "1"; //�Զ���
	} else {
		byid("power_detail_box").style.display = "none";
		byid("powermode").value = "2"; //��ɫ
	}
}

function check_repeat(o, type) {
	if (op == "add") {
		if (o.value == '') {
			byid(type+"_tips").innerHTML = '';
		} else {
			var s = o.value;
			var xm = new ajax();
			xm.connect("/http/check_admin_repeat.php", "GET", "&s="+(s)+"&type="+(type), check_repeat_do);
		}
	}
}

function check_repeat_do(o) {
	var out = ajax_out(o);
	if (out["status"] == "ok") {
		if (out["tips"] != '') {
			byid(out["type"]+"_tips").innerHTML = '<font color=red>'+out["tips"]+"</font> ";
		} else {
			byid(out["type"]+"_tips").innerHTML = "�� ";
		}
	}
}

function update_check_color(o) {
	if (o.nextSibling.tagName.toLowerCase() == "label") {
		o.nextSibling.style.color = o.checked ? "blue" : "";
	}
}

// ����ѡ��ҽԺ/����
function h_gp_set(area, o) {
	var chk = false;
	if (o.title == null || o.title == '' || o.title == "ȫ��ѡ��") {
		o.title = "ȫ����ѡ";
		chk = true;
	} else {
		o.title = "ȫ��ѡ��";
	}
	var el = byid("f_"+area).getElementsByTagName("INPUT");
	for (var i = 0; i < el.length; i++) {
		var o = el[i];
		if (o.type.toLowerCase() == "checkbox") {
			o.checked = chk;
			o.onclick();
		}
	}
}

function set_h_list(s) {
	byid("_h_list_area").style.display = "none";
	byid("_h_list_depart").style.display = "none";
	if (s == "area") {
		byid("_h_list_area").style.display = "block";
		byid("_h_001").checked = true;
		set_check_disabled("_h_list_area", false);
		set_check_disabled("_h_list_depart", true);
	} else {
		byid("_h_list_depart").style.display = "block";
		byid("_h_002").checked = true;
		set_check_disabled("_h_list_area", true);
		set_check_disabled("_h_list_depart", false);
	}
}

function set_check_disabled(id, value) {
	var objs = byid(id).getElementsByTagName("INPUT");
	for (var i=0; i<objs.length; i++) {
		var o = objs[i];
		if (o.type == "checkbox") {
			o.disabled = value;
		}
	}
}

</script>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $title; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>

<div class="description">
	<div class="d_title">�޸���ʾ��</div>
	<li class="d_item">��ҳ����<font color="red"><b>ÿ�����Ҫ������д</b></font>�������д����ȷ�����ܵ��·ǳ����صĺ����<font color="red"><b>�������ݶ�ʧ���˺��޷���¼��</b></font>��������д�����ʣ�����ѯ������Ա��</li>
</div>

<div class="space"></div>

<form name="mainform" method="POST" onSubmit="return check_data()">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">��������</td>
	</tr>

	<tr>
		<td class="left"><font color="red">��¼����</font></td>
		<td class="right"><input name="name" value="<?php echo $line["name"]; ?>" class="input" size="20" style="width:120px" onBlur="check_repeat(this,'name')" <?php if ($id > 0) echo "disabled"; ?>> <span id="name_tips"></span><span class="intro">�������ܸ���</span></td>
	</tr>

	<tr>
		<td class="left"><font color='red'>��ʵ������</font></td>
		<td class="right"><input name="realname" value="<?php echo $line["realname"]; ?>" class="input" size="20" style="width:120px" onBlur="check_repeat(this,'realname')" <?php if ($id > 0) echo "disabled"; ?>> <span id="realname_tips"></span><span class="intro">��ʵ������������ʾ</span></td>
	</tr>

	<tr>
		<td class="left"><font color="red">���룺</font></td>
		<td class="right"><input type="password" name="pass" value="" class="input" size="20" style="width:120px"> <a href="javascript:void(0);" onClick="show_pass();return false;">[��ʾ�����������]</a> <?php if ($id) echo '<span class="intro">�����µ����뽫����ԭ����</span>'; ?></td>
	</tr>

	<tr>
		<td colspan="2" class="head">��Ȩ</td>
	</tr>

	<tr>
		<td class="left" valign="top" style="padding-top:8px;">
			<font color="red">ҽԺ��Ȩ��</font><br>
			<br>
			ҽԺչ�ַ���<br>
			<input type="radio" name="_h_list_mode_" value="area" id="_h_001" onClick="set_h_list(this.value)" /><label for="_h_001">����&nbsp;</label><br>
			<input type="radio" name="_h_list_mode_" value="depart" id="_h_002" onClick="set_h_list(this.value)" /><label for="_h_002">����&nbsp;</label><br>
		</td>
		<td class="right" id="_h_data_area">

			<!-- �������г� begin -->
			<table id="_h_list_area" width="100%" class="HLI" cellpadding="0" cellspacing="0" style="display:none;">
<?php
$hs_ids = implode(",", $hospital_ids);
$area_arr = $db->query("select area,count(area) as c from hospital where id in ($hs_ids) group by area order by area asc", "area", "c");
foreach ($area_arr as $a => $c) {
?>
				<tr>
					<td class="HL"><nobr><b><?php echo $a ? $a : "����"; ?></b> (<?php echo $c; ?>) <a href="#ȫѡ" onClick="h_gp_set('<?php echo $a; ?>', this);return false;">ȫѡ</a></nobr></td>
					<td class="HR" id="f_<?php echo $a; ?>">
<?php
$hs_id_name = $db->query("select id,name from hospital where area='$a' and id in ($hs_ids) order by name asc", "id", "name");
foreach ($hs_id_name as $_hid => $_hname) {
$checked = in_array($_hid, explode(",", $line["hospitals"])) ? "checked" : "";
?>
					&nbsp;<nobr><input disabled="true" type="checkbox" class="check" name="hospital_ids[]" value="<?php echo $_hid; ?>" id="hc_<?php echo $_hid; ?>" <?php echo $checked; ?> onClick="update_check_color(this)"><label for="hc_<?php echo $_hid; ?>" <?php if ($checked) echo ' style="color:red"'; ?>><?php echo $_hname; ?></label></nobr><?php echo " "; ?>
<?php } ?>
					</td>
				</tr>
<?php } ?>
			</table>
			<!-- �������г� end -->

			<!-- �������г� begin -->
			<table id="_h_list_depart" width="100%" class="HLI" cellpadding="0" cellspacing="0" style="display:none;">
<?php
$hs_ids = implode(",", $hospital_ids);
$dep_arr = $db->query("select depart,count(depart) as c from hospital where id in ($hs_ids) group by depart order by depart asc", "depart", "c");
foreach ($dep_arr as $a => $c) {
?>
				<tr>
					<td class="HL"><nobr><b><?php echo $a ? $a : "����"; ?></b> (<?php echo $c; ?>) <a href="#ȫѡ" onClick="h_gp_set('<?php echo $a; ?>', this);return false;">ȫѡ</a></nobr></td>
					<td class="HR" id="f_<?php echo $a; ?>">
<?php
$hs_id_name = $db->query("select id,name from hospital where depart='$a' and id in ($hs_ids) order by name asc", "id", "name");
foreach ($hs_id_name as $_hid => $_hname) {
$checked = in_array($_hid, explode(",", $line["hospitals"])) ? "checked" : "";
?>
					&nbsp;<nobr><input disabled="true" type="checkbox" class="check" name="hospital_ids[]" value="<?php echo $_hid; ?>" id="hc2_<?php echo $_hid; ?>" <?php echo $checked; ?> onClick="update_check_color(this)"><label for="hc2_<?php echo $_hid; ?>" <?php if ($checked) echo ' style="color:red"'; ?>><?php echo $_hname; ?></label></nobr><?php echo " "; ?>
<?php } ?>
					</td>
				</tr>
<?php } ?>
			</table>
			<!-- �������г� end -->

			<script type="text/javascript">
			set_h_list('area'); // area|depart ���ֵ����Ĭ����ʾ�ķ�ʽ��Ҳ��ҳ����ز���ȱ��
			</script>

		</td>
	</tr>

	<tr>
		<td class="left"><font color="red">�Һź������ã�</font></td>
		<td class="right">
<?php
$line_config = @explode(",", $line["guahao_config"]);
foreach ($guahao_config_arr as $k => $v) {
	$checked = in_array($k, $line_config) ? "checked" : "";
?>
			<span><input type="checkbox" name="guahao_config[]" value="<?php echo $k; ?>" <?php echo $checked; ?> id="chk_<?php echo $k; ?>" onClick="update_check_color(this)"><label for="chk_<?php echo $k; ?>"<?php if ($checked) echo ' style="color:red"'; ?>><?php echo $v; ?></label></span>
<?php } ?>
			<!-- <font color="gray">&nbsp;(�˹����ݲ���Ч)</font> -->
		</td>
	</tr>

<?php
	$select_ch = $line["powermode"] == 1 ? 0 : $line["character_id"];
?>
	<tr>
		<td class="left"><font color="red">ѡ��Ȩ�ޣ�</font></td>
		<td class="right">
			<input type="hidden" name="powermode" id="powermode" value="<?php echo $line["powermode"]; ?>">
			<select name="character_id" onChange="show_hide_detail(this)" class="combo">
				<option value="0" style="color:gray">--��ѡ��--</option>
				<option value="-1" style="color:red"<?php if ($line["powermode"]==1) echo " selected"; ?>>-�Զ���-</option>
				<?php echo list_option($ch_data, "id", "name", $select_ch); ?>
			</select> &nbsp;
			<span id="power_detail_box" style="display:<?php echo $line["powermode"] != 1 ? "none" : ""; ?>">
				<button class="buttonb" onClick="load_detail_box();return false;">�Զ���</button>
				<b>��<font color="red">��ص㡰�Զ��塱��ť���ú�Ȩ��</font>�������޷��ύ</b>
			</span>
		</td>
	</tr>

	<tr>
		<td class="left"><font color="red">�������ţ�</font></td>
		<td class="right" valign="top">
			<select name="part_id" class="combo">
			<?php //echo list_option($part->get_sub_part_list(intval($uinfo["part_id"]), 1), "_key_", "_value_", $line["part_id"]); ?>
			<?php echo list_option(get_part_list('array'), "id", "name", $line["part_id"]); ?>
			</select>
<?php if ($debug_mode || $username == "admin" || $uinfo["part_admin"]) { ?>
			<input type="checkbox" class="check" name="part_admin" value="1" id="part_admin" <?php if ($line["part_admin"]) echo "checked"; ?>><label for="part_admin">���Ź���Ա</label>
<?php } ?>
			<span class="intro">�������ű���ѡ�񡣲��Ź���Ա�൱�ڡ��鳤��Ȩ��</span>
		</td>
	</tr>

	<tr>
		<td class="left"><font color="red">���ݹ���</font></td>
		<td class="right">
<?php

// 2013-5-13
$index_module = $db->query("select name from index_module_set where isshow='1'");
foreach ($index_module as $_v) {
	$data_power_arr[$_v["name"]] = $_v["name"];
}

$cur_data_power = @explode(",", $line["data_power"]);
foreach ($data_power_arr as $k => $v) {
	$chk = @in_array($k, $cur_data_power) ? " checked" : "";
?>
			<input type="checkbox" name="data_power[]" value="<?php echo $k; ?>" id="dp_<?php echo $k; ?>" <?php echo $chk; ?>><label for="dp_<?php echo $k; ?>"><?php echo $v; ?></label> &nbsp;
<?php } ?>
			<span class="intro">��ѡ���ж�ӦȨ�ޣ�ͬʱ��ҳ����ʾ��Ӧ��ͳ������ģ��</span>
		</td>
	</tr>

<?php if ($debug_mode || $username == "admin" || $uinfo["character_id"] == 73) { ?>
	<tr>
		<td class="left"><font color="red">�����</font></td>
		<td class="right">
			<input type="checkbox" name="jiuzhen_view" value="1" <?php if ($line["jiuzhen_view"]) echo "checked"; ?> id="chk_jzb_view"><label for="chk_jzb_view">�鿴</label>&nbsp;&nbsp;
			<input type="checkbox" name="jiuzhen_edit" value="1" <?php if ($line["jiuzhen_edit"]) echo "checked"; ?> id="chk_jzb_edit"><label for="chk_jzb_edit">�޸�</label>&nbsp;&nbsp;
		</td>
	</tr>
	<tr>
		<td class="left"><font color="red">�����б�</font></td>
		<td class="right"><input type="checkbox" name="show_list" value="1" <?php if ($line["show_list"]) echo "checked"; ?> id="chk0003"><label for="chk0003">��ʾ��ҳ�����б�</label> </td>
	</tr>
	<tr>
		<td class="left"><font color="red">��ʾ���룺</font></td>
		<td class="right"><input type="checkbox" name="show_tel" value="1" <?php if ($line["show_tel"]) echo "checked"; ?> id="chk0001"><label for="chk0001">��ʾ�������˵ĵ绰����</label> <span class="intro">(����ǵ绰�طÿͷ��������Ҳ����ʾ����)</span></td>
	</tr>
	<tr>
		<td class="left"><font color="red">uKey��¼��</font></td>
		<td class="right">
			<input type="checkbox" name="use_ukey" onClick="show_hide_ukey_box(this.checked)" <?php echo ($line["use_ukey"] == 1) ? "checked" : ""; ?> value="1" id="use_ukey_001"><label for="use_ukey_001">ʹ��uKey��¼</label>&nbsp; &nbsp;
			<span id="use_ukey_box" style="display:<?php echo ($line["use_ukey"] == 1) ? "" : "none"; ?>">Ӳ����ţ�<input name="ukey_sn" id="ukey_sn" value="<?php echo $line["ukey_sn"]; ?>" class="input" style="width:120px"> <a href="javascript:write_cur_ukey_sn()">��д��ǰ�����uKey���к�</a>&nbsp;&nbsp;&nbsp;&nbsp;
			��ע��<input name="ukey_no" value="<?php echo $line["ukey_no"]; ?>" class="input" style="width:80px">
			</span>
		</td>
	</tr>

	<script type="text/javascript">
	function show_hide_ukey_box(value) {
		byid("use_ukey_box").style.display = (value) ? "" : "none";
	}
	</script>

	<tr>
		<td class="left">��¼�ֻ��棺</td>
		<td class="right">
			<input type="checkbox" name="allow_mobile_login" id="allow_mobile_login" <?php echo $line["allow_mobile_login"] ? "checked" : ""; ?>><label for="allow_mobile_login">��ѡ�������¼�ֻ��� (Ĭ�����Ϊ������)</label>
		</td>
	</tr>
<?php } ?>


<?php if ($debug_mode || $username == "admin" || $realname == "�ƿ���" || $uinfo["character_id"] == 61) { ?>
	<tr>
		<td class="left">��ʾ�����¼��</td>
		<td class="right">
			<input type="checkbox" name="show_talk" id="show_talk" <?php echo $line["show_talk"] ? "checked" : ""; ?>><label for="show_talk">��ѡ��ʾ�����¼</label>
		</td>
	</tr>

<?php
$line["worklog"] = explode(",", $line["worklog"]);
?>
	<tr>
		<td class="left">������־��</td>
		<td class="right">
			��ѯ������
			<input type="checkbox" name="worklog[]" value="zixun_view" id="zixun_view" onClick="update_check_color(this)" <?php echo in_array("zixun_view", $line["worklog"]) ? "checked" : ""; ?>><label for="zixun_view">�鿴</label>
			<input type="checkbox" name="worklog[]" value="zixun_edit" id="zixun_edit" onClick="update_check_color(this)" <?php echo in_array("zixun_edit", $line["worklog"]) ? "checked" : ""; ?>><label for="zixun_edit">�޸�</label>&nbsp;&nbsp;
			ִ�з�����
			<input type="checkbox" name="worklog[]" value="zhixing_view" id="zhixing_view" onClick="update_check_color(this)" <?php echo in_array("zhixing_view", $line["worklog"]) ? "checked" : ""; ?>><label for="zhixing_view">�鿴</label>
			<input type="checkbox" name="worklog[]" value="zhixing_edit" id="zhixing_edit" onClick="update_check_color(this)" <?php echo in_array("zhixing_edit", $line["worklog"]) ? "checked" : ""; ?>><label for="zhixing_edit">�޸�</label>&nbsp;&nbsp;
			���ܷ�����
			<input type="checkbox" name="worklog[]" value="zhuguan_view" id="zhuguan_view" onClick="update_check_color(this)" <?php echo in_array("zhuguan_view", $line["worklog"]) ? "checked" : ""; ?>><label for="zhuguan_view">�鿴</label>
			<input type="checkbox" name="worklog[]" value="zhuguan_edit" id="zhuguan_edit" onClick="update_check_color(this)" <?php echo in_array("zhuguan_edit", $line["worklog"]) ? "checked" : ""; ?>><label for="zhuguan_edit">�޸�</label>&nbsp;&nbsp;
			���η�����
			<input type="checkbox" name="worklog[]" value="zhuren_view" id="zhuren_view" onClick="update_check_color(this)" <?php echo in_array("zhuren_view", $line["worklog"]) ? "checked" : ""; ?>><label for="zhuren_view">�鿴</label>
			<input type="checkbox" name="worklog[]" value="zhuren_edit" id="zhuren_edit" onClick="update_check_color(this)" <?php echo in_array("zhuren_edit", $line["worklog"]) ? "checked" : ""; ?>><label for="zhuren_edit">�޸�</label>&nbsp;&nbsp;
		</td>
	</tr>

<?php } ?>


<?php if ($debug_mode || $username == "admin") { ?>
	<tr>
		<td class="left">�˻����</td>
		<td class="right">
			�����¼��<?php echo $line["thislogin"] > 0 ? date("Y-m-d H:i:s", $line["thislogin"]) : "(�޼�¼)"; ?> &nbsp;
			��¼������<?php echo intval($line["logintimes"]); ?> &nbsp;
			��ǰ�Ƿ����ߣ�<?php echo $line["online"] ? "��" : "��"; ?> &nbsp;
			�ʺŴ���ʱ�䣺<?php echo date("Y-m-d", $line["addtime"]); ?> &nbsp;
			�����ˣ�<?php echo $line["author"]; ?> &nbsp;
			��Ļ��С��<?php echo $line["window_size"]; ?> &nbsp;
			IE��<?php echo $line["ie_ver"] ? $line["ie_ver"] : "(��)"; ?> &nbsp;
		</td>
	</tr>

<?php } ?>

</table>

<object classid="clsid:e6bd6993-164f-4277-ae97-5eb4bab56443" id="ET99" name="ET99" style="left:0px; top:0px;" width="0" height="0"></object>
<script type="text/javascript">
function write_cur_ukey_sn() {
	et99 = byid("ET99");
	if (et99) {
		window.onerror = function() {
			alert("��ȡET99�豸���ִ���");
			return true;
		}
		var count = et99.FindToken("FFFFFFFF");
		if (count > 0) {
			et99.OpenToken("FFFFFFFF", 1)
			sn = et99.GetSN();
			if (sn != '') {
				byid("ukey_sn").value = sn;
				return;
			}
		}
	}
}
</script>

<div class="space"></div>

<input type="hidden" name="id" value="<?php echo $line["id"]; ?>">
<input type="hidden" name="op" value="<?php echo $op; ?>">

<div class="button_line">
	<input type="submit" class="submit" value="�ύ����">
</div>

</form>


<div id="dl_layer_div" onClick="set_detail_power(0)" style="position:absolute; filter:Alpha(opacity=70); display:none; background:#e1e1e1; z-index:99998; opacity:0.7;"></div>
<div id="dl_box_div" class="obox" style="position:absolute; display:none; z-index:99999;"></div>

<script type="text/javascript">
function load_detail_box() {
<?php if ($op == "add") { ?>
	var src = "/m/sys/character_owner_set.php";
<?php } else { ?>
	var src = "/m/sys/character_owner_set.php?uid=<?php echo $_GET["id"]; ?>&crc=<?php echo $line["addtime"]; ?>";
<?php } ?>
	set_detail_power(src, null);
}

function set_detail_power(src, obj) {
	if (src) {
		var scrollTop = document.documentElement.scrollTop || window.pageYOffset || document.body.scrollTop;
		//alert("scrollTop: "+scrollTop);

		// ���ع�����
		var isIE = 0/*@cc_on+1@*/;
		var oBody = isIE ? document.body : document.documentElement;
		if (isIE) oBody.className = "hide_scroll";

		var s = get_size();
		var width = s[0];
		var height = s[1];

		byid("dl_layer_div").style.top = byid("dl_layer_div").style.left = "0px";
		byid("dl_layer_div").style.width = width+"px";
		byid("dl_layer_div").style.height = height+"px";
		byid("dl_layer_div").style.display = "block";

		byid("dl_box_div").style.left = (width - 800 - 4) / 2;
		byid("dl_box_div").style.top = scrollTop + (s[3] - 500) / 2;
		byid("dl_box_div").style.width = "800px";
		byid("dl_box_div").style.height = "500px";
		byid("dl_box_div").style.display = "block";

		byid("dl_box_div").innerHTML = '<iframe src="'+src+'" width="800" height="500" title="�����ɫ����ر�" frameborder="0" style="border:2px solid #4e92cf"></iframe>';

	} else {
		byid("dl_layer_div").style.display = "none";
		byid("dl_box_div").style.display = "none";
		byid("dl_box_div").innerHTML = '';
		var isIE = 0/*@cc_on+1@*/;
		var oBody = isIE ? document.body : document.documentElement;
		if (isIE) oBody.className = "show_scroll";
	}
}
</script>

</body>
</html>