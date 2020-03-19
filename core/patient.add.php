<?php
/*
// - ����˵�� : ��������
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2012-07-16
*/

if ($_POST) {
	$po = &$_POST; //���� $_POST

	// ���һ�����ڵĲ����������ظ���:
	$tel = trim($po["tel"]);
	if (strlen($tel) >= 7) {
		$thetime = strtotime("-1 month");
		$list = $db->query("select * from $table where tel='$tel' and addtime>$thetime limit 1", 1);
		if ($list && count($list) > 0) {
			exit_html("�绰���룺{$tel} ���ظ����ò����޷��ύ��");
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
		$r["status"] = 1; // ��ҽ���ֱ������Ϊ�ѵ�:
		$r["order_date"] = time(); //ԤԼʱ��ֱ��Ϊ��Ժʱ��
		$r["doctor"] = $po["doctor"]; //�Ӵ�ҽ��
	} else {
		$order_date_post = @strtotime($po["order_date"]);
		if ($order_date_post < strtotime("-1 day")) {
			exit_html("ԤԼʱ�������δ����ʱ�䣬�������Ѿ����˵�ʱ�䣬�뷵��������д��");
		}
		$r["order_date"] = $order_date_post; //����
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
		// �����¼����Ϊ�ļ�@2012-02-26
		if (isset($_POST["talk_content"])) {
			@put_talk_content($hid, $id, stripslashes($_POST["talk_content"]));
		}
		echo '<script type="text/javascript">';
		echo 'parent.load_box(0);';
		echo 'parent.msg_box("��ӳɹ�");';
		echo 'parent.update_content();';
		echo '</script>';
	} else {
		echo "�����ύ��������ϵ������Ա���: <br><br>";
		echo $db->sql."<br><br>";
		echo $error."<br><br>";
	}
	exit;
}


// ��ȡ�ֵ�:
$hospital_list = $db->query("select id,name from hospital");
$disease_list = $db->query("select id,name from disease where hospital_id='$hid' and isshow=1 order by sort desc,sort2 desc", "id", "name");
$disease_2_list = $db->query("select id,disease_2 from disease where hospital_id='$hid' and isshow=1 and disease_2!=''", "id", "disease_2");
$doctor_list = $db->query("select id,name from doctor where hospital_id='$hid'");
$part_id_name = $db->query("select id,name from sys_part", "id", "name");
$depart_list = $db->query("select id,name from depart where hospital_id='$hid'");
$engine_list = $db->query("select id,name from engine", "id", "name");
$sites_list = $db->query("select id,url from sites where hid=$hid", "id", "url");
$account_list = $db->query("select id,concat(name,if(type='web',' (����)',' (�绰)')) as fname from count_type where hid=$hid order by id asc", "id", "fname");

$account_first = 0;
if (count($account_list) > 0) {
	$tmp = @array_keys($account_list);
	$account_first = $tmp[0];
}

$status_array = array(
	array("id"=>0, "name"=>'�ȴ�'),
	array("id"=>1, "name"=>'�ѵ�'),
	array("id"=>2, "name"=>'δ��'),
);

$xiaofei_array = array(
	array("id"=>0, "name"=>'δ����'),
	array("id"=>1, "name"=>'������'),
);


// ȡǰ30������:
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
$media_from_array = explode(" ", "���� �绰");
$media_from_array2 = $db->query("select name from media where (hospital_id=0 or hospital_id='$hid') order by sort desc, id asc", "", "name");
foreach ($media_from_array2 as $v) {
	if (!in_array($v, $media_from_array)) {
		$media_from_array[] = $v;
	}
}

// 2010-10-23
$is_local_array = array(1 => "����", 2 => "���");


// page begin ----------------------------------------------------
?>
<html>
<head>
<title><?php echo $hinfo["name"]; ?>����Ӳ���</title>
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
		alert("�����벡��������"); oForm.name.focus(); return false;
	}
	if (oForm.tel.value != "" && get_num(oForm.tel.value) == '') {
		alert("����ȷ���벡�˵���ϵ�绰��"); oForm.tel.focus(); return false;
	}
	if (oForm.sex.value == '') {
		alert("�����롰�Ա𡱣�"); oForm.sex.focus(); return false;
	}
	if (oForm.media_from.value == '') {
		alert("��ѡ��ý����Դ����"); oForm.media_from.focus(); return false;
	}
	if (oForm.order_date.value.length < 12) {
		alert("����ȷ��д��ԤԼʱ�䡱��"); oForm.order_date.focus(); return false;
	}
	if (oForm.content.value.length > 200) {
		alert("��ѯ��������д�˳���200���֣�����ϵͳ�����޷��ύ�����顣"); oForm.content.focus(); return false;
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
		alert("������д���ڣ�����дʱ�䣡");
		return;
	}
	var date = s.split(" ")[0];
	var datetime = date+" "+time;

	if (byid(id).disabled != true) {
		byid(id).value = datetime;
	}
}


// ��������ظ�:
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
	byid("engine_show").style.display = (o.value == "����" ? "inline" : "none");
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
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center">��Ӳ���</td><td class="bar_right"></td></tr></table></div>
	<div class="header_center"></div>
	<div class="headers_oprate"></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>
<div class="description">
	<div class="d_item">��ʾ�� 1.����������д����2.�绰���������д������������֣�������7λ����3.δ��������д�ڱ�ע�С�</div>
</div>

<div class="space"></div>
<form name="mainform" method="POST" onsubmit="return check_data(this)">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">����ԤԼ����</td>
	</tr>
	<tr>
		<td class="left">������</td>
		<td class="right"><input name="name" id="name" value="" class="input" style="width:200px" onchange="check_repeat('name', this)"> <span class="intro">* ���Ʊ�����д</span></td>
	</tr>
	<tr>
		<td class="left">�Ա�</td>
		<td class="right"><input name="sex" id="sex" value="" class="input" style="width:80px"> <a href="javascript:input('sex', '��')">[��]</a> <a href="javascript:input('sex', 'Ů')">[Ů]</a> <span class="intro">��д�����Ա�</span></td>
	</tr>
	<tr>
		<td class="left">���䣺</td>
		<td class="right"><input name="age" id="age" value="" class="input" style="width:80px"> <span class="intro">��д����</span></td>
	</tr>
	<tr>
		<td class="left">�绰��</td>
		<td class="right"><input name="tel" id="tel" value="" class="input" style="width:200px" onchange="check_repeat('tel', this)">  <span class="intro">�绰������ֻ�(�ɲ���)</span></td>
	</tr>
	<tr>
		<td class="left">QQ��</td>
		<td class="right"><input name="qq" value="" class="input" style="width:140px">  <span class="intro">����QQ����</span></td>
	</tr>
	<tr>
		<td class="left" valign="top">��ѯ���ݣ�</td>
		<td class="right">
			<font color="red">��ע�⣺������д��ѯ����ժҪ��<b>��Ҫճ�������¼</b>�������¼�븴�Ƶ����桰�����¼��һ���С�</font><br>
			<textarea name="content" style="width:60%; height:50px;vertical-align:middle;" class="input"></textarea> <span class="intro">��ѯ�����ܽᣬ���200����</span>
		</td>
	</tr>
	<tr>
		<td class="left" valign="top">�����¼��</td>
		<td class="right"><textarea name="talk_content" style="width:60%; height:100px;vertical-align:middle;" class="input"></textarea> <span class="intro">�������ݣ��ɲ��</span></td>
	</tr>
	<tr>
		<td class="left" valign="top">�������ͣ�</td>
		<td class="right">
			<select id="disease_1" name="disease_id" onchange="update_disease_2(this.value)" class="combo">
				<option value="" style="color:gray">-��ѡ��һ������-</option>
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
							s += '  <option value="" style="color:gray">-��ѡ���������-</option>';
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
		<td class="left">�������ң�</td>
		<td class="right">
			<select name="depart" class="combo">
				<option value="0" style="color:gray">--��ѡ��--</option>
				<?php echo list_option($depart_list, 'id', 'name'); ?>
			</select>
			<span class="intro">��ѡ��ҽԺ����</span>
		</td>
	</tr>
<?php } ?>

	<tr>
		<td class="left">ý����Դ��</td>
		<td class="right">
			<select name="media_from" class="combo" onchange="show_hide_engine(this)">
				<option value="" style="color:gray">--��ѡ��--</option>
				<?php echo list_option($media_from_array, '_value_', '_value_'); ?>
			</select>&nbsp;
			<span id="engine_show" style="display:none">
				<select name="engine" class="combo">
					<option value="" style="color:gray">--����������Դ--</option>
					<?php echo list_option($engine_list, '_value_', '_value_'); ?>
				</select>
				�ؼ��ʣ�<input name="engine_key" value="" class="input" size="15">
				<select name="from_site" class="combo">
					<option value="" style="color:gray">--��Դ��վ--</option>
					<?php echo list_option($sites_list, '_value_', '_value_'); ?>
				</select>
			</span>
			<span class="intro">��ѡ��ý����Դ</span>
		</td>
	</tr>

	<tr>
		<td class="left">������Դ��</td>
		<td class="right">
			<select name="is_local" class="combo" onchange="show_hide_area(this)">
				<option value="0" style="color:gray">--��ѡ��--</option>
				<?php echo list_option($is_local_array, '_key_', '_value_', 1); ?>
			</select>&nbsp;
			<span id="area_from_box" style="display:none">
				������<input name="area" value="" class="input" size="14">&nbsp;
			</span>
			<span class="intro">������ԴĬ��Ϊ����(����������ʱ)</span>
		</td>
	</tr>

	<tr>
		<td class="left">����ͳ���ʻ���</td>
		<td class="right">
			<select name="from_account" class="combo">
				<option value="0" style="color:gray">--��ѡ��--</option>
				<?php echo list_option($account_list, '_key_', '_value_', $account_first); ?>
			</select>&nbsp;

			<span class="intro">��ѡ������ͳ���ʻ�</span>
		</td>
	</tr>

	<tr>
		<td class="left">ר�Һţ�</td>
		<td class="right"><input name="zhuanjia_num" value="" class="input" size="30" style="width:200px">  <span class="intro">ԤԼר�Һ�</span></td>
	</tr>
	<tr>
		<td class="left" valign="top">ԤԼʱ�䣺</td>
		<td class="right">
<?php if ($uinfo["part_id"] == 4) { ?>
		<b>���ǵ�ҽ��ݣ���ʱ�佫�Զ���Ϊ��ǰʱ�䣬���ɸ���</b>
		<input type="hidden" name="order_date" value="<?php echo date("Y-m-d H:i:s"); ?>" /><!-- ֻ��Ϊjs���ͨ���� -->
<?php } else { ?>
		<input name="order_date" value="" class="input" style="width:150px" id="order_date"> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'order_date',dateFmt:'yyyy-MM-dd HH:mm:ss'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��">
<?php
		$show_days = array(
			"��" => $today = date("Y-m-d"), //����
			"��" => date("Y-m-d", strtotime("+1 day")), //����
			"��" => date("Y-m-d", strtotime("+2 days")), //����
			"�����" => date("Y-m-d", strtotime("+3 days")), //�����
			"����" => date("Y-m-d", strtotime("next Saturday")), //����
			"����" => date("Y-m-d", strtotime("next Sunday")), // ����
			"��һ" => date("Y-m-d", strtotime("next Monday")), // ��һ
			"һ�ܺ�" => date("Y-m-d", strtotime("+7 days")), // һ�ܺ�
			"���º�" => date("Y-m-d", strtotime("+15 days")), //����º�
		);
		echo '<div style="padding-top:6px;">����: ';
		foreach ($show_days as $name => $value) {
			echo '<a href="javascript:input_date(\'order_date\', \''.$value.'\')">['.$name.']</a>&nbsp;';
		}
		echo '<br>ʱ��: ';
		echo '<a href="javascript:input_time(\'order_date\',\'00:00:00\')">[ʱ�䲻��]</a>&nbsp;';
		echo '<a href="javascript:input_time(\'order_date\',\'09:00:00\')">[����9��]</a>&nbsp;';
		echo '<a href="javascript:input_time(\'order_date\',\'14:00:00\')">[����2��]</a>&nbsp;</div>';

?>
<?php } ?>
		</td>
	</tr>

	<tr>
		<td class="left" valign="top">��ע��</td>
		<td class="right"><textarea name="memo" style="width:60%; height:48px;vertical-align:middle;" class="input"></textarea> <span class="intro">������ע��Ϣ</span></td>
	</tr>
</table>


<?php if ($uinfo["part_id"] == 4) { ?>
<div class="space"></div>
<table width="100%" class="edit">
	<tr>
		<td class="head">��ҽѡ��</td>
		<td class="head">�������ǵ�ҽ��ݣ���ӵĻ�������ֱ��Ϊ��<b style="color:red">�ѵ�Ժ</b>��״̬������������ĽӴ�ҽ����</td>
	</tr>
	<tr>
		<td class="left">�Ӵ�ҽ����</td>
		<td class="right">
			<select name="doctor" class="combo">
				<option value="" style="color:gray">--��ѡ��--</option>
				<?php echo list_option($doctor_list, 'name', 'name'); ?>
			</select>&nbsp;<span class="intro">���˽Ӵ�ҽ��</span>
		</td>
	</tr>
</table>
<?php } ?>

<input type="hidden" name="op" value="add">

<div class="button_line">
	<input type="submit" class="submit" value="�ύ����">
</div>

</form>

</body>
</html>