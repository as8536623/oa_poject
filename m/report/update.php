<?php
/*
// 说明: 更新数据
// 作者: 幽兰 (weelia@126.com)
// 时间: 2012-03-09
*/
require "../../core/core.php";
$table = "jiuzhen_report";
set_time_limit(0);

if (count($hospital_ids) > 0) {
	$hids = implode(",", $hospital_ids);
} else {
	exit_html("对不起，你没有医院权限...");
}

// 查询
$h_arr = $db->query("select id,name,area,depart from hospital where id in ($hids)", "id");

?>
<html>
<head>
<title>更新数据</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
</head>

<body>
<div style="height:500px; width:560px; ">
<?php
// 更新数据:

$_b = strtotime(date("Y-m")."-1 0:0:0");
$_e = strtotime("+1 month", $_b) - 1;

echo str_repeat("&nbsp;", 100)."<br>";
flush();
ob_flush();
ob_end_flush();

$month = date("Ym");

foreach ($h_arr as $_hid => $h) {
	$r = array();
	// 到院数据:
	$r["daoyuan"] = $db->query("select count(id) as c from patient_{$_hid} where order_date>=$_b and order_date<$_e and status=1 and part_id=2", 1, "c");
	$r["wangcha"] = $db->query("select count(id) as c from patient_{$_hid} where order_date>=$_b and order_date<$_e and status=1 and part_id!=2 and media_from='网络'", 1, "c");

	$r["zongdaoyuan"] = intval($r["daoyuan"]) + intval($r["wangcha"]);

	// 统计竞价消费额:
	$_bb = date("Ymd", $_b);
	$_ee = date("Ymd", $_e);
	$r["zongxiaofei"] = $db->query("select sum(xiaofei) as c from jingjia_xiaofei where hid={$_hid} and date>=$_bb and date<=$_ee", 1, "c");
	$r["baiduxiaofei"] = $db->query("select sum(x1) as c from jingjia_xiaofei where hid={$_hid} and date>=$_bb and date<=$_ee", 1, "c");

	// 修改还是添加：
	$old_id = $db->query("select id from $table where hid=$_hid and month=$month limit 1", 1, "id");

	if ($old_id > 0) {
		$r["updatetime"] = time();
		$sqldata = $db->sqljoin($r);
		$sql = "update $table set $sqldata where id=$old_id limit 1";
	} else {
		$r["hid"] = $_hid;
		$r["hname"] = $h["name"];
		$r["month"] = $month;
		$r["addtime"] = time();
		$r["author"] = $realname;
		$sqldata = $db->sqljoin($r);
		$sql = "insert into $table set $sqldata";
	}

	//echo $sql;

	$db->query($sql);

	echo $h["name"]." 已完成；　";
	flush();
	ob_flush();
	ob_end_flush();

	usleep(100000);
}


echo "<br><br>共 <b>".count($h_arr)."</b> 个科室，全部更新完成！";
echo '<script> parent.update_content(); </script>';
//echo '<script> parent.load_src(0); </script>';
//echo '<script> parent.msg_box("全部更新完成", 2); </script>';

?>
</div>
</body>
</html>