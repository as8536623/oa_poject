<?php
/*
// - 功能说明 : 导出病人
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2011-02-28
*/
require "../../core/core.php";

set_time_limit(0);

if ($hid == 0) {
	exit_html("对不起，没有选择医院，不能执行该操作！");
}

$table = "patient_".$hid;
$h_name = $db->query("select name from hospital where id=$hid limit 1", 1, "name");

$time_array = array("order_date"=>"来院就诊时间", "addtime"=>"预约操作时间", "djsj"=>"登记时间");
$status_array = array("all"=>"不限", "come"=>"已到", "not"=>"未到");
$sort_array = array("order_date"=>"来院就诊时间", "name"=>"名字");
$part_array = array("2"=>"网络", "3"=>"电话");
$depart_array = $db->query("select id,name from disease where hospital_id='$hid'", "id", "name");


$op = $_GET["op"];

// 处理时间:
if ($op == "show") {
	$where = "";

	
	if ($_GET["ty"]) {
		$time_ty = $_GET["ty"];
		//echo $time_ty;
	}else{
		$time_ty = "order_date";
		}

	if ($_GET["btime"]) {
		$tb = strtotime($_GET["btime"]." 0:0:0");
		$where[] = "$time_ty>=$tb";
	}
	if ($_GET["etime"]) {
		$te = strtotime($_GET["etime"]." 23:59:59");
		$where[] = "$time_ty<$te";
	}

	if ($_GET["status"] == '') $_GET["status"] = "come";
	if ($st = $_GET["status"]) {
		if ($st != "all") {
			$where[] = ($st == "come") ? "status=1" : "status!=1";
		}
	}

	if ($_GET["part"]) {
		$where[] = "part_id=".intval($_GET["part"]);
	}

	if ($_GET["depart"]) {
		$where[] = "disease_id=".intval($_GET["depart"]);
	}
	
	if($uinfo["show_tel"]!=1){
		$where[] = "author='".$realname."'";
	}

	$sqlwhere = count($where) ? ("where ".implode(" and ", $where)) : "";
	//echo $sqlwhere;

	$sort = $_GET["sort"] ? $_GET["sort"] : "order_date";


	$list = $db->query("select * from $table $sqlwhere order by $sort asc", "");


	// 输出:
	$fields = array(
		"name"=>"姓名",
		"sex"=>"性别",
		"age"=>"年龄",
		"vocation"=>"职业",
		"tel"=>"电话号码",
		"zhuanjia_num"=>"门诊号",
		"doctor"=>"接诊医生",
		"disease_id"=>"科室",
		"disease_2"=>"疾病类型",
		"content"=>"咨询内容",
		"media_from"=>"来源",
		"engine_key"=>"关键词",
		"memo"=>"备注",
		"area"=>"地区",
		"author"=>"客服",
		"order_date"=>"来院就诊时间",
		"addtime"=>"预约操作时间",
		"djsj"=>"登记时间"
	);

	// 疾病类型转换:
	if ($fields["disease_id"]) {
		$disease_id_name = $db->query("select id,name from disease", "id", "name");
	}

	$output_name = array();
	foreach ($list as $li) {
		$line = array();
		foreach ($fields as $k=>$v) {
			if ($k == "order_date" || $k == "addtime" || $k == "djsj") {
				$y = @date("Y-m-d H:i:s", $li[$k]);
			} else if ($k == "disease_id") {
				$y = $disease_id_name[$li[$k]];
			} else {
				$y = $li[$k];
			}
			// 替换所有回车换行为空格:
			$y = str_replace("\n", " ", str_replace("\r", "", $y));
			// 压缩两边空格
			$y = trim($y);
			// 多个空格替换为一个:
			while (substr_count($y, "  ") > 0) {
				$y = str_replace("  ", " ", $y);
			}
			
			// 空值显示横线作为占位
			$line[] = (trim($y) == "" ? "-" : $y);
		}
		$output_name[] = $line;
	}
	
	include  "../../core/exportExcel.php";
	$exportexcel = exportExcel($h_name,array_values($fields),$output_name);

	
	
	

}



$title = '导出病人';
?>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<style>
#tiaojian {margin:10px 0 0 30px; }
form {display:inline; }

#result {margin-left:30px; margin-top:10px; }
.h_name {font-weight:bold; margin-top:20px; }
.h_kf {margin-left:20px; }
.kf_li {border-bottom:0px dotted silver; }
</style>
</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $h_name." ".$title; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"><button onClick="history.back()" class="button">返回</button></div>
</div>
<!-- 头部 end -->

<div class="space"></div>
<div id="tiaojian">
<span>设置条件：</span>
<form method="GET">
	<select name="ty" class="combo">
		<option value="" style="color:gray">-时间类型-</option>
		<?php echo list_option($time_array, "_key_", "_value_", $time_ty); ?>
	</select>&nbsp;
	<input name="btime" id="begin_time" class="input" style="width:80px" value="<?php echo $_GET["btime"] ? $_GET["btime"] : date("Y-m-01"); ?>">
	<img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'begin_time',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="选择时间">

	<input name="etime" id="end_time" class="input" style="width:80px" value="<?php echo $_GET["etime"] ? $_GET["etime"] : date("Y-m-d"); ?>">
	<img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'end_time',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="选择时间">

	<select name="status" class="combo">
		<option value="" style="color:gray">-是否到院-</option>
		<?php echo list_option($status_array, "_key_", "_value_", $_GET["status"]); ?>
	</select>&nbsp;
	<select name="sort" class="combo">
		<option value="" style="color:gray">-结果排序-</option>
		<?php echo list_option($sort_array, "_key_", "_value_", $_GET["sort"]); ?>
	</select>&nbsp;
	<select name="part" class="combo">
		<option value="" style="color:gray">-部门-</option>
		<?php echo list_option($part_array, "_key_", "_value_", $_GET["part"]); ?>
	</select>&nbsp;
	<select name="depart" class="combo">
		<option value="" style="color:gray">-科室-</option>
		<?php echo list_option($depart_array , "_key_", "_value_", $_GET["disease_id"]); ?>
	</select>&nbsp;<br>
	
	<input type="submit" class="button" value="导出">
	<input type="hidden" name="op" value="show">
</form>
</div>

</body>
</html>