<?php
/*
// 说明: copy_history_data
// 作者: 幽兰 (weelia@126.com)
// 时间: 2013-11-18
*/
require "../../core/core.php";
$table = "jiuzhen_report";
set_time_limit(0);

$from_month_arr = array();
$to_month_arr = array();

$dt = strtotime(date("Y-m")."-01");

for ($i = 0; $i <= 10; $i++) {
	$from_month_arr[] = $to_month_arr[] = date("Y-m", strtotime("-".$i." month", $dt));
}

if ($_POST) {
	$hid_arr = $db->query("select id,name from hospital order by id asc", "id", "name");

	$is_fugai = $_POST["skip_copy_if_set"] ? 0 : 1;

	$from_month = intval(str_replace("-", "", $_POST["from_month"]));
	$to_month = intval(str_replace("-", "", $_POST["to_month"]));

	if (strlen($from_month) != 6 || strlen($to_month) != 6) {
		exit("月份设置错误，请重新设置！");
	}

	if (empty($_POST["copy"])) {
		exit("没有设置要复制的字段...");
	}

	foreach ($hid_arr as $_hid => $_hname) {
		$tip = $_hname." ";
		$old = $db->query("select * from jiuzhen_report where hid={$_hid} and month={$from_month} limit 1", 1);
		if (empty($old)) {
			$tip .= $from_month." 无数据 跳过";
		} else {
			$old_arr = @unserialize($old["config"]);
			$new = $db->query("select * from jiuzhen_report where hid={$_hid} and month={$to_month} limit 1", 1);
			$new_arr = @unserialize($new["config"]);
			foreach ($_POST["copy"] as $f) {
				if ($new_arr[$f] != '') {
					if ($is_fugai && $old_arr[$f] != '') {
						$old_value = $new_arr[$f];
						$new_arr[$f] = $old_arr[$f];
						$tip .= $f."由[".$old_value."]更新为[".$new_arr[$f]."] ";
					} else {
						$tip .= $f."不为空[".$new_arr[$f]."]不覆盖 ";
					}
				} else {
					$new_arr[$f] = $old_arr[$f];
					$tip .= $f."更新为[".$new_arr[$f]."] ";
				}
			}
			$new_str = @serialize($new_arr);
			if ($new["hid"] > 0) {
				//echo "update jiuzhen_report set config='$new_str' where hid='$_hid' and month='$to_month' limit 1<br>";
				$db->query("update jiuzhen_report set config='$new_str' where hid='$_hid' and month='$to_month' limit 1");
			} else {
				//echo "insert into jiuzhen_report set hid='$_hid', month='$to_month', sub_id=0, config='$new_str'<br>";
				$db->query("insert into jiuzhen_report set hid='$_hid', month='$to_month', sub_id=0, config='$new_str'");
			}
		}
		echo $tip."<br><br>";
	}

	echo "<br>全部完成！";
	exit;
}

?>
<html>
<head>
<title>复制数据</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
</head>

<body>
<form method="POST">
把：
<select class="combo" name="from_month">
	<option value="" style="color:gray">-月份-</option>
	<?php echo list_option($from_month_arr, "_value_", "_value_"); ?>
</select>
数据复制到：
<select class="combo" name="to_month">
	<option value="" style="color:gray">-月份-</option>
	<?php echo list_option($to_month_arr, "_value_", "_value_"); ?>
</select>
<br>
<br>

复制内容：<input type="checkbox" name="copy[]" value="fuzeren" id="fuzeren"><label for="fuzeren">负责人</label>&nbsp;
<input type="checkbox" name="copy[]" value="dabiaozhishu1" id="dabiaozhishu1"><label for="dabiaozhishu1">达标指数</label>&nbsp;
<input type="checkbox" name="copy[]" value="dabiaozhishu3" id="dabiaozhishu3"><label for="dabiaozhishu3">达标指数3</label>&nbsp;
<input type="checkbox" name="copy[]" value="jianglijishu1" id="jianglijishu1"><label for="jianglijishu1">奖励基数</label>&nbsp;
<input type="checkbox" name="copy[]" value="jianglizhibiao1" id="jianglizhibiao1"><label for="jianglizhibiao1">奖励指标</label>&nbsp;
<br>
<br>
<input type="checkbox" name="skip_copy_if_set" id="skip_copy_if_set" checked><label for="skip_copy_if_set">如果目标月份已有数据，则不复制</label>&nbsp;
<br>
<br>
<input type="submit" class="submit" value="开始复制">


</form>

</body>
</html>