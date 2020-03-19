<?php
// --------------------------------------------------------
// - ����˵�� : 2011����ͳ��
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2012-12-12
// --------------------------------------------------------
require "../../core/core.php";

//exit_html("�ù������ڵ�����...");

$table = "count_web_2011";

// ���пɹ�����Ŀ:
if ($debug_mode || in_array($uinfo["part_id"], array(9))) {
	$types = $db->query("select id,name from count_type where type='web' order by id asc", "id", "name");
} else {
	$hids = implode(",", $hospital_ids);
	$types = $db->query("select id,name from count_type where type='web' and hid in ($hids) order by id asc", "id", "name");
}
if (count($types) == 0) {
	exit("û�п��Թ������Ŀ");
}

// �����Ĵ���:
if ($op = $_REQUEST["op"]) {
	if ($op == "change_type") {
		$cur_type = $_SESSION["count_type_id_web"] = intval($_GET["type_id"]);
	}
}

$cur_type = intval($_SESSION["count_type_id_web"]);
if ($cur_type == 0) {
	$type_ids = array_keys($types);
	$cur_type = $_SESSION["count_type_id_web"] = $type_ids[0];
}
$type_detail = $db->query("select * from count_type where id=$cur_type limit 1", 1);



// �·�
$days = array();
for ($i=1; $i<=12; $i++) {
	$mon = strtotime("2011-".$i."-1 0:0:0");
	$time_array[date("Y-m", $mon)] = array($mon, strtotime("+1 month", $mon) - 1);
	$days[date("Y-m", $mon)] = get_month_days(date("Y-m", $mon));
}


// ��ʱ�����:
$rs = array();

$_begin_time = now();

$cal_field = explode(" ", "click click_local click_other zero_talk ok_click ok_click_local ok_click_other talk talk_local talk_other orders order_local order_other come come_local come_other");

// �����ֶ�:
$f = array();
foreach ($cal_field as $v) {
	$f[] = 'sum('.$v.') as '.$v;
}
$f = implode(", ", $f);

// ���6���µ�ƽ������
foreach ($time_array as $tname => $tt) {

	$b = date("Ymd", $tt[0]);
	$e = date("Ymd", $tt[1]);

	//��ѯ��ҽԺ��������:
	$tmp = $db->query("select $f from $table where hid=$hid and sub_id=$sub_id and date>=$b and date<=$e order by date asc", 1);

	//echo $db->sql."<br>";
	//echo "<pre>";
	//print_r($tmp);

	$rs[$tname] = $tmp;

	// ��ѯԤԼ��:
	$rs[$tname]["per_1"] = @round($rs[$tname]["talk"] / $rs[$tname]["click"] * 100, 2);
	// Ԥ��������:
	$rs[$tname]["per_2"] = @round($rs[$tname]["come"] / $rs[$tname]["orders"] * 100, 2);
	// ��ѯ������:
	$rs[$tname]["per_3"] = @round($rs[$tname]["come"] / $rs[$tname]["click"] * 100, 2);
	// ��Ч��ѯ��:
	$rs[$tname]["per_4"] = @round($rs[$tname]["ok_click"] / $rs[$tname]["click"] * 100, 2);
	// ��ЧԤԼ��:
	$rs[$tname]["per_5"] = @round($rs[$tname]["talk"] / $rs[$tname]["ok_click"] * 100, 2);

}

$_used_time = round(now() - $_begin_time, 4);


?>
<html>
<head>
<title>��ʷ����ͳ��</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style>
body {padding:5px 8px; }
.main_title {margin:0 auto; padding-top:30px; padding-bottom:15px; text-align:center; font-weight:bold; font-size:12px; font-family:"����"; }
.item {padding:8px 3px 6px 3px !important; }
.head {padding:12px 3px !important;}

form {display:inline; }
#date_tips {float:left; font-weight:bold; padding-top:1px; }
#ch_date {float:left; margin-left:20px; }
.site_name {display:block; padding:4px 0px;}
.site_name, .site_name a {font-family:"Arial", "Tahoma"; }
.ch_date_a b, .ch_date_a a {font-family:"Arial"; }
.ch_date_a b {border:0px; padding:1px 5px 1px 5px; color:red; }
.ch_date_a a {border:0px; padding:1px 5px 1px 5px; }
.ch_date_a a:hover {border:1px solid silver; padding:0px 4px 0px 4px; }
.ch_date_b {padding-top:8px; text-align:left; width:80%; color:silver; }
.ch_date_b a {padding:0 3px; }

.main_title {margin:0 auto; padding-top:30px; padding-bottom:15px; text-align:center; font-weight:bold; font-size:12px; font-family:"����"; }

.item {padding:8px 3px 6px 3px !important; }

.head {padding:6px 3px !important;}

.rate_tips {padding:30px 0 0 30px; line-height:24px; }

.item {font-family:"Tahoma"; }
</style>

<script language="javascript">
function hgo(dir, o) {
	var obj = byid("type_id");
	if (dir == "up") {
		if (obj.selectedIndex > 1) {
			obj.selectedIndex = obj.selectedIndex - 1;
			obj.onchange();
			o.disabled = true;
		} else {
			parent.msg_box("�Ѿ�����ǰ��", 3);
		}
	}
	if (dir == "down") {
		if (obj.selectedIndex < obj.options.length-1) {
			obj.selectedIndex = obj.selectedIndex + 1;
			obj.onchange();
			o.disabled = true;
		} else {
			parent.msg_box("�Ѿ������һ����", 3);
		}
	}
}
</script>
</head>

<body>
<div style="margin:10px 0 0 0px;">
	<div id="date_tips">ҽԺ��Ŀ��</div>
	<form method="GET" style="margin-left:30px;">
		<select name="type_id" id="type_id" class="combo" onchange="this.form.submit()">
			<option value="" style="color:gray">-��ѡ����Ŀ-</option>
			<?php echo list_option($types, "_key_", "_value_", $cur_type); ?>
		</select>&nbsp;
		<button class="button" onclick="hgo('up',this);">��</button>&nbsp;
		<button class="button" onclick="hgo('down',this);">��</button>
		<input type="hidden" name="op" value="change_type">
	</form>&nbsp;&nbsp;&nbsp;
</div>

<div class="main_title"><?php echo $type_detail["name"]; ?> - 2011ͳ������</div>

<table width="100%" align="center" class="list">
	<tr>
		<td class="head" align="center" width="60">����</td>
		<td class="head" align="center" style="color:red">�ܵ��</td>
		<td class="head" align="center">����</td>
		<td class="head" align="center">���</td>
		<td class="head" align="center" style="color:red">����Ч</td>
		<td class="head" align="center">����</td>
		<td class="head" align="center">���</td>

		<td class="head" align="center" style="color:red">����Լ</td>
		<td class="head" align="center" style="color:red">Ԥ�Ƶ�Ժ</td>
		<td class="head" align="center" style="color:red">ʵ�ʵ�Ժ</td>

		<td class="head" align="center" style="color:red">��ѯԤԼ��</td>
		<td class="head" align="center" style="color:red">Ԥ��������</td>
		<td class="head" align="center" style="color:red">��ѯ������</td>
		<td class="head" align="center" style="color:red">��Ч��ѯ��</td>
		<td class="head" align="center" style="color:red">��ЧԤԼ��</td>
	</tr>

<?php
foreach ($time_array as $tname => $tt) {
	$li = $rs[$tname];
?>
	<tr>
		<td class="item" align="center"><?php echo $tname; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["click"]; ?></td>
		<td class="item" align="center"><?php echo $li["click_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["click_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["ok_click"]; ?></td>
		<td class="item" align="center"><?php echo $li["ok_click_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["ok_click_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["talk"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["orders"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["come"]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_1"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_2"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_3"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_4"]); ?>%</td>
		<td class="item" align="center" style="color:red"><?php echo floatval($li["per_5"]); ?>%</td>
	</tr>
<?php } ?>

</table>

<br>
<div style="color:silver; margin-left:20px;">��ʱ��<?php echo $_used_time; ?>s</div>

<br>
<br>

</body>
</html>
