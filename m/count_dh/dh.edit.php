<?php
// --------------------------------------------------------
// - ����˵�� : ��ӡ��޸�����
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2010-10-08 13:31
// --------------------------------------------------------
$date = $_REQUEST["date"];

if (!$date) {
	exit("��������");
}

if ($sub_id == 0) {
	exit("ϵͳ����[sub_id=0]����ˢ��ҳ�������...");
}

$date = date("Y-m-d", strtotime($date));

/*
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
*/

$kefu = $_GET["kefu"];
if (!$kefu) {
	exit("��������");
}

if ($_POST) {

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

	$r["a1"] = $_POST["a1"];
	$r["a2"] = $_POST["a2"];
	$r["a3"] = $_POST["a3"];
	$r["a4"] = $_POST["a4"];

	$r["b1"] = $_POST["b1"];
	$r["b2"] = $_POST["b2"];
	$r["b3"] = $_POST["b3"];
	$r["b4"] = $_POST["b4"];

	$r["c1"] = $_POST["c1"];
	$r["c2"] = $_POST["c2"];
	$r["c3"] = $_POST["c3"];
	$r["c4"] = $_POST["c4"];

	$r["d1"] = $_POST["d1"];
	$r["d2"] = $_POST["d2"];
	$r["d3"] = $_POST["d3"];

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


$s_date = date("Ymd", strtotime($date." 0:0:0"));
$line = $db->query("select * from $table where hid=$hid and sub_id=$sub_id and kefu='$kefu' and date='$s_date' limit 1", 1);
if (!$line["id"] > 0) {
	$line = array();
}


?>
<html>
<head>
<title>�޸� [<?php echo $kefu; ?> <?php echo $date; ?> <?php echo $sub_name; ?>]</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style>
.item {padding:8px 3px 6px 3px; }
</style>

<script language="javascript">
function check_data() {
	var oForm = document.mainform;
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

function toNum(s) {
	if (s == "") return 0;
	return parseInt(s);
}

function w(to, obj, d2, d3) {
	var f = byid("mainform");
	var a1 = toNum(obj.value);
	var a2 = toNum(f[d2].value);
	if (f[d3]) {
		var a3 = toNum(f[d3].value);
	} else {
		var a3 = 0;
	}
	f[to].value = a1 + a2 + a3;
}
</script>
</head>

<body>

<div class="space"></div>

<form name="mainform" id="mainform" action="" method="POST" onsubmit="return check_data()">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">��ϸ����</td>
	</tr>

	<tr>
		<td class="left">�ܵ绰��</td>
		<td class="right">
			<input name="a1" value="<?php echo $line["a1"]; ?>" class="input" style="width:100px">&nbsp;&nbsp;=
			�ѽӣ�<input name="a2" onchange="w('a1', this, 'a3', 'a4')" value="<?php echo $line["a2"]; ?>" class="input" style="width:100px">&nbsp;&nbsp;+
			δ�ӣ�<input name="a3" onchange="w('a1', this, 'a2', 'a4')" value="<?php echo $line["a3"]; ?>" class="input" style="width:100px">&nbsp;&nbsp;+
			���������<input name="a4" onchange="w('a1', this, 'a2', 'a3')" value="<?php echo $line["a4"]; ?>" class="input" style="width:100px">&nbsp;&nbsp;
		</td>
	</tr>

	<tr>
		<td class="left">����Ч��</td>
		<td class="right">
			<input name="b1" value="<?php echo $line["b1"]; ?>" class="input" style="width:100px">&nbsp;&nbsp;=
			�ѽӣ�<input name="b2" onkeyup="w('b1', this, 'b3', 'b4')" value="<?php echo $line["b2"]; ?>" class="input" style="width:100px">&nbsp;&nbsp;+
			δ�ӣ�<input name="b3" onkeyup="w('b1', this, 'b2', 'b4')" value="<?php echo $line["b3"]; ?>" class="input" style="width:100px">&nbsp;&nbsp;+
			������Ч��<input name="b4" onkeyup="w('b1', this, 'b2', 'b3')" value="<?php echo $line["b4"]; ?>" class="input" style="width:100px">&nbsp;&nbsp;
		</td>
	</tr>

	<tr>
		<td class="left">��ԤԼ��</td>
		<td class="right">
			<input name="c1" value="<?php echo $line["c1"]; ?>" class="input" style="width:100px">&nbsp;&nbsp;=
			�ѽӣ�<input name="c2" onkeyup="w('c1', this, 'c3', 'c4')" value="<?php echo $line["c2"]; ?>" class="input" style="width:100px">&nbsp;&nbsp;+
			δ�ӣ�<input name="c3" onkeyup="w('c1', this, 'c2', 'c4')" value="<?php echo $line["c3"]; ?>" class="input" style="width:100px">&nbsp;&nbsp;+
			����ԤԼ��<input name="c4" onkeyup="w('c1', this, 'c2', 'c3')" value="<?php echo $line["c4"]; ?>" class="input" style="width:100px">&nbsp;&nbsp;
		</td>
	</tr>

	<tr>
		<td class="left">�ܾ��</td>
		<td class="right">
			<input name="d1" value="<?php echo $line["d1"]; ?>" class="input" style="width:100px">&nbsp;&nbsp;=
			�绰���<input name="d3" onkeyup="w('d1', this, 'd2')" value="<?php echo $line["d3"]; ?>" class="input" style="width:100px">&nbsp;&nbsp;+
			������<input name="d2" onkeyup="w('d1', this, 'd3')" value="<?php echo $line["d2"]; ?>" class="input" style="width:100px">&nbsp;&nbsp;
		</td>
	</tr>

</table>
<input type="hidden" name="op" value="edit">
<input type="hidden" name="date" value="<?php echo date("Y-m-d", strtotime($date." 0:0:0")); ?>">
<input type="hidden" name="kefu" value="<?php echo $kefu; ?>">

<div class="button_line">
	<input id="submit_button" type="submit" class="submit" value="�ύ����">
</div>

</form>

</body>
</html>