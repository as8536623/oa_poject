<?php
// --------------------------------------------------------
// - ����˵�� : �������Ͽ�
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2013-7-11
// --------------------------------------------------------
require "../../core/core.php";
include_once ROOT."/core/patient_field_name.php";
$table = "ku_list";




$disease_list = $db->query("select id,name from disease where hospital_id='$hid' and isshow=1 order by sort desc,sort2 desc", "id", "name");
$disease_2_list = $db->query("select id,disease_2 from disease where hospital_id='$hid' and isshow=1 and disease_2!=''", "id", "disease_2");
$media_from_array2 = $db->query("select name from media where (hospital_id=0 or hospital_id='$hid') order by sort desc, id asc", "", "name");
foreach ($media_from_array2 as $v) {
		$media_from_array[] = $v;
}

$id = intval($_REQUEST["id"]);
$mode = $id > 0 ? "edit" : "add";

if ($_POST) {
	if ($id > 0) {
		$old = $db->query("select * from $table where id=$id limit 1", 1);
	}
	
	$r = array();
	$yyb = array(); //����ԤԼ����(���������䣬�Ա�QQ��΢�ţ��绰���룬��ѯ���ݣ��������ͣ�ý����Դ���ؼ��ʣ�������ַ)
	$r["hid"] = $_hid = intval($_POST["hid"]);
	if ($_hid > 0) {
		$r["h_name"] = $db->query("select name from hospital where id=$_hid limit 1", 1, "name");
	} else {
		exit("����ҽԺ����ѡ��");
	}
	$area = $_POST["area1"]." ".$_POST["area2"];
	$yyb["name"] = $r["name"] = trim($_POST["name"]);
	$yyb["sex"] = $r["sex"] = $_POST["sex"];
	$yyb["age"] = $r["age"] = $_POST["age"];
	$r["vocation"] = $_POST["vocation"];
	$yyb["area"] = $r["area"] = $area;
	$yyb["tel"] = $r["mobile"] = trim($_POST["mobile"]);
	$r["intention"] = $_POST["intention"];
	$r["jblx"] = $_POST["disease_id"].",".$_POST["disease_2"];  
	$yyb["disease_id"] = $_POST["disease_id"]; 
	$yyb["disease_2"] = $_POST["disease_2"]; 
	$yyb["engine_key"] = $r["laiyuan"] = $_POST["laiyuan"];
	$yyb["media_from"] = $r["media_from"] = $_POST["media_from"];
	$yyb["from_site"] = $r["from_site"] = $_POST["from_site"];
	$r["IP"] = $_POST["IP"];
	$r["huifang_nexttime"] = $_POST["huifang_nexttime"];
	$yyb["qq"] = $r["qq"] = trim($_POST["qq"]);
	$r["order_qq"] = $_POST["order_qq"];
	$yyb_wx = $r["weixin"] = trim($_POST["weixin"]);
	$r["order_weixin"] = $_POST["order_weixin"];
	$yyb["content"] = $r["zx_content"] = $_POST["zx_content"];
	$yyb["author"] = $r["u_name"] = $_u_name = $_POST["realname"];
	$yyb["w_name"] = $r["w_name"] = $_POST["w_name"];
    
    $r["order_qq"] = $_POST["order_qq"];
	$r["order_weixin"] = $_POST["order_weixin"];
      
	$yyb["uid"] = $r["uid"] = $db->query("select id from sys_admin where realname ='$_u_name' limit 1", 1, "id");
	
	if ($yyb_wx) {
		$yyb["content"] .= " ΢��:".$yyb_wx;
		}

	if ($mode == "add") {
		$r["addtime"] = time();
		$r["u_name"] = $_u_name = $_POST["realname"];
		$r["uid"] = $db->query("select id from sys_admin where realname ='$_u_name' limit 1", 1, "id");
	}
	

	$sqldata = $db->sqljoin($r);
	
	if ($mode == "edit") {
		$sql = "update $table set $sqldata where id='$id' limit 1";

		$line = $db->query("select * from $table where id=$id limit 1", 1);
		// Ҫ���ӵ��޸��ֶ�:
		$log_field_str = "name sex age jblx mobile zx_content talk_content addtime media_from laiyuan from_site u_name IP QQ weixin";
	
		$s2 = patient_modify_log_s($r, $line, $log_field_str);
		if ($s2 != '') {
			$log->add("�޸ĵǼǲ��ˣ�".$line["name"], $s2, $line, $table);
		}
		
		// �����¼����Ϊ�ļ�
		if ($_POST["talk_content"] != '') {
				@put_talk_content($_hid, $id, stripslashes($_POST["talk_content"])); 
			}
		
		//����ԤԼ����
		if ($line["is_yuyue"] && count($yyb) > 0) {
			$yyb_sqldata = $db->sqljoin($yyb);
			$patient_hid = "patient_".$line["hid"];
			$yyb_sql = "update $patient_hid set $yyb_sqldata where lid = $id limit 1";
			$db->query($yyb_sql);
			} 

	} else {
		$sql = "insert into $table set $sqldata";
	}
	
	

	if ($id1 = $db->query($sql)) {
		if ($mode == "add") {
			echo '<script> parent.update_content(); </script>';
			
			// �����¼����Ϊ�ļ�
			if ($_POST["talk_content"] != '') {
				@put_talk_content($_hid, $id1, stripslashes($_POST["talk_content"])); 
			}
		}
		echo '<script> parent.msg_box("�����ύ�ɹ�", 2); </script>';
		echo '<script> parent.load_src(0); </script>';
	} else {
		echo "�ύʧ�ܣ����Ժ����ԣ�";
	}
	exit;
}

if ($mode == "edit") {
	$line = $db->query("select * from $table where id='$id' limit 1", 1);
	
	// ��ȡ�����¼��
	$line["talk_content"] = @get_talk_content($line["hid"], $id);
}

$title = $mode == "edit" ? "�޸�" : "����";
$huifang_time = $line["huifang_nexttime"] ? int_date_to_date($line["huifang_nexttime"]) : "";


//����������

if($uinfo["part_id"]==2 || $uinfo["part_id"]==209){
	$addop = "display:none";
}

if($uinfo["part_id"]==209){
	$whop = "display:none";
}



?>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<script src="/res/jquery.min.js" language="javascript"></script>
<script src="/res/distpicker.min.js" language="javascript"></script>

<style>
#rec_part, #rec_user {margin-top:6px; }
.rec_user_b {width:100px; float:left; }
.rec_group_part {clear:both; margin:10px 0 5px 0; font-weight:bold; }
.l {text-align:right; color:#000000; background:#f4fbf7; }
.r {text-align:left; }
</style>

<script language="javascript">
function check_submit(o) {
	/*if (o.w_name.value == "") {
		alert("��ѡ��ά���ˣ�"); o.w_name.focus(); return false;
	}*/
	if (o.hid.value == "") {
		alert("��ѡ��������ҽԺ��"); o.hid.focus(); return false;
	}
	if (o.name.value == "") {
		alert("�����뻼�ߡ����ơ���"); o.name.focus(); return false;
	}
	if (o.zx_content.value == "") {
		alert("�����뻼����ѯ���ݣ�"); o.zx_content.focus(); return false;
	}
	if (o.laiyuan.value == "") {
		alert("������ �ؼ���"); o.laiyuan.focus(); return false;
	}
	if (o.media_from.value == "") {
		alert("������ ý����Դ"); o.media_from.focus(); return false;
	}
	if (o.from_site.value == "") {
		alert("������ ��ַ����"); o.from_site.focus(); return false;
	}
	if (o.huifang_nexttime.value == "") {
		alert("������ �´λط�ʱ��"); o.huifang_nexttime.focus(); return false;
	}
	if (o.disease_id.selectedIndex == 0) {
		alert("��ѡ�� ����"); o.disease_id.focus(); return false;
	}
	if (o.disease_2.selectedIndex == 0) {
		alert("��ѡ�� ��������"); o.disease_2.focus(); return false;
	}
	return true;
}
function update_check_color(o) {
	o.parentNode.getElementsByTagName("label")[0].style.color = o.checked ? "blue" : "";
}
//�ط�ʱ����
function input_date(id, value) {
	var cv = byid(id).value;
	//var time = cv.split(" ")[1];

	if (byid(id).disabled != true) {
		byid(id).value = value;
	}
}

<?php if($mode == "add"){ ?>
function lens(cstr){
	var j = 0;
	for(var i = 0; i<=cstr.length; i++){
		charCode = cstr.charCodeAt(i);  
		if(charCode < 299) j++;
		else j+=2;
		}
		return j;
	}

$(function (){
	
	//��֤�ֻ�����
	var anum2 = 1;
	$("input[name='mobile']").blur(function(){
		if($(this).val().length >10){
			$.ajax({
				url:"/m/mestest.php",
				type: "get",
				data:{
					mobile:$(this).val()
					},
				success: function(response){
					$("#test2").html(response);
					if(response != ''){
						let mobile = response.split(' ')[0];
						parent.repeatlist_src(1,'/m/ziliaoku/repeatlist.php?mobile='+mobile, 1000, 550);
						anum2 = 0;
						}
					}
				});
			}
		})
	
	//��֤����
	var anum1 = 1;
	$("input[name='name']").blur(function(){
		if(lens($(this).val()) >3){
			$.ajax({
				url:"/m/mestest.php",
				type: "post",
				data:{
					name:$(this).val()
					},
				
				success: function(response){
					$("#test1").html(response);
					if(response != ''){
						let name = response.split(' ')[0];
						parent.repeatlist_src(1,'/m/ziliaoku/repeatlist.php?name='+name, 1000, 550);
						anum1 = 0;
						}
					}
				});
			}
		})
		
	//��֤΢��
	var anum4 = 1;
	$("input[name='weixin']").blur(function(){
		if($(this).val().length >3){
			$.ajax({
				url:"/m/mestest.php",
				type: "get",
				data:{
					weixin:$(this).val()
					},
				success: function(response){
					$("#test4").html(response);
					if(response != ''){
						let weixin = response.split(' ')[0];
						parent.repeatlist_src(1,'/m/ziliaoku/repeatlist.php?weixin='+weixin, 1000, 550);
						anum4 = 0;
						}
					}
				});
			}
		})
	
	//��֤QQ
	var anum3 = 1;
	$("input[name='qq']").blur(function(){
		if($(this).val().length >5){
			$.ajax({
				url:"/m/mestest.php",
				type: "get",
				data:{
					qq:$(this).val()
					},
				success: function(response){
					$("#test3").html(response);
					if(response != ''){
						let qq = response.split(' ')[0];
						parent.repeatlist_src(1,'/m/ziliaoku/repeatlist.php?qq='+qq, 1000, 550);
						anum3 = 0;
						}
					}
				});
			}
		})
	
		
	})
<?php }?>


</script>

</head>

<body>

<form name="mainform" action="" method="POST" onSubmit="return check_submit(this)">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">��������</td>
	</tr>
	<tr style="<?php echo $addop; ?>">
		<td class="left"><font color="red">*</font> �����ˣ�</td>
		<td class="right">
			<select name="realname" class="combo" style="width:200px">
			<option value="" style="color:gray">-��ѡ��������-</option>
<?php
	$add_name = $db->query("select id,realname from sys_admin where isshow = 1 and (part_id = 2 or part_id = 209) and hospitals in (".implode(",", $hospital_ids).")","id","realname");
	if($line["u_name"]){
		$tjname = $line["u_name"];
	}else{
		$tjname = $realname;
	}
	echo list_option($add_name, "_value_", "_value_", $tjname);
?>
			</select>
			<span class="intro">����������</span>
		</td>
	</tr>
    <tr style="<?php echo $whop; ?>">
		<td class="left"><!--<font color="red">*</font>--> ά���ˣ�</td>
		<td class="right">
        <?php if($mode == "edit" && $uinfo["part_id"] == 2){ ?>
			<!-- <input name="w_name" value="<?php echo $line["w_name"]; ?>" readonly class="input" style="width:200px"> -->
			<select name="w_name" class="combo" style="width:200px">
			<option value="" style="color:gray">-��ѡ��ά����-</option>
<?php
	$wh_name = $db->query("select id,realname from sys_admin where realname <> '".$realname."' and isshow = 1 and part_id = 2 and hospitals in (".implode(",", $hospital_ids).")","id","realname");
	echo list_option($wh_name, "_value_", "_value_", $line["w_name"]);
?>
			</select>
			<span class="intro">ά����ѡ��</span>
        <?php }else{?>
        	<select name="w_name" class="combo" style="width:200px">
			<option value="" style="color:gray">-��ѡ��ά����-</option>
<?php
	$wh_name = $db->query("select id,realname from sys_admin where realname <> '".$realname."' and isshow = 1 and part_id = 2 and hospitals in (".implode(",", $hospital_ids).")","id","realname");
	echo list_option($wh_name, "_value_", "_value_", $line["w_name"]);
?>
			</select>
			<span class="intro">ά����ѡ��</span>
        <?php }?>
			
		</td>
	</tr>
	<tr>
		<td class="left"><font color="red">*</font> ����ҽԺ��</td>
		<td class="right">
			<select name="hid" class="combo" style="width:200px">
				<!--<option value="" style="color:gray">-��ѡ������ҽԺ-</option>-->
<?php
	$h_id_name = $db->query("select id,name from hospital where id in (".implode(",", $hospital_ids).") order by name asc", "id", "name");
	echo list_option($h_id_name, "_key_", "_value_", $line["hid"]);
?>
			</select>
			<span class="intro">����ҽԺ����ѡ��</span>
		</td>
	</tr>
	<tr>
		<td class="left" style="width:25%"><font color="red">*</font> ������</td>
		<td class="right" style="width:75%"><input name="name" value="<?php echo $line["name"]; ?>" class="input" style="width:200px"> <span class="intro">���� <b id="test1" class="red"></b></span></td>
	</tr>
	<tr>
		<td class="left"><font color="red">*</font> ��ѯ���ݣ�</td>
		<td class="right">
        	<font color="red">��ע�⣺������д��ѯ����ժҪ��<b>��Ҫճ�������¼</b>�������¼�븴�Ƶ����桰�����¼��һ���С�</font><br>
			<textarea name="zx_content" style="width:80%; height:60px; vertical-align:middle;" class="input"><?php echo $line["zx_content"]; ?></textarea>
		</td>
	</tr>
    <tr>
		<td class="left"> �����¼��</td>
		<td class="right">
			<textarea name="talk_content" style="width:80%; height:100px; vertical-align:middle;" class="input"><?php echo $line["talk_content"]; ?></textarea>
		</td>
	</tr>
	<tr>
		<td class="left">�Ա�</td>
		<td class="right">
			<select name="sex" class="combo" style="width:100px">
				<option value="" style="color:gray">-��ѡ��-</option>
				<?php echo list_option(array("��", "Ů"), "_value_", "_value_", $line["sex"]); ?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="left">���䣺</td>
		<td class="right"><input name="age" value="<?php echo $line["age"]; ?>" class="input" size="20" style="width:100px"></td>
	</tr>
	<tr>
		<td class="left">ְҵ��</td>
		<td class="right">
			<select name="vocation" class="combo" style="width:100px">
				<option value="" style="color:gray">-��ѡ��-</option>
				<?php echo list_option(array("ѧ��", "ְԱ", "����", "����", "��ũ", "����", "��ҵ", "��ʦ", "����", "˾��", "ҽ��", "����", "����/����", "����/����", "����/װ��", "����"), "_value_", "_value_", $line["vocation"]); ?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="left">������</td>
		<td class="right">
			<div id="distpicker1">
				<select name="area1" class="combo" data-province="<?php $area = explode(" ",$line["area"]); echo $area[0]; ?>" >
					
				</select>
				<select name="area2" class="combo" data-city="<?php $area = explode(" ",$line["area"]); echo $area[1]; ?>" value="<?php $area = explode(" ",$line["area"]); echo $area[1]; ?>">
					
				</select>
			</div>
		</td>
	</tr>
	<script>
		$("#distpicker1").distpicker({province: '����ʡ',city: '�Ͼ���'});
	</script>
	
	<tr>
		<td class="left">�ֻ���</td>
		<td class="right"><input name="mobile" value="<?php echo $line["mobile"]; ?>" class="input" size="20" style="width:200px"> <span class="intro"><b id="test2" class="red"></b></span></td>
	</tr>
	<tr>
	    <td class="left"><font color="red">*</font> �������ͣ�</td>
		<td class="right">
			<select id="disease_1" name="disease_id" onChange="update_disease_2(this.value)" class="combo">
				<option value="" style="color:gray">-��ѡ�����-</option>
				<?php 
					$disease2 = $line["jblx"];
					$disease = explode(",",$disease2);
					echo list_option($disease_list, "_key_", "_value_", $disease[0]); 
				?>
			</select>
            
			<span id="disease_2_area">
            	 <?php 
					if($disease[0]){ ?>
            	<select  name="disease_2"  class="combo">
                	<option value="" style="color:gray">-��ѡ�񼲲�-</option>
                    <?php 
						$zibz = $db->query("select * from disease where hospital_id='$hid' and isshow=1 and disease_2!='' and id = '$disease[0]'", 1);
						$zibz1 = explode(",",$zibz["disease_2"]);
						echo list_option($zibz1, "_value_", "_value_", $disease[1]);
					?>
                </select>
                <?php } ?>
                
                
			</span>
            
<?php
if (count($disease_2_list) > 0) {
	foreach ($disease_2_list as $_id => $_s) {
?>
			<input type="hidden" id="disease_id_2_<?php echo $_id; ?>" value="<?php echo $_s; ?>" />
<?php
	}
}
?>
			<script type="text/javascript">
			var default_disease_2 = "<?php echo $disease[1]; ?>";
			function update_disease_2(id) {
				var s = '';
				if (id > 0) {
					var o = byid("disease_id_2_"+id);
					if (o) {
						var s_arr = o.value.split(",");
						if (s_arr.length > 0) {
							s += '<select name="disease_2" class="combo">';
							s += '  <option value="" style="color:gray">-��ѡ�񼲲�-</option>';
							for (var i=0; i<s_arr.length; i++) {
								var title = s_arr[i];
								var chk = default_disease_2 == title ? " selected" : "";
								s += '  <option value="'+title+'"'+chk+'>'+title+(chk ? " *" : "")+'</option>';
							}
							s += '</select>';
						}
					}
				}
				byid("disease_2_area").innerHTML = s;
			}
			</script>
		</td>
    </tr>
	<tr>
	    <td class="left">��������</td>
		<td class="right">
			<select name="intention" class="combo" style="width:100px">
				<option value="" style="color:gray">-��ѡ��-</option>
				<?php echo list_option(array("A", "B", "C", "D"), "_value_", "_value_", $line["intention"]); ?>
			</select>
		</td>
    </tr>
	<tr>
		<td class="left"><font color="red">*</font> �ؼ��ʣ�</td>
		<td class="right"><input name="laiyuan" value="<?php echo $line["laiyuan"]; ?>" class="input" size="20" style="width:200px"> </td>
	</tr>
    <tr>
		<td class="left"><font color="red">*</font> ý����Դ��</td>
		<td class="right">
			<select name="media_from" class="combo" style="width:100px">
				<option value="" style="color:gray">-��ѡ��-</option>
				<?php echo list_option($media_from_array, "_value_", "_value_", $line["media_from"]); ?>
			</select>
		</td>
	</tr>
    <tr>
		<td class="left"><font color="red">*</font> ��ַ���ӣ�</td>
		<td class="right"><input name="from_site" value="<?php echo $line["from_site"]; ?>" class="input" size="110" style="width:400px"></td>
	</tr>
    <tr>
		<td class="left">IP��</td>
		<td class="right"><input name="IP" value="<?php echo $line["IP"]; ?>" class="input" size="20" style="width:200px"></td>
	</tr>
	<tr>
		<td class="l" valign="top"><font color="red">*</font> �´λط����ѣ�</td>
		<td class="r" colspan="3"><input name="huifang_nexttime" value="<?php echo $huifang_time; ?>" class="input" style="width:150px" id="huifang_nexttime"> <img src="/res/img/calendar.gif" id="huifang_nexttime" onClick="picker({el:'huifang_nexttime',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��"><span class="intro">�����´λطõ�����ʱ�䣬ԤԼ����ѡ�����</span>
        <?php
		$show_days = array(
		
			"��" => $today = date("Y-m-d"), //����
			"��" => date("Y-m-d", strtotime("+1 day")), //����
			"��" => date("Y-m-d", strtotime("+2 days")), //����
			"7���" => date("Y-m-d", strtotime("+7 days")), 
			"15���" => date("Y-m-d", strtotime("+15 days")), 
			"1���º�" => date("Y-m-d", strtotime("next Month")), 
		);
		echo '<div style="padding-top:6px;">����: ';
		foreach ($show_days as $name => $value) {
			echo '<a href="javascript:input_date(\'huifang_nexttime\', \''.$value.'\')">['.$name.']</a>&nbsp;';
		}

?>
        
        </td>
	</tr>
	<tr>
		<td class="left">����QQ��</td>
		<td class="right"><input name="qq" value="<?php echo $line["qq"]; ?>" class="input" size="20" style="width:200px"> <span class="intro"><b id="test3" class="red"></b></span></td>
	</tr>
    <tr>
		<td class="left">�ҷ�QQ��</td>
		<td class="right"><input name="order_qq" value="<?php echo $line["order_qq"]; ?>" class="input" size="20" style="width:200px"> <span class="intro">�뻼�߹�ͨ���õ�QQ���� (ѡ��)</span></td>
	</tr>
	
	<tr>
		<td class="left">����΢�ţ�</td>
		<td class="right"><input name="weixin" value="<?php echo $line["weixin"]; ?>" class="input" size="20" style="width:200px"> <span class="intro"><b id="test4" class="red"></b></span></td>
	</tr>
    <tr>
		<td class="left">�ҷ�΢�ţ�</td>
		<td class="right"><input name="order_weixin" value="<?php echo $line["order_weixin"]; ?>" class="input" size="20" style="width:200px"> <span class="intro">�뻼�߹�ͨ���õ�΢���ʺ�</span></td>
	</tr>
	
	
</table>

<input type="hidden" name="id" value="<?php echo $id; ?>">

<div class="button_line"><input type="submit" class="submit" value="�ύ����"></div>
</form>
</body>
</html>