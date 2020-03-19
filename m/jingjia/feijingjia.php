<?php
/* --------------------------------------------------------
// 说明: 非竞价数据录入
// 作者: 幽兰 (weelia@126.com)
// 时间: 2014-3-6
// ----------------------------------------------------- */
$table = "jingjia_feijingjia";
require "../../core/core.php";

// 当前月开始
$cur_m = strtotime(date("Y-m-01 0:0:0"));

// 月份:
$month_arr = array();
$month_arr[] = date("Y-m", strtotime("+1 month", $cur_m));
$month_arr[] = date("Y-m", $cur_m);
for ($i = 1; $i <= 5; $i++) {
	$month_arr[] = date("Y-m", strtotime("-{$i} month", $cur_m));
}


// 读取这些月份的设置:
foreach ($month_arr as $m) {
	$int_m = str_replace("-", "", $m);
	$data[$m] = $db->query("select x1 from $table where hid='$hid' and month='$int_m' limit 1", 1, "x1");
}



if ($_POST) {
	foreach ($_POST["data"] as $m => $v) {
		$v = intval($v);
		$int_m = str_replace("-", "", $m);

		$m_days = get_month_days($m);

		$per_day = @round($v / $m_days, 2);

		$old = $db->query("select * from $table where hid='$hid' and month='$int_m' limit 1", 1);
		if ($old["id"] > 0) {
			$old_id = $old["id"];
			$db->query("update $table set x1='$v', x1_per_day='$per_day' where id=$old_id limit 1");
		} else {
			$db->query("insert into $table set hid=$hid, month='$int_m', x1='$v', days='$m_days', x1_per_day='$per_day', addtime='$time', author='$realname'");
		}
	}

	echo '<script type="text/javascript">';
	echo 'parent.update_content();';
	echo 'parent.load_box(0);';
	echo 'parent.msg_box("数据保存成功");';
	echo '</script>';
	exit;
}




?>
<html>
<head>
<title>非竞价数据设置</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script type="text/javascript">
function check_data(oForm) {
	return true;
}
</script>
</head>

<body>

<div class="space"></div>
<form method="POST" onsubmit="return check_data(this)">
<table width="100%" class="edit">

<?php foreach ($month_arr as $m) { ?>
	<tr>
		<td class="left" style="width:35%"><?php echo $m; ?>：</td>
		<td class="right">
			<input name="data[<?php echo $m; ?>]" value="<?php echo $data[$m]; ?>" class="input" style="width:50%">
		</td>
	</tr>
<?php } ?>

</table>

<div class="button_line"><input type="submit" class="submit" value="保存设置"></div>

</form>


</body>
</html>