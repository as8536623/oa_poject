<?php
// --------------------------------------------------------
// - 功能说明 : 添加、修改资料
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2010-10-08 13:31
// --------------------------------------------------------
$date = $_REQUEST["date"];

// 权限 @ 2012-03-21
$can_edit = array();
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


$s_date = date("Ymd", strtotime($date." 0:0:0"));
$line = $db->query("select * from $table where hid=$hid and sub_id=$sub_id and kefu='$kefu' and date='$s_date' limit 1", 1);
$data_count = 0;
if ($line["id"] > 0) {
	$data_count = $line["click"] + $line["click_local"] + $line["click_other"] + $line["ok_click"] + $line["ok_click_local"] + $line["ok_click_other"];
}
if ($sub_id == 1 && $data_count == 0) {
	header("location: ?op=edit_multi&kefu=".urlencode($kefu)."&date={$date}");
	exit;
}

if (!$line["id"] > 0) {
	$line = array();
}


if ($_POST) {

	// 重复提交检测，如果通过，则立即更改token
	if ($_SESSION["web_fangke_token"] != $_POST["token"]) {
		exit("请不要重复提交...");
	}
	$_SESSION["web_fangke_token"] = time();

	$r = array();

	// 判断是否已经添加:
	$mode = "add";
	$s_date = date("Ymd", strtotime($date." 0:0:0"));
	$kefu = $_POST["kefu"];
	$cur_data = $db->query("select * from $table where hid=$hid and sub_id=$sub_id and kefu='$kefu' and date='$s_date' limit 1", 1);
	$cur_id = $cur_data["id"];
	if ($cur_id > 0) {
		$mode = "edit";
		$id = $cur_id;
	}

	if ($mode == "add") {
		$r["hid"] = $hid;
		$r["sub_id"] = $sub_id;
		$r["date"] = $s_date;
		$r["kefu"] = $_POST["kefu"];
		$r["repeatcheck"] = $hid."_".$sub_id."_".$s_date."_".$_POST["kefu"];
	}


	$r["click"] = $_POST["click"];
	$r["click_local"] = $_POST["click_local"];
	$r["click_other"] = $_POST["click_other"];

	$r["ok_click"] = $_POST["ok_click"];
	$r["ok_click_local"] = $_POST["ok_click_local"];
	$r["ok_click_other"] = $_POST["ok_click_other"];


	if ($mode == "add") {
		$r["addtime"] = time();
		$r["uid"] = $uid;
		$r["u_realname"] = $realname;
	}

	// 操作日志:
	if ($mode == "add") {
		$r["log"] = date("Y-m-d H:i")." ".$realname." 添加记录\r\n";
	} else {
		// 记录具体修改了哪些:
		$log_it = array();
		foreach ($r as $x => $y) {
			if ($cur_data[$x] != $y) {
				$log_it[] = $x.":".$cur_data[$x]."=>".$y;
			}
		}
		if (count($log_it) > 0) {
			$r["log"] = $cur_data["log"].date("Y-m-d H:i")." ".$realname." 修改: ".implode(", ", $log_it)." \r\n";
		}
	}


	$sqldata = $db->sqljoin($r);
	if ($mode == "add") {
		$sql = "insert into $table set $sqldata";

		// 提交之前检查重复:
		$_a = $r["hid"];
		$_b = $r["sub_id"];
		$_c = $r["date"];
		$_d = $r["kefu"];
		if ($db->query("select count(*) as c from $table where hid=$_a and sub_id=$b and date=$_c and kefu='$_d'", 1, "c") > 0) {
			exit("目标资料已存在于数据库中，请勿重复提交。");
		}

	} else {
		// 2013-12-27 检查是否有重复的值 并删除
		if ($hid > 0 && $sub_id > 0 && $kefu != '' && $s_date != '' && $id > 0) {
			$_arr = $db->query("select * from $table where hid=$hid and sub_id=$sub_id and kefu='$kefu' and date='$s_date' and id!='$id'");
			if (count($_arr) > 0) {
				$_s = serialize($_arr);
				// 写入日志:
				@file_put_contents(ROOT."data/auto_delete_repeat.log", date("Y-m-d H:i:s")." ".$username.": ".$_s."\r\n", FILE_APPEND);
				foreach ($_arr as $_li) {
					$_del_id = $_li["id"];
					if ($_del_id > 0) {
						$db->query("delete from $table where id='$_del_id' limit 1");
					}
				}
			}
		}

		$sql = "update $table set $sqldata where id='$id' limit 1";
	}

	if ($db->query($sql)) {
		echo '<script> parent.msg_box("修改成功。列表未更新", 2); </script>';
		echo '<script> parent.load_src(0); </script>';
	} else {
		echo "提交失败，请稍后再试！";
	}
	exit;
}

$token = $_SESSION["web_fangke_token"] = time();

$title = "修改资料";
?>
<html>
<head>
<title><?php echo $title; ?></title>
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
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $title; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"></div>
</div>
<!-- 头部 end -->

<div class="space"></div>

<form name="mainform" action="" method="POST" onsubmit="return check_data()">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">网络详细资料</td>
	</tr>

	<tr>
		<td class="left">总点击：</td>
		<td class="right">
			<input name="click" id="c_a" onchange="update_cnt(this,'c_a', 'c_b', 'c_c')" value="<?php echo $line["click"]; ?>" class="input" style="width:100px">
			　=　本地：<input name="click_local" id="c_b" onchange="update_cnt(this,'c_a', 'c_b', 'c_c')" value="<?php echo $line["click_local"]; ?>" class="input"style="width:100px">
			　+　外地：<input name="click_other" id="c_c" onchange="update_cnt(this,'c_a', 'c_b', 'c_c')" value="<?php echo $line["click_other"]; ?>" class="input" style="width:100px">
		</td>
	</tr>

	<tr>
		<td class="left">总有效：</td>
		<td class="right">
			<input name="ok_click" id="d_a" onchange="update_cnt(this,'d_a', 'd_b', 'd_c')" value="<?php echo $line["ok_click"]; ?>" class="input" style="width:100px">
			　=　本地：<input name="ok_click_local" id="d_b" onchange="update_cnt(this,'d_a', 'd_b', 'd_c')" value="<?php echo $line["ok_click_local"]; ?>" class="input"style="width:100px">
			　+　外地：<input name="ok_click_other" id="d_c" onchange="update_cnt(this,'d_a', 'd_b', 'd_c')" value="<?php echo $line["ok_click_other"]; ?>" class="input" style="width:100px">
		</td>
	</tr>

</table>
<input type="hidden" name="op" value="edit_fangke">
<input type="hidden" name="date" value="<?php echo date("Y-m-d", strtotime($date." 0:0:0")); ?>">
<input type="hidden" name="kefu" value="<?php echo $kefu; ?>">
<input type="hidden" name="token" value="<?php echo $token; ?>">

<div class="button_line">
	<input id="submit_button" type="submit" class="submit" value="提交数据">
</div>

</form>

</body>
</html>