<?php
/*
// - ����˵�� : ��������
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2011-02-28
*/
require "../../core/core.php";

set_time_limit(0);

if ($hid == 0) {
	exit_html("�Բ���û��ѡ��ҽԺ������ִ�иò�����");
}

$table = "ku_list";
$h_name = $db->query("select name from hospital where id=$hid limit 1", 1, "name");

$time_array = array("addtime"=>"�Ǽ�ʱ��");
$status_array = array("all"=>"����", "come"=>"��ԤԼ", "not"=>"δԤԼ");
$sort_array = array("addtime"=>"�Ǽ�ʱ��", "name"=>"����");
$part_array = array("pall"=>"����", "dianhua"=>"�绰", "wxqq"=>"΢��/QQ");
$depart_array = $db->query("select id,name from depart where hospital_id='$hid'", "id", "name");


$op = $_GET["op"];

// ����ʱ��:
if ($op == "show") {
	$where = "";
	$where[] = "hid = $hid";
	$time_ty = "addtime";
	if ($ty = $_GET["ty"] && array_key_exists($ty, $time_array)) {
		$time_ty = $_GET["ty"];
	}

	if ($_GET["btime"]) {
		$tb = strtotime($_GET["btime"]." 0:0:0");
		$where[] = "$time_ty>=$tb";
	}
	if ($_GET["etime"]) {
		$te = strtotime($_GET["etime"]." 23:59:59");
		$where[] = "$time_ty<$te";
	}

	if ($_GET["status"] == '') $_GET["status"] = "not";
	if ($st = $_GET["status"]) {
		if ($st != "all") {
			$where[] = ($st == "come") ? "is_yuyue=1" : "is_yuyue=0";
		}
	}

	if ($_GET["part"] == '') $_GET["part"] = "dianhua"; 
	if ($part = $_GET["part"]){
		if ($part != "pall") {
			$where[] = ($part == "dianhua") ? "LENGTH(mobile)>3" : "LENGTH(mobile)<3";
		}
	}
	
	if($uinfo["show_tel"]!=1){
		$where[] = "u_name='".$realname."'";
	}

	$sqlwhere = count($where) ? ("where ".implode(" and ", $where)) : "";
	//echo $sqlwhere;

	$sort = $_GET["sort"] ? $_GET["sort"] : "addtime";
	//echo $sort;

	$list = $db->query("select * from $table $sqlwhere order by $sort asc", "");
	
	
	

	// ���:
	$fields = array(
		"addtime"=>"�Ǽ�ʱ��",
		"u_name"=>"�ͷ�",
		"name"=>"����",
		"sex"=>"�Ա�",
		"age"=>"����",
		"vocation"=>"ְҵ",
		"area"=>"����",
		"mobile"=>"�绰����",
		"qq"=>"QQ",
		"weixin"=>"΢��",
		"jblx"=>"��������",
		"intention"=>"��������",
		"laiyuan"=>"�ؼ���",
		"media_from"=>'ý����Դ',
		"from_site"=>'������ַ',
		"zx_content"=>"��ѯ����",
		"is_yuyue"=>"�Ƿ�ԤԼ"
		
	);
	
	// ��������ת��:
	if ($fields["jblx"]) {
		$disease_id_name = $db->query("select id,name from disease", "id", "name");
	}
	

	$output_name = array();
	foreach ($list as $li) {
		$line = array();
		foreach ($fields as $k=>$v) {
			if ($k == "addtime") {
				$y = @date("Y-m-d H:i:s", $li[$k]);
			} else if ($k == "is_yuyue") {
				$y = ($li[$k] == 1) ? "��":"��";
			} else if ($k == "jblx") {
				$jblx = explode(',',$li[$k]);
				$y = $disease_id_name[$jblx[0]]." ".$jblx[1];
			} else {
				$y = $li[$k];
			}
			// �滻���лس�����Ϊ�ո�:
			$y = str_replace("\n", " ", str_replace("\r", "", $y));
			// ѹ�����߿ո�
			$y = trim($y);
			// ����ո��滻Ϊһ��:
			while (substr_count($y, "  ") > 0) {
				$y = str_replace("  ", " ", $y);
			}
			
			// ��ֵ��ʾ������Ϊռλ
			$line[] = (trim($y) == "" ? "-" : $y);
			
		}
		$output_name[] = $line;
	}
	
	include  "../../core/exportExcel.php";
	$exportexcel = exportExcel($h_name,array_values($fields),$output_name);

	
	
	

}



$title = '��������';
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
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $h_name." ".$title; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"><button onClick="history.back()" class="button">����</button></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>
<div id="tiaojian">
<span>����������</span>
<form method="GET">
	<select name="ty" class="combo">
		<option value="" style="color:gray">-ʱ������-</option>
		<?php echo list_option($time_array, "_key_", "_value_", $time_ty); ?>
	</select>&nbsp;
	<input name="btime" id="begin_time" class="input" style="width:80px" value="<?php echo $_GET["btime"] ? $_GET["btime"] : date("Y-m-01"); ?>">
	<img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'begin_time',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��">

	<input name="etime" id="end_time" class="input" style="width:80px" value="<?php echo $_GET["etime"] ? $_GET["etime"] : date("Y-m-d"); ?>">
	<img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'end_time',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��">

	<select name="status" class="combo">
		<option value="" style="color:gray">-�Ƿ�ԤԼ-</option>
		<?php echo list_option($status_array, "_key_", "_value_", $_GET["status"]); ?>
	</select>&nbsp;
	<select name="sort" class="combo">
		<option value="" style="color:gray">-�������-</option>
		<?php echo list_option($sort_array, "_key_", "_value_", $_GET["sort"]); ?>
	</select>&nbsp;
	<select name="part" class="combo">
		<option value="" style="color:gray">-��ϵ��ʽ-</option>
		<?php echo list_option($part_array, "_key_", "_value_", $_GET["part"]); ?>
	</select>&nbsp;<br>
	
	<input type="submit" class="button" value="����">
	<input type="hidden" name="op" value="show">
</form>
</div>

</body>
</html>