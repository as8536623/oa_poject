<?php
/*
// 说明: ajax 提交数据
// 作者: 幽兰 (weelia@126.com)
// 时间: 2010-11-24 16:53
*/
require "../../core/core.php";
$table = "count_web_day";

// 检查权限 @ 2012-03-21
$can_edit = array();
if ($uinfo["character_id"] == 16 || $debug_mode || (check_power("fangke") && check_power("yuyue")) ) { //管理人员
	$can_edit = explode(" ", "click_all zero_talk wangcha");
} else if ($uinfo["character_id"] == 23 || check_power("yuyue")) { //咨询组长
	$can_edit = explode(" ", "wangcha");
} else if ($uinfo["character_id"] == 28 || check_power("fangke")) { //统计
	$can_edit = explode(" ", "click_all zero_talk");
}

$hid = $_SESSION["hospital_id"];
$sub_id = $_SESSION["sub_id"];

$date = intval($_REQUEST["date"]);
$type = $_REQUEST["type"];
$type_arr = array("click_all" => "总点击", "zero_talk" => "零对话", "wangcha" => "网查");
$typename = $type_arr[$type];

// 检查权限 @ 2012-03-21
//if (!in_array($type, $can_edit)) {
//	exit_html("对不起，你没有权限修改此内容...");
//}


$chk_date = date("Y-m-d", strtotime(substr($date,0,4)."-".substr($date,4,2)."-".substr($date,6,2)));
$allow_day = $db->query("select value from count_config where name='允许修改天数' limit 1", 1, "value");
$allow_day = intval($allow_day);
if ($allow_day > 0) {
	$allow = 0;
	if ($chk_date == date("Y-m-d")) {
		$allow = 1; //当天总是允许修改
	} else {
		for ($i = 1; $i < $allow_day; $i++) {
			if ($chk_date == date("Y-m-d", strtotime("-".$i." day"))) {
				$allow = 1;
			}
		}
	}

	if (!$allow) {
		exit_html("对不起，只能修改最近".$allow_day."天的数据。");
	}
}

$old = $db->query("select * from $table where hid='$hid' and sub_id='$sub_id' and date='$date' limit 1", 1);

if ($_POST) {

	// 重复提交检测，如果通过，则立即更改token
	if ($_SESSION["data_edit_token"] != $_POST["token"]) {
		exit("请不要重复提交...");
	}
	$_SESSION["data_edit_token"] = time();

	$data = floatval($_REQUEST["data"]);

	// 判断是否已经添加

	$r = array();

	$mode = "add";
	if ($old) {
		$r[$type] = $data;
		$mode = "edit";
	} else {
		$r["hid"] = $hid;
		$r["sub_id"] = $sub_id;
		$r["date"] = $date;
		$r["repeatcheck"] = $hid."_".$sub_id."_".$date;
		$r[$type] = $data;
		$r["addtime"] = time();
		$r["uid"] = $uid;
		$r["u_name"] = $realname;
	}

	// 操作日志:
	if ($mode == "add") {
		$r["log"] = date("Y-m-d H:i")." ".$realname." 添加: ".$type.":".$r[$type]."\r\n";
	} else {
		$r["log"] = $old["log"].date("Y-m-d H:i")." ".$realname." 修改: ".$type.":".$old[$type]."=>".$r[$type]."\r\n";
	}

	$sqldata = $db->sqljoin($r);

	if ($mode == "add") {
		$rs = $db->query("insert into $table set $sqldata");
	} else {
		$rs = $db->query("update $table set $sqldata where hid='$hid' and sub_id='$sub_id' and date='$date' limit 1");
	}

	if ($rs) {
		//if ($mode == "add") {
			echo '<script> parent.update_content(); </script>';
		//}
		echo '<script> parent.msg_box("资料提交成功", 2); </script>';
		echo '<script> parent.load_src(0); </script>';
	} else {
		echo "提交失败，请稍后再试！";
	}
	exit;
}

$token = $_SESSION["data_edit_token"] = time();


?>
<html>
<head>
<title>修改数据</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script type="text/javascript">
function check_data() {
	return true;
}
</script>
</head>

<body>
<div class="space"></div>
<form name="mainform" action="" method="POST" onsubmit="return check_data()">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">请输入数据</td>
	</tr>

	<tr>
		<td class="left"><?php echo $typename; ?>：</td>
		<td class="right">
			<input name="data" value="<?php echo $old[$type]; ?>" class="input" style="width:100px">
		</td>
	</tr>
</table>

<input type="hidden" name="type" value="<?php echo $type; ?>">
<input type="hidden" name="date" value="<?php echo $date; ?>">
<input type="hidden" name="token" value="<?php echo $token; ?>">

<div class="button_line">
	<input id="submit_button" type="submit" class="submit" value="提交数据">
</div>

</form>

</body>
</html>