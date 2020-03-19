<?php
/*
// - ����˵�� : ����ά��
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2013-4-20
*/
require "../../core/core.php";
require "config.inc.php";
$table = "zixun_data";

if (count($hospital_ids) == 0) {
	exit_html("����Աû��Ϊ�����ҽԺ������ʹ�ô˹��ܡ�");
}

$change_op = $_GET["go"];
if (!$hid || $change_op != '') {
	// ҽԺ�л�����:
	$hids = implode(",", $hospital_ids);
	$h_list = $db->query("select id,name from hospital where id in ($hids) order by sort desc, name asc", "", "id");

	if (!$hid) {
		$check_hid = $h_list[0];
	}
	if ($change_op == "prev") {
		$cur_k = array_search($hid, $h_list);
		if ($cur_k > 0) {
			$check_hid = $h_list[$cur_k - 1];
		} else {
			msg_box("�Ѿ�����ǰһ��ҽԺ��", "back", 1, 2);
		}
	}
	if ($change_op == "next") {
		$cur_k = array_search($hid, $h_list);
		if ($cur_k < count($h_list) - 1) {
			$check_hid = $h_list[$cur_k + 1];
		} else {
			msg_box("�Ѿ������һ��ҽԺ��", "back", 1, 2);
		}
	}
	if ($check_hid > 0) {
		$_SESSION["hospital_id"] = $check_hid;
		header("location: mydata.php");
	}
	exit;
}


$h_name = $db->query("select name from hospital where id=$hid limit 1", 1, "name");

// ��ѯ��ǰ����:
$hour_set_arr = $db->query("select * from zixun_hour_set where hid=$hid limit 1", 1);
$cur_hour_set = $hour_set_arr["h_set"];
if (trim($cur_hour_set) == '') {
	$cur_hour_set = $default_hour_set;
}
$cur_hour_set_arr = hour_set_to_show(explode(",", $cur_hour_set));

// ��ǰ����ѯԱ����:
$kf_name = $realname;
$date = $_GET["date"] ? $_GET["date"] : date("Y-m-d");
$int_date = intval(str_replace("-", "", $date));


// �����ύ����:
if ($op == "submit") {
	$date = $_POST["date"];
	$int_date = intval(str_replace("-", "", $date));
	if ($int_date == 0 || strlen($int_date) != 8) {
		exit("���ڸ�ʽ����...");
	}

	$time = time();

	foreach ($_POST["hour_data"] as $h => $h_click) {
		// ��������:
		$old = $db->query("select * from zixun_data where hid=$hid and kefu='$kf_name' and date='$int_date' and hour='$h' limit 1", 1);
		$update_calc = 0;
		if (($old_id = $old["id"]) > 0) {
			if ($h_click > 0) {
				$db->query("update zixun_data set click_all='$h_click' where id=$old_id limit 1");
				$update_calc = 1;
			} else {
				$db->query("delete from zixun_data where id=$old_id limit 1"); //ɾ����������
				$db->query("delete from zixun_calc where main_id=$old_id "); //ɾ���ӱ�����
			}
		} else {
			if ($h_click > 0) {
				$old_id = $db->query("insert into zixun_data set hid=$hid, kefu='$kf_name', date='$int_date', hour='$h', click_all='$h_click', addtime='$time' ");
				$update_calc = 1;
			}
		}

		// ���¼����:
		if ($old_id > 0 && $update_calc) {
			list($h1, $h2) = explode("~", $h, 2);
			$hour_list = get_between_hour($h1, $h2);
			$per_hour_click = round($h_click / count($hour_list), 2);

			// ɾ��ԭ�е�����ID�������¸���:
			$db->query("delete from zixun_calc where main_id=$old_id");
			foreach ($hour_list as $_h) {
				$db->query("insert into zixun_calc set main_id=$old_id, hid=$hid, kefu='$kf_name', date='$int_date', hour='$_h', click_all='$per_hour_click' ");
			}
		}
		// end ���¼����

	}

	echo '<script type="text/javascript">';
	echo 'parent.msg_box("����ɹ�");';
	echo 'self.location = "mydata.php?date='.$date.'";';
	echo '</script>';
	exit;
}

// ��ȡ�ͷ�����:
$kf_list = $db->query("select * from zixun_data where hid=$hid and kefu='$kf_name' and date='$int_date' order by id asc", "hour");

?>
<html>
<head>
<title>��ѯ����ά��</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<script src="/res/sorttable_keep.js" language="javascript"></script>
<style type="text/css">
.column_sortable {cursor:pointer; color:blue; font-family:"΢���ź�"; }
.sorttable_nosort {font-family:"΢���ź�"; }
.hour_set_list {border:1px solid #97e6a5; }
.hour_set_list .head td {border:1px solid #e7e7e7; background:#f2f8f9; padding:4px 3px 3px 3px; font-weight:bold; }
.hour_set_list .data td {border:1px solid #e7e7e7; padding:4px 3px 3px 3px; }
.al {text-align:left; }
.ac {text-align:center; }
.ar {text-align:right; }
.yh {font-family:"΢���ź�"; }
</style>

<script type="text/javascript">
function change_hospital(link) {
	parent.load_src(1, link, 600, 570);
}

function change_to_date() {
	var date = byid("date").value;
	self.location = "?date="+date;
}

function check_data() {
	return confirm("ȷ��Ҫ�ύ������");
}
</script>

</head>

<body>

<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $h_name; ?> ��ѯ����</td><td class="bar_right"></td></tr></table></div>
	<div class="header_center">
		<b class="yh">�л����ڣ�</b><input name="date" onchange="change_to_date();" value="<?php echo $_GET["date"] ? $_GET["date"] : date("Y-m-d"); ?>" class="input" style="width:80px" id="date"> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'date',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="ѡ������">&nbsp;&nbsp;

		<button onclick="self.location.reload();return false;" class="button">ˢ��</button>
	</div>
	<div class="headers_oprate">
		<button onclick="change_hospital('m/chhos.php'); return false;" class="buttonb" title="�л�������ҽԺ">�л�ҽԺ</button>&nbsp;
		<button onclick="location = '/m/zixun/mydata.php?go=prev'; return false;" class="button" title="�л�����һ��ҽԺ">��</button>&nbsp;
		<button onclick="location = '/m/zixun/mydata.php?go=next'; return false;" class="button" title="�л�����һ��ҽԺ">��</button>
	</div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>

<table id="hour_set" class="round_table hour_set_list" cellpadding="0" cellspacing="0" width="100%">
	<tr class="head">
		<td class="ac" width="25%"><font color="red"><?php echo $date; ?></font> ʱ���</td>
		<td class="ac" width="25%">�ܵ��</td>
		<td class="ac" width="25%">����ͨԤԼ</td>
		<td class="ac" width="25%">QQԤԼ</td>
	</tr>

<?php
foreach ($cur_hour_set_arr as $h) {
	list($h1, $h2) = explode("~", $h, 2);
	$tb = strtotime($date." ".$h1.":00:00");
	$te = strtotime($date." ".$h2.":00:00") - 1;
	$swt_count = $db->query("select count(*) as c from patient_{$hid} where addtime>=$tb and addtime<=$te and author='$kf_name' and soft_from='swt'", 1, "c");
	$qq_count = $db->query("select count(*) as c from patient_{$hid} where addtime>=$tb and addtime<=$te and author='$kf_name' and soft_from='qq'", 1, "c");
?>
	<tr class="data" onmouseover="mi(this)" onmouseout="mo(this)">
		<td class="ac yh"><?php echo str_replace("~", "�� ~ ", $h)."��"; ?></td>
		<td class="ac">
<?php if (!isset($kf_list[$h]["addtime"]) || ($kf_list[$h]["addtime"] > 0 && time() - $kf_list[$h]["addtime"] < $zixun_edit_timeout)) { ?>
			<form method="POST" onsubmit="return check_data(this)">
				<input class="input" name="hour_data[<?php echo $h; ?>]" value="<?php echo $kf_list[$h]["click_all"]; ?>" style="width:100px;">&nbsp;
				<button class="button" onclick="this.form.submit();">�ύ</button>
				<input type="hidden" name="op" value="submit">
				<input type="hidden" name="date" value="<?php echo $date; ?>">
			</form>
<?php } else { ?>
			<?php echo $kf_list[$h]["click_all"]; ?>
<?php } ?>
		</td>
		<td class="ac yh"><?php echo intval($swt_count); ?></td>
		<td class="ac yh"><?php echo intval($qq_count); ?></td>
	</tr>
<?php } ?>

</table>




</body>
</html>