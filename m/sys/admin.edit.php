<?php
defined("ROOT") or exit("Error.");

// 菜单的处理
$_SESSION["global_user_menu"] = '';
if ($op == "edit" && $line["menu"] != '') {
	$_SESSION["global_user_menu"] = $line["menu"];
}

?>
<html>
<head>
<title>人员管理</title>
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
.HLI b {font-family:"微软雅黑" !important; color:; }

.hide_scroll {overflow-y:hidden; overflow-x:hidden; }
.show_scroll, html {overflow-y:auto; overflow-x:hidden; }
</style>
<script language="javascript">
var op = "<?php echo $op; ?>";

function show_pass() {
	var pass = document.mainform.pass.value;
	if (pass != "") {
		alert("您输入的密码是： " + pass + "　　　　");
	} else {
		alert("您还没有输入密码！　　　　　　");
	}
}

function check_data() {
	oForm = document.mainform;
	if (oForm.name.value.length < 2) {
		msg_box("必须输入用户名，且长度至少2位");
		oForm.name.focus();
		return false;
	}
	if (oForm.realname.value == "") {
		msg_box("请输入真实姓名！");
		oForm.realname.focus();
		return false;
	}

	if (byid("powermode").value == "") {
		msg_box("请选择授权方式！");
		oForm.powermode.focus();
		return false;
	}
	if (byid("powermode").value == "2" && byid("character_id").value == "0") {
		msg_box("请选择权限！");
		oForm.character_id.focus();
		return false;
	}
	if (!confirm("每一项都填好了吧？请检查清楚哦，如果还想再看一下，请点击“取消”")) {
		return false;
	}
	return true;
}

function show_hide_detail(o) {
	if (o.value == "-1") {
		byid("power_detail_box").style.display = "inline";
		byid("powermode").value = "1"; //自定义
	} else {
		byid("power_detail_box").style.display = "none";
		byid("powermode").value = "2"; //角色
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
			byid(out["type"]+"_tips").innerHTML = "√ ";
		}
	}
}

function update_check_color(o) {
	if (o.nextSibling.tagName.toLowerCase() == "label") {
		o.nextSibling.style.color = o.checked ? "blue" : "";
	}
}

// 按组选定医院/科室
function h_gp_set(area, o) {
	var chk = false;
	if (o.title == null || o.title == '' || o.title == "全部选中") {
		o.title = "全部不选";
		chk = true;
	} else {
		o.title = "全部选中";
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
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $title; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"></div>
</div>
<!-- 头部 end -->

<div class="space"></div>

<div class="description">
	<div class="d_title">修改提示：</div>
	<li class="d_item">本页数据<font color="red"><b>每项均需要认真填写</b></font>，如果填写不正确，可能导致非常严重的后果，<font color="red"><b>比如数据丢失，账号无法登录等</b></font>。若对填写有疑问，请咨询开发人员。</li>
</div>

<div class="space"></div>

<form name="mainform" method="POST" onSubmit="return check_data()">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">基本数据</td>
	</tr>

	<tr>
		<td class="left"><font color="red">登录名：</font></td>
		<td class="right"><input name="name" value="<?php echo $line["name"]; ?>" class="input" size="20" style="width:120px" onBlur="check_repeat(this,'name')" <?php if ($id > 0) echo "disabled"; ?>> <span id="name_tips"></span><span class="intro">创建后不能更改</span></td>
	</tr>

	<tr>
		<td class="left"><font color='red'>真实姓名：</font></td>
		<td class="right"><input name="realname" value="<?php echo $line["realname"]; ?>" class="input" size="20" style="width:120px" onBlur="check_repeat(this,'realname')" <?php if ($id > 0) echo "disabled"; ?>> <span id="realname_tips"></span><span class="intro">真实姓名仅用于显示</span></td>
	</tr>

	<tr>
		<td class="left"><font color="red">密码：</font></td>
		<td class="right"><input type="password" name="pass" value="" class="input" size="20" style="width:120px"> <a href="javascript:void(0);" onClick="show_pass();return false;">[显示我输入的密码]</a> <?php if ($id) echo '<span class="intro">输入新的密码将覆盖原密码</span>'; ?></td>
	</tr>

	<tr>
		<td colspan="2" class="head">授权</td>
	</tr>

	<tr>
		<td class="left" valign="top" style="padding-top:8px;">
			<font color="red">医院授权：</font><br>
			<br>
			医院展现方法<br>
			<input type="radio" name="_h_list_mode_" value="area" id="_h_001" onClick="set_h_list(this.value)" /><label for="_h_001">地区&nbsp;</label><br>
			<input type="radio" name="_h_list_mode_" value="depart" id="_h_002" onClick="set_h_list(this.value)" /><label for="_h_002">科室&nbsp;</label><br>
		</td>
		<td class="right" id="_h_data_area">

			<!-- 按地区列出 begin -->
			<table id="_h_list_area" width="100%" class="HLI" cellpadding="0" cellspacing="0" style="display:none;">
<?php
$hs_ids = implode(",", $hospital_ids);
$area_arr = $db->query("select area,count(area) as c from hospital where id in ($hs_ids) group by area order by area asc", "area", "c");
foreach ($area_arr as $a => $c) {
?>
				<tr>
					<td class="HL"><nobr><b><?php echo $a ? $a : "其它"; ?></b> (<?php echo $c; ?>) <a href="#全选" onClick="h_gp_set('<?php echo $a; ?>', this);return false;">全选</a></nobr></td>
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
			<!-- 按地区列出 end -->

			<!-- 按科室列出 begin -->
			<table id="_h_list_depart" width="100%" class="HLI" cellpadding="0" cellspacing="0" style="display:none;">
<?php
$hs_ids = implode(",", $hospital_ids);
$dep_arr = $db->query("select depart,count(depart) as c from hospital where id in ($hs_ids) group by depart order by depart asc", "depart", "c");
foreach ($dep_arr as $a => $c) {
?>
				<tr>
					<td class="HL"><nobr><b><?php echo $a ? $a : "其它"; ?></b> (<?php echo $c; ?>) <a href="#全选" onClick="h_gp_set('<?php echo $a; ?>', this);return false;">全选</a></nobr></td>
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
			<!-- 按科室列出 end -->

			<script type="text/javascript">
			set_h_list('area'); // area|depart 这个值既是默认显示的方式，也是页面加载不可缺的
			</script>

		</td>
	</tr>

	<tr>
		<td class="left"><font color="red">挂号核心设置：</font></td>
		<td class="right">
<?php
$line_config = @explode(",", $line["guahao_config"]);
foreach ($guahao_config_arr as $k => $v) {
	$checked = in_array($k, $line_config) ? "checked" : "";
?>
			<span><input type="checkbox" name="guahao_config[]" value="<?php echo $k; ?>" <?php echo $checked; ?> id="chk_<?php echo $k; ?>" onClick="update_check_color(this)"><label for="chk_<?php echo $k; ?>"<?php if ($checked) echo ' style="color:red"'; ?>><?php echo $v; ?></label></span>
<?php } ?>
			<!-- <font color="gray">&nbsp;(此功能暂不生效)</font> -->
		</td>
	</tr>

<?php
	$select_ch = $line["powermode"] == 1 ? 0 : $line["character_id"];
?>
	<tr>
		<td class="left"><font color="red">选择权限：</font></td>
		<td class="right">
			<input type="hidden" name="powermode" id="powermode" value="<?php echo $line["powermode"]; ?>">
			<select name="character_id" onChange="show_hide_detail(this)" class="combo">
				<option value="0" style="color:gray">--请选择--</option>
				<option value="-1" style="color:red"<?php if ($line["powermode"]==1) echo " selected"; ?>>-自定义-</option>
				<?php echo list_option($ch_data, "id", "name", $select_ch); ?>
			</select> &nbsp;
			<span id="power_detail_box" style="display:<?php echo $line["powermode"] != 1 ? "none" : ""; ?>">
				<button class="buttonb" onClick="load_detail_box();return false;">自定义</button>
				<b>请<font color="red">务必点“自定义”按钮设置好权限</font>，否则无法提交</b>
			</span>
		</td>
	</tr>

	<tr>
		<td class="left"><font color="red">所属部门：</font></td>
		<td class="right" valign="top">
			<select name="part_id" class="combo">
			<?php //echo list_option($part->get_sub_part_list(intval($uinfo["part_id"]), 1), "_key_", "_value_", $line["part_id"]); ?>
			<?php echo list_option(get_part_list('array'), "id", "name", $line["part_id"]); ?>
			</select>
<?php if ($debug_mode || $username == "admin" || $uinfo["part_admin"]) { ?>
			<input type="checkbox" class="check" name="part_admin" value="1" id="part_admin" <?php if ($line["part_admin"]) echo "checked"; ?>><label for="part_admin">部门管理员</label>
<?php } ?>
			<span class="intro">所属部门必须选择。部门管理员相当于“组长”权限</span>
		</td>
	</tr>

	<tr>
		<td class="left"><font color="red">数据管理：</font></td>
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
			<span class="intro">勾选则有对应权限，同时首页会显示对应的统计数据模块</span>
		</td>
	</tr>

<?php if ($debug_mode || $username == "admin" || $uinfo["character_id"] == 73) { ?>
	<tr>
		<td class="left"><font color="red">就诊表：</font></td>
		<td class="right">
			<input type="checkbox" name="jiuzhen_view" value="1" <?php if ($line["jiuzhen_view"]) echo "checked"; ?> id="chk_jzb_view"><label for="chk_jzb_view">查看</label>&nbsp;&nbsp;
			<input type="checkbox" name="jiuzhen_edit" value="1" <?php if ($line["jiuzhen_edit"]) echo "checked"; ?> id="chk_jzb_edit"><label for="chk_jzb_edit">修改</label>&nbsp;&nbsp;
		</td>
	</tr>
	<tr>
		<td class="left"><font color="red">汇总列表：</font></td>
		<td class="right"><input type="checkbox" name="show_list" value="1" <?php if ($line["show_list"]) echo "checked"; ?> id="chk0003"><label for="chk0003">显示首页汇总列表</label> </td>
	</tr>
	<tr>
		<td class="left"><font color="red">显示号码：</font></td>
		<td class="right"><input type="checkbox" name="show_tel" value="1" <?php if ($line["show_tel"]) echo "checked"; ?> id="chk0001"><label for="chk0001">显示其他病人的电话号码</label> <span class="intro">(如果是电话回访客服不勾这个也能显示号码)</span></td>
	</tr>
	<tr>
		<td class="left"><font color="red">uKey登录：</font></td>
		<td class="right">
			<input type="checkbox" name="use_ukey" onClick="show_hide_ukey_box(this.checked)" <?php echo ($line["use_ukey"] == 1) ? "checked" : ""; ?> value="1" id="use_ukey_001"><label for="use_ukey_001">使用uKey登录</label>&nbsp; &nbsp;
			<span id="use_ukey_box" style="display:<?php echo ($line["use_ukey"] == 1) ? "" : "none"; ?>">硬件序号：<input name="ukey_sn" id="ukey_sn" value="<?php echo $line["ukey_sn"]; ?>" class="input" style="width:120px"> <a href="javascript:write_cur_ukey_sn()">填写当前插入的uKey序列号</a>&nbsp;&nbsp;&nbsp;&nbsp;
			备注：<input name="ukey_no" value="<?php echo $line["ukey_no"]; ?>" class="input" style="width:80px">
			</span>
		</td>
	</tr>

	<script type="text/javascript">
	function show_hide_ukey_box(value) {
		byid("use_ukey_box").style.display = (value) ? "" : "none";
	}
	</script>

	<tr>
		<td class="left">登录手机版：</td>
		<td class="right">
			<input type="checkbox" name="allow_mobile_login" id="allow_mobile_login" <?php echo $line["allow_mobile_login"] ? "checked" : ""; ?>><label for="allow_mobile_login">勾选则允许登录手机版 (默认情况为不允许)</label>
		</td>
	</tr>
<?php } ?>


<?php if ($debug_mode || $username == "admin" || $realname == "黄开章" || $uinfo["character_id"] == 61) { ?>
	<tr>
		<td class="left">显示聊天记录：</td>
		<td class="right">
			<input type="checkbox" name="show_talk" id="show_talk" <?php echo $line["show_talk"] ? "checked" : ""; ?>><label for="show_talk">勾选显示聊天记录</label>
		</td>
	</tr>

<?php
$line["worklog"] = explode(",", $line["worklog"]);
?>
	<tr>
		<td class="left">工作日志：</td>
		<td class="right">
			咨询反馈：
			<input type="checkbox" name="worklog[]" value="zixun_view" id="zixun_view" onClick="update_check_color(this)" <?php echo in_array("zixun_view", $line["worklog"]) ? "checked" : ""; ?>><label for="zixun_view">查看</label>
			<input type="checkbox" name="worklog[]" value="zixun_edit" id="zixun_edit" onClick="update_check_color(this)" <?php echo in_array("zixun_edit", $line["worklog"]) ? "checked" : ""; ?>><label for="zixun_edit">修改</label>&nbsp;&nbsp;
			执行分析：
			<input type="checkbox" name="worklog[]" value="zhixing_view" id="zhixing_view" onClick="update_check_color(this)" <?php echo in_array("zhixing_view", $line["worklog"]) ? "checked" : ""; ?>><label for="zhixing_view">查看</label>
			<input type="checkbox" name="worklog[]" value="zhixing_edit" id="zhixing_edit" onClick="update_check_color(this)" <?php echo in_array("zhixing_edit", $line["worklog"]) ? "checked" : ""; ?>><label for="zhixing_edit">修改</label>&nbsp;&nbsp;
			主管分析：
			<input type="checkbox" name="worklog[]" value="zhuguan_view" id="zhuguan_view" onClick="update_check_color(this)" <?php echo in_array("zhuguan_view", $line["worklog"]) ? "checked" : ""; ?>><label for="zhuguan_view">查看</label>
			<input type="checkbox" name="worklog[]" value="zhuguan_edit" id="zhuguan_edit" onClick="update_check_color(this)" <?php echo in_array("zhuguan_edit", $line["worklog"]) ? "checked" : ""; ?>><label for="zhuguan_edit">修改</label>&nbsp;&nbsp;
			主任分析：
			<input type="checkbox" name="worklog[]" value="zhuren_view" id="zhuren_view" onClick="update_check_color(this)" <?php echo in_array("zhuren_view", $line["worklog"]) ? "checked" : ""; ?>><label for="zhuren_view">查看</label>
			<input type="checkbox" name="worklog[]" value="zhuren_edit" id="zhuren_edit" onClick="update_check_color(this)" <?php echo in_array("zhuren_edit", $line["worklog"]) ? "checked" : ""; ?>><label for="zhuren_edit">修改</label>&nbsp;&nbsp;
		</td>
	</tr>

<?php } ?>


<?php if ($debug_mode || $username == "admin") { ?>
	<tr>
		<td class="left">账户活动：</td>
		<td class="right">
			最近登录：<?php echo $line["thislogin"] > 0 ? date("Y-m-d H:i:s", $line["thislogin"]) : "(无记录)"; ?> &nbsp;
			登录次数：<?php echo intval($line["logintimes"]); ?> &nbsp;
			当前是否在线：<?php echo $line["online"] ? "是" : "否"; ?> &nbsp;
			帐号创建时间：<?php echo date("Y-m-d", $line["addtime"]); ?> &nbsp;
			创建人：<?php echo $line["author"]; ?> &nbsp;
			屏幕大小：<?php echo $line["window_size"]; ?> &nbsp;
			IE：<?php echo $line["ie_ver"] ? $line["ie_ver"] : "(无)"; ?> &nbsp;
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
			alert("读取ET99设备出现错误。");
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
	<input type="submit" class="submit" value="提交资料">
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

		// 隐藏滚动条
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

		byid("dl_box_div").innerHTML = '<iframe src="'+src+'" width="800" height="500" title="点击灰色区域关闭" frameborder="0" style="border:2px solid #4e92cf"></iframe>';

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