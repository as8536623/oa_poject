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
<script src="/res/datejs/picker.js" language="javascript"></script>
<script src="/res/jquery.min.js" language="javascript"></script>

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

//获取资源数据
function getsd(){
	
	$.ajax({
		url:"/m/source_load_data.php",
		type:"get",
		data:{
			op:"show",
			smb:$("#smb").val(),
			sme:$("#sme").val(),
			},
		success: function(res){
			$("#source_data").html("");
			var data = res.split(',');
			for(var i=0;i<data.length;i++){
				$("#source_data").append(data[i]);
				}
			//$("#source_data").html(res);
			}
			
		})
	}
getsd();
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
		<select name="hospital_id" id="hospital_id" class="combo" onChange="if (this.value!='-1') location='?do=change&hospital_id='+this.value" style="width:200px;">
			<option value="" style="color:gray">--请选择--</option>
<?php foreach ($options as $v) { ?>
			<option value="<?php echo $v[0]; ?>" <?php echo ($sel_hid == $v[0]  ? ' selected' : ''); ?> style="<?php echo $v[2]; ?>"><?php echo $v[1].($sel_hid == $v[0] ? ' *' : ''); ?></option>
<?php } ?>
		</select>&nbsp;
		<button class="button" onClick="hgo('up');">上</button>&nbsp;
		<button class="button" onClick="hgo('down');">下</button>&nbsp;
<?php if ($hid > 0) { ?>	
	<?php if ($list_power) { ?>
		<button class="buttonb" onClick="self.location='?list=1'" title="查看汇总页面">汇总页面</button>&nbsp;
	<?php } ?>
<?php } ?>
	</div>
<?php } else if ($hid > 0) { ?>
	<div style="margin-top:20px;">
		当前医院：<b><?php echo $db->query("select name from hospital where id=$hid limit 1", 1, "name"); ?></b>&nbsp;&nbsp;
	</div>
<?php } else { ?>
	<div style="margin-top:20px;">没有为您分配医院，请联系上级管理人员处理。</div>
<?php } ?>
</div>

<?php if ($hid > 0) { ?>
<?php
// 电话回访组的提醒:
if ($uinfo["part_id"] == 2 || $uinfo["part_id"] == 209 || $uinfo["character_id"] == 71 || $username == "admin" || $uinfo["character_id"] == 73 && @in_array("huifang", $guahao_config)) {
	$hf_str = '今日没有回访提醒';
	$hf_str1 = '明日没有回访提醒';
	$hf_str2 = '后天没有回访提醒';
	$ht = date("Y-m-d");
	$ht1 = date("Y-m-d",strtotime("+1 day"));
	$ht2 = date("Y-m-d",strtotime("+2 day"));
	$p_table = "ku_list";
	if ($uinfo["character_id"] == 71 || $username == "admin" || $username == "林飞渐") {  //回访组长
		
		//今日数据
		$count = $db->query("select count(*) as c from $p_table where huifang_nexttime='$ht' and hid='$hid'", 1, "c");
		if ($count > 0) {
			$hf_link = "/m/ziliaoku/ku_list1.php?hf_time=$ht";
			$hf_str = '<a href="'.$hf_link.'">今日回访提醒： <b>'.$count.'</b> 位</a>';
		}
		//明日数据
		$count1 = $db->query("select count(*) as c1 from $p_table where huifang_nexttime='$ht1' and hid='$hid'", 1, "c1");
		if ($count1 > 0) {
			$hf_link1 = "/m/ziliaoku/ku_list1.php?hf_time=$ht1";
			$hf_str1 = '<a href="'.$hf_link1.'">明日回访提醒： <b>'.$count1.'</b> 位</a>';
		}
		//后天数据
		$count2 = $db->query("select count(*) as c2 from $p_table where huifang_nexttime='$ht2' and hid='$hid'", 1, "c2");
		if ($count2 > 0) {
			$hf_link2 = "/m/ziliaoku/ku_list1.php?hf_time=$ht2";
			$hf_str2 = '<a href="'.$hf_link2.'">后天回访提醒： <b>'.$count2.'</b> 位</a>';
		}

		
	} else { //组员

		//今日数据
		$count = $db->query("select count(*) as c from $p_table where huifang_nexttime='$ht' and (u_name='$realname' or w_name='$realname')  and hid='$hid'", 1, "c");
		if ($count > 0) {
			$hf_link = "/m/ziliaoku/ku_list1.php?hf_time=$ht";
			$hf_str = '<a href="'.$hf_link.'">今日回访提醒： <b>'.$count.'</b> 位</a>';
		}
		//明日数据
		$count1 = $db->query("select count(*) as c1 from $p_table where huifang_nexttime='$ht1' and (u_name='$realname' or w_name='$realname')  and hid='$hid'", 1, "c1");
		if ($count1 > 0) {
			$hf_link1 = "/m/ziliaoku/ku_list1.php?hf_time=$ht1";
			$hf_str1 = '<a href="'.$hf_link1.'">明日回访提醒： <b>'.$count1.'</b> 位</a>';
		}
		//后天数据
		$count2 = $db->query("select count(*) as c2 from $p_table where huifang_nexttime='$ht2' and (u_name='$realname' or w_name='$realname')  and hid='$hid'", 1, "c2");
		if ($count2 > 0) {
			$hf_link2 = "/m/ziliaoku/ku_list1.php?hf_time=$ht2";
			$hf_str2 = '<a href="'.$hf_link2.'">后天回访提醒： <b>'.$count2.'</b> 位</a>';
		}
	}
	if ($hf_str || $hf_str1 || $hf_str2) {
		echo '<br><div class="huifang_tixing">'.$hf_str.'</div><div class="huifang_tixing">'.$hf_str1.'</div><div class="huifang_tixing">'.$hf_str2.'</div><br>';
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
	<div style="float:left; width:340px; padding:10px 0 0 10px;">
	<table width="100%" class="edit">
		<tr>
			<td class="head">总数据</td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">今日：咨询 <?php echo a4($d["今日咨询"]); ?> 预约 <?php echo a4($d["今日预约"]); ?> 预到 <?php echo a4($d["今日预到"]); ?> 实到 <?php echo a4($d["今日实到"]); ?></td>
		</tr>
		
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">昨日：咨询 <?php echo a4($d["昨日咨询"]); ?> 预约 <?php echo a4($d["昨日预约"]); ?> 预到 <?php echo a4($d["昨日预到"]); ?> 实到 <?php echo a4($d["昨日实到"]); ?></td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">本月：咨询 <?php echo a4($d["本月咨询"]); ?> 预约 <?php echo a4($d["本月预约"]); ?> 预到 <?php echo a4($d["本月预到"]); ?> 实到 <?php echo a4($d["本月实到"]); ?></td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata" style="color:silver">同比：咨询 <?php echo a4($d["同比咨询"]); ?> 预约 <?php echo a4($d["同比预约"]); ?> 预到 <?php echo a4($d["同比预到"]); ?> 实到 <?php echo a4($d["同比实到"]); ?></td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">上月：咨询 <?php echo a4($d["上月咨询"]); ?> 预约 <?php echo a4($d["上月预约"]); ?> 预到 <?php echo a4($d["上月预到"]); ?> 实到 <?php echo a4($d["上月实到"]); ?></td>
		</tr>
	</table>
	</div>
    
    <!--导医没有权限查看咨询员-->
    <?php if($uinfo["part_id"] != 4){?>
    <!--个人数据-->
    <div style="float:left; width:340px; padding:10px 0 0 10px;">
	<table width="100%" class="edit">
		<tr>
			<td class="head">本月个人数据</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
		</tr>
        <?php 
			$author_arr = $db->query("select realname from sys_admin where isshow = 1 and part_id in (2,209) and hospitals like '%".$hid."%'");
			if($author_arr){
				foreach($author_arr as $n){
		?>
        <tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata" align="center"><?php echo $n["realname"]; ?>&nbsp;</td>
            <td>咨询 <?php echo a4($d[$n["realname"]."咨询"]); ?></td>
            <td>预约 <?php echo a4($d[$n["realname"]."预约"]); ?></td>
            <td>预到 <?php echo a4($d[$n["realname"]."预到"]); ?></td>
            <td>实到 <?php echo a4($d[$n["realname"]."实到"]); ?></td>
		</tr>
        <?php
				}
			}
		?>
	</table>
	</div>
    
    
    <!--管理员查看数据-->
    
    <!--资源数据-->
   <!-- <div style="float:left; width:380px; padding:10px 0 0 10px;">
    
	<table width="100%" class="edit">
		<tr>
			<td class="head">资源数据</td>
            <td colspan="2">
            	<input name="smb" id="smb" class="input" style="width:76px" onClick="picker({el:'smb',dateFmt:'yyyy-MM-dd',maxDate:'#F{$dp.$D(\'sme\')}' })" value="" placeholder="开始时间">
                <input name="sme" id="sme" class="input" style="width:76px" onClick="picker({el:'sme',dateFmt:'yyyy-MM-dd',minDate:'#F{$dp.$D(\'smb\')}' })" value="" placeholder="结束时间">
                <input type="submit" value="查询" id="smSubmit" onClick="getsd();">
            
            </td>
            <td></td>
		</tr>
    </table>
    <table width="100%" class="edit" id="source_data">
        
        
	</table>
	</div>-->
    <?php } ?> 
    
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
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">今日：预约 <?php echo a4($d["今日预约"]); ?> 预到 <?php echo a4($d["今日预到"]); ?> 实到 <?php echo a4($d["今日实到"]); ?></td>
		</tr>
		
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">昨日：预约 <?php echo a4($d["昨日预约"]); ?> 预到 <?php echo a4($d["昨日预到"]); ?> 实到 <?php echo a4($d["昨日实到"]); ?></td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">本月：预约 <?php echo a4($d["本月预约"]); ?> 预到 <?php echo a4($d["本月预到"]); ?> 实到 <?php echo a4($d["本月实到"]); ?></td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata" style="color:silver">同比：预约 <?php echo a4($d["同比预约"]); ?> 预到 <?php echo a4($d["同比预到"]); ?> 实到 <?php echo a4($d["同比实到"]); ?></td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
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
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">今日：预约 <?php echo a4($d["今日预约"]); ?> 预到 <?php echo a4($d["今日预到"]); ?> 实到 <?php echo a4($d["今日实到"]); ?></td>
		</tr>
		
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">昨日：预约 <?php echo a4($d["昨日预约"]); ?> 预到 <?php echo a4($d["昨日预到"]); ?> 实到 <?php echo a4($d["昨日实到"]); ?></td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">本月：预约 <?php echo a4($d["本月预约"]); ?> 预到 <?php echo a4($d["本月预到"]); ?> 实到 <?php echo a4($d["本月实到"]); ?></td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata" style="color:silver">同比：预约 <?php echo a4($d["同比预约"]); ?> 预到 <?php echo a4($d["同比预到"]); ?> 实到 <?php echo a4($d["同比实到"]); ?></td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
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
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">今日：预约 <?php echo a4($d["今日预约"]); ?> 预到 <?php echo a4($d["今日预到"]); ?> 实到 <?php echo a4($d["今日实到"]); ?></td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">昨日：预约 <?php echo a4($d["昨日预约"]); ?> 预到 <?php echo a4($d["昨日预到"]); ?> 实到 <?php echo a4($d["昨日实到"]); ?></td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">本月：预约 <?php echo a4($d["本月预约"]); ?> 预到 <?php echo a4($d["本月预到"]); ?> 实到 <?php echo a4($d["本月实到"]); ?></td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">上月：预约 <?php echo a4($d["上月预约"]); ?> 预到 <?php echo a4($d["上月预到"]); ?> 实到 <?php echo a4($d["上月实到"]); ?></td>
		</tr>
	</table>
	</div>
<?php
	}
}
?>


	<div class="clear"></div>
<?php
if ($index_data["总"]) {?>
	<!-- 注释 -->
	<div style="padding-top:20px; padding-left:20px;">
		* <b>同比</b>：上个月的同期数据。比如，今天是4月5日，则同比就是3月1日至3月5日这段时间的数据<br>
	</div>
<?php } ?>
	

</div>


<?php } else { ?>

	<!-- <div style="padding:10px 0 0 50px; color:gray;">(请先选择医院)</div> -->

<?php } ?>

<?php if ($uinfo["ukey_sn"] == '025') { ?>
<div style="padding-top:20px; padding-left:50px;">
	<input type="submit" onClick="location='/m/set_ukey.php'" class="submit" value="自助绑定uKey">
</div>
<?php } ?>

<div style="padding:10px 0 0 50px; color:#CDCDCD">* 页面执行时间：<?php echo round(now() - $pagebegintime, 4); ?>s  <?php echo $log_time1." ".$log_time2; ?></div>

</body>
</html>