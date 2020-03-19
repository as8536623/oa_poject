<?php
/*
// - 功能说明 : 修改 病人资料
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2012-07-16
*/



if ($_POST) {
	$po = &$_POST; //引用 $_POST
	$r = array();
	$zlk = array(); //更新登记表单(姓名，年龄，性别，QQ，微信，电话号码，咨询内容，疾病类型，媒体来源，关键词，渠道网址)

	$ef_check = $po["ef_check"];
	$f_arr = explode(",", base64_decode($ef_check));

	if (@in_array("name", $f_arr)) $zlk["name"] = $r["name"] = trim($po["name"]);

	if (@in_array("sex", $f_arr)) $zlk["sex"] = $r["sex"] = $po["sex"];
	if (@in_array("qq", $f_arr)) $zlk["qq"] = $r["qq"] = $po["qq"]; 
	if (@in_array("age", $f_arr)) $zlk["age"] = $r["age"] = $po["age"];
	if (@in_array("content", $f_arr)) {
		$r["content"] = $po["content"]; 
		
		$zx_content = explode(" 微信:",$po["content"]); 
		$zlk["zx_content"] = $zx_content[0];
		if($zx_content[1]) {
			$zlk["weixin"] = $zx_content[1];
			}
		
		}
		
		
	if (@in_array("disease", $f_arr)) {$r["disease_id"] = $po["disease_id"];$r["disease_2"] = $po["disease_2"];$zlk["jblx"] = $po["disease_id"].",".$po["disease_2"];}
	if (@in_array("depart", $f_arr)) $r["depart"] = $po["depart"];
	if (@in_array("media", $f_arr)) {
		$zlk["media_from"] = $r["media_from"] = $po["media_from"];
		$zlk["laiyuan"] = $r["engine_key"] = $po["engine_key"];
		$zlk["from_site"] = $r["from_site"] = $po["from_site"];
	}
	if (@in_array("account", $f_arr)) $r["from_account"] = $po["from_account"]; // 2010-11-04
	if (@in_array("zhuanjia_num", $f_arr)) $r["zhuanjia_num"] = $po["zhuanjia_num"];
	if (@in_array("ordernum", $f_arr)) $r["ordernum"] = $po["ordernum"];
	if (@in_array("area", $f_arr)) {
		$r["is_local"] = $po["is_local"];
		$zlk["area"] = $r["area"] = $po["area"];
	}

	if (@in_array("tel", $f_arr)) {
		$tel = trim($po["tel"]);
		$zlk["mobile"] = $r["tel"] = $tel;
		if (strlen($r["tel"]) == 11) {
			$r["tel_location"] = @get_mobile_location($r["tel"]);
		}
	}

	// 修改时间:
	if (@in_array("order_date", $f_arr)) {
		$order_date_post = @strtotime($po["order_date"]);
		//判断时间是否有修改
		if ($order_date_post != $line["order_date"]) {

			// 如果修改，该时间不能被修改为当前时间的一个月之前
			if ($order_date_post < strtotime("-1 month")) {
				exit_html("预约时间不能被修改到一个月之前。（请先检查您的电脑时间是否有误！）  请返回重新填写。");
			}

			$r["order_date"] = $order_date_post;

			// 如果修改预约时间，自动修改状态为等待
			if ($line["status"] == 2) {
				$r["status"] = 0;
			}
		}
	}

	if (@in_array("memo", $f_arr) && trim($po["memo"]) != '') {
		$r["memo"] = trim(trim($line["memo"])."\r\n".date("Y-m-d H:i ").$realname.": ".trim($po["memo"]));
	}

	// 2012-11-30 1.营销医生 2、营销专员
	if ($uinfo["part_id"] == 203) {
		$r["yingxiao_doctor"] = $po["yingxiao_doctor"];
		$r["yingxiao_name"] = $po["yingxiao_name"];
	}

	// 要监视的修改字段:
	$log_field_str = "name sex age disease_id disease_2 tel zhuanjia_num content order_date media_from engine engine_key from_site status ordernum";

	if (count($r) > 0) {
		$s = patient_modify_log($r, $line, $log_field_str);
		if ($s != '') {
			$r["edit_log"] = $s;
			
		}
		$s2 = patient_modify_log_s($r, $line, $log_field_str);
		if ($s2 != '') {
			$log->add("修改预约病人：".$line["name"], $s2, $line, $table);
		}
		
	}
	
	//更新资料库信息
	if (count($zlk) > 0) {
		$zlk_sqldata = $db->sqljoin($zlk);
		$zlk_id = $line["lid"];
		$zlk_sql = "update ku_list set $zlk_sqldata where id='$zlk_id' limit 1";
		$db->query($zlk_sql);
		}


	if (count($r) > 0) {
		$sqldata = $db->sqljoin($r);
		$sql = "update $table set $sqldata where id='$id' limit 1";

		ob_start();
		$db->query($sql);
		if (@in_array("talk_content", $f_arr) && $po["talk_content"] != '') {
			@put_talk_content($hid, $line["lid"], stripslashes($_POST["talk_content"])); // 聊天记录保存为文件@2012-02-26
		}
		$error = ob_get_clean();

		if (empty($error)) {
			echo '<script type="text/javascript">';
			echo 'parent.load_box(0);';
			echo 'parent.msg_box("修改成功");';
			echo '</script>';
		} else {
			echo "资料提交出错，请联系开发人员解决: <br><br>";
			echo $db->sql."<br><br>";
			echo $error."<br><br>";
		}
	} else {
			echo '<script type="text/javascript">';
			echo 'parent.load_box(0);';
			echo 'parent.msg_box("资料无变动");';
			echo '</script>';
	}
	exit;
}

// 读取聊天记录：
$line["talk_content"] = @get_talk_content($hid, $line["lid"]);

// 读取字典:
$disease_list = $db->query("select id,name from disease where hospital_id='$hid' and isshow=1 order by sort desc,sort2 desc", "id", "name");
$disease_2_list = $db->query("select id,disease_2 from disease where hospital_id='$hid' and isshow=1 and disease_2!=''", "id", "disease_2");
$doctor_list = $db->query("select id,name from doctor where hospital_id='$hid'");
$part_id_name = $db->query("select id,name from sys_part", "id", "name");
$depart_list = $db->query("select id,name from depart where hospital_id='$hid'");
$engine_list = $db->query("select id,name from engine", "id", "name");
$sites_list = $db->query("select id,url from sites where hid=$hid", "id", "url");

$account_list = $db->query("select id, name from count_web_sub_type order by sort desc,id asc", "id", "name");
$_tel = $db->query("select id, '电话' as fname from count_type where type='tel' and hid=$hid order by id asc", "id", "fname");
if (count($_tel) > 0) {
	foreach ($_tel as $k => $v) {
		$account_list[$k] = $v;
	}
}

$account_first = 0;
if (count($account_list) > 0) {
	$tmp = @array_keys($account_list);
	$account_first = $tmp[0];
}

$status_array = array(
	array("id"=>0, "name"=>'等待'),
	array("id"=>1, "name"=>'已到'),
	array("id"=>2, "name"=>'未到'),
);

$xiaofei_array = array(
	array("id"=>0, "name"=>'未消费'),
	array("id"=>1, "name"=>'已消费'),
);


// 取前30个病种:
$show_disease = array();
foreach ($disease_list as $k => $v) {
	$show_disease[$k] = $v;
	if (count($show_disease) >= 30) {
		break;
	}
}

// 读取编辑 资料
$cur_disease_list = explode(",", $line["disease_id"]);
foreach ($cur_disease_list as $v) {
	if ($v && !array_key_exists($v, $show_disease)) {
		$show_disease[$v] = $disease_list[$v];
	}
}



// 2010-08-18
//$media_from_array = explode(" ", "网络 电话");
$media_from_array2 = $db->query("select name from media where (hospital_id=0 or hospital_id='$hid') order by sort desc, id asc", "", "name");
foreach ($media_from_array2 as $v) {
		$media_from_array[] = $v;
}
// 修正 @ 2012-07-18
if ($line["media_from"] != '' && !in_array($line["media_from"], $media_from_array)) {
	$media_from_array[] = $line["media_from"];
}

// 控制各选项是否可以编辑:
$all_field = explode(" ", "name sex age qq tel content talk_content disease media zhuanjia_num order_date memo depart area account");
$edit_field = array();
if ($username == "admin" || $username == "林飞渐" || $uinfo["character_id"] == 71 || $debug_mode) {
	$edit_field = $all_field;
} else {
	// 是添加者自己的资料，且未到院
	if ($line["author"] == $realname) {
		$edit_field = $all_field;
	} else {
		// 非添加者资料:
		if ($line["status"] == 1 && $uinfo["part_id"] != 202) {
			$edit_field = explode(' ', 'memo'); //已到院还是可以添加备注嘛
		} else if($uinfo["part_id"] == 202){
			$edit_field = explode(' ', 'media memo');
		} else{
			$edit_field = explode(' ', 'content disease media zhuanjia_num memo depart area account');
		}
	}
}

if ($debug_mode) {
	//$edit_field = $all_field;
}

// page begin ----------------------------------------------------
?>
<html>
<head>
<title>修改资料：<?php echo $line["name"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/jquery.min.js" language="javascript"></script>
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<style>
.dischk {width:6em; height:16px; line-height:16px; vertical-align:middle; white-space:nowrap; text-overflow:ellipsis; overflow:hidden; padding:0; margin:0; }
.p_data {height:12px; line-height:12px; font-size:12px; padding:4px 20px 2px 3px; border:1px solid #7f9db9; background:#f5f4eb; }
</style>
<script language="javascript">
function check_data(oForm) {
	if (!confirm("确认编辑好了要提交吗？如果还要再看看，请点“取消”")) {
		return false;
	}
	return true;
}

function input(id, value) {
	if (byid(id).disabled != true) {
		byid(id).value = value;
	}
}

function input_date(id, value) {
	var cv = byid(id).value;
	var time = cv.split(" ")[1];

	if (byid(id).disabled != true) {
		byid(id).value = value+" "+(time ? time : '00:00:00');
	}
}

function input_time(id, time) {
	var s = byid(id).value;
	if (s == '') {
		alert("请先填写日期，再填写时间！");
		return;
	}
	var date = s.split(" ")[0];
	var datetime = date+" "+time;

	if (byid(id).disabled != true) {
		byid(id).value = datetime;
	}
}

// 检查数据重复:
function check_repeat(type, obj) {
	if (!byid("id") || (byid("id").value == '0' || byid("id").value == '')) {
		var value = obj.value;
		if (value != '') {
			var xm = new ajax();
			xm.connect("/http/check_repeat.php?type="+type+"&value="+value+"&r="+Math.random(), "GET", "", check_repeat_do);
		}
	}
}

function check_repeat_do(o) {
	var out = ajax_out(o);
	if (out["status"] == "ok") {
		if (out["tips"] != '') {
			alert(out["tips"]);
		}
	}
}

function show_hide_engine(o) {
	byid("engine_show").style.display = (o.value == "网络" ? "inline" : "none");
}

function show_hide_area(o) {
	byid("area_from_box").style.display = (o.value == "2" ? "inline" : "none");
}

function show_hide_disease_add(o) {
	byid("disease_add_box").style.display = (o.value == "-1" ? "inline" : "none");
}

function set_color(o) {
	if (o.checked) {
		o.nextSibling.style.color = "blue";
	} else {
		o.nextSibling.style.color = "";
	}
}
//获取预约号
function getonum(){
	$.ajax({
				url:"/m/patient/getordernum.php",
				type: "get",
				data:{
					op:"getonum"
					},
				success: function(response){
					$("#ordernum").val(response);
					}
				});
	}

</script>
</head>

<body>
<form name="mainform" method="POST" onSubmit="return check_data(this)">
<table width="100%" class="edit">
	<tr>
		<td class="head">病人资料</td>
		<td class="head"><b>提示：</b>下面的每项资料如果修改，都会有日志记录，如无必要，请勿修改。</td>
	</tr>

	<tr>
		<td class="left">姓名：</td>
		<td class="right">
<?php if (@in_array("name", $edit_field)) { ?>
			<input name="name" value="<?php echo $line["name"]; ?>" class="input" style="width:200px" onChange="check_repeat('name', this)"> <span class="intro">* 名称必须填写</span>
<?php } else { ?>
			<span class="p_data"><?php echo $line["name"]; ?></span>
<?php } ?>
		</td>
	</tr>

	<tr>
		<td class="left">性别：</td>
		<td class="right">
<?php if (@in_array("sex", $edit_field)) { ?>
			<input name="sex" id="sex" value="<?php echo $line["sex"]; ?>" class="input" style="width:80px"> <a href="javascript:input('sex', '男')">[男]</a> <a href="javascript:input('sex', '女')">[女]</a> <span class="intro">填写病人性别</span>
<?php } else { ?>
			<span class="p_data"><?php echo $line["sex"] ? $line["sex"] : '<font color="silver">(无)</font>'; ?></span>
<?php } ?>
		</td>
	</tr>

	<tr>
		<td class="left">年龄：</td>
		<td class="right">
<?php if (@in_array("age", $edit_field)) { ?>
			<input name="age" value="<?php echo $line["age"]; ?>" class="input" style="width:80px"> <span class="intro">填写年龄</span>
<?php } else { ?>
			<span class="p_data"><?php echo $line["age"] ? $line["age"] : '<font color="silver">(无)</font>'; ?></span>
<?php } ?>
		</td>
	</tr>

	<tr>
		<td class="left">电话：</td>
		<td class="right">
<?php if (@in_array("tel", $edit_field)) { ?>
			<input name="tel" value="<?php echo $line["tel"]; ?>" class="input" style="width:200px" onChange="check_repeat('tel', this)">  <span class="intro">电话号码或手机(可不填)</span>
<?php } else { ?>
			<span class="p_data"><?php echo ($show_tel || $line["author"] == $realname) ? ($line["tel"] ? $line["tel"] : '<font color="silver">(无)</font>') : '<font color="silver">(无权限)</font>'; ?></span>
<?php } ?>
		</td>
	</tr>

	<tr>
		<td class="left">QQ：</td>
		<td class="right">
<?php if (@in_array("qq", $edit_field)) { ?>
			<input name="qq" value="<?php echo $line["qq"]; ?>" class="input" style="width:140px">  <span class="intro">病人QQ号码</span>
<?php } else { ?>
			<span class="p_data"><?php echo ($show_tel || $line["author"] == $realname) ? ($line["qq"] ? $line["qq"] : '<font color="silver">(无)</font>') : '<font color="silver">(无权限)</font>'; ?></span>
<?php } ?>
		</td>
	</tr>

	<tr>
		<td class="left" valign="top">咨询内容：</td>
		<td class="right">
<?php if (@in_array("content", $edit_field)) { ?>
			<font color="red">请注意：此栏填写咨询内容摘要，<b>不要粘贴聊天记录</b>。聊天记录请复制到下面“聊天记录”一栏中。</font><br>
			<textarea name="content" style="width:60%; height:50px;vertical-align:middle;" class="input"><?php echo $line["content"]; ?></textarea> <span class="intro">咨询内容总结，最多200个字</span>
<?php } else { ?>
			<span class="p_data"><?php echo $line["content"] ? $line["content"] : '<font color="silver">(无)</font>'; ?></span>
<?php } ?>
		</td>
	</tr>

	<tr>
		<td class="left" valign="top">聊天记录：</td>
		<td class="right">
<?php if (@in_array("talk_content", $edit_field)) { ?>
			<textarea name="talk_content" style="width:60%; height:100px;vertical-align:middle;" class="input"><?php echo $line["talk_content"]; ?></textarea> <span class="intro">聊天内容（可不填）</span>
<?php } else { ?>
	<?php if (trim($line["talk_content"]) != '') { ?>
			<div class="p_data" style="height:150px; overflow-y:scroll;"><?php echo $line["talk_content"]; ?></div>
	<?php } else { ?>
			<font color="silver">(无)</font>
	<?php } ?>
<?php } ?>
		</td>
	</tr>

	<tr>
		<td class="left" valign="top">病患类型：</td>
		<td class="right">
<?php if (@in_array("disease", $edit_field)) { ?>
<select id="disease_1" name="disease_id" onChange="update_disease_2(this.value)" class="combo">
				<option value="" style="color:gray">-请选择科室-</option>
				<?php
					echo list_option($disease_list, "_key_", "_value_", $line["disease_id"]); 
				?>
			</select>
			<span id="disease_2_area">
            	 <?php 
					if($diseaseid = $line["disease_id"]){ ?>
            	<select  name="disease_2"  class="combo">
                	<option value="" style="color:gray">-请选择疾病-</option>
                    <?php 
						$zibz = $db->query("select * from disease where hospital_id='$hid' and isshow=1 and disease_2!='' and id = '$diseaseid'", 1);
						$zibz1 = explode(",",$zibz["disease_2"]);
						echo list_option($zibz1, "_value_", "_value_", $line["disease_2"]);
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
							s += '  <option value="" style="color:gray">-请选择疾病-</option>';
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
<?php } else { ?>
			<span class="p_data"><?php echo $line["disease_id"] ? _show_disease($line["disease_id"]) : '<font color="silver">(无)</font>'; ?></span>
			<span class="p_data"><?php echo $line["disease_2"] ? _show_disease($line["disease_2"]) : '<font color="silver">(无)</font>'; ?></span>
<?php } ?>
		</td>
	</tr>

<?php if (count($depart_list) > 0) { ?>

	<tr>
		<td class="left">所属科室：</td>
		<td class="right">
<?php if (@in_array("depart", $edit_field)) { ?>
			<select name="depart" class="combo" <?php echo $ce["depart"]; ?>>
				<option value="0" style="color:gray">--请选择--</option>
				<?php echo list_option($depart_list, 'id', 'name', $line["depart"]); ?>
			</select>
			<span class="intro">请选择医院科室</span>
<?php } else { ?>
			<span class="p_data"><?php echo $line["depart"] ? $line["depart"] : '<font color="silver">(无)</font>'; ?></span>
<?php } ?>
		</td>
	</tr>
<?php } ?>

	<tr>
		<td class="left">媒体来源：</td>
		<td class="right">
<?php if (@in_array("media", $edit_field)) { ?>
			<select name="media_from" class="combo" onChange="show_hide_engine(this)">
				<option value="" style="color:gray">--请选择--</option>
				<?php echo list_option($media_from_array, '_value_', '_value_', $line["media_from"]); ?>
			</select>&nbsp;
				关键词：<input name="engine_key" value="<?php echo $line["engine_key"]; ?>" class="input" size="25">
                渠道网址：<input name="from_site" value="<?php echo $line["from_site"]; ?>" class="input" size="110">
<?php } else { ?>
			<span class="p_data"><?php echo $line["media_from"]."  ".$line["engine_key"]."  ".$line["from_site"]; ?> </span>
<?php } ?>
		</td>
	</tr>

	<tr>
		<td class="left">地区来源：</td>
		<td class="right">
<?php if (@in_array("area", $edit_field)) { ?>
				<input name="area" value="<?php echo $line["area"]; ?>" class="input" size="14">
<?php } else { ?>
			<span class="p_data"><?php echo $line["area"]; ?></span>
<?php } ?>
		</td>
	</tr>

	<!--<tr>
		<td class="left">所属统计帐户：</td>
		<td class="right">
<?php if (@in_array("account", $edit_field)) { ?>
			<select name="from_account" class="combo" <?php echo $ce["from_account"]; ?>>
				<option value="0" style="color:gray">--请选择--</option>
				<?php echo list_option($account_list, '_key_', '_value_', ($op == "add" ? $account_first : $line["from_account"])); ?>
			</select>&nbsp;
			<span class="intro">请选择所属统计帐户</span>
<?php } else { ?>
			<span class="p_data"><?php echo $line["from_account"] ? $line["from_account"] : '<font color="silver">(无)</font>'; ?> </span>
<?php } ?>
		</td>
	</tr>-->
	
	<tr>
		<td class="left">门诊号：</td>
		<td class="right">
<?php if (@in_array("zhuanjia_num", $edit_field) && $line["status"] != 1) { ?>
			<input name="zhuanjia_num" value="<?php echo $line["zhuanjia_num"]; ?>" class="input" size="30" style="width:80px" >  <span class="intro"></span>
<?php } else { ?>
	<input name="zhuanjia_num" value="<?php echo $line["zhuanjia_num"]; ?>" class="input" size="30" style="width:80px" readonly>
<?php } ?>
		</td>
	</tr>

	<tr>
		<td class="left" valign="top">预约到院时间：</td>
		<td class="right">
<?php if (@in_array("order_date", $edit_field) && $line["status"] != 1) { ?>
			<input name="order_date" value="<?php echo $line["order_date"] ? @date('Y-m-d H:i:s', $line["order_date"]) : ''; ?>" class="input" style="width:150px" id="order_date" <?php echo $ce["order_date"]; ?>> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'order_date',dateFmt:'yyyy-MM-dd HH:mm:ss'})" align="absmiddle" style="cursor:pointer" title="选择时间"> <span class="intro">已修改<?php echo intval($line["order_date_changes"]); ?>次</span> <span class="intro">请注意，此处已调整，预约时间不能早于上个月<?php echo date("j"); ?>号，否则资料无法提交。</span><?php if ($line["order_date_log"]) { ?><a href="javascript:void(0)" onClick="byid('order_date_log').style.display = (byid('order_date_log').style.display == 'none' ? 'block' : 'none'); ">查看修改记录</a><?php } ?>
		<?php
		$show_days = array(
			"今" => $today = date("Y-m-d"), //今天
			"明" => date("Y-m-d", strtotime("+1 day")), //明天
			"后" => date("Y-m-d", strtotime("+2 days")), //后天
			"大后天" => date("Y-m-d", strtotime("+3 days")), //大后天
			"周六" => date("Y-m-d", strtotime("next Saturday")), //周六
			"周日" => date("Y-m-d", strtotime("next Sunday")), // 周日
			"周一" => date("Y-m-d", strtotime("next Monday")), // 周一
			"一周后" => date("Y-m-d", strtotime("+7 days")), // 一周后
			"半月后" => date("Y-m-d", strtotime("+15 days")), //半个月后
		);
		if (!$ce["order_date"]) {
			echo '<div style="padding-top:6px;">日期: ';
			foreach ($show_days as $name => $value) {
				echo '<a href="javascript:input_date(\'order_date\', \''.$value.'\')">['.$name.']</a>&nbsp;';
			}
			echo '<br>时间: ';
			echo '<a href="javascript:input_time(\'order_date\',\'00:00:00\')">[时间不限]</a>&nbsp;';
			echo '<a href="javascript:input_time(\'order_date\',\'09:00:00\')">[上午9点]</a>&nbsp;';
			echo '<a href="javascript:input_time(\'order_date\',\'14:00:00\')">[下午2点]</a>&nbsp;</div>';
		}
		?>
		<?php if ($line["order_date_log"]) { ?>
		<div id="order_date_log" style="display:none; padding-top:6px;"><b>预约时间修改记录:</b> <br><?php echo strim($line["order_date_log"], '<br>'); ?></div>
		<?php } ?>
<?php } else { ?>
			<!--<span class="p_data"><?php echo @date('Y-m-d H:i', $line["order_date"]); ?> </span>-->
            <input name="order_date" value="<?php echo @date('Y-m-d H:i:s', $line["order_date"])?>" class="input" readonly style="width:150px" id="order_date">
<?php } ?>
		</td>
	</tr>
	<tr>
		<td class="left">预约号：</td>
		<td class="right">
<?php if (@in_array("ordernum", $edit_field)) { ?>
			<input name="ordernum" id="ordernum" value="<?php echo $line["ordernum"]; ?>" readonly class="input" size="30" style="width:80px" >&nbsp;
            <button type="button" onClick="getonum();return false;">获取预约号</button>
<?php } else { ?>
			<span class="p_data"><?php echo $line["ordernum"] ? $line["ordernum"] : '<font color="silver">(无)</font>'; ?> </span>
<?php } ?>
		</td>
	</tr>
	<tr>
		<td class="left" valign="top">当前备注：</td>
		<td class="right"><?php echo trim($line["memo"]) != '' ? text_show(strip_tags($line["memo"])) : '<font color="silver">(无)</font>'; ?></td>
	</tr>

<?php if (@in_array("memo", $edit_field)) { ?>
	<tr>
		<td class="left" valign="top">添加备注：</td>
		<td class="right"><textarea name="memo" style="width:60%; height:48px;vertical-align:middle;" class="input"></textarea> <span class="intro">添加一条备注信息</span></td>
	</tr>
<?php } ?>

<?php if ($line["edit_log"]) { ?>
	<tr>
		<td class="left" valign="top">资料修改记录：</td>
		<td class="right"><?php echo text_show(strip_tags(trim($line["edit_log"]))); ?></td>
	</tr>
<?php } ?>

</table>

<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="crc" value="<?php echo $_GET["crc"]; ?>">
<input type="hidden" name="op" value="edit">
<input type="hidden" name="ef_check" value="<?php echo base64_encode(implode(",", $edit_field)); ?>">

<div class="button_line">
	<input type="submit" class="submit" value="提交资料">
</div>

</form>

</body>
</html>