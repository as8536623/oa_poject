<?php
// --------------------------------------------------------
// - ����˵�� : ��ӡ��޸�����
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2010-10-08 13:31
// --------------------------------------------------------
$date = $_REQUEST["date"];

// Ȩ�� @ 2012-03-21
if ($uinfo["character_id"] == 16 || $debug_mode || $uinfo["character_id"] == 23 || check_power("yuyue")) { //������Ա
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
	if ($_SESSION["web_yuyue_token"] != $_POST["token"]) {
		exit("�벻Ҫ�ظ��ύ...");
	}
	$_SESSION["web_yuyue_token"] = time();

	$r = array();

	// �ж��Ƿ��Ѿ����:
	$mode = "add";
	$s_date = date("Ymd", strtotime($date." 0:0:0"));
	$kefu = $_POST["kefu"];
	$cur_data = $db->query("select * from $table where hid=$hid and sub_id=$sub_id and kefu='$kefu' and date='$s_date' limit 1", 1);
	$cur_id = $cur_data["id"];
	if ($cur_id > 0) {
		$mode = "edit";
		$id = $cur_id;
	}

	if ($mode == "add") {
		$r["hid"] = $hid;
		$r["sub_id"] = $sub_id;
		$r["date"] = $s_date;
		$r["kefu"] = $_POST["kefu"];
		$r["repeatcheck"] = $hid."_".$sub_id."_".$s_date."_".$_POST["kefu"];
	}

	$r["talk"] = $_POST["talk"];
	$r["talk_local"] = $_POST["talk_local"];
	$r["talk_other"] = $_POST["talk_other"];

	$r["orders"] = $_POST["orders"];
	$r["order_local"] = $_POST["order_local"];
	$r["order_other"] = $_POST["order_other"];

	$r["come"] = $_POST["come"];
	$r["come_local"] = $_POST["come_local"];
	$r["come_other"] = $_POST["come_other"];

	if ($mode == "add") {
		$r["addtime"] = time();
		$r["uid"] = $uid;
		$r["u_realname"] = $realname;
	}

	// ������־:
	if ($mode == "add") {
		$r["log"] = date("Y-m-d H:i")." ".$realname." ��Ӽ�¼\r\n";
	} else {
		// ��¼�����޸�����Щ:
		$log_it = array();
		foreach ($r as $x => $y) {
			if ($cur_data[$x] != $y) {
				$log_it[] = $x.":".$cur_data[$x]."=>".$y;
			}
		}
		if (count($log_it) > 0) {
			$r["log"] = $cur_data["log"].date("Y-m-d H:i")." ".$realname." �޸�: ".implode(", ", $log_it)." \r\n";
		}
	}


	$sqldata = $db->sqljoin($r);
	if ($mode == "add") {
		$sql = "insert into $table set $sqldata";

		// �ύ֮ǰ����ظ�:
		$_a = $r["hid"];
		$_b = $r["sub_id"];
		$_c = $r["date"];
		$_d = $r["kefu"];
		if ($db->query("select count(*) as c from $table where hid=$_a and sub_id=$b and date=$_c and kefu='$_d'", 1, "c") > 0) {
			exit("Ŀ�������Ѵ��������ݿ��У������ظ��ύ��");
		}

	} else {
		// 2013-12-27 ����Ƿ����ظ���ֵ ��ɾ��
		if ($hid > 0 && $sub_id > 0 && $kefu != '' && $s_date != '' && $id > 0) {
			$_arr = $db->query("select * from $table where hid=$hid and sub_id=$sub_id and kefu='$kefu' and date='$s_date' and id!='$id'");
			if (count($_arr) > 0) {
				$_s = serialize($_arr);
				// д����־:
				@file_put_contents(ROOT."data/auto_delete_repeat.log", date("Y-m-d H:i:s")." ".$username.": ".$_s."\r\n", FILE_APPEND);
				foreach ($_arr as $_li) {
					$_del_id = $_li["id"];
					if ($_del_id > 0) {
						$db->query("delete from $table where id='$_del_id' limit 1");
					}
				}
			}
		}

		$sql = "update $table set $sqldata where id='$id' limit 1";
	}

	if ($db->query($sql)) {
		if ($op == "add") {
			echo '<script> parent.update_content(); </script>';
			echo '<script> parent.msg_box("��ӳɹ�", 2); </script>';
		} else {
			echo '<script> parent.msg_box("�޸ĳɹ����б�δ����", 2); </script>';
		}
		echo '<script> parent.load_src(0); </script>';
	} else {
		echo "�ύʧ�ܣ����Ժ����ԣ�";
	}
	exit;
}


// ��ӦҽԺID:
$hs_id = $type_detail["hid"];

// ��ȡ������Լ��������
$day_begin = strtotime($date." 0:0:0");
$day_end = strtotime($date." 23:59:59");

$sql_add = $weixin_tips = "";

if ($hs_id > 0) {
	// ����Լ:
	$talk_all = $db->query("select count(*) as count from patient_{$hs_id} where part_id=2 and from_account='$sub_id' and addtime>=$day_begin and addtime<=$day_end and author='$kefu' $sql_add", 1, "count");
	// ����Լ - ����:
	$talk_local = $db->query("select count(*) as count from patient_{$hs_id} where part_id=2 and from_account='$sub_id' and addtime>=$day_begin and addtime<=$day_end and author='$kefu' and is_local=1 $sql_add", 1, "count");
	// ����Լ - ��أ�
	$talk_other = $talk_all - $talk_local;

	// Ԥ�Ƶ�Ժ:
	$order_all = $db->query("select count(*) as count from patient_{$hs_id} where part_id=2 and from_account='$sub_id' and order_date>=$day_begin and order_date<=$day_end and author='$kefu' $sql_add", 1, "count");
	$sql_1 = $db->sql;
	// Ԥ�Ƶ�Ժ - ����:
	$order_local = $db->query("select count(*) as count from patient_{$hs_id} where part_id=2 and from_account='$sub_id' and order_date>=$day_begin and order_date<=$day_end and author='$kefu' and is_local=1 $sql_add", 1, "count");
	// Ԥ�Ƶ�Ժ - ��أ�
	$order_other = $order_all - $order_local;

	// ʵ�ʵ�Ժ:
	$come_all = $db->query("select count(*) as count from patient_{$hs_id} where part_id=2 and from_account='$sub_id' and order_date>=$day_begin and order_date<=$day_end and author='$kefu' and status=1 $sql_add", 1, "count");
	// ʵ�ʵ�Ժ: - ����:
	$come_local = $db->query("select count(*) as count from patient_{$hs_id} where part_id=2 and from_account='$sub_id' and order_date>=$day_begin and order_date<=$day_end and author='$kefu' and is_local=1 and status=1 $sql_add", 1, "count");
	// ʵ�ʵ�Ժ: - ��أ�
	$come_other = $come_all - $come_local;
}


$op = "edit";
$s_date = date("Ymd", strtotime($date." 0:0:0"));
$line = $db->query("select * from $table where hid=$hid and sub_id=$sub_id and kefu='$kefu' and date='$s_date' limit 1", 1);
if (!$line["id"] > 0) {
	$line = array();
	$op = "add";
}

$title = "�޸�����";

$token = $_SESSION["web_yuyue_token"] = time();

?>
<html>
<head>
<title><?php echo $title; ?></title>
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
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $title; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>

<form name="mainform" action="" method="POST" onsubmit="return check_data()">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">��ϸ����</td>
	</tr>

	<tr>
		<td class="left"></td>
		<td class="right">
			<button type="button" class="buttonb" onclick="update_data()">����</button>&nbsp;&nbsp;&nbsp;&nbsp;(��������������������Ǵ�ԤԼ���˹�����ѯ�ó�)
		</td>
	</tr>

	<tr>
		<td class="left">����Լ��</td>
		<td class="right">
			<input name="talk" id="dt1" value="<?php echo $op == "add" ? $talk_all : $line["talk"]; ?>" class="input" style="width:100px">
			<?php if ($op == "edit") { ?>(<span id="d1"><?php echo $talk_all; ?></span>)<?php } ?>
			��=�����أ�<input name="talk_local" id="dt2" value="<?php echo $op == "add" ? $talk_local : $line["talk_local"]; ?>" class="input"style="width:100px">
			<?php if ($op == "edit") { ?>(<span id="d2"><?php echo $talk_local; ?></span>)<?php } ?>
			��+����أ�<input name="talk_other" id="dt3" value="<?php echo $op == "add" ? $talk_other : $line["talk_other"]; ?>" class="input" style="width:100px">
			<?php if ($op == "edit") { ?>(<span id="d3"><?php echo $talk_other; ?></span>)<?php } ?>
			<?php echo $weixin_tips; ?>
		</td>
	</tr>

	<tr>
		<td class="left">Ԥ�Ƶ�Ժ��</td>
		<td class="right">
			<input name="orders" id="dt4" value="<?php echo $op == "add" ? $order_all : $line["orders"]; ?>" class="input" style="width:100px">
			<?php if ($op == "edit") { ?>(<span id="d4"><?php echo $order_all; ?></span>)<?php } ?>
			��=�����أ�<input name="order_local" id="dt5" value="<?php echo $op == "add" ? $order_local : $line["order_local"]; ?>" class="input"style="width:100px">
			<?php if ($op == "edit") { ?>(<span id="d5"><?php echo $order_local; ?></span>)<?php } ?>
			��+����أ�<input name="order_other" id="dt6" value="<?php echo $op == "add" ? $order_other : $line["order_other"]; ?>" class="input" style="width:100px">
			<?php if ($op == "edit") { ?>(<span id="d6"><?php echo $order_other; ?></span>)<?php } ?>
			<?php echo $weixin_tips; ?>
		</td>
	</tr>

	<tr>
		<td class="left">ʵ�ʵ�Ժ��</td>
		<td class="right">
			<input name="come" id="dt7" value="<?php echo $op == "add" ? $come_all : $line["come"]; ?>" class="input" style="width:100px">
			<?php if ($op == "edit") { ?>(<span id="d7"><?php echo $come_all; ?></span>)<?php } ?>
			��=�����أ�<input name="come_local" id="dt8" value="<?php echo $op == "add" ? $come_local : $line["come_local"]; ?>" class="input"style="width:100px">
			<?php if ($op == "edit") { ?>(<span id="d8"><?php echo $come_local; ?></span>)<?php } ?>
			��+����أ�<input name="come_other" id="dt9" value="<?php echo $op == "add" ? $come_other : $line["come_other"]; ?>" class="input" style="width:100px">
			<?php if ($op == "edit") { ?>(<span id="d9"><?php echo $come_other; ?></span>)<?php } ?>
			<?php echo $weixin_tips; ?>
		</td>
	</tr>

</table>
<input type="hidden" name="op" value="edit_yuyue">
<input type="hidden" name="date" value="<?php echo date("Y-m-d", strtotime($date." 0:0:0")); ?>">
<input type="hidden" name="kefu" value="<?php echo $kefu; ?>">
<input type="hidden" name="token" value="<?php echo $token; ?>">

<div class="button_line">
	<input id="submit_button" type="submit" class="submit" value="�ύ����">
</div>

</form>

</body>
</html>