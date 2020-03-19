<?php
/*
// 说明: 修改报表数据
// 作者: 幽兰 (weelia@126.com)
// 时间: 2013-08-30
*/
require "../../core/core.php";
include "config.php";
$table = "jiuzhen_report";


$_hid = intval($_REQUEST["hid"]);
$_month = intval(str_replace("-", "", $_REQUEST["month"]));
$_fname = $_REQUEST["fname"];

if ($_hid > 0) {
	$h_name = $db->query("select name from hospital where id={$_hid} limit 1", 1, "name");
}


if ($_hid > 0 && $_month > 0) {
	$res = $db->query("select * from jiuzhen_report where hid={$_hid} and month={$_month} and sub_id=$sub_id limit 1", 1);
	$line = @unserialize($res["config"]);
} else {
	exit("参数不完整...");
}

$fname_arr = array(
	"fuzeren" => "负责人",
	"h_jiuzhen" => "就诊数",
	"h_wangcha" => "网查",
	"h_renjun" => "人均成本",
	"dabiaozhishu1" => "达标指数",
	"dabiaozhishu2" => "达标指数2",
	"dabiaozhishu3" => "达标指数3",
	"jianglijishu1" => "奖励基数",
	"jianglijishu2" => "奖励基数2",
	"jianglijishu3" => "奖励基数3",
	"jianglizhibiao1" => "奖励指标",
	"jianglizhibiao2" => "奖励指标2",
	"jianglizhibiao3" => "奖励指标3",
	"mubiao1" => "目标",
	"mubiao2" => "目标2",
	"mubiao3" => "目标3",
);

if (!array_key_exists($_fname, $fname_arr)) {
	exit("不支持编辑的字段: $_fname ");
}

$title = $h_name." - ".$_REQUEST["month"]." - ".$fname_arr[$_fname]." - ".$sub_name;


if ($_POST) {
	ob_start();

	$line[$_fname] = $value = _safe_word($_POST["value"]);
	$str = serialize($line);

	if ($res["hid"] > 0) {
		$db->query("update $table set config='{$str}' where hid='{$_hid}' and month='{$_month}' and sub_id='{$sub_id}' limit 1");
	} else {
		$db->query("insert into $table set hid='{$_hid}', month='{$_month}', sub_id='{$sub_id}', config='{$str}'");
	}

	$err = ob_get_clean();
	if ($err == '') {
		$id = $_hid."_".str_replace("-", "", $_month)."_".$_fname;
		if ($value == '') {
			$value = "添加";
		}
		echo '<script> parent.update_content_byid("'.$id.'", "'.$value.'", "innerHTML"); </script>';
		echo '<script> parent.load_src(0); </script>';
		echo '<script> parent.msg_box("资料提交成功", 2); </script>';
	} else {
		echo "提交失败，请联系程序员检查：<br>".$db->sql."<br><br><br>";
	}
	exit;
}




?>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script type="text/javascript">
function check_data() {
	return true;
}

function write_s_data(s) {
	byid(s).value = byid("s_"+s).innerHTML;
}
</script>
</head>

<body>
<form name="mainform" action="" method="POST" onsubmit="return check_data()">
<table width="100%" class="edit">
	<tr>
		<td class="left" style="width:35%;"><?php echo $fname_arr[$_fname]; ?>：</td>
		<td class="right"><input name="value" id="input1" value="<?php echo $line[$_fname]; ?>" class="input" style="width:100px"></td>
	</tr>
</table>

<input type="hidden" name="hid" value="<?php echo $_hid; ?>">
<input type="hidden" name="month" value="<?php echo $_month; ?>">
<input type="hidden" name="fname" value="<?php echo $_fname; ?>">
<div class="button_line"><input id="submit_button" type="submit" class="submit" value="提交数据"></div>

</form>

<script type="text/javascript">
byid("input1").focus();
</script>

</body>
</html>