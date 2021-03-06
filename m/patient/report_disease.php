<?php
/*
// - 功能说明 : 客服报表 按病种
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2011-04-11 16:38
*/
require "../../core/core.php";
include "../../res/chart/FusionCharts_Gen.php";
set_time_limit(0);
$table = "patient_".$hid;
$h_name = $db->query("select name from hospital where id=$hid limit 1", 1, "name");

// 病种列表:
$disease_list = $db->query("select id,name from disease where hospital_id='$hid' order by sort desc,sort2 desc, id asc", "id", "name");

//$web_kefu_list = $db->query("select id, realname from sys_admin where concat(',',hospitals,',') like '%{$hid}%' and part_id=2 order by binary name asc", "id", "realname");
//$tel_kefu_list = $db->query("select id, realname from sys_admin where concat(',',hospitals,',') like '%{$hid}%' and part_id=3 order by binary name asc", "id", "realname");
//$kefu_list = array_merge($web_kefu_list, $tel_kefu_list);

//if ($debug_mode) {
//	echo "<pre>";
//	print_r($web_kefu_list);
//	print_r($tel_kefu_list);
//	print_r($kefu_list);
//	exit;
//}

$res_type_array = array(1 => "预约", 2 => "预计到院", 3 => "已到院");


// 默认时间:
if (!isset($_GET["btime"])) {
	$_GET["btime"] = date("Y-m-01");
	$_GET["etime"] = date("Y-m-d", strtotime("+1 month", strtotime($_GET["btime"])) - 1);
}

// 默认类型:
if (!isset($_GET["res_type"])) {
	$_GET["res_type"] = 1;
}


$op = $_GET["op"];

// 处理时间:
if ($op == "show") {
	$where = array();

	$tb = strtotime($_GET["btime"]." 0:0:0");
	$te = strtotime($_GET["etime"]." 23:59:59");

	if ($_GET["res_type"] == 1) {
		$where[] = "addtime>=".$tb." and addtime<=".$te;
	} else if ($_GET["res_type"] == 2) {
		$where[] = "order_date>=".$tb." and order_date<=".$te;
	} else {
		$where[] = "order_date>=".$tb." and order_date<=".$te." and status=1";
	}

	if (isset($_GET["disease"])) {
		$disease = implode(",", $_GET["disease"]);
		$where[] = "disease_id in (".$disease.")";
	}

	/*
	if (isset($_GET["kefu"])) {
		$run_kefu = $_GET["kefu"];
	} else {
		$run_kefu = $kefu_list;
	}
	foreach ($run_kefu as $k => $v) {
		if (trim($v) == '') unset($run_kefu[$k]);
	}
	*/

	$sqlwhere = '';
	if (count($where) > 0) {
		$sqlwhere = "where ".implode(" and ", $where);
	}


	/*
	// 这个的效率真的很差呀:
	$rs = array();
	foreach ($run_kefu as $kf) {
		foreach ($disease_list as $did => $dname) {
			$rs[$kf][$did] = $db->query("select count(id) as c from $table where author='$kf' and concat(',',disease_id, ',') like '%,{$did},%' $sqlwhere", 1, "c");
		}
	}
	*/

	// 改进的: 一次性读取数据:
	$datas = $db->query("select part_id,disease_id,author from $table $sqlwhere");
	if ($debug_mode) {
		//echo $db->sql;
	}

	// 客服数据：
	$kefu_all = $kefu_part = array();
	foreach ($datas as $v) {
		$part = $v["part_id"];
		$name = $v["author"];
		if ($name != '') {
			if (!@in_array($name, $kefu_part[$part])) {
				$kefu_part[$part][] = $name;
			}
			if (!@in_array($name, $kefu_all)) {
				$kefu_all[] = $name;
			}
		}
	}
	asort($kefu_all);


	// 按客服进行病种叠加计算:
	$rs = array();
	foreach ($kefu_all as $kf) {
		foreach ($datas as $v) {
			if ($v["author"] == $kf) {
				$dis_s = explode(",", $v["disease_id"]);
				foreach ($dis_s as $did) {
					$did = intval($did);
					if ($did > 0) {
						$rs[$kf][$did] = intval($rs[$kf][$did]) + 1;
					}
				}
			}
		}
	}



	// 各行总计:
	$ch = $cl = array();
	foreach ($disease_list as $did => $dname) {
		foreach ($kefu_all as $kf) {
			$ch[$did] = intval($ch[$did]) + intval($rs[$kf][$did]);
		}
	}
	// 各列总计:
	foreach ($kefu_all as $kf) {
		$cl[$kf] = @array_sum($rs[$kf]);
	}
	$cl["all"] = @array_sum($ch);

	// 占总病种的百分数
	$bb = $bba = array();
	foreach ($kefu_all as $kf) {
		foreach ($disease_list as $did => $dname) {
			$bb[$kf][$did] = @round(intval($rs[$kf][$did]) / intval($cl[$kf]) * 100, 1);
		}
	}
	//总计:
	foreach ($disease_list as $did => $dname) {
		$bba[$did] = @round(intval($ch[$did]) / intval($cl["all"]) * 100, 1);
	}

	// 占总客服的百分数
	$bk = $bka = array();
	foreach ($kefu_all as $kf) {
		foreach ($disease_list as $did => $dname) {
			$bk[$kf][$did] = @round(intval($rs[$kf][$did]) / intval($ch[$did]) * 100, 1);
		}
	}
	//总计:
	foreach ($kefu_all as $kf) {
		$bka[$kf] = @round(intval($cl[$kf]) / intval($cl["all"]) * 100, 1);
	}



	// 病种百分比:
	// 由于图表显示不宜过多,合并掉一些:
	$bba_s = array();
	$bbb = $bba;
	arsort($bbb);
	//$bba_s = array_slice($bbb, 0, 14, true);
	$bba_s = $bbb;
	foreach ($bba_s as $did => $per) {
		if ($per < 3 || $disease_list[$did] == "其它") {
			unset($bba_s[$did]);
		}
	}
	// 重新计算其它是多少:
	$qita_per = 100 - array_sum($bba_s);
	$bba_s["qita"] = $qita_per;

	$FC1 = new FusionCharts("Pie3D","600","600", "chart_1", 1);
	$FC1->setSWFPath("/res/chart/");
	$FC1->setChartParams("shownames=1;showPercentValues=0;showValues=0;showLabels=0;baseFontSize=12;outCnvBaseFontSize=10;labelDistance=5;");
	foreach ($bba_s as $did => $per) {
		if ($did == "qita") {
			$dname = "其它";
		} else {
			$dname = $disease_list[$did];
		}
		$FC1->addChartData($per, "name=".$dname."：".$per."%;hoverText=".$dname);
	}

	// 客服百分比:
	$FC2 = new FusionCharts("Pie3D","600","600", "chart_2", 1);
	$FC2->setSWFPath("/res/chart/");
	$FC2->setChartParams("shownames=1;showPercentValues=0;showValues=0;showLabels=0;baseFontSize=12;outCnvBaseFontSize=10;");
	foreach ($kefu_all as $kf) {
		if (intval($cl[$kf]) > 0) {
			$FC2->addChartData(intval($cl[$kf]),"name=".$kf."：".$bka[$kf]."%;hoverText=".$kf."：".intval($cl[$kf]));
		}
	}

}

$title = '疾病报表';

// 时间定义
// 昨天
$yesterday_begin = strtotime("-1 day");
// 本月
$this_month_begin = mktime(0,0,0,date("m"), 1);
$this_month_end = strtotime("+1 month", $this_month_begin) - 1;
// 上个月
$last_month_end = $this_month_begin - 1;
$last_month_begin = strtotime("-1 month", $this_month_begin);
//今年
$this_year_begin = mktime(0,0,0,1,1);
$this_year_end = strtotime("+1 year", $this_year_begin) - 1;
// 最近一个月
$near_1_month_begin = strtotime("-1 month");
// 最近三个月
$near_3_month_begin = strtotime("-3 month");
// 最近一年
$near_1_year_begin = strtotime("-12 month");

?>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<script src='/res/chart/FusionCharts.js' language='javascript'></script>
<style>
#tiaojian {margin:10px 0 0 30px; }
form {display:inline; }

#result {margin-left:50px; }
.h_name {font-weight:bold; margin-top:20px; }
.h_kf {margin-left:20px; }
.kf_li {border-bottom:0px dotted silver; }

s {width: 20px; text-align:center; text-decoration:none; }
.dh td, .dt td, .ds td {border:1px solid #E4E4E4; padding:4px 3px 2px 3px; text-align:center; }
.dh td {font-weight:bold; background:#EFF8F8; }
.ds td {background:#FFF2EC; }

u {text-decoration:none; color:#FF8888; }
i {font-style:normal !important;  color:#96CBCB; }

.w400 {width:400px }
.w800 {width:800px; margin-top:6px; }
.hr {border:0; margin:0; padding:0; height:3px; line-height:0; font-size:0; background-color:red; color:white; border-top:1px solid silver; }

#chart_1_border, #chart_2_border {height:300px; overflow:hidden; border:2px solid #EBEBEB; width:600px; }
#chart_1, #chart_2 {margin-top:-150px; }
</style>
<script type="text/javascript">
function write_dt(da, db) {
	byid("begin_time").value = da;
	byid("end_time").value = db;
}
function check_data(form) {
	byid("submit_button_1").value = '提交中';
	byid("submit_button_1").disabled = true;
}

function m1(o) {
	o.style.backgroundColor = "#D8EBEB";
}
function m2(o) {
	o.style.backgroundColor = "";
}
</script>
</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $h_name." ".$title; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"><button onclick="history.back()" class="button">返回</button></div>
</div>
<!-- 头部 end -->

<div class="space"></div>
<form method="GET" onsubmit="return check_data(this)">
<table width="100%" style="background:#FAFCFC;">
	<tr>
		<td style="padding:5px 5px 5px 10px; line-height:180%; border:2px solid #D8EBEB;">
			<b>时间条件：</b>
			<span id="t_day">
				&nbsp; 起始时间：<input name="btime" id="begin_time" class="input" style="width:100px" value="<?php echo $_GET["btime"]; ?>"> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'begin_time',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="选择时间">
				&nbsp; 终止时间：<input name="etime" id="end_time" class="input" style="width:100px" value="<?php echo $_GET["etime"]; ?>"> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'end_time',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="选择时间">
				&nbsp; 速填：
				<a href="javascript:write_dt('<?php echo date("Y-m-d"); ?>','<?php echo date("Y-m-d"); ?>')">今天</a>
				<a href="javascript:write_dt('<?php echo date("Y-m-d", $yesterday_begin); ?>','<?php echo date("Y-m-d", $yesterday_begin); ?>')">昨天</a>
				<a href="javascript:write_dt('<?php echo date("Y-m-d", $this_month_begin); ?>','<?php echo date("Y-m-d", $this_month_end); ?>')">本月</a>
				<a href="javascript:write_dt('<?php echo date("Y-m-d", $last_month_begin); ?>','<?php echo date("Y-m-d", $last_month_end); ?>')">上月</a>&nbsp; &nbsp;
			</span>

			<!--  -->
			<b>结果类型：</b>
			<select name="res_type" class="combo">
				<option value="" style="color:gray">-请选择-</option>
				<?php echo list_option($res_type_array, "_key_", "_value_", $_GET["res_type"]); ?>
			</select>&nbsp;
		</td>
		<td width="150" align="center" style="border:2px solid #D8EBEB;">
			<input id="submit_button_1" type="submit" class="button" value="提交">
			<input type="hidden" name="op" value="show">
		</td>
	</tr>
</table>
</form>


<?php if ($op == "show") { ?>
<div style="padding:15px 0 8px 12px;">
	<u>这种颜色的百分比为: 病种百分比</u> &nbsp;&nbsp;&nbsp;&nbsp; <i>这种颜色的百分比为: 客服百分比</i>
</div>
<table width="100%"  style="border:2px solid #DFDFDF; background:#FAFCFC;">
	<tr class="dh">
		<td width="10%">病种</td>
<?php foreach ($kefu_all as $v) { ?>
		<td><?php echo $v; ?></td>
<?php } ?>
		<td>总计</td>
	</tr>

<?php foreach ($disease_list as $k => $v) { ?>
	<tr class="dt">
		<td><?php echo $v; ?></td>
<?php foreach ($kefu_all as $kf) { ?>
		<td onmouseover="m1(this)" onmouseout="m2(this)"><?php echo "<b>".intval($rs[$kf][$k])."</b><br><u>".$bb[$kf][$k]."%</u>&nbsp; <i>".$bk[$kf][$k]."%</i>"; ?></td>
<?php } ?>
		<td><?php echo "<b>".intval($ch[$k])."</b><br><u>".$bba[$k]."%</u>"; ?></td>
	</tr>
<?php } ?>

	<tr class="ds">
		<td>总计：</td>
<?php foreach ($kefu_all as $kf) { ?>
		<td><?php echo "<b>".intval($cl[$kf])."</b><br><i>".$bka[$kf]."%</i>"; ?></td>
<?php } ?>
		<td><?php echo intval($cl["all"]); ?></td>
	</tr>

</table>
<br>
<u>&nbsp;&nbsp;病种百分比: 该数据占病种总数据的百分比(竖向)</u><br>
<i>&nbsp;&nbsp;客服百分比: 该数据占客服总数据的百分比(横向)</i><br>
<br>
<br>
<br>

<!-- 显示百分比饼图 -->
<div style="text-align:center">

	<div id="chart_1_border"><?php $FC1->renderChart(); ?></div>
	<div class="w800" style="text-align:center"><b>病种百分比 (小于3%的病种已归入其它)</b></div>
	<br>

	<div id="chart_2_border"><?php $FC2->renderChart(); ?></div>
	<div class="w800" style="text-align:center"><b>客服百分比</b></div>
</div>

<br>
<br>
<br>

<?php } ?>

</body>
</html>