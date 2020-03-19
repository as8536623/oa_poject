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


// 允许的权限:
$data_power = explode(",", $uinfo["data_power"]);

$power_show = array();

if ($debug_mode || in_array("all", $data_power)) {
	$power_show["总"] = "总数据";
}
if ($debug_mode || in_array("web", $data_power)) {
	$power_show["网络"] = "网络";
}
if ($debug_mode || in_array("tel", $data_power)) {
	$power_show["电话"] = "电话";
}
// 其它:
$z_info = $db->query("select name,type,sum_condition from index_module_set where isshow=1");
foreach ($z_info as $li) {
	if ($debug_mode || in_array($li["name"], $data_power)) {
		$power_show[$li["name"]] = $li["name"];
	}
}

if (count($power_show) == 0) {
	exit("对不起，您没有任何权限，请联系管理员。");
}

// 当前设置：
$cur_field_arr = explode(",", $uinfo["list_field"]);
if ($uinfo["list_field"] == '' && count($cur_field_arr) < 2) {
	// 默认设置:  从前往后，依次选择字段:
	$cur_field_arr = array();
	foreach ($power_show as $k => $v) {
		$cur_field_arr[] = $k.":今日:预约";
		$cur_field_arr[] = $k.":本月:预约";
		$cur_field_arr[] = $k.":今日:实到";
		$cur_field_arr[] = $k.":本月:实到";
		$cur_field_arr[] = $k.":今日:跟踪";
		$cur_field_arr[] = $k.":本月:跟踪";
		if ($k == "总") {
			$cur_field_arr[] = "总:增幅";
		}
		if ($k == "网络") {
			$cur_field_arr[] = "网络:增幅";
		}
		if (count($cur_field_arr) >= 12) {
			break;
		}
	}
}

/*
if ($debug_mode) {
	$cur_field_arr = array();
	$cur_field_arr[] = "总:今日:实到";
	$cur_field_arr[] = "总:本月:实到";
	$cur_field_arr[] = "总:增幅";
	$cur_field_arr[] = "网络:今日:实到";
	$cur_field_arr[] = "网络:本月:实到";
	$cur_field_arr[] = "网络:增幅";
	$cur_field_arr[] = "网络:今日:预约";
	$cur_field_arr[] = "网络:本月:预约";
	$cur_field_arr[] = "微信:今日:实到";
	$cur_field_arr[] = "微信:本月:实到";
	$cur_field_arr[] = "企划部:今日:实到";
	$cur_field_arr[] = "企划部:本月:实到";
};
*/



$hospital_list = $db->query("select id,name,area from hospital where id in ($hospitals) order by sort desc,id asc", 'id');

$data_arr = $db->query("select * from patient_data where hid in ($hospitals) ", "hid");

$counter = array();
$hid_data_arr = array();
foreach ($data_arr as $_hid => $li) {
	$tmp = $li["data"];
	if ($tmp != '') {
		$res = @unserialize($tmp);
		$index = 0;
		foreach ($cur_field_arr as $v) {
			list($a, $b, $c) = explode(":", $v, 3);
			if ($v == "总:增幅") {
				$zengfu = intval($res["总"]["实到"]["本月"]) - intval($res["总"]["实到"]["同比"]);
				if ($zengfu > 0) {
					$counter["up_num"] += 1;
					$counter["up_count"] += $zengfu;
					$zengfu = '+'.$zengfu.' <img src="/res/img/yeji_up.gif" align="absmiddle">';
				} else if ($zengfu < 0) {
					$counter["down_num"] += 1;
					$counter["down_count"] += $zengfu;
					$zengfu = $zengfu.' <img src="/res/img/yeji_down.gif" align="absmiddle">';
				}
				$hid_data_arr[$_hid][$index++] = $zengfu;
			} else if ($v == "网络:增幅") {
				$zengfu = intval($res["网络"]["实到"]["本月"]) - intval($res["网络"]["实到"]["同比"]);
				if ($zengfu > 0) {
					$zengfu = '+'.$zengfu.' <img src="/res/img/yeji_up.gif" align="absmiddle">';
				} else if ($zengfu < 0) {
					$zengfu = $zengfu.' <img src="/res/img/yeji_down.gif" align="absmiddle">';
				}
				$hid_data_arr[$_hid][$index++] = $zengfu;
			} else {
				$hid_data_arr[$_hid][$index++] = intval($res[$a][$c][$b]);
			}
		}
	}
}



$head_count = count($cur_field_arr) + 2;
$per_head_width = round(100 / $head_count, 2)."%";


// 计算增长率:
/*
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
*/


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
<script src="../res/sorttable_keep.js" language="javascript"></script>
<style type="text/css">
.column_sortable {cursor:pointer; color:blue; font-family:"微软雅黑"; }
</style>
<script type="text/javascript">
function set_table_head() {
	parent.load_src(1, "/m/list_set_head.php", 800, 500);
}

window.onscroll = function () {
	var s_top = document.body.scrollTop;
	var top = byid("data_list").offsetTop;
	var top_head = byid("data_head").offsetHeight;
	var top_width = byid("data_list").offsetWidth;
	byid("float_head").style.width = top_width;

	if (s_top >= (0 + top + top_head)) {
		var o = byid("float_head");
		o.style.display = "";
		o.style.position = "absolute";
		o.style.left = byid("data_list").style.left;
		o.style.top = s_top;
	} else {
		byid("float_head").style.display = "none";
	}
};
</script>
</head>

<body>

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
			注：本页数据为缓存结果，并非实时，服务器每隔10分钟自动更新。最后更新：<?php echo $data_update_time; ?>&nbsp; &nbsp;
			<button onClick="self.location.reload();" class="button" title="点击刷新页面">刷新</button>&nbsp; &nbsp;
			<button onClick="set_table_head(); return false;" class="buttonb" title="点击选择表头">选择表头</button>&nbsp; &nbsp;
		</div>

		<!-- 浮动表头 -->
		<table style="display:none;" id="float_head" class="come_list" cellpadding="0" cellspacing="0" width="0" >
			<tr class="come_head">
				<td class="ac red" width="<?php echo $per_head_width; ?>">地区</td>
				<td class="ac red" width="<?php echo $per_head_width; ?>">医院</td>
				
<?php
foreach ($cur_field_arr as $v) {
list($a, $b, $c) = explode(":", $v, 3);
$_name = str_replace(":", "", $v);
$_name = str_replace("今日", "今", $_name);
$_name = str_replace("昨日", "昨", $_name);
$_name = str_replace("本月", "月", $_name);
$_name = str_replace("同比", "同", $_name);

$_name = str_replace("预约", "约", $_name);
$_name = str_replace("预到", "预", $_name);
$_name = str_replace("实到", "到", $_name);
$_name = str_replace("跟踪", "跟", $_name);
?>
				<td class="ac" width="<?php echo $per_head_width; ?>"><?php echo $_name; ?></td>
<?php } ?>
			</tr>
		</table>

		<div style="width:95%;">

			<!-- 数据表格 -->
			<table id="data_list" class="round_table come_list sortable" cellpadding="0" cellspacing="0" width="100%">
				<tr id="data_head" class="come_head">
					<td class="ac red column_sortable" title="点击可排序" width="<?php echo $per_head_width; ?>">地区</td>
					<td class="ac red column_sortable" title="点击可排序" width="<?php echo $per_head_width; ?>">医院</td>
<?php
foreach ($cur_field_arr as $v) {
	list($a, $b, $c) = explode(":", $v, 3);
	$_name = str_replace(":", "", $v);
	$_name = str_replace("今日", "今", $_name);
	$_name = str_replace("昨日", "昨", $_name);
	$_name = str_replace("本月", "月", $_name);
	$_name = str_replace("同比", "同", $_name);

	$_name = str_replace("预约", "约", $_name);
	$_name = str_replace("预到", "预", $_name);
	$_name = str_replace("实到", "到", $_name);
	$_name = str_replace("跟踪", "跟", $_name);
?>
					<td class="ac column_sortable" title="点击可排序" width="<?php echo $per_head_width; ?>"><?php echo $_name; ?></td>
<?php } ?>
				</tr>


<?php
	$skip_hospitals = array();
	foreach ($hospital_list as $_hid => $_li) {
		$line = $hid_data_arr[$_hid];
		if (array_sum($line) == 0) {
			$skip_hospitals[] = '<a href="main.php?do=change&hospital_id='.$_hid.'">'.$_li["name"].'</a>';
			continue;
		}
?>
				<tr onMouseOver="mi(this)" onMouseOut="mo(this)" class="come_line" style="color:<?php echo $_li["color"]; ?>">
					<td class="ac"><nobr>&nbsp;<?php echo $_li["area"]; ?>&nbsp;</nobr></td>
					<td class="ac"><nobr>&nbsp;<a href="main.php?do=change&hospital_id=<?php echo $_hid; ?>" style="color:<?php echo $_li["color"]; ?>" title="点击切换到此医院"><?php echo $_li["name"]; ?></a>&nbsp;</nobr></td>
<?php
$index = 0;
foreach ($cur_field_arr as $v) {
?>
					<td class="ac" title="<?php echo $v; ?>"><?php echo $line[$index++]; ?></td>
<?php } ?>
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
<?php if ($counter) { ?>
		* 上升医院：<?php echo $counter["up_num"]; ?> 家，整体上升量：<?php echo $counter["up_count"]; ?> ；下降医院：<?php echo $counter["down_num"]; ?> 家，整体下降量：<?php echo $counter["down_count"]; ?> <font color="silver">(依据总数据统计)</font><br>
<?php } ?>
		<?php if (count($skip_hospitals) > 0) echo ("* <b>以下医院/科室无数据，已被忽略：</b>".implode("、", $skip_hospitals)."<br>"); ?>
		* 页面执行时间：<?php echo round(now() - $pagebegintime, 4); ?>s  <?php echo $log_time1." ".$log_time2; ?>
	</div>
</div>

</body>
</html>