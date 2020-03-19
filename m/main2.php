<?php
/*
// - 功能说明 : main.php
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2008-05-13 12:28
*/
define("WEE_MAIN", "1");
require "../core/core.php";
include "../core/function.lunar.php";


// -------------------- 2009-05-01 23:39
$sel_hid = $hid;
if ($_GET["do"] == 'change') {
	if (is_numeric($_GET["hospital_id"])) {
		if (!in_array($_GET["hospital_id"], $hospital_ids)) {
			// 记录日志：
			$log_string = date("Y-m-d H:i:s ").$realname." 尝试医院ID: ".$_GET["hospital_id"]."\r\n";
			@file_put_contents(ROOT."data/hospital_try_err.txt", $log_string, FILE_APPEND);
			exit("你访问了不属于你权限范围的医院科室...");
		}
		$_SESSION["hospital_id"] = $_GET["hospital_id"];
		$hid = $hid = $_SESSION["hospital_id"];
		$sel_hid = $hid;
	} else {
		$hid = $hid = $_SESSION["hospital_id"] = 0;
		$sel_hid = $sum_hids = $_GET["hospital_id"];
	}
}
$sel_hid = (string) $sel_hid;

// zhuwenya @ 2013-01-26
$list_power = 0;
if ($debug_mode || $uinfo["show_list"] == 1 || (in_array($uinfo["part_id"], array(1,9,201,202)) && substr_count($uinfo["data_power"], "all") > 0)) {
	$list_power = 1;
}
if ($list_power) {
	if ((empty($hid) && empty($sel_hid)) || $_GET["list"] == '1') {
		header("location: list.php");
		exit;
	}
}

$part_id_name = $db->query("select id,name from sys_part", 'id', 'name');
// --------------------


// 切换医院下拉列表:
$options = array();
$hids = implode(",", $hospital_ids);

// 新的处理方法，更快 @ 2012-05-24
$h_list = $db->query("select * from hospital where id in ($hids) order by area asc, sname asc", "id");

// 统计医院和科室
$_area = $_depart = $a_s_id = array();
foreach ($h_list as $_id => $v) {
	$_area[$v["area"]] = @intval($_area[$v["area"]]) + 1;
	$_depart[$v["area"]][$v["sname"]] = @intval($_depart[$v["area"]][$v["sname"]]) + 1;
	$a_s_id[$v["area"]][$v["sname"]][] = $_id;
}

@arsort($_area);

foreach ($_area as $k1 => $v1) {
	$options[] = array(-1, $k1.' ('.$v1.')', "color:red");
	@arsort($_depart[$k1]);
	foreach ($_depart[$k1] as $k2 => $v2) {
		if ($v2 > 1) {
			$v3 = $a_s_id[$k1][$k2];
			$options[] = array("[".implode(",", $v3)."]", "　".$k2." (".$v2.")", "");
			foreach ($v3 as $v4) {
				$options[] = array($v4, "　　".$h_list[$v4]["name"], "color:blue");
			}
		} else {
			$v4 = $a_s_id[$k1][$k2][0];
			$options[] = array($v4, "　".$h_list[$v4]["name"], "color:blue");
		}
	}
}

// 时间界限定义:
$today_tb = mktime(0,0,0);
$today_te = $today_tb + 24*3600;
$yesterday_tb = $today_tb - 24*3600;
$month_tb = mktime(0,0,0,date("m"),1);
$month_te = strtotime("+1 month", $month_tb);
$lastmonth_tb = strtotime("-1 month", $month_tb);
// 同比日期定义(2010-11-27):
$tb_tb = strtotime("-1 month", $month_tb);
$tb_te = strtotime("-1 month", time());
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
// 同比:
$tb_tb = strtotime("-1 month", $month_tb); //同比时间开始
$tb_te = strtotime("-1 month", time()); //同比时间结束

// 2012-07-27 校正
$time_arr = array(
	"今日" => array(date("Ymd"), date("Ymd")),
	"月比" => array(date("Ymd", $yuebi_tb), date("Ymd", $yuebi_tb)),
	"周比" => array(date("Ymd", $zhoubi_tb), date("Ymd", $zhoubi_tb)),
	"昨日" => array(date("Ymd", $yesterday_tb), date("Ymd", $yesterday_tb)),
	"本月" => array(date("Ymd", $month_tb), date("Ymd")),
	"同比" => array(date("Ymd", $tb_tb), date("Ymd", $tb_te)),
	"上月" => array(date("Ymd", $lastmonth_tb), date("Ymd", $month_tb - 1)),
);


$d = $d = $d = array(); //需要定义的数组
if ($sel_hid || $hid > 0) {
	include "main.load_data.php";
}

// 生成链接的快捷函数
function a5($arr) {
	return aa($arr, 5);
}
function a4($arr) {
	return aa($arr, 4);
}
function a3($arr) {
	return aa($arr, 3);
}
function aa($arr, $data_len=3) {
	$a = empty($arr["data"]) ? "0" : $arr["data"];
	if (strlen(trim($a)) > $data_len) {
		$a = str_pad("", $data_len, '*'); //数值溢出标记(出现多个星号后，表示数字位数要增加啦)
	} else {
		$a = str_replace(" ", "&nbsp;", str_pad($a, $data_len, " ")); //尾部加空格
	}
	if ($arr["link"]) {
		$a = '<b class="fa"><a href="'.$arr["link"].'" class="fb">'.$a.'</a></b>';
	} else {
		$a = '<b class="fa">'.$a.'</b>';
	}
	return $a;
}

?>
<html>
<head>
<title>数据摘要</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="../res/base.css" rel="stylesheet" type="text/css">
<script src="../res/base.js" language="javascript"></script>
<style>
.idata {padding:5px 3px 3px 20px; border-top:1px solid #F2F2F2; }
.fa, .fb {font-family:"Consolas","Courier New"; color:#FF8040; }
.fb {color:#008000; }
.fb:hover {color:#FF0000; }
.huifang_tixing {border:1px solid #FF8040; padding:6px 5px 4px 5px; background:#FFF8F0; display:inline; margin:0px 0px 0px 40px;  }
</style>
<script language="javascript">
function hgo(dir) {
	var obj = byid("hospital_id");
	if (dir == "up") {
		var i = obj.selectedIndex - 1;
		while (i > 0) {
			if (obj.options[i].value > 0) {
				obj.selectedIndex = i;
				obj.onchange();
				break;
			}
			i--;
		}
		if (i == 0) {
			parent.msg_box("已经是最上一家医院了", 3);
		}
	}
	if (dir == "down") {
		var i = obj.selectedIndex + 1;
		while (i < obj.options.length) {
			if (obj.options[i].value > 0) {
				obj.selectedIndex = i;
				obj.onchange();
				break;
			}
			i++;
		}
		if (i == obj.options.length) {
			parent.msg_box("已经是最下一家医院了", 3);
		}
	}
}
</script>
</head>

<body>
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

<?php if (count($hospital_ids) > 1) { ?>
	<div style="margin-top:20px;">
		<b>切换医院：</b>
		<select name="hospital_id" id="hospital_id" class="combo" onchange="if (this.value!='-1') location='?do=change&hospital_id='+this.value" style="width:200px;">
			<option value="" style="color:gray">--请选择--</option>
<?php foreach ($options as $v) { ?>
			<option value="<?php echo $v[0]; ?>" <?php echo ($sel_hid == $v[0]  ? ' selected' : ''); ?> style="<?php echo $v[2]; ?>"><?php echo $v[1].($sel_hid == $v[0] ? ' *' : ''); ?></option>
<?php } ?>
		</select>&nbsp;
		<button class="button" onclick="hgo('up');">上</button>&nbsp;
		<button class="button" onclick="hgo('down');">下</button>&nbsp;
<?php if ($hid > 0) { ?>
		<button class="buttonb" onclick="self.location='/m/patient/patient.php?time_type=order_date&sort=order_date&show=today&come=0'" title="查看今日未到需回访病人">回访病人</button>&nbsp;
	<?php if ($debug_mode || $username == "admin" || $uinfo["part_id"] == 3) { ?>
		<button class="buttonb" onclick="self.location='/m/patient/patient.php?list_huifang=1'" title="查看我最近回访过的病人">我的回访</button>&nbsp;
	<?php } ?>
	<?php if ($list_power) { ?>
		<button class="buttonb" onclick="self.location='?list=1'" title="查看汇总页面">汇总页面</button>&nbsp;
	<?php } ?>
	<?php if ($debug_mode || $uinfo["jiuzhen_view"]) { ?>
		<button class="buttonb" onclick="self.location='/m/report/jiuzhen.php'" title="查看就诊表">就诊表</button>&nbsp;
	<?php } ?>
<?php } ?>
	</div>
<?php } else if ($hid > 0) { ?>
	<div style="margin-top:20px;">
		当前医院：<b><?php echo $db->query("select name from hospital where id=$hid limit 1", 1, "name"); ?></b>&nbsp;&nbsp;
	<?php if ($debug_mode || $uinfo["jiuzhen_view"]) { ?>
		<button class="buttonb" onclick="self.location='/m/report/jiuzhen.php'" title="查看就诊表">就诊表</button>&nbsp;
	<?php } ?>
	</div>
<?php } else { ?>
	<div style="margin-top:20px;">没有为您分配医院，请联系上级管理人员处理。</div>
<?php } ?>
</div>

<?php if ($hid > 0) { ?>
<?php
// 电话回访组的提醒:
if ($uinfo["part_id"] == 12 && @in_array("huifang", $guahao_config)) {
	$hf_str = '电话回访客服：今日没有提醒数据。';
	$ht = date("Ymd");
	$p_table = "patient_".$hid;
	if ($uinfo["part_admin"]) {  //电话回访组长
		$count = $db->query("select count(*) as c from $p_table where huifang_nexttime='$ht'", 1, "c");
		if ($count > 0) {
			$hf_link = "/m/patient/patient.php?hf_time=$ht";
			$hf_str = '<a href="'.$hf_link.'">提醒：本组今日共有 <b>'.$count.'</b> 位病人需要回访 (由回访提醒时间查询) 点击查看详情</a>';
		}
	} else { //组员
		$count = $db->query("select count(*) as c from $p_table where huifang_nexttime='$ht' and huifang_kf='$realname'", 1, "c");
		if ($count > 0) {
			$names = $db->query("select name from $p_table where huifang_nexttime='$ht' and huifang_kf='$realname' order by id desc limit 3", "", "name");
			$hf_link = "/m/patient/patient.php?hf_time=$ht";
			$hf_str = '<a href="'.$hf_link.'">提醒：今日有 '.implode("、", $names).' 等 <b>'.$count.'</b> 位病人需要回访 (由回访提醒时间查询) 点击查看详情</a>';
		}
	}
	if ($hf_str) {
		echo '<br><div class="huifang_tixing">'.$hf_str.'</div><br><br>';
	}
}
?>
<?php } ?>


<?php if ($h_name_show) { ?>
<!-- 多家医院科室汇总 -->
<div style="padding:10px 0 0 40px; font-weight:bold;"><?php echo $h_name_show; ?></div>
<?php } ?>

<?php if ($sel_hid || $hid > 0) { ?>
<div style="padding:0px 0 0 30px;">
<!-- 选择医院后 -->
<?php
if ($index_data["总"]) {
	$d = $index_data["总"];
?>
	<div style="float:left; width:300px; padding:10px 0 0 10px;">
	<table width="100%" class="edit">
		<tr>
			<td class="head">总数据</td>
		</tr>
		<tr onmouseover="mi(this)" onmouseout="mo(this)">
			<td class="idata">今日：预约 <?php echo a4($d["今日预约"]); ?> 预到 <?php echo a4($d["今日预到"]); ?> 实到 <?php echo a4($d["今日实到"]); ?></td>
		</tr>
		<tr onmouseover="mi(this)" onmouseout="mo(this)">
			<td class="idata" style="color:silver">月比：预约 <?php echo a4($d["月比预约"]); ?> 预到 <?php echo a4($d["月比预到"]); ?> 实到 <?php echo a4($d["月比实到"]); ?></td>
		</tr>
		<tr onmouseover="mi(this)" onmouseout="mo(this)">
			<td class="idata" style="color:silver">周比：预约 <?php echo a4($d["周比预约"]); ?> 预到 <?php echo a4($d["周比预到"]); ?> 实到 <?php echo a4($d["周比实到"]); ?></td>
		</tr>
		<tr onmouseover="mi(this)" onmouseout="mo(this)">
			<td class="idata">昨日：预约 <?php echo a4($d["昨日预约"]); ?> 预到 <?php echo a4($d["昨日预到"]); ?> 实到 <?php echo a4($d["昨日实到"]); ?></td>
		</tr>
		<tr onmouseover="mi(this)" onmouseout="mo(this)">
			<td class="idata">本月：预约 <?php echo a4($d["本月预约"]); ?> 预到 <?php echo a4($d["本月预到"]); ?> 实到 <?php echo a4($d["本月实到"]); ?></td>
		</tr>
		<tr onmouseover="mi(this)" onmouseout="mo(this)">
			<td class="idata" style="color:silver">同比：预约 <?php echo a4($d["同比预约"]); ?> 预到 <?php echo a4($d["同比预到"]); ?> 实到 <?php echo a4($d["同比实到"]); ?></td>
		</tr>
		<tr onmouseover="mi(this)" onmouseout="mo(this)">
			<td class="idata">上月：预约 <?php echo a4($d["上月预约"]); ?> 预到 <?php echo a4($d["上月预到"]); ?> 实到 <?php echo a4($d["上月实到"]); ?></td>
		</tr>
	</table>
	</div>
<?php } ?>


<!-- 网络统计数据 -->
<?php
if ($index_data["网络"]) {
	$d = $index_data["网络"];
?>
	<div style="float:left; width:300px; padding:10px 0 0 10px;">
	<table width="100%" class="edit">
		<tr>
			<td class="head">网络</td>
		</tr>
		<tr onmouseover="mi(this)" onmouseout="mo(this)">
			<td class="idata">今日：预约 <?php echo a4($d["今日预约"]); ?> 预到 <?php echo a4($d["今日预到"]); ?> 实到 <?php echo a4($d["今日实到"]); ?></td>
		</tr>
		<tr onmouseover="mi(this)" onmouseout="mo(this)">
			<td class="idata" style="color:silver">月比：预约 <?php echo a4($d["月比预约"]); ?> 预到 <?php echo a4($d["月比预到"]); ?> 实到 <?php echo a4($d["月比实到"]); ?></td>
		</tr>
		<tr onmouseover="mi(this)" onmouseout="mo(this)">
			<td class="idata" style="color:silver">周比：预约 <?php echo a4($d["周比预约"]); ?> 预到 <?php echo a4($d["周比预到"]); ?> 实到 <?php echo a4($d["周比实到"]); ?></td>
		</tr>
		<tr onmouseover="mi(this)" onmouseout="mo(this)">
			<td class="idata">昨日：预约 <?php echo a4($d["昨日预约"]); ?> 预到 <?php echo a4($d["昨日预到"]); ?> 实到 <?php echo a4($d["昨日实到"]); ?></td>
		</tr>
		<tr onmouseover="mi(this)" onmouseout="mo(this)">
			<td class="idata">本月：预约 <?php echo a4($d["本月预约"]); ?> 预到 <?php echo a4($d["本月预到"]); ?> 实到 <?php echo a4($d["本月实到"]); ?></td>
		</tr>
		<tr onmouseover="mi(this)" onmouseout="mo(this)">
			<td class="idata" style="color:silver">同比：预约 <?php echo a4($d["同比预约"]); ?> 预到 <?php echo a4($d["同比预到"]); ?> 实到 <?php echo a4($d["同比实到"]); ?></td>
		</tr>
		<tr onmouseover="mi(this)" onmouseout="mo(this)">
			<td class="idata">上月：预约 <?php echo a4($d["上月预约"]); ?> 预到 <?php echo a4($d["上月预到"]); ?> 实到 <?php echo a4($d["上月实到"]); ?></td>
		</tr>
	</table>
	</div>
<?php } ?>


<?php
if ($index_data["电话"]) {
	$d = $index_data["电话"];
?>
	<div style="float:left; width:300px; padding:10px 0 0 10px;">
	<table width="100%" class="edit">
		<tr>
			<td class="head">电话</td>
		</tr>
		<tr onmouseover="mi(this)" onmouseout="mo(this)">
			<td class="idata">今日：预约 <?php echo a4($d["今日预约"]); ?> 预到 <?php echo a4($d["今日预到"]); ?> 实到 <?php echo a4($d["今日实到"]); ?></td>
		</tr>
		<tr onmouseover="mi(this)" onmouseout="mo(this)">
			<td class="idata" style="color:silver">月比：预约 <?php echo a4($d["月比预约"]); ?> 预到 <?php echo a4($d["月比预到"]); ?> 实到 <?php echo a4($d["月比实到"]); ?></td>
		</tr>
		<tr onmouseover="mi(this)" onmouseout="mo(this)">
			<td class="idata" style="color:silver">周比：预约 <?php echo a4($d["周比预约"]); ?> 预到 <?php echo a4($d["周比预到"]); ?> 实到 <?php echo a4($d["周比实到"]); ?></td>
		</tr>
		<tr onmouseover="mi(this)" onmouseout="mo(this)">
			<td class="idata">昨日：预约 <?php echo a4($d["昨日预约"]); ?> 预到 <?php echo a4($d["昨日预到"]); ?> 实到 <?php echo a4($d["昨日实到"]); ?></td>
		</tr>
		<tr onmouseover="mi(this)" onmouseout="mo(this)">
			<td class="idata">本月：预约 <?php echo a4($d["本月预约"]); ?> 预到 <?php echo a4($d["本月预到"]); ?> 实到 <?php echo a4($d["本月实到"]); ?></td>
		</tr>
		<tr onmouseover="mi(this)" onmouseout="mo(this)">
			<td class="idata" style="color:silver">同比：预约 <?php echo a4($d["同比预约"]); ?> 预到 <?php echo a4($d["同比预到"]); ?> 实到 <?php echo a4($d["同比实到"]); ?></td>
		</tr>
		<tr onmouseover="mi(this)" onmouseout="mo(this)">
			<td class="idata">上月：预约 <?php echo a4($d["上月预约"]); ?> 预到 <?php echo a4($d["上月预到"]); ?> 实到 <?php echo a4($d["上月实到"]); ?></td>
		</tr>
	</table>
	</div>
<?php } ?>

	<div class="clear"></div>

<?php
$u_power_arr = explode(",", $uinfo["data_power"]);
$index_module_arr = $db->query("select name from index_module_set where isshow=1 order by sort desc, id asc");
foreach ($index_module_arr as $z) {
	if ($debug_mode || in_array($z["name"], $u_power_arr)) {
		$d = $index_data[$z["name"]];
?>
	<div style="float:left; width:300px; padding:10px 0 0 10px;">
	<table width="100%" class="edit">
		<tr>
			<td class="head"><?php echo $z["name"]; ?></td>
		</tr>
		<tr onmouseover="mi(this)" onmouseout="mo(this)">
			<td class="idata">今日：预约 <?php echo a4($d["今日预约"]); ?> 预到 <?php echo a4($d["今日预到"]); ?> 实到 <?php echo a4($d["今日实到"]); ?></td>
		</tr>
		<tr onmouseover="mi(this)" onmouseout="mo(this)">
			<td class="idata">昨日：预约 <?php echo a4($d["昨日预约"]); ?> 预到 <?php echo a4($d["昨日预到"]); ?> 实到 <?php echo a4($d["昨日实到"]); ?></td>
		</tr>
		<tr onmouseover="mi(this)" onmouseout="mo(this)">
			<td class="idata">本月：预约 <?php echo a4($d["本月预约"]); ?> 预到 <?php echo a4($d["本月预到"]); ?> 实到 <?php echo a4($d["本月实到"]); ?></td>
		</tr>
		<tr onmouseover="mi(this)" onmouseout="mo(this)">
			<td class="idata">上月：预约 <?php echo a4($d["上月预约"]); ?> 预到 <?php echo a4($d["上月预到"]); ?> 实到 <?php echo a4($d["上月实到"]); ?></td>
		</tr>
	</table>
	</div>
<?php
	}
}
?>


	<div class="clear"></div>

	<!-- 注释 -->
	<div style="padding-top:20px; padding-left:20px;">
		* <b>月比</b>：上个月和今天同一号的数据。比如，今天是4月5日，则月比就是3月5日的数据<br>
		* <b>周比</b>：上周同一天的数据。比如今天是周三，则周比就是上周三的数据<br>
		* <b>同比</b>：上个月的同期数据。比如，今天是4月5日，则同比就是3月1日至3月5日这段时间的数据<br>
	</div>

</div>


<?php } else { ?>

	<!-- <div style="padding:10px 0 0 50px; color:gray;">(请先选择医院)</div> -->

<?php } ?>

<?php if ($uinfo["ukey_sn"] == '') { ?>
<div style="padding-top:20px; padding-left:50px;">
	<input type="submit" onclick="location='/m/set_ukey.php'" class="submit" value="自助绑定uKey">
</div>
<?php } ?>

<div style="padding:10px 0 0 50px; color:#CDCDCD">* 页面执行时间：<?php echo round(now() - $pagebegintime, 4); ?>s  <?php echo $log_time1." ".$log_time2; ?></div>

</body>
</html>