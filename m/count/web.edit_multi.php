<?php
// --------------------------------------------------------
// - ����˵�� : ��ӡ��޸�����
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2010-10-08 13:31
// --------------------------------------------------------
$date = $_REQUEST["date"];

// Ȩ�� @ 2012-03-21
if ($uinfo["character_id"] == 16 || $debug_mode || $uinfo["character_id"] == 28 || check_power("fangke")) { //������Ա
	//
} else {
	exit_html("�Բ�����û���޸�Ȩ��...");
}

if (!$date) {
	exit("��������");
}

$date = date("Y-m-d", strtotime($date));

$allow_day = $db->query("select value from count_config where name='�����޸�����' limit 1", 1, "value");
$allow_day = intval($allow_day);
if ($allow_day > 0) {
	$allow = 0;
	if ($date == date("Y-m-d")) {
		$allow = 1; //�������������޸�
	} else {
		for ($i = 1; $i < $allow_day; $i++) {
			if ($date == date("Y-m-d", strtotime("-".$i." day"))) {
				$allow = 1;
			}
		}
	}

	if (!$allow) {
		exit_html("�Բ���ֻ���޸����".$allow_day."������ݡ�");
	}
}

$kefu = $_GET["kefu"];
if (!$kefu) {
	exit("��������");
}

if ($_POST) {

	// �ظ��ύ��⣬���ͨ��������������token
	if ($_SESSION["web_multi_token"] != $_POST["token"]) {
		exit("�벻Ҫ�ظ��ύ...");
	}
	$_SESSION["web_multi_token"] = time();

	ob_start();
	$zong = $_POST["zong"];
	$shouji = $_POST["shouji"];
	$web = array();

	foreach ($zong as $k => $v) {
		$web[$k] = max(0, $v - $shouji[$k]);
	}

	$s_date = date("Ymd", strtotime($date." 0:0:0"));


	// ��ѯ�Ƿ�������ӵļ�¼��
	$line1 = $db->query("select * from $table where hid=$hid and sub_id=1 and kefu='$kefu' and date='$s_date' limit 1", 1);
	$line2 = $db->query("select * from $table where hid=$hid and sub_id=2 and kefu='$kefu' and date='$s_date' limit 1", 1);

	$r = array();
	if ($line1["id"] > 0) {
		//
	} else {
		$r["hid"] = $hid;
		$r["sub_id"] = 1;
		$r["date"] = $s_date;
		$r["kefu"] = $kefu;
		$r["repeatcheck"] = $hid."_1_".$s_date."_".$kefu;
		$r["addtime"] = time();
		$r["uid"] = $uid;
		$r["u_realname"] = $realname;
	}

	$r["click"] = $web["click"];
	$r["click_local"] = $web["click_local"];
	$r["click_other"] = $web["click_other"];

	$r["ok_click"] = $web["ok_click"];
	$r["ok_click_local"] = $web["ok_click_local"];
	$r["ok_click_other"] = $web["ok_click_other"];

	$sqldata = $db->sqljoin($r);
	if ($line1["id"] > 0) {
		$id = $line1["id"];
		$sql = "update $table set $sqldata where id='$id' limit 1";
	} else {
		$sql = "insert into $table set $sqldata";
	}

	$db->query($sql);


	// �����ֻ����ݣ�
	$r = array();
	if ($line2["id"] > 0) {
		//
	} else {
		$r["hid"] = $hid;
		$r["sub_id"] = 2;
		$r["date"] = $s_date;
		$r["kefu"] = $kefu;
		$r["repeatcheck"] = $hid."_2_".$s_date."_".$kefu;
		$r["addtime"] = time();
		$r["uid"] = $uid;
		$r["u_realname"] = $realname;
	}

	$r["click"] = $shouji["click"];
	$r["click_local"] = $shouji["click_local"];
	$r["click_other"] = $shouji["click_other"];

	$r["ok_click"] = $shouji["ok_click"];
	$r["ok_click_local"] = $shouji["ok_click_local"];
	$r["ok_click_other"] = $shouji["ok_click_other"];


	$sqldata = $db->sqljoin($r);
	if ($line2["id"] > 0) {
		$id = $line2["id"];
		$sql = "update $table set $sqldata where id='$id' limit 1";
	} else {
		$sql = "insert into $table set $sqldata";
	}

	$db->query($sql);



	$error = ob_get_clean();

	if (!$error) {
		echo '<script> parent.msg_box("�ύ�ɹ�", 2); parent.load_src(0); </script>';
	} else {
		echo "�ύʧ�ܣ����Ժ����ԣ�";
	}
	exit;
}


$token = $_SESSION["web_multi_token"] = time();


?>
<html>
<head>
<title>�����������</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style>
.item {padding:8px 3px 6px 3px; }
</style>

<script language="javascript">
function check_data() {
	var oForm = document.mainform;
	if (oForm.code.value == "") {
		alert("�����롰��š���"); oForm.code.focus(); return false;
	}

	byid("submit_button").disabled = true;
	byid("submit_button").title = "�����ظ��ύ";
	setTimeout("clear_button_lock()", 10000);

	return true;
}

function clear_button_lock() {
	byid("submit_button").disabled = false;
	byid("submit_button").title = "";
	alert("�ύ�쳣������������������ݿⷴӦ�������ɳ��������ύ��");
}

function update_data() {
	for (var i = 1; i <= 9; i++) {
		byid("dt"+i).value = byid("d"+i).innerHTML;
	}
}

function update_cnt(o, id_a, id_b, id_c) {
	var a = byid(id_a).value;
	var b = byid(id_b).value;
	var c = byid(id_c).value;

	var cnt = (a != "" ? 1 : 0) + (b != "" ? 1 : 0) + (c != "" ? 1 : 0);

	if (cnt == 2 && (a == "" || b == "" || c == "")) {
		if (a == "") {
			byid(id_a).value = parseInt(b) + parseInt(c);
		} else if (b == "") {
			byid(id_b).value = a - c;
		} else {
			byid(id_c).value = a - b;
		}
	}
	if (cnt == 3) {
		if (o.id == id_a) {
			byid(id_c).value = a - b;
		} else if (o.id == id_b) {
			byid(id_c).value = a - b;
		} else {
			byid(id_b).value = a - c;
		}
	}
}
</script>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center">�����������</td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>

<form name="mainform" action="" method="POST" onsubmit="return check_data()">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">1. ������</td>
	</tr>

	<tr>
		<td class="left">�ܵ����</td>
		<td class="right">
			<input name="zong[click]" id="c_a" onchange="update_cnt(this,'c_a', 'c_b', 'c_c')" value="" class="input" style="width:100px">
			��=�����أ�<input name="zong[click_local]" id="c_b" onchange="update_cnt(this,'c_a', 'c_b', 'c_c')" value="" class="input"style="width:100px">
			��+����أ�<input name="zong[click_other]" id="c_c" onchange="update_cnt(this,'c_a', 'c_b', 'c_c')" value="" class="input" style="width:100px">
		</td>
	</tr>

	<tr>
		<td class="left">����Ч��</td>
		<td class="right">
			<input name="zong[ok_click]" id="d_a" onchange="update_cnt(this,'d_a', 'd_b', 'd_c')" value="" class="input" style="width:100px">
			��=�����أ�<input name="zong[ok_click_local]" id="d_b" onchange="update_cnt(this,'d_a', 'd_b', 'd_c')" value="" class="input"style="width:100px">
			��+����أ�<input name="zong[ok_click_other]" id="d_c" onchange="update_cnt(this,'d_a', 'd_b', 'd_c')" value="" class="input" style="width:100px">
		</td>
	</tr>

	<tr>
		<td colspan="2" class="head">2. �ֻ�����</td>
	</tr>

	<tr>
		<td class="left">�ܵ����</td>
		<td class="right">
			<input name="shouji[click]" id="x_a" onchange="update_cnt(this,'x_a', 'x_b', 'x_c')" value="" class="input" style="width:100px">
			��=�����أ�<input name="shouji[click_local]" id="x_b" onchange="update_cnt(this,'x_a', 'x_b', 'x_c')" value="" class="input"style="width:100px">
			��+����أ�<input name="shouji[click_other]" id="x_c" onchange="update_cnt(this,'x_a', 'x_b', 'x_c')" value="" class="input" style="width:100px">
		</td>
	</tr>

	<tr>
		<td class="left">����Ч��</td>
		<td class="right">
			<input name="shouji[ok_click]" id="y_a" onchange="update_cnt(this,'y_a', 'y_b', 'y_c')" value="" class="input" style="width:100px">
			��=�����أ�<input name="shouji[ok_click_local]" id="y_b" onchange="update_cnt(this,'y_a', 'y_b', 'y_c')" value="" class="input"style="width:100px">
			��+����أ�<input name="shouji[ok_click_other]" id="y_c" onchange="update_cnt(this,'y_a', 'y_b', 'y_c')" value="" class="input" style="width:100px">
		</td>
	</tr>

	<tr>
		<td colspan="2" class="head">ע�⣺1��2������ó�����PCͳ������</td>
	</tr>
</table>
<input type="hidden" name="op" value="edit_multi">
<input type="hidden" name="date" value="<?php echo date("Y-m-d", strtotime($date." 0:0:0")); ?>">
<input type="hidden" name="kefu" value="<?php echo $kefu; ?>">
<input type="hidden" name="token" value="<?php echo $token; ?>">

<div class="button_line">
	<input id="submit_button" type="submit" class="submit" value="�ύ����">
</div>

</form>

</body>
</html>