<?php
/*
// - ����˵�� : main.php
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2008-05-13 12:28
*/
define("WEE_MAIN", "1");
require "../core/core.php";
include "../core/function.lunar.php";


// -------------------- 2009-05-01 23:39
$sel_hid = $hid;
if ($_GET["do"] == 'change') {
	if (is_numeric($_GET["hospital_id"])) {
		if (!in_array($_GET["hospital_id"], $hospital_ids)) {
			// ��¼��־��
			$log_string = date("Y-m-d H:i:s ").$realname." ����ҽԺID: ".$_GET["hospital_id"]."\r\n";
			@file_put_contents(ROOT."data/hospital_try_err.txt", $log_string, FILE_APPEND);
			exit("������˲�������Ȩ�޷�Χ��ҽԺ����...");
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


// �л�ҽԺ�����б�:
$options = array();
$hids = implode(",", $hospital_ids);

// �µĴ����������� @ 2012-05-24
$h_list = $db->query("select * from hospital where id in ($hids) order by area asc, sname asc", "id");

// ͳ��ҽԺ�Ϳ���
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
			$options[] = array("[".implode(",", $v3)."]", "��".$k2." (".$v2.")", "");
			foreach ($v3 as $v4) {
				$options[] = array($v4, "����".$h_list[$v4]["name"], "color:blue");
			}
		} else {
			$v4 = $a_s_id[$k1][$k2][0];
			$options[] = array($v4, "��".$h_list[$v4]["name"], "color:blue");
		}
	}
}

// ʱ����޶���:
$today_tb = mktime(0,0,0);
$today_te = $today_tb + 24*3600;
$yesterday_tb = $today_tb - 24*3600;
$month_tb = mktime(0,0,0,date("m"),1);
$month_te = strtotime("+1 month", $month_tb);
$lastmonth_tb = strtotime("-1 month", $month_tb);
// ͬ�����ڶ���(2010-11-27):
$tb_tb = strtotime("-1 month", $month_tb);
$tb_te = strtotime("-1 month", time());
// �±�:
$yuebi_tb = strtotime("-1 month", $today_tb);
if (date("d", $yuebi_tb) != date("d", $today_tb)) {
	$yuebi_tb = $yuebi_te = -1;
} else {
	$yuebi_te = $yuebi_tb + 24*3600;
}
// �ܱ�:
$zhoubi_tb = strtotime("-7 day", $today_tb);
$zhoubi_te = $zhoubi_tb + 24*3600;
// ͬ��:
$tb_tb = strtotime("-1 month", $month_tb); //ͬ��ʱ�俪ʼ
$tb_te = strtotime("-1 month", time()); //ͬ��ʱ�����

// 2012-07-27 У��
$time_arr = array(
	"����" => array(date("Ymd"), date("Ymd")),
	"����" => array(date("Ymd", $yesterday_tb), date("Ymd", $yesterday_tb)),
	"����" => array(date("Ymd", $month_tb), date("Ymd")),
	"ͬ��" => array(date("Ymd", $tb_tb), date("Ymd", $tb_te)),
	"����" => array(date("Ymd", $lastmonth_tb), date("Ymd", $month_tb - 1)),
);


$d = $d = $d = array(); //��Ҫ���������
if ($sel_hid || $hid > 0) {
	include "main.load_data.php";
}

// �������ӵĿ�ݺ���
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
		$a = str_pad("", $data_len, '*'); //��ֵ������(���ֶ���Ǻź󣬱�ʾ����λ��Ҫ������)
	} else {
		$a = str_replace(" ", "&nbsp;", str_pad($a, $data_len, " ")); //β���ӿո�
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
<title>����ժҪ</title>
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
			parent.msg_box("�Ѿ�������һ��ҽԺ��", 3);
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
			parent.msg_box("�Ѿ�������һ��ҽԺ��", 3);
		}
	}
}

//��ȡ��Դ����
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
$str = '���ã�<font color="#FF0000"><b>'.$realname.'</b></font>';

if ($uinfo["hospitals"] || $uinfo["part_id"] > 0) {
	if ($uinfo["part_id"] > 0) {
		$str .= '��(��ݣ�'.$part_id_name[$uinfo["part_id"]].")";
	}
}

$onlines = $db->query("select count(*) as count from sys_admin where online=1", 1, "count");
$str .= '������ <font color="red"><b>'.date("Y-m-d").'</b></font>';
$str .= '������<b><font color="red">'.substr("��һ����������", date("w")*2, 2).'</font></b>';
$str .= '���������� <font color="red"><b>'.$onlines.'</b></font> ��';

echo $str;
?>
	</div>

<?php if (count($hospital_ids) > 1) { ?>
	<div style="margin-top:20px;">
		<b>�л�ҽԺ��</b>
		<select name="hospital_id" id="hospital_id" class="combo" onChange="if (this.value!='-1') location='?do=change&hospital_id='+this.value" style="width:200px;">
			<option value="" style="color:gray">--��ѡ��--</option>
<?php foreach ($options as $v) { ?>
			<option value="<?php echo $v[0]; ?>" <?php echo ($sel_hid == $v[0]  ? ' selected' : ''); ?> style="<?php echo $v[2]; ?>"><?php echo $v[1].($sel_hid == $v[0] ? ' *' : ''); ?></option>
<?php } ?>
		</select>&nbsp;
		<button class="button" onClick="hgo('up');">��</button>&nbsp;
		<button class="button" onClick="hgo('down');">��</button>&nbsp;
<?php if ($hid > 0) { ?>	
	<?php if ($list_power) { ?>
		<button class="buttonb" onClick="self.location='?list=1'" title="�鿴����ҳ��">����ҳ��</button>&nbsp;
	<?php } ?>
<?php } ?>
	</div>
<?php } else if ($hid > 0) { ?>
	<div style="margin-top:20px;">
		��ǰҽԺ��<b><?php echo $db->query("select name from hospital where id=$hid limit 1", 1, "name"); ?></b>&nbsp;&nbsp;
	</div>
<?php } else { ?>
	<div style="margin-top:20px;">û��Ϊ������ҽԺ������ϵ�ϼ�������Ա����</div>
<?php } ?>
</div>

<?php if ($hid > 0) { ?>
<?php
// �绰�ط��������:
if ($uinfo["part_id"] == 2 || $uinfo["part_id"] == 209 || $uinfo["character_id"] == 71 || $username == "admin" || $uinfo["character_id"] == 73 && @in_array("huifang", $guahao_config)) {
	$hf_str = '����û�лط�����';
	$hf_str1 = '����û�лط�����';
	$hf_str2 = '����û�лط�����';
	$ht = date("Y-m-d");
	$ht1 = date("Y-m-d",strtotime("+1 day"));
	$ht2 = date("Y-m-d",strtotime("+2 day"));
	$p_table = "ku_list";
	if ($uinfo["character_id"] == 71 || $username == "admin" || $username == "�ַɽ�") {  //�ط��鳤
		
		//��������
		$count = $db->query("select count(*) as c from $p_table where huifang_nexttime='$ht' and hid='$hid'", 1, "c");
		if ($count > 0) {
			$hf_link = "/m/ziliaoku/ku_list1.php?hf_time=$ht";
			$hf_str = '<a href="'.$hf_link.'">���ջط����ѣ� <b>'.$count.'</b> λ</a>';
		}
		//��������
		$count1 = $db->query("select count(*) as c1 from $p_table where huifang_nexttime='$ht1' and hid='$hid'", 1, "c1");
		if ($count1 > 0) {
			$hf_link1 = "/m/ziliaoku/ku_list1.php?hf_time=$ht1";
			$hf_str1 = '<a href="'.$hf_link1.'">���ջط����ѣ� <b>'.$count1.'</b> λ</a>';
		}
		//��������
		$count2 = $db->query("select count(*) as c2 from $p_table where huifang_nexttime='$ht2' and hid='$hid'", 1, "c2");
		if ($count2 > 0) {
			$hf_link2 = "/m/ziliaoku/ku_list1.php?hf_time=$ht2";
			$hf_str2 = '<a href="'.$hf_link2.'">����ط����ѣ� <b>'.$count2.'</b> λ</a>';
		}

		
	} else { //��Ա

		//��������
		$count = $db->query("select count(*) as c from $p_table where huifang_nexttime='$ht' and (u_name='$realname' or w_name='$realname')  and hid='$hid'", 1, "c");
		if ($count > 0) {
			$hf_link = "/m/ziliaoku/ku_list1.php?hf_time=$ht";
			$hf_str = '<a href="'.$hf_link.'">���ջط����ѣ� <b>'.$count.'</b> λ</a>';
		}
		//��������
		$count1 = $db->query("select count(*) as c1 from $p_table where huifang_nexttime='$ht1' and (u_name='$realname' or w_name='$realname')  and hid='$hid'", 1, "c1");
		if ($count1 > 0) {
			$hf_link1 = "/m/ziliaoku/ku_list1.php?hf_time=$ht1";
			$hf_str1 = '<a href="'.$hf_link1.'">���ջط����ѣ� <b>'.$count1.'</b> λ</a>';
		}
		//��������
		$count2 = $db->query("select count(*) as c2 from $p_table where huifang_nexttime='$ht2' and (u_name='$realname' or w_name='$realname')  and hid='$hid'", 1, "c2");
		if ($count2 > 0) {
			$hf_link2 = "/m/ziliaoku/ku_list1.php?hf_time=$ht2";
			$hf_str2 = '<a href="'.$hf_link2.'">����ط����ѣ� <b>'.$count2.'</b> λ</a>';
		}
	}
	if ($hf_str || $hf_str1 || $hf_str2) {
		echo '<br><div class="huifang_tixing">'.$hf_str.'</div><div class="huifang_tixing">'.$hf_str1.'</div><div class="huifang_tixing">'.$hf_str2.'</div><br>';
	}
}
?>
<?php } ?>


<?php if ($h_name_show) { ?>
<!-- ���ҽԺ���һ��� -->
<div style="padding:10px 0 0 40px; font-weight:bold;"><?php echo $h_name_show; ?></div>
<?php } ?>

<?php if ($sel_hid || $hid > 0) { ?>
<div style="padding:0px 0 0 30px;">
<!-- ѡ��ҽԺ�� -->
<?php
if ($index_data["��"]) {
	$d = $index_data["��"];
?>
	<div style="float:left; width:340px; padding:10px 0 0 10px;">
	<table width="100%" class="edit">
		<tr>
			<td class="head">������</td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">���գ���ѯ <?php echo a4($d["������ѯ"]); ?> ԤԼ <?php echo a4($d["����ԤԼ"]); ?> Ԥ�� <?php echo a4($d["����Ԥ��"]); ?> ʵ�� <?php echo a4($d["����ʵ��"]); ?></td>
		</tr>
		
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">���գ���ѯ <?php echo a4($d["������ѯ"]); ?> ԤԼ <?php echo a4($d["����ԤԼ"]); ?> Ԥ�� <?php echo a4($d["����Ԥ��"]); ?> ʵ�� <?php echo a4($d["����ʵ��"]); ?></td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">���£���ѯ <?php echo a4($d["������ѯ"]); ?> ԤԼ <?php echo a4($d["����ԤԼ"]); ?> Ԥ�� <?php echo a4($d["����Ԥ��"]); ?> ʵ�� <?php echo a4($d["����ʵ��"]); ?></td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata" style="color:silver">ͬ�ȣ���ѯ <?php echo a4($d["ͬ����ѯ"]); ?> ԤԼ <?php echo a4($d["ͬ��ԤԼ"]); ?> Ԥ�� <?php echo a4($d["ͬ��Ԥ��"]); ?> ʵ�� <?php echo a4($d["ͬ��ʵ��"]); ?></td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">���£���ѯ <?php echo a4($d["������ѯ"]); ?> ԤԼ <?php echo a4($d["����ԤԼ"]); ?> Ԥ�� <?php echo a4($d["����Ԥ��"]); ?> ʵ�� <?php echo a4($d["����ʵ��"]); ?></td>
		</tr>
	</table>
	</div>
    
    <!--��ҽû��Ȩ�޲鿴��ѯԱ-->
    <?php if($uinfo["part_id"] != 4){?>
    <!--��������-->
    <div style="float:left; width:340px; padding:10px 0 0 10px;">
	<table width="100%" class="edit">
		<tr>
			<td class="head">���¸�������</td>
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
            <td>��ѯ <?php echo a4($d[$n["realname"]."��ѯ"]); ?></td>
            <td>ԤԼ <?php echo a4($d[$n["realname"]."ԤԼ"]); ?></td>
            <td>Ԥ�� <?php echo a4($d[$n["realname"]."Ԥ��"]); ?></td>
            <td>ʵ�� <?php echo a4($d[$n["realname"]."ʵ��"]); ?></td>
		</tr>
        <?php
				}
			}
		?>
	</table>
	</div>
    
    
    <!--����Ա�鿴����-->
    
    <!--��Դ����-->
   <!-- <div style="float:left; width:380px; padding:10px 0 0 10px;">
    
	<table width="100%" class="edit">
		<tr>
			<td class="head">��Դ����</td>
            <td colspan="2">
            	<input name="smb" id="smb" class="input" style="width:76px" onClick="picker({el:'smb',dateFmt:'yyyy-MM-dd',maxDate:'#F{$dp.$D(\'sme\')}' })" value="" placeholder="��ʼʱ��">
                <input name="sme" id="sme" class="input" style="width:76px" onClick="picker({el:'sme',dateFmt:'yyyy-MM-dd',minDate:'#F{$dp.$D(\'smb\')}' })" value="" placeholder="����ʱ��">
                <input type="submit" value="��ѯ" id="smSubmit" onClick="getsd();">
            
            </td>
            <td></td>
		</tr>
    </table>
    <table width="100%" class="edit" id="source_data">
        
        
	</table>
	</div>-->
    <?php } ?> 
    
<?php } ?>


<!-- ����ͳ������ -->
<?php
if ($index_data["����"]) {
	$d = $index_data["����"];
?>
	<div style="float:left; width:300px; padding:10px 0 0 10px;">
	<table width="100%" class="edit">
		<tr>
			<td class="head">����</td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">���գ�ԤԼ <?php echo a4($d["����ԤԼ"]); ?> Ԥ�� <?php echo a4($d["����Ԥ��"]); ?> ʵ�� <?php echo a4($d["����ʵ��"]); ?></td>
		</tr>
		
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">���գ�ԤԼ <?php echo a4($d["����ԤԼ"]); ?> Ԥ�� <?php echo a4($d["����Ԥ��"]); ?> ʵ�� <?php echo a4($d["����ʵ��"]); ?></td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">���£�ԤԼ <?php echo a4($d["����ԤԼ"]); ?> Ԥ�� <?php echo a4($d["����Ԥ��"]); ?> ʵ�� <?php echo a4($d["����ʵ��"]); ?></td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata" style="color:silver">ͬ�ȣ�ԤԼ <?php echo a4($d["ͬ��ԤԼ"]); ?> Ԥ�� <?php echo a4($d["ͬ��Ԥ��"]); ?> ʵ�� <?php echo a4($d["ͬ��ʵ��"]); ?></td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">���£�ԤԼ <?php echo a4($d["����ԤԼ"]); ?> Ԥ�� <?php echo a4($d["����Ԥ��"]); ?> ʵ�� <?php echo a4($d["����ʵ��"]); ?></td>
		</tr>
	</table>
	</div>
<?php } ?>


<?php
if ($index_data["�绰"]) {
	$d = $index_data["�绰"];
?>
	<div style="float:left; width:300px; padding:10px 0 0 10px;">
	<table width="100%" class="edit">
		<tr>
			<td class="head">�绰</td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">���գ�ԤԼ <?php echo a4($d["����ԤԼ"]); ?> Ԥ�� <?php echo a4($d["����Ԥ��"]); ?> ʵ�� <?php echo a4($d["����ʵ��"]); ?></td>
		</tr>
		
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">���գ�ԤԼ <?php echo a4($d["����ԤԼ"]); ?> Ԥ�� <?php echo a4($d["����Ԥ��"]); ?> ʵ�� <?php echo a4($d["����ʵ��"]); ?></td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">���£�ԤԼ <?php echo a4($d["����ԤԼ"]); ?> Ԥ�� <?php echo a4($d["����Ԥ��"]); ?> ʵ�� <?php echo a4($d["����ʵ��"]); ?></td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata" style="color:silver">ͬ�ȣ�ԤԼ <?php echo a4($d["ͬ��ԤԼ"]); ?> Ԥ�� <?php echo a4($d["ͬ��Ԥ��"]); ?> ʵ�� <?php echo a4($d["ͬ��ʵ��"]); ?></td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">���£�ԤԼ <?php echo a4($d["����ԤԼ"]); ?> Ԥ�� <?php echo a4($d["����Ԥ��"]); ?> ʵ�� <?php echo a4($d["����ʵ��"]); ?></td>
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
			<td class="idata">���գ�ԤԼ <?php echo a4($d["����ԤԼ"]); ?> Ԥ�� <?php echo a4($d["����Ԥ��"]); ?> ʵ�� <?php echo a4($d["����ʵ��"]); ?></td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">���գ�ԤԼ <?php echo a4($d["����ԤԼ"]); ?> Ԥ�� <?php echo a4($d["����Ԥ��"]); ?> ʵ�� <?php echo a4($d["����ʵ��"]); ?></td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">���£�ԤԼ <?php echo a4($d["����ԤԼ"]); ?> Ԥ�� <?php echo a4($d["����Ԥ��"]); ?> ʵ�� <?php echo a4($d["����ʵ��"]); ?></td>
		</tr>
		<tr onMouseOver="mi(this)" onMouseOut="mo(this)">
			<td class="idata">���£�ԤԼ <?php echo a4($d["����ԤԼ"]); ?> Ԥ�� <?php echo a4($d["����Ԥ��"]); ?> ʵ�� <?php echo a4($d["����ʵ��"]); ?></td>
		</tr>
	</table>
	</div>
<?php
	}
}
?>


	<div class="clear"></div>
<?php
if ($index_data["��"]) {?>
	<!-- ע�� -->
	<div style="padding-top:20px; padding-left:20px;">
		* <b>ͬ��</b>���ϸ��µ�ͬ�����ݡ����磬������4��5�գ���ͬ�Ⱦ���3��1����3��5�����ʱ�������<br>
	</div>
<?php } ?>
	

</div>


<?php } else { ?>

	<!-- <div style="padding:10px 0 0 50px; color:gray;">(����ѡ��ҽԺ)</div> -->

<?php } ?>

<?php if ($uinfo["ukey_sn"] == '025') { ?>
<div style="padding-top:20px; padding-left:50px;">
	<input type="submit" onClick="location='/m/set_ukey.php'" class="submit" value="������uKey">
</div>
<?php } ?>

<div style="padding:10px 0 0 50px; color:#CDCDCD">* ҳ��ִ��ʱ�䣺<?php echo round(now() - $pagebegintime, 4); ?>s  <?php echo $log_time1." ".$log_time2; ?></div>

</body>
</html>