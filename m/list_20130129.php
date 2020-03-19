<?php
//
// - 功能说明 : 首页
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2009-10-01 => 2013-01-07
//
require "../core/core.php";
define("BTOA_MAIN", 1);

$page_begintime = now();

function _get_month_days($month = '') {
	if ($month == '') $month = date("Y-m");
	return date("j", strtotime("+1 month", strtotime($month."-1 0:0:0")) - 1);
}

$part_id_name = $db->query("select id,name from sys_part", 'id', 'name');

// 时间定义 2011-12-28:
// 时间的起始点都是 YYYY-MM-DD 00:00:00 结束则是 YYYY-MM-DD 23:59:59
$today_tb = mktime(0,0,0); //今天开始
$today_te = strtotime("+1 day", $today_tb) - 1; //今天结束

$yesterday_tb = strtotime("-1 day", $today_tb); //昨天开始
$yesterday_te = $today_tb - 1; //昨天结束

$month_tb = mktime(0, 0, 0, date("m"), 1); //本月开始
$month_te = strtotime("+1 month", $month_tb) - 1; //本月结束

$lastmonth_tb = strtotime("-1 month", $month_tb); //上月开始
$lastmonth_te = $month_tb - 1; //上月结束

$tb_tb = strtotime("-1 month", $month_tb); //同比时间开始
$tb_te = strtotime("-1 month", time()); //同比时间结束
if (date("d", $tb_te) != date("d")) {
	$tb_te = $month_tb - 1;
}

// 月比:
$yuebi_tb = strtotime("-1 month", $today_tb);
if (date("d", $yuebi_tb) != date("d", $today_tb)) {
	$yuebi_tb = $yuebi_te = -1;
} else {
	$yuebi_te = $yuebi_tb + 24*3600;
}
// 周比:
$zhoubi_tb = strtotime("-7 day", $today_tb);
$zhoubi_te = $zhoubi_tb + 24*3600;

// 去年同月比
$yb_tb = strtotime("-1 year", $month_tb);
$_days = _get_month_days(date("Y-m", $yb_tb));
if (date("j") > $_days) { //当前的日已经大于去年同月的天数了(比如29日和去年的28日)
	$yb_te = strtotime(date("Y-m-", $yb_tb).$_days.date(" 23:59:59")); //对比为去年同月的整月
} else {
	$yb_te = strtotime(date("Y-m-", $yb_tb).date("d H:i:s"));
}

// 数据查询依照此数组定义
$time_arr = array(
	"今日" => array($today_tb, $today_te),
	"月比" => array($yuebi_tb, $yuebi_te),
	"周比" => array($zhoubi_tb, $zhoubi_tb),
	"昨日" => array($yesterday_tb, $yesterday_te),
	"本月" => array($month_tb, $today_te),
	"同比" => array($tb_tb, $tb_te),
	"上月" => array($lastmonth_tb, $lastmonth_te),
);

$hospital_list = $db->query("select id,name,area from hospital where id in ($hospitals) order by sort desc,id asc", 'id');

$data_arr = $db->query("select * from patient_data where hid in ($hospitals) ", "hid");

$hid_data_arr = array();
foreach ($data_arr as $_hid => $li) {
	$tmp = $li["data"];
	if ($tmp != '') {
		$res = @unserialize($tmp);
		$hid_data_arr[$_hid]["x1"] = intval($res["总"]["实到"]["今日"]);
		$hid_data_arr[$_hid]["x2"] = intval($res["总"]["实到"]["昨日"]);
		$hid_data_arr[$_hid]["x3"] = intval($res["总"]["实到"]["本月"]);
		$hid_data_arr[$_hid]["x4"] = intval($res["总"]["实到"]["同比"]);
		$hid_data_arr[$_hid]["x5"] = intval($res["总"]["实到"]["上月"]);
		$hid_data_arr[$_hid]["x6"] = intval($res["网络"]["实到"]["今日"]);
		$hid_data_arr[$_hid]["x7"] = intval($res["网络"]["实到"]["本月"]);
		$hid_data_arr[$_hid]["x8"] = intval($res["电话"]["实到"]["今日"]);
		$hid_data_arr[$_hid]["x9"] = intval($res["电话"]["实到"]["本月"]);
		$hid_data_arr[$_hid]["x10"] = intval($res["微信"]["实到"]["今日"]);
		$hid_data_arr[$_hid]["x11"] = intval($res["微信"]["实到"]["本月"]);
	}
}


// 计算增长率:
$hid_percent = array();
$up_all = $up_num = $down_all = $down_num = 0;
foreach ($hid_data_arr as $k => $v) {
	$per = $v["x3"] - $v["x4"];
	$hid_percent[$k] = $per;
	if ($per > 0) {
		$up_all += $per;
		$up_num += 1;
	}
	if ($per < 0) {
		$down_all += abs($per);
		$down_num += 1;
	}
}
arsort($hid_percent);


?>
<html>
<head>
<title>后台首页</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="../res/base.css?ver=20130126" rel="stylesheet" type="text/css">
<script src="../res/base.js?ver=20130126" language="javascript"></script>
<style type="text/css">
#come_list_area {margin:30px 0px 20px 0px; }
.come_list {border:1px solid #97e6a5; }
.come_head td {border:1px solid #e7e7e7; background:#f2f8f9; padding:4px 3px 3px 3px; font-weight:bold; }
.come_line td {border:1px solid #e7e7e7; padding:4px 3px 3px 3px; }
.al {text-align:left; }
.ac {text-align:center; }
.ar {text-align:right; padding-right:5px !important; }
.red {color:red; }
</style>
<script src="../res/sorttable.js" language="javascript"></script>
<style type="text/css">
.column_sortable {cursor:pointer; color:blue; }
</style>
<script type="text/javascript">
function update_come_data(o) {
	//var url = "/v4/lib/update_come.php";
	//parent.load_src(1, url, 500, 200);
}

function float_head() {
	var s_top = document.body.scrollTop;
	var top = byid("data_list").offsetTop;
	var top_head = byid("data_head").offsetHeight;

	if (s_top >= (0 + top + top_head)) {
		var o = byid("float_head");
		o.style.display = "";
		o.style.position = "absolute";
		o.style.left = byid("data_list").style.left;
		o.style.top = s_top;
	} else {
		byid("float_head").style.display = "none";
	}
}
</script>
</head>

<body onscroll="float_head()">

<a name="top"></a>

<div style='padding:20px 12px 12px 40px;'>
	<div style="line-height:24px">
<?php
$str = '您好，<font color="#FF0000"><b>'.$realname.'</b></font>';

if ($uinfo["hospitals"] || $uinfo["part_id"] > 0) {
	if ($uinfo["part_id"] > 0) {
		$str .= '　(身份：'.$part_id_name[$uinfo["part_id"]].")";
	}
}

$onlines = $db->query("select count(*) as count from sys_admin where online=1", 1, "count");
$str .= '　日期 <font color="red"><b>'.date("Y-m-d").'</b></font>';
$str .= '　星期<b><font color="red">'.substr("日一二三四五六", date("w")*2, 2).'</font></b>';
$str .= '　在线人数 <font color="red"><b>'.$onlines.'</b></font> 人';

echo $str;
?>
	</div>


	<div id="come_list_area">

<?php
$_time = @file_get_contents("../data/update_data.txt");
$data_update_time = "未知";
if ($_time > 0) {
	$data_update_time = date("Y.n.j H:i:s", $_time);
}

?>
	<div style="width:900px; text-align:center; padding-bottom:5px;">
		注：本页数据为缓存结果，并非实时，服务器每隔10分钟自动更新。最后更新：<?php echo $data_update_time; ?>&nbsp; &nbsp;<button onclick="self.location.reload();" class="button" title="点击刷新页面">刷新</button>&nbsp; &nbsp;<!-- <a href="#" onclick="update_come_data(this); return false;" title="一般情况无需手工更新，除非服务器自动更新机制出现故障" style="color:silver;">手工更新</a> -->
	</div>

		<div style="width:900px">
			<table id="float_head" style="display:none;" class="come_list sortable" cellpadding="0" cellspacing="0" width="900">
				<tr class="come_head">
					<td class="ac" width="6%">地区</td>
					<td class="ac">医院</td>
					<td class="ac" width="6%">今日</td>
					<td class="ac" width="6%">昨日</td>
					<td class="ac" width="7%">本月</td>
					<td class="ac" width="7%">增幅</td>
					<td class="ac" width="7%">同比</td>
					<td class="ac" width="7%">上月</td>
					<td class="ac" width="7%">网络今日</td>
					<td class="ac" width="7%">网络本月</td>
					<td class="ac" width="7%">电话今日</td>
					<td class="ac" width="7%">电话本月</td>
					<td class="ac" width="7%">微信今日</td>
					<td class="ac" width="7%">微信本月</td>
				</tr>
			</table>

			<table id="data_list" class="round_table come_list sortable" cellpadding="0" cellspacing="0" width="100%">
				<tr id="data_head" class="come_head">
					<td class="ac red column_sortable" width="6%" title="点击可排序">地区</td>
					<td class="ac red column_sortable" title="点击可排序">医院</td>
					<td class="ac column_sortable" width="6%" title="点击可排序">今日</td>
					<td class="ac column_sortable" width="6%" title="点击可排序">昨日</td>
					<td class="ac column_sortable" width="7%" title="点击可排序">本月</td>
					<td class="ac column_sortable" width="7%" title="点击可排序">增幅</td>
					<td class="ac column_sortable" width="7%" title="点击可排序">同比</td>
					<td class="ac column_sortable" width="7%" title="点击可排序">上月</td>
					<td class="ac column_sortable" width="7%" title="点击可排序">网络今日</td>
					<td class="ac column_sortable" width="7%" title="点击可排序">网络本月</td>
					<td class="ac column_sortable" width="7%" title="点击可排序">电话今日</td>
					<td class="ac column_sortable" width="7%" title="点击可排序">电话本月</td>
					<td class="ac column_sortable" width="7%" title="点击可排序">微信今日</td>
					<td class="ac column_sortable" width="7%" title="点击可排序">微信本月</td>
				</tr>
<?php
	$skip_hospitals = array();
	foreach ($hid_percent as $_hid => $_per) {
		if (!@array_key_exists($_hid, $hospital_list)) continue;
		$_li = $hospital_list[$_hid];
		$line = $hid_data_arr[$_hid];
		if (array_sum($line) == 0) {
			$skip_hospitals[] = $_li["name"];
			continue;
		}
?>
				<tr onmouseover="mi(this)" onmouseout="mo(this)" class="come_line" style="color:<?php echo $_li["color"]; ?>">
					<td class="ac"><?php echo $_li["area"]; ?></td>
					<td class="ac"><a href="main.php?do=change&hospital_id=<?php echo $_hid; ?>" style="color:<?php echo $_li["color"]; ?>" title="点击切换到此医院"><?php echo $_li["name"]; ?></a></td>
					<td class="ac" title="今日已到"><?php echo $line["x1"]; ?></td>
					<td class="ac" title="昨日已到"><?php echo $line["x2"]; ?></td>
					<td class="ac" title="本月已到"><?php echo $line["x3"]; ?></td>
					<td class="ac" title="对比上月增幅"><?php echo ($_per > 0 ? ("+".$_per) : $_per).($line["x3"] == $line["x4"] ? '' : ($line["x3"] > $line["x4"] ? ' <img src="/res/img/yeji_up.gif" align="absmiddle">' : ' <img src="/res/img/yeji_down.gif" align="absmiddle">')); ?></td>
					<td class="ac" title="同比已到"><?php echo $line["x4"]; ?></td>
					<td class="ac" title="上月已到"><?php echo $line["x5"]; ?></td>
					<td class="ac" title="网络今日已到"><?php echo $line["x6"]; ?></td>
					<td class="ac" title="网络本月已到"><?php echo $line["x7"]; ?></td>
					<td class="ac" title="电话今日已到"><?php echo $line["x8"]; ?></td>
					<td class="ac" title="电话本月已到"><?php echo $line["x9"]; ?></td>
					<td class="ac" title="微信今日已到"><?php echo $line["x10"]; ?></td>
					<td class="ac" title="微信本月已到"><?php echo $line["x11"]; ?></td>
				</tr>
<?php
	}
?>

			</table>
		</div>
	</div>

	<div style="padding:10px 0 0 30px;">
		<a href="#top">回顶部</a><br>
		<br>
		* 上升医院：<?php echo $up_num; ?> 家，整体上升量：<?php echo $up_all; ?> ；下降医院：<?php echo $down_num; ?> 家，整体下降量：<?php echo $down_all; ?><br>
		<?php if (count($skip_hospitals) > 0) echo ("* <b>以下医院/科室无数据，已被忽略：</b>".implode("、", $skip_hospitals)."<br>"); ?>
		* 页面执行时间：<?php echo round(now() - $pagebegintime, 4); ?>s  <?php echo $log_time1." ".$log_time2; ?>
	</div>
</div>


</body>
</html>