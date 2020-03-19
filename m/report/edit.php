<?php
/*
// 说明: 修改报表数据
// 作者: 幽兰 (weelia@126.com)
// 时间: 2012-03-08
*/
require "../../core/core.php";
$table = "jiuzhen_report";

$_hid = intval($_REQUEST["hid"]);
$month = intval($_REQUEST["month"]);
$_hname = $db->query("select name from hospital where id=$_hid limit 1", 1, "name");

$line = $db->query("select * from $table where hid=$_hid and month=$month limit 1", 1);

if ($_POST) {

	$r = array();
	$mode = "add";
	if ($line) {
		$mode = "edit";
	}

	$to_save = explode(" ", "fuzeren zongxiaofei daoyuan wangcha mubiao xiaofei_mubiao dabiaozhishu jiangli_jishu jiangli_zhibiao renjundabiao renjunmubiao");
	foreach ($to_save as $v) {
		if (isset($_POST[$v])) {
			$r[$v] = $_POST[$v];
		}
	}

	$r["zongdaoyuan"] = intval($_POST["daoyuan"]) + intval($_POST["wangcha"]);

	if ($mode == "add") {
		$r["hid"] = $_hid;
		$r["hname"] = $_hname;
		$r["month"] = $month;
		$r["addtime"] = time();
		$r["author"] = $realname;
	}

	$sqldata = $db->sqljoin($r);

	if ($mode == "add") {
		$rs = $db->query("insert into $table set $sqldata");
	} else {
		$rs = $db->query("update $table set $sqldata where hid=$_hid and month=$month limit 1");
	}

	if ($rs) {
		//echo '<script> parent.update_content(); </script>';
		echo '<script> parent.load_src(0); </script>';
		echo '<script> parent.msg_box("资料提交成功", 2); </script>';
	} else {
		echo "提交失败，请联系程序员检查：<br>".$db->sql."<br><br><br>";
	}
	exit;
}


$sdata = array();

// 统计该月到院数据:
$m_begin = strtotime(substr($month, 0, 4)."-".substr($month, 4, 2)."-1 0:0:0");
$m_end = strtotime("+1 month", $m_begin) - 1;
$sdata["daoyuan"] = $db->query("select count(id) as c from patient_{$_hid} where order_date>=$m_begin and order_date<$m_end and status=1 and part_id=2", 1, "c");
$sdata["wangcha"] = $db->query("select count(id) as c from patient_{$_hid} where order_date>=$m_begin and order_date<$m_end and status=1 and part_id!=2 and media_from='网络'", 1, "c");

// 统计竞价消费额:
$d_begin = date("Ymd", $m_begin);
$d_end = date("Ymd", $m_end);
$sdata["zongxiaofei"] = $db->query("select sum(xiaofei) as c from jingjia_xiaofei where hid={$_hid} and date>=$d_begin and date<=$d_end", 1, "c");

// 负责人
$fuzeren_tips = "";
if ($line["fuzeren"] == '') {
	$fuzeren = $db->query("select fuzeren from $table where hid={$_hid} and month<$month and fuzeren!='' order by month desc limit 1", 1, "fuzeren");
	if ($fuzeren != '') {
		$line["fuzeren"] = $fuzeren;
		$fuzeren_tips = " ←此姓名为系统自动填写";
	} else {
		// 查询其它月份
		$fuzeren = $db->query("select fuzeren from $table where hid={$_hid} and month>$month and fuzeren!='' order by month asc limit 1", 1, "fuzeren");
		if ($fuzeren != '') {
			$line["fuzeren"] = $fuzeren;
			$fuzeren_tips = " ←此姓名为系统自动填写";
		}
	}
}


function _int_month_to_text($m) {
	return substr($m, 0, 4)."年".substr($m, 4, 2)."月";
}

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

function write_s_data(s) {
	byid(s).value = byid("s_"+s).innerHTML;
}
</script>
</head>

<body>
<form name="mainform" action="" method="POST" onsubmit="return check_data()">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">请输入“<?php echo $_hname; ?>” <?php echo _int_month_to_text($month); ?> 数据</td>
	</tr>
	<tr>
		<td class="left" style="width:35%;">负责人：</td>
		<td class="right"><input name="fuzeren" value="<?php echo $line["fuzeren"]; ?>" class="input" style="width:100px"> <?php echo $fuzeren_tips; ?></td>
	</tr>

	<tr>
		<td class="left">总消费：</td>
		<td class="right"><input name="zongxiaofei" id="zongxiaofei" value="<?php echo $line["zongxiaofei"]; ?>" class="input" style="width:100px"> <a href="javascript:void(0);" onclick="write_s_data('zongxiaofei')" title="点击填入系统数据">系统数据：<b id="s_zongxiaofei"><?php echo $sdata["zongxiaofei"]; ?></b></a></td>
	</tr>
	<tr>
		<td class="left">网络到院：</td>
		<td class="right"><input name="daoyuan" id="daoyuan" value="<?php echo $line["daoyuan"]; ?>" class="input" style="width:100px"> <a href="javascript:void(0);" onclick="write_s_data('daoyuan')" title="点击填入系统数据">系统数据：<b id="s_daoyuan"><?php echo $sdata["daoyuan"]; ?></b></a></td>
	</tr>
	<tr>
		<td class="left">网查：</td>
		<td class="right"><input name="wangcha" id="wangcha" value="<?php echo $line["wangcha"]; ?>" class="input" style="width:100px"> <a href="javascript:void(0);" onclick="write_s_data('wangcha')" title="点击填入系统数据">系统数据：<b id="s_wangcha"><?php echo $sdata["wangcha"]; ?></b></a></td>
	</tr>

	<tr>
		<td class="left">到诊目标：</td>
		<td class="right"><input name="mubiao" value="<?php echo $line["mubiao"]; ?>" class="input" style="width:200px"></td>
	</tr>
	<tr>
		<td class="left">消费目标：</td>
		<td class="right"><input name="xiaofei_mubiao" value="<?php echo $line["xiaofei_mubiao"]; ?>" class="input" style="width:200px"></td>
	</tr>
	<tr>
		<td class="left">达标指数：</td>
		<td class="right"><input name="dabiaozhishu" value="<?php echo $line["dabiaozhishu"]; ?>" class="input" style="width:200px"></td>
	</tr>
	<tr>
		<td class="left">奖励基数：</td>
		<td class="right"><input name="jiangli_jishu" value="<?php echo $line["jiangli_jishu"]; ?>" class="input" style="width:200px"></td>
	</tr>
	<tr>
		<td class="left">奖励指标：</td>
		<td class="right"><input name="jiangli_zhibiao" value="<?php echo $line["jiangli_zhibiao"]; ?>" class="input" style="width:200px"></td>
	</tr>
	<tr>
		<td class="left">人均达标：</td>
		<td class="right"><input name="renjundabiao" value="<?php echo $line["renjundabiao"]; ?>" class="input" style="width:200px"></td>
	</tr>
	<tr>
		<td class="left">人均目标：</td>
		<td class="right"><input name="renjunmubiao" value="<?php echo $line["renjunmubiao"]; ?>" class="input" style="width:200px"></td>
	</tr>
</table>

<input type="hidden" name="hid" value="<?php echo $_hid; ?>">
<input type="hidden" name="month" value="<?php echo $month; ?>">
<div class="button_line"><input id="submit_button" type="submit" class="submit" value="提交数据"></div>

</form>

</body>
</html>