<?php
// --------------------------------------------------------
// - 功能说明 : 添加、修改资料
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2010-10-08 13:31
// --------------------------------------------------------
$date = $_REQUEST["date"];

// 权限 @ 2012-03-21
if ($uinfo["character_id"] == 16 || $debug_mode || $uinfo["character_id"] == 28 || check_power("fangke")) { //管理人员
	//
} else {
	exit_html("对不起，你没有修改权限...");
}

if (!$date) {
	exit("参数错误");
}

$date = date("Y-m-d", strtotime($date));

$allow_day = $db->query("select value from count_config where name='允许修改天数' limit 1", 1, "value");
$allow_day = intval($allow_day);
if ($allow_day > 0) {
	$allow = 0;
	if ($date == date("Y-m-d")) {
		$allow = 1; //当天总是允许修改
	} else {
		for ($i = 1; $i < $allow_day; $i++) {
			if ($date == date("Y-m-d", strtotime("-".$i." day"))) {
				$allow = 1;
			}
		}
	}

	if (!$allow) {
		exit_html("对不起，只能修改最近".$allow_day."天的数据。");
	}
}

$kefu = $_GET["kefu"];
if (!$kefu) {
	exit("参数错误");
}

if ($_POST) {

	// 重复提交检测，如果通过，则立即更改token
	if ($_SESSION["web_multi_token"] != $_POST["token"]) {
		exit("请不要重复提交...");
	}
	$_SESSION["web_multi_token"] = time();

	ob_start();
	$zong = $_POST["zong"];
	$shouji = $_POST["shouji"];
	$web = array();

	foreach ($zong as $k => $v) {
		$web[$k] = max(0, $v - $shouji[$k]);
	}

	$s_date = date("Ymd", strtotime($date." 0:0:0"));


	// 查询是否已有添加的记录：
	$line1 = $db->query("select * from $table where hid=$hid and sub_id=1 and kefu='$kefu' and date='$s_date' limit 1", 1);
	$line2 = $db->query("select * from $table where hid=$hid and sub_id=2 and kefu='$kefu' and date='$s_date' limit 1", 1);

	$r = array();
	if ($line1["id"] > 0) {
		//
	} else {
		$r["hid"] = $hid;
		$r["sub_id"] = 1;
		$r["date"] = $s_date;
		$r["kefu"] = $kefu;
		$r["repeatcheck"] = $hid."_1_".$s_date."_".$kefu;
		$r["addtime"] = time();
		$r["uid"] = $uid;
		$r["u_realname"] = $realname;
	}

	$r["click"] = $web["click"];
	$r["click_local"] = $web["click_local"];
	$r["click_other"] = $web["click_other"];

	$r["ok_click"] = $web["ok_click"];
	$r["ok_click_local"] = $web["ok_click_local"];
	$r["ok_click_other"] = $web["ok_click_other"];

	$sqldata = $db->sqljoin($r);
	if ($line1["id"] > 0) {
		$id = $line1["id"];
		$sql = "update $table set $sqldata where id='$id' limit 1";
	} else {
		$sql = "insert into $table set $sqldata";
	}

	$db->query($sql);


	// 更新手机数据：
	$r = array();
	if ($line2["id"] > 0) {
		//
	} else {
		$r["hid"] = $hid;
		$r["sub_id"] = 2;
		$r["date"] = $s_date;
		$r["kefu"] = $kefu;
		$r["repeatcheck"] = $hid."_2_".$s_date."_".$kefu;
		$r["addtime"] = time();
		$r["uid"] = $uid;
		$r["u_realname"] = $realname;
	}

	$r["click"] = $shouji["click"];
	$r["click_local"] = $shouji["click_local"];
	$r["click_other"] = $shouji["click_other"];

	$r["ok_click"] = $shouji["ok_click"];
	$r["ok_click_local"] = $shouji["ok_click_local"];
	$r["ok_click_other"] = $shouji["ok_click_other"];


	$sqldata = $db->sqljoin($r);
	if ($line2["id"] > 0) {
		$id = $line2["id"];
		$sql = "update $table set $sqldata where id='$id' limit 1";
	} else {
		$sql = "insert into $table set $sqldata";
	}

	$db->query($sql);



	$error = ob_get_clean();

	if (!$error) {
		echo '<script> parent.msg_box("提交成功", 2); parent.load_src(0); </script>';
	} else {
		echo "提交失败，请稍后再试！";
	}
	exit;
}


$token = $_SESSION["web_multi_token"] = time();


?>
<html>
<head>
<title>批量添加资料</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style>
.item {padding:8px 3px 6px 3px; }
</style>

<script language="javascript">
function check_data() {
	var oForm = document.mainform;
	if (oForm.code.value == "") {
		alert("请输入“编号”！"); oForm.code.focus(); return false;
	}

	byid("submit_button").disabled = true;
	byid("submit_button").title = "请勿重复提交";
	setTimeout("clear_button_lock()", 10000);

	return true;
}

function clear_button_lock() {
	byid("submit_button").disabled = false;
	byid("submit_button").title = "";
	alert("提交异常，可能网络问题或数据库反应缓慢，可尝试重新提交。");
}

function update_data() {
	for (var i = 1; i <= 9; i++) {
		byid("dt"+i).value = byid("d"+i).innerHTML;
	}
}

function update_cnt(o, id_a, id_b, id_c) {
	var a = byid(id_a).value;
	var b = byid(id_b).value;
	var c = byid(id_c).value;

	var cnt = (a != "" ? 1 : 0) + (b != "" ? 1 : 0) + (c != "" ? 1 : 0);

	if (cnt == 2 && (a == "" || b == "" || c == "")) {
		if (a == "") {
			byid(id_a).value = parseInt(b) + parseInt(c);
		} else if (b == "") {
			byid(id_b).value = a - c;
		} else {
			byid(id_c).value = a - b;
		}
	}
	if (cnt == 3) {
		if (o.id == id_a) {
			byid(id_c).value = a - b;
		} else if (o.id == id_b) {
			byid(id_c).value = a - b;
		} else {
			byid(id_b).value = a - c;
		}
	}
}
</script>
</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center">批量添加资料</td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"></div>
</div>
<!-- 头部 end -->

<div class="space"></div>

<form name="mainform" action="" method="POST" onsubmit="return check_data()">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">1. 总数据</td>
	</tr>

	<tr>
		<td class="left">总点击：</td>
		<td class="right">
			<input name="zong[click]" id="c_a" onchange="update_cnt(this,'c_a', 'c_b', 'c_c')" value="" class="input" style="width:100px">
			　=　本地：<input name="zong[click_local]" id="c_b" onchange="update_cnt(this,'c_a', 'c_b', 'c_c')" value="" class="input"style="width:100px">
			　+　外地：<input name="zong[click_other]" id="c_c" onchange="update_cnt(this,'c_a', 'c_b', 'c_c')" value="" class="input" style="width:100px">
		</td>
	</tr>

	<tr>
		<td class="left">总有效：</td>
		<td class="right">
			<input name="zong[ok_click]" id="d_a" onchange="update_cnt(this,'d_a', 'd_b', 'd_c')" value="" class="input" style="width:100px">
			　=　本地：<input name="zong[ok_click_local]" id="d_b" onchange="update_cnt(this,'d_a', 'd_b', 'd_c')" value="" class="input"style="width:100px">
			　+　外地：<input name="zong[ok_click_other]" id="d_c" onchange="update_cnt(this,'d_a', 'd_b', 'd_c')" value="" class="input" style="width:100px">
		</td>
	</tr>

	<tr>
		<td colspan="2" class="head">2. 手机数据</td>
	</tr>

	<tr>
		<td class="left">总点击：</td>
		<td class="right">
			<input name="shouji[click]" id="x_a" onchange="update_cnt(this,'x_a', 'x_b', 'x_c')" value="" class="input" style="width:100px">
			　=　本地：<input name="shouji[click_local]" id="x_b" onchange="update_cnt(this,'x_a', 'x_b', 'x_c')" value="" class="input"style="width:100px">
			　+　外地：<input name="shouji[click_other]" id="x_c" onchange="update_cnt(this,'x_a', 'x_b', 'x_c')" value="" class="input" style="width:100px">
		</td>
	</tr>

	<tr>
		<td class="left">总有效：</td>
		<td class="right">
			<input name="shouji[ok_click]" id="y_a" onchange="update_cnt(this,'y_a', 'y_b', 'y_c')" value="" class="input" style="width:100px">
			　=　本地：<input name="shouji[ok_click_local]" id="y_b" onchange="update_cnt(this,'y_a', 'y_b', 'y_c')" value="" class="input"style="width:100px">
			　+　外地：<input name="shouji[ok_click_other]" id="y_c" onchange="update_cnt(this,'y_a', 'y_b', 'y_c')" value="" class="input" style="width:100px">
		</td>
	</tr>

	<tr>
		<td colspan="2" class="head">注意：1和2相减，得出的是PC统计数据</td>
	</tr>
</table>
<input type="hidden" name="op" value="edit_multi">
<input type="hidden" name="date" value="<?php echo date("Y-m-d", strtotime($date." 0:0:0")); ?>">
<input type="hidden" name="kefu" value="<?php echo $kefu; ?>">
<input type="hidden" name="token" value="<?php echo $token; ?>">

<div class="button_line">
	<input id="submit_button" type="submit" class="submit" value="提交数据">
</div>

</form>

</body>
</html>