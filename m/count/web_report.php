<?php
// --------------------------------------------------------
// - 功能说明 : 网络 数据统计
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2011-05-18
// --------------------------------------------------------
require "../../core/core.php";
include "web_config.php";

$table = "count_web";


// 时间定义
$jintian_b = mktime(0,0,0); //今天的开始
$jintian_e = strtotime("+1 day", $jintian_b) - 1; //今天结束
$zuotian_b = strtotime("-1 day", $jintian_b); // 昨天
$qiantian_b = strtotime("-1 day", $zuotian_b); // 前天
$benyue_b = mktime(0,0,0,date("m"), 1); // 本月开始
$benyue_e = strtotime("+1 month", $benyue_b) - 1; //本月结束
$shangyue_e = $benyue_b - 1; // 上个月结束
$shangyue_b = strtotime("-1 month", $benyue_b) + 1; //上月开始
$tb_b = $shangyue_b;
$tb_e = strtotime("-1 month", time());


// 要处理的时间:
$time_array = array(
	//"今天" => array($jintian_b, $jintian_e),
	"昨天" => array($zuotian_b, $jintian_b - 1),
	"前天" => array($qiantian_b, $zuotian_b - 1),
	"本月" => array($benyue_b, $benyue_e),
	"同比" => array($tb_b, $tb_e),
	"上月" => array($shangyue_b, $shangyue_e)
);


// 加入最近6个月的数据:
for ($i=2; $i<=7; $i++) {
	$mon = strtotime("-".$i." month", $benyue_b);
	$time_array[date("Y-m", $mon)] = array($mon, strtotime("+1 month", $mon) - 1);
}


// 按时间汇总:
$rs = array();

$cal_field = explode(" ", "click click_local click_other zero_talk ok_click ok_click_local ok_click_other talk talk_local talk_other orders order_local order_other come come_local come_other");

// 处理字段:
$f = array();
foreach ($cal_field as $v) {
	$f[] = 'sum('.$v.') as '.$v;
}
$f = implode(", ", $f);

foreach ($time_array as $tname => $tt) {

	$b = date("Ymd", $tt[0]);
	$e = date("Ymd", $tt[1]);

	//查询汇总数据:
	$tmp = $db->query("select $f from $table where hid=$hid and sub_id=$sub_id and date>=$b and date<=$e order by date asc", 1);

	$rs[$tname] = $tmp;

	// 咨询预约率:
	$rs[$tname]["per_1"] = @round($rs[$tname]["talk"] / $rs[$tname]["click"] * 100, 2);
	// 预到就诊率:
	$rs[$tname]["per_2"] = @round($rs[$tname]["come"] / $rs[$tname]["orders"] * 100, 2);
	// 咨询就诊率:
	$rs[$tname]["per_3"] = @round($rs[$tname]["come"] / $rs[$tname]["click"] * 100, 2);
	// 有效咨询率:
	$rs[$tname]["per_4"] = @round($rs[$tname]["ok_click"] / $rs[$tname]["click"] * 100, 2);
	// 有效预约率:
	$rs[$tname]["per_5"] = @round($rs[$tname]["talk"] / $rs[$tname]["ok_click"] * 100, 2);

}


// 最近6个月的平均数据
$days = array();
$days["本月"] = date("j")-1;
$days["上月"] = get_month_days(date("Y-m", $shangyue_b));
for ($i=2; $i<=5; $i++) {
	$mon = strtotime("-".$i." month", $benyue_b);
	$days[date("Y-m", $mon)] = get_month_days(date("Y-m", $mon));
}

$rijun = array();
foreach ($days as $t => $d) {
	$line = $rs[$t];
	foreach ($cal_field as $k) {
		$v = $line[$k];
		if (in_array($k, explode("|", "talk|talk_local|talk_other|orders|order_local|order_other|come|come_local|come_other"))) {
			$rijun[$t][$k] = @round($v / $d, 1);
		} else {
			$rijun[$t][$k] = @round($v / $d);
		}
	}
}



?>
<html>
<head>
<title>数据汇总统计</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style>
body {padding:5px 8px; }
.main_title {margin:0 auto; padding-top:30px; padding-bottom:15px; text-align:center; font-weight:bold; font-size:12px; font-family:"宋体"; }
.item {padding:8px 3px 6px 3px !important; }
.head {padding:12px 3px !important;}

form {display:inline; }
#date_tips {float:left; font-weight:bold; padding-top:1px; }
#ch_date {float:left; margin-left:20px; }
.site_name {display:block; padding:4px 0px;}
.site_name, .site_name a {font-family:"Arial", "Tahoma"; }
.ch_date_a b, .ch_date_a a {font-family:"Arial"; }
.ch_date_a b {border:0px; padding:1px 5px 1px 5px; color:red; }
.ch_date_a a {border:0px; padding:1px 5px 1px 5px; }
.ch_date_a a:hover {border:1px solid silver; padding:0px 4px 0px 4px; }
.ch_date_b {padding-top:8px; text-align:left; width:80%; color:silver; }
.ch_date_b a {padding:0 3px; }

.main_title {margin:0 auto; padding-top:30px; padding-bottom:15px; text-align:center; font-weight:bold; font-size:12px; font-family:"宋体"; }

.item {padding:8px 3px 6px 3px !important; }

.head {padding:6px 3px !important;}

.rate_tips {padding:30px 0 0 30px; line-height:24px; }

.item {font-family:"Tahoma"; }
</style>

<script language="javascript">
function hgo(dir, o) {
	var obj = byid("hid");
	if (dir == "up") {
		if (obj.selectedIndex > 1) {
			obj.selectedIndex = obj.selectedIndex - 1;
			obj.onchange();
			o.disabled = true;
		} else {
			parent.msg_box("已经是最前了", 3);
		}
	}
	if (dir == "down") {
		if (obj.selectedIndex < obj.options.length-1) {
			obj.selectedIndex = obj.selectedIndex + 1;
			obj.onchange();
			o.disabled = true;
		} else {
			parent.msg_box("已经是最后一个了", 3);
		}
	}
}
</script>
</head>

<body>
<div style="margin:10px 0 0 0px;">
	<div id="date_tips">医院项目：</div>
	<form method="GET" style="margin-left:30px;">
		<select name="hid" id="hid" class="combo" onchange="this.form.submit()">
			<option value="" style="color:gray">-请选择项目-</option>
			<?php echo list_option($types, "_key_", "_value_", $hid); ?>
		</select>&nbsp;
		<button class="button" onclick="hgo('up',this);">上</button>&nbsp;
		<button class="button" onclick="hgo('down',this);">下</button>
		<input type="hidden" name="op" value="change_type">
	</form>&nbsp;&nbsp;&nbsp;
</div>

<div class="main_title"><?php echo $type_detail["h_name"]; ?> - 统计数据</div>

<table width="100%" align="center" class="list">
	<tr>
		<td class="head" align="center" width="60">日期</td>
		<td class="head" align="center" style="color:red">总点击</td>
		<td class="head" align="center">本地</td>
		<td class="head" align="center">外地</td>
		<td class="head" align="center" style="color:red">总有效</td>
		<td class="head" align="center">本地</td>
		<td class="head" align="center">外地</td>

		<td class="head" align="center" style="color:red">当天约</td>
		<td class="head" align="center">本地</td>
		<td class="head" align="center">外地</td>
		<td class="head" align="center" style="color:red">预计到院</td>
		<td class="head" align="center">本地</td>
		<td class="head" align="center">外地</td>
		<td class="head" align="center" style="color:red">实际到院</td>
		<td class="head" align="center">本地</td>
		<td class="head" align="center">外地</td>

		<td class="head" align="center" style="color:red">咨询预约率</td>
		<td class="head" align="center" style="color:red">预到就诊率</td>
		<td class="head" align="center" style="color:red">咨询就诊率</td>
		<td class="head" align="center" style="color:red">有效咨询率</td>
		<td class="head" align="center" style="color:red">有效预约率</td>
	</tr>

<?php
foreach ($time_array as $tname => $tt) {
	$li = $rs[$tname];
?>
	<tr>
		<td class="item" align="center"><?php echo $tname; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["click"]; ?></td>
		<td class="item" align="center"><?php echo $li["click_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["click_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["ok_click"]; ?></td>
		<td class="item" align="center"><?php echo $li["ok_click_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["ok_click_other"]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo $li["talk"]; ?></td>
		<td class="item" align="center"><?php echo $li["talk_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["talk_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["orders"]; ?></td>
		<td class="item" align="center"><?php echo $li["order_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["order_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["come"]; ?></td>
		<td class="item" align="center"><?php echo $li["come_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["come_other"]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_1"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_2"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_3"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_4"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_5"]); ?>%</td>
	</tr>
<?php if ($tname == "上月") { ?>
	<tr>
		<td colspan="100" class="item" align="center" style="background:#E9F3F3"><b>(以下为前6个月数据)</b></td>
	</tr>
<?php } ?>
<?php } ?>

</table>

<br>

<div class="main_title"><?php echo $type_detail["h_name"]; ?> - 最近6个月日均数据</div>
<table width="100%" align="center" class="list">
	<tr>
		<td class="head" align="center">日期</td>
		<td class="head" align="center" style="color:red">总点击</td>
		<td class="head" align="center">本地</td>
		<td class="head" align="center">外地</td>
		<td class="head" align="center" style="color:red">总有效</td>
		<td class="head" align="center">本地</td>
		<td class="head" align="center">外地</td>

		<td class="head" align="center" style="color:red">当天约</td>
		<td class="head" align="center">本地</td>
		<td class="head" align="center">外地</td>
		<td class="head" align="center" style="color:red">预计到院</td>
		<td class="head" align="center">本地</td>
		<td class="head" align="center">外地</td>
		<td class="head" align="center" style="color:red">实际到院</td>
		<td class="head" align="center">本地</td>
		<td class="head" align="center">外地</td>
	</tr>

<?php
foreach ($rijun as $t => $li) {
?>
	<tr>
		<td class="item" align="center"><?php echo $t; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["click"]; ?></td>
		<td class="item" align="center"><?php echo $li["click_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["click_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["ok_click"]; ?></td>
		<td class="item" align="center"><?php echo $li["ok_click_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["ok_click_other"]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo $li["talk"]; ?></td>
		<td class="item" align="center"><?php echo $li["talk_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["talk_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["orders"]; ?></td>
		<td class="item" align="center"><?php echo $li["order_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["order_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["come"]; ?></td>
		<td class="item" align="center"><?php echo $li["come_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["come_other"]; ?></td>
	</tr>
<?php } ?>


</table>

<br>
<br>

</body>
</html>
