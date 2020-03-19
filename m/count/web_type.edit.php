<?php
// --------------------------------------------------------
// - 功能说明 : 项目新增，修改
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2010-10-13 11:40
// --------------------------------------------------------


function _kefu_replace($s) {
	// 处理客服
	$kefu_arr = explode(",", str_replace("、", ",", str_replace("，", ",", $s)));
	$new_arr = array();
	foreach ($kefu_arr as $v) {
		$v = trim($v);
		if ($v) $new_arr[] = $v;
	}
	return @implode(",", $new_arr);
}


if ($_POST) {

	$old = $db->query("select * from $table where hid=$hid limit 1", 1);
	$old_uids = explode(",", $old["uids"]);

	$op = $old["hid"] > 0 ? "edit" : "add";


	$r = array();
	if ($op == "add") {
		$r["hid"] = $hid;
		$r["h_name"] = $db->query("select name from hospital where id=".$hid." limit 1", 1, "name");
	}

	// 处理客服
	$r["kefu"] = _kefu_replace($_POST["kefu"]);
	$r["kefu_dy"] = _kefu_replace($_POST["kefu_dy"]);
	$r["kefu_sx"] = _kefu_replace($_POST["kefu_sx"]);

	$r["uids"] = $_POST["uids"];

	//$r["sort"] = intval($_POST["sort"]);

	if ($op == "add") {
		$r["addtime"] = time();
		$r["uid"] = $uid;
		$r["u_realname"] = $realname;
	}

	if ($op == "edit") {
		$to_log = array();
		foreach ($r as $k => $v) {
			if ($old[$k] != $v) {
				$to_log[] = '['.$k.'] 由“'.$old[$k].'”修改为“'.$v.'”';
			}
		}
		if (count($to_log) > 0) {
			$r["log"] = $old["log"].date("Y-m-d H:i").' '.$realname.' 将'.implode("、", $to_log)."\r\n";
		}
	}

	$sqldata = $db->sqljoin($r);
	if ($op == "add") {
		$sql = "insert into $table set $sqldata";
	} else {
		$sql = "update $table set $sqldata where hid='$hid' limit 1";
	}

	ob_start();
	$db->query($sql);
	$error = ob_get_clean();

	if (empty($error)) {
		echo '<script> parent.msg_box("资料提交成功", 2); </script>';
		echo '<script> parent.load_src(0); </script>';
	} else {
		exit_html("提交出错：".$error);
	}

	exit;
}


$line = $db->query("select * from $table where hid='$hid' limit 1", 1);
$line["uids_name"] = '';
if ($line["uids"] != '') {
	$arr = explode(",", $line["uids"]);
	$new_ids = array();
	foreach ($arr as $v) {
		$v = intval($v);
		if ($v > 0) $new_ids[] = $v;
	}
	if (count($new_ids) > 0) {
		$admin_names = $db->query("select realname from sys_admin where id in (".implode(",", $new_ids).") order by realname asc", "", "realname");
		$line["uids_name"] = implode("、", $admin_names);
	}
}

$title = $op == "edit" ? "医院项目 - 修改" : "医院项目 - 新增";
?>
<html>
<head>
<title>医院项目管理</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>

<style>
#rec_part, #rec_user {margin-top:6px; }
.rec_user_b {width:140px; float:left; }
.rec_group_part {clear:both; margin:10px 0 5px 0; font-weight:bold; }

.hide_scroll {overflow-y:hidden; overflow-x:hidden; }
.show_scroll, html {overflow-y:auto; overflow-x:hidden; }

.left {padding:10px 5px !important; }
.right {padding:10px 5px !important; }
</style>

<script language="javascript">
function Check() {
	var oForm = document.mainform;
	if (oForm.name.value == "") {
		alert("请输入“名称”！"); oForm.name.focus(); return false;
	}
	return true;
}
function update_check_color(o) {
	o.parentNode.getElementsByTagName("label")[0].style.color = o.checked ? "blue" : "";
}
</script>

</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title"><table class="ber"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $title; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="header_center"></div>
	<div class="headers_oprate"></div>
</div>
<!-- 头部 end -->

<div class="space"></div>

<form name="mainform" action="" method="POST" onsubmit="return Check()">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">项目资料</td>
	</tr>
	<tr>
		<td class="left" valign="top">白班客服：</td>
		<td class="right">
			<input name="kefu" value="<?php echo $line["kefu"]; ?>" class="input" style="width:90%"><br>
		</td>
	</tr>
	<tr>
		<td class="left" valign="top">大夜班客服：</td>
		<td class="right">
			<input name="kefu_dy" value="<?php echo $line["kefu_dy"]; ?>" class="input" style="width:90%"><br>
		</td>
	</tr>
	<tr>
		<td class="left" valign="top">实习客服：</td>
		<td class="right">
			<input name="kefu_sx" value="<?php echo $line["kefu_sx"]; ?>" class="input" style="width:90%"><br>
			<b>填写说明：</b><br>
			　　1. 请注意名字不要填错，要和系统已登记的人员真实姓名一致。<br>
			　　2. 如果填写的名字在系统里查不到，数据无法进行关联查询（比如到院人数等）<br>
			　　3. 各名字用逗号（大小写逗号均可）隔开。
		</td>
	</tr>
	<tr>
		<td class="left">管理员：</td>
		<td class="right">
			<span id="admin_show" style="height:12px; line-height:12px; padding:4px 30px 2px 3px; border:1px solid #79acc1; background:#f2f0e3;"><?php echo $line["uids_name"]; ?></span>&nbsp;
			<button class="button" onclick="load_admin_set_box();return false;">选择</button>&nbsp;
			<span class="intro">请点击"选择"按钮进行设置</span>
			<input type="hidden" name="uids" id="admin_uids" value="<?php echo $line["uids"]; ?>" class="input" style="width:500px">&nbsp;
		</td>
	</tr>

</table>

<input type="hidden" id="suoshu_hid" value="<?php echo $_GET["hid"]; ?>">
<input type="hidden" name="hid" value="<?php echo $_GET["hid"]; ?>">
<input type="hidden" name="op" value="<?php echo $op; ?>">

<br>

<div class="button_line">
	<input type="submit" class="submit" value="提交资料">
</div>

</form>

<div id="dl_layer_div" onclick="wee_show_box(0)" style="position:absolute; filter:Alpha(opacity=70); display:none; background:#e1e1e1; z-index:99998; opacity:0.7;"></div>
<div id="dl_box_div" class="obox" style="position:absolute; display:none; z-index:99999;"></div>

<script type="text/javascript">
function load_admin_set_box() {
	var base_url = "/m/count/set_admin.php";
	var hid = byid("suoshu_hid").value;
	if (hid == '0' || hid == '') {
		alert("请先设置所属医院，才能进行管理员设置。");
		byid("suoshu_hid").focus();
		return false;
	}
	var cur_uids = byid("admin_uids").value;
	var src = base_url + "?hid="+hid+"&uids="+cur_uids;
	wee_show_box(src, null);
}

function update_uids(str1, str2) {
	byid("admin_uids").value = str1;
	byid("admin_show").innerHTML = str2;
}

function wee_show_box(src, obj) {
	if (src) {
		var scrollTop = document.documentElement.scrollTop || window.pageYOffset || document.body.scrollTop;

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

		byid("dl_box_div").style.left = (width - 600 - 4) / 2;
		byid("dl_box_div").style.top = scrollTop + (s[3] - 350) / 2;
		byid("dl_box_div").style.width = "600px";
		byid("dl_box_div").style.height = "350px";
		byid("dl_box_div").style.display = "block";

		byid("dl_box_div").innerHTML = '<iframe src="'+src+'" width="600" height="350" title="点击灰色区域关闭" frameborder="0" style="border:2px solid #4e92cf"></iframe>';

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