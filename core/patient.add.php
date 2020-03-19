<?php
/*
// - 功能说明 : 新增病人
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2012-07-16
*/

if ($_POST) {
	$po = &$_POST; //引用 $_POST

	// 检查一个月内的病人中有无重复的:
	$tel = trim($po["tel"]);
	if (strlen($tel) >= 7) {
		$thetime = strtotime("-1 month");
		$list = $db->query("select * from $table where tel='$tel' and addtime>$thetime limit 1", 1);
		if ($list && count($list) > 0) {
			exit_html("电话号码：{$tel} 有重复，该病人无法提交！");
		}
	}

	$r = array();
	$r["name"] = trim($po["name"]);
	$r["tel"] = trim($po["tel"]);
	if (strlen($r["tel"]) == 11) {
		$r["tel_location"] = @get_mobile_location($r["tel"]);
	}
	$r["sex"] = $po["sex"];
	$r["qq"] = $po["qq"];
	$r["age"] = $po["age"];
	$r["content"] = $po["content"];
	//$r["disease_id"] = trim(implode(",", $po["disease_id"]), ",");
	$r["disease_id"] = $po["disease_id"];
	$r["disease_2"] = trim($po["disease_2"]);
	$r["depart"] = $po["depart"];
	$r["media_from"] = $po["media_from"];
	$r["engine"] = $po["engine"];
	$r["engine_key"] = $po["engine_key"];
	$r["from_site"] = $po["from_site"];
	$r["from_account"] = $po["from_account"];
	$r["zhuanjia_num"] = $po["zhuanjia_num"];
	$r["is_local"] = $po["is_local"];
	$r["area"] = $po["area"];


	if ($uinfo["part_id"] == 4) {
		$r["status"] = 1; // 导医添加直接设置为已到:
		$r["order_date"] = time(); //预约时间直接为到院时间
		$r["doctor"] = $po["doctor"]; //接待医生
	} else {
		$order_date_post = @strtotime($po["order_date"]);
		if ($order_date_post < strtotime("-1 day")) {
			exit_html("预约时间必须是未来的时间，不能是已经过了的时间，请返回重新填写。");
		}
		$r["order_date"] = $order_date_post; //新增
	}

	$r["memo"] = $po["memo"];
	$r["part_id"] = $uinfo["part_id"];
	$r["addtime"] = time();
	$r["author"] = $realname;

	$sqldata = $db->sqljoin($r);
	$sql = "insert into $table set $sqldata";

	ob_start();
	$id = $db->query($sql);
	$error = ob_get_clean();

	if ($error == '' && $id > 0) {
		// 聊天记录保存为文件@2012-02-26
		if (isset($_POST["talk_content"])) {
			@put_talk_content($hid, $id, stripslashes($_POST["talk_content"]));
		}
		echo '<script type="text/javascript">';
		echo 'parent.load_box(0);';
		echo 'parent.msg_box("添加成功");';
		echo 'parent.update_content();';
		echo '</script>';
	} else {
		echo "资料提交出错，请联系开发人员解决: <br><br>";
		echo $db->sql."<br><br>";
		echo $error."<br><br>";
	}
	exit;
}


// 读取字典:
$hospital_list = $db->query("select id,name from hospital");
$disease_list = $db->query("select id,name from disease where hospital_id='$hid' and isshow=1 order by sort desc,sort2 desc", "id", "name");
$disease_2_list = $db->query("select id,disease_2 from disease where hospital_id='$hid' and isshow=1 and disease_2!=''", "id", "disease_2");
$doctor_list = $db->query("select id,name from doctor where hospital_id='$hid'");
$part_id_name = $db->query("select id,name from sys_part", "id", "name");
$depart_list = $db->query("select id,name from depart where hospital_id='$hid'");
$engine_list = $db->query("select id,name from engine", "id", "name");
$sites_list = $db->query("select id,url from sites where hid=$hid", "id", "url");
$account_list = $db->query("select id,concat(name,if(type='web',' (网络)',' (电话)')) as fname from count_type where hid=$hid order by id asc", "id", "fname");

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
/*
$show_disease = array();
foreach ($disease_list as $k => $v) {
	$show_disease[$k] = $v;
	if (count($show_disease) >= 30) {
		break;
	}
}
*/

// 2010-08-18
$media_from_array = explode(" ", "网络 电话");
$media_from_array2 = $db->query("select name from media where (hospital_id=0 or hospital_id='$hid') order by sort desc, id asc", "", "name");
foreach ($media_from_array2 as $v) {
	if (!in_array($v, $media_from_array)) {
		$media_from_array[] = $v;
	}
}

// 2010-10-23
$is_local_array = array(1 => "本市", 2 => "外地");


// page begin ----------------------------------------------------
?>
<html>
<head>
<title><?php echo $hinfo["name"]; ?>：添加病人</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<style>
.dischk {width:6em; height:16px; line-height:16px; vertical-align:middle; white-space:nowrap; text-overflow:ellipsis; overflow:hidden; padding:0; margin:0; }
</style>
<script language="javascript">
function check_data(oForm) {
	if (oForm.name.value == "") {
		alert("请输入病人姓名！"); oForm.name.focus(); return false;
	}
	if (oForm.tel.value != "" && get_num(oForm.tel.value) == '') {
		alert("请正确输入病人的联系电话！"); oForm.tel.focus(); return false;
	}
	if (oForm.sex.value == '') {
		alert("请输入“性别”！"); oForm.sex.focus(); return false;
	}
	if (oForm.media_from.value == '') {
		alert("请选择“媒体来源”！"); oForm.media_from.focus(); return false;
	}
	if (oForm.order_date.value.length < 12) {
		alert("请正确填写“预约时间”！"); oForm.order_date.focus(); return false;
	}
	if (oForm.content.value.length > 200) {
		alert("咨询内容中填写了超过200个字，超过系统限制无法提交，请检查。"); oForm.content.focus(); return false;
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


function set_color(o) {
	if (o.checked) {
		o.nextSibling.style.color = "blue";
	} else {
		o.nextSibling.style.color = "";
	}
}

</script>
</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center">添加病人</td><td class="bar_right"></td></tr></table></div>
	<div class="header_center"></div>
	<div class="headers_oprate"></div>
</div>
<!-- 头部 end -->

<div class="space"></div>
<div class="description">
	<div class="d_item">提示： 1.姓名必须填写；　2.电话号码如果填写，则必须是数字，不少于7位；　3.未尽资料填写于备注中。</div>
</div>

<div class="space"></div>
<form name="mainform" method="POST" onsubmit="return check_data(this)">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">病人预约资料</td>
	</tr>
	<tr>
		<td class="left">姓名：</td>
		<td class="right"><input name="name" id="name" value="" class="input" style="width:200px" onchange="check_repeat('name', this)"> <span class="intro">* 名称必须填写</span></td>
	</tr>
	<tr>
		<td class="left">性别：</td>
		<td class="right"><input name="sex" id="sex" value="" class="input" style="width:80px"> <a href="javascript:input('sex', '男')">[男]</a> <a href="javascript:input('sex', '女')">[女]</a> <span class="intro">填写病人性别</span></td>
	</tr>
	<tr>
		<td class="left">年龄：</td>
		<td class="right"><input name="age" id="age" value="" class="input" style="width:80px"> <span class="intro">填写年龄</span></td>
	</tr>
	<tr>
		<td class="left">电话：</td>
		<td class="right"><input name="tel" id="tel" value="" class="input" style="width:200px" onchange="check_repeat('tel', this)">  <span class="intro">电话号码或手机(可不填)</span></td>
	</tr>
	<tr>
		<td class="left">QQ：</td>
		<td class="right"><input name="qq" value="" class="input" style="width:140px">  <span class="intro">病人QQ号码</span></td>
	</tr>
	<tr>
		<td class="left" valign="top">咨询内容：</td>
		<td class="right">
			<font color="red">请注意：此栏填写咨询内容摘要，<b>不要粘贴聊天记录</b>。聊天记录请复制到下面“聊天记录”一栏中。</font><br>
			<textarea name="content" style="width:60%; height:50px;vertical-align:middle;" class="input"></textarea> <span class="intro">咨询内容总结，最多200个字</span>
		</td>
	</tr>
	<tr>
		<td class="left" valign="top">聊天记录：</td>
		<td class="right"><textarea name="talk_content" style="width:60%; height:100px;vertical-align:middle;" class="input"></textarea> <span class="intro">聊天内容（可不填）</span></td>
	</tr>
	<tr>
		<td class="left" valign="top">病患类型：</td>
		<td class="right">
			<select id="disease_1" name="disease_id" onchange="update_disease_2(this.value)" class="combo">
				<option value="" style="color:gray">-请选择一级疾病-</option>
				<?php echo list_option($disease_list, "_key_", "_value_", intval($line["disease"])); ?>
			</select>
			<span id="disease_2_area">
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
			var default_disease_2 = "<?php echo $line["disease_2"]; ?>";
			function update_disease_2(id) {
				var s = '';
				if (id > 0) {
					var o = byid("disease_id_2_"+id);
					if (o) {
						var s_arr = o.value.split(",");
						if (s_arr.length > 0) {
							s += '<select name="disease_2" class="combo">';
							s += '  <option value="" style="color:gray">-请选择二级疾病-</option>';
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

<?php if (count($depart_list) > 0) { ?>
	<tr>
		<td class="left">所属科室：</td>
		<td class="right">
			<select name="depart" class="combo">
				<option value="0" style="color:gray">--请选择--</option>
				<?php echo list_option($depart_list, 'id', 'name'); ?>
			</select>
			<span class="intro">请选择医院科室</span>
		</td>
	</tr>
<?php } ?>

	<tr>
		<td class="left">媒体来源：</td>
		<td class="right">
			<select name="media_from" class="combo" onchange="show_hide_engine(this)">
				<option value="" style="color:gray">--请选择--</option>
				<?php echo list_option($media_from_array, '_value_', '_value_'); ?>
			</select>&nbsp;
			<span id="engine_show" style="display:none">
				<select name="engine" class="combo">
					<option value="" style="color:gray">--搜索引擎来源--</option>
					<?php echo list_option($engine_list, '_value_', '_value_'); ?>
				</select>
				关键词：<input name="engine_key" value="" class="input" size="15">
				<select name="from_site" class="combo">
					<option value="" style="color:gray">--来源网站--</option>
					<?php echo list_option($sites_list, '_value_', '_value_'); ?>
				</select>
			</span>
			<span class="intro">请选择媒体来源</span>
		</td>
	</tr>

	<tr>
		<td class="left">地区来源：</td>
		<td class="right">
			<select name="is_local" class="combo" onchange="show_hide_area(this)">
				<option value="0" style="color:gray">--请选择--</option>
				<?php echo list_option($is_local_array, '_key_', '_value_', 1); ?>
			</select>&nbsp;
			<span id="area_from_box" style="display:none">
				地区：<input name="area" value="" class="input" size="14">&nbsp;
			</span>
			<span class="intro">病人来源默认为本地(在新增病人时)</span>
		</td>
	</tr>

	<tr>
		<td class="left">所属统计帐户：</td>
		<td class="right">
			<select name="from_account" class="combo">
				<option value="0" style="color:gray">--请选择--</option>
				<?php echo list_option($account_list, '_key_', '_value_', $account_first); ?>
			</select>&nbsp;

			<span class="intro">请选择所属统计帐户</span>
		</td>
	</tr>

	<tr>
		<td class="left">专家号：</td>
		<td class="right"><input name="zhuanjia_num" value="" class="input" size="30" style="width:200px">  <span class="intro">预约专家号</span></td>
	</tr>
	<tr>
		<td class="left" valign="top">预约时间：</td>
		<td class="right">
<?php if ($uinfo["part_id"] == 4) { ?>
		<b>您是导医身份，该时间将自动设为当前时间，不可更改</b>
		<input type="hidden" name="order_date" value="<?php echo date("Y-m-d H:i:s"); ?>" /><!-- 只作为js检查通过用 -->
<?php } else { ?>
		<input name="order_date" value="" class="input" style="width:150px" id="order_date"> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'order_date',dateFmt:'yyyy-MM-dd HH:mm:ss'})" align="absmiddle" style="cursor:pointer" title="选择时间">
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
		echo '<div style="padding-top:6px;">日期: ';
		foreach ($show_days as $name => $value) {
			echo '<a href="javascript:input_date(\'order_date\', \''.$value.'\')">['.$name.']</a>&nbsp;';
		}
		echo '<br>时间: ';
		echo '<a href="javascript:input_time(\'order_date\',\'00:00:00\')">[时间不限]</a>&nbsp;';
		echo '<a href="javascript:input_time(\'order_date\',\'09:00:00\')">[上午9点]</a>&nbsp;';
		echo '<a href="javascript:input_time(\'order_date\',\'14:00:00\')">[下午2点]</a>&nbsp;</div>';

?>
<?php } ?>
		</td>
	</tr>

	<tr>
		<td class="left" valign="top">备注：</td>
		<td class="right"><textarea name="memo" style="width:60%; height:48px;vertical-align:middle;" class="input"></textarea> <span class="intro">其他备注信息</span></td>
	</tr>
</table>


<?php if ($uinfo["part_id"] == 4) { ?>
<div class="space"></div>
<table width="100%" class="edit">
	<tr>
		<td class="head">导医选项</td>
		<td class="head">由于您是导医身份，添加的患者资料直接为“<b style="color:red">已到院</b>”状态，请设置下面的接待医生：</td>
	</tr>
	<tr>
		<td class="left">接待医生：</td>
		<td class="right">
			<select name="doctor" class="combo">
				<option value="" style="color:gray">--请选择--</option>
				<?php echo list_option($doctor_list, 'name', 'name'); ?>
			</select>&nbsp;<span class="intro">病人接待医生</span>
		</td>
	</tr>
</table>
<?php } ?>

<input type="hidden" name="op" value="add">

<div class="button_line">
	<input type="submit" class="submit" value="提交资料">
</div>

</form>

</body>
</html>