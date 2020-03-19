<?php
if ($table == '') exit;

$_hid = intval($_REQUEST["hid"]);
if ($_hid == 0) exit("参数错误...");

$line = $hour_set_arr[$_hid];

if ($_POST) {
	ob_start();
	$h_set = $_POST["h_set"];
	if ($hour_set_arr[$_hid]) {
		if ($h_set != '') {
			$db->query("update $table set h_set='$h_set' where hid='$_hid' limit 1");
		} else {
			$db->query("delete from $table where hid='$_hid' limit 1");
		}
	} else {
		if ($h_set != '') {
			$time = time();
			$db->query("insert into $table set hid='$_hid', h_set='$h_set'");
		}
	}

	$error = ob_get_clean();
	if ($error != '') {
		echo "资料提交失败，系统繁忙，请稍后再试。";
	} else {
		//echo '<script> parent.update_content(); </script>';
		$show_h_set = implode(" &nbsp;", hour_set_to_show(explode(",", $h_set)));
		echo '<script> parent.update_content_byid("h_set_'.$_hid.'", "'.$show_h_set.'", "innerHTML"); </script>';
		echo '<script> parent.msg_box("保存成功", 2); </script>';
		echo '<script> parent.load_src(0); </script>';
	}
	exit;
}

?>
<html>
<head>
<title>设置时间段</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style type="text/css">
#time_select {margin-top:10px; }
.time_h {display:"block"; float:left; width:22px; height:15px; line-height:15px; font-family:"Tahoma"; text-align:center; }
.time_h:hover {color:red; background:url("img/time_round_bg.png") no-repeat; }
.time_h_sel {display:"block"; float:left; color:red; width:22px; height:15px; line-height:15px; font-family:"Tahoma"; font-weight:bold; background:url("img/time_round_bg.png") no-repeat; text-align:center; }
.yh {font-family:"微软雅黑"; }
</style>
<script type="text/javascript">
function set_h(o, hour) {
	if (o.className == "time_h") {
		o.className = "time_h_sel";
	} else {
		o.className = "time_h";
	}

	set_select_value();
	update_select_tips();
}

function set_select_value() {
	var select_h = "";
	var ls = byid("time_select").getElementsByTagName("A");
	for (var i=0; i<ls.length; i++) {
		if (ls[i].className == "time_h_sel") {
			select_h += (select_h != '' ? "," : "") + ls[i].innerHTML;
		}
	}
	byid("h_set").value = select_h;
}

function init_hour_select() {
	var s = byid("h_set").value;
	if (s != '') {
		var s_arr = s.split(",");
		var ls = byid("time_select").getElementsByTagName("A");
		for (var i=0; i<ls.length; i++) {
			if (in_array(ls[i].innerHTML, s_arr)) {
				ls[i].className = "time_h_sel";
			}
		}
	}
}

function update_select_tips() {
	var last_hour = '';
	var tip_arr = new Array();
	var tip_index = 0;
	var ls = byid("time_select").getElementsByTagName("A");
	for (var i=0; i<ls.length; i++) {
		if (ls[i].className == "time_h_sel") {
			if (last_hour != '') {
				tip_arr[tip_index++] = last_hour+"~"+ls[i].innerHTML+"";
			}
			last_hour = ls[i].innerHTML;
		}
	}
	if (tip_arr.length == 0) {
		byid("time_select_tips").innerHTML = '(请在上方设置时间段)';
	} else {
		byid("time_select_tips").innerHTML = tip_arr.join(" &nbsp; ");
	}
}

function check_data(form) {
	var h = byid("h_set").value;
	if (h == '') {
		return true;
	}
	var h_arr = h.split(",");
	if (h_arr.length == 1) {
		alert("必须选择两个时间，才能组成一个时间段，请重新设置！");
		return false;
	}
	return true;
}
</script>
</head>

<body>

<div style="margin-top:20px; color:gray; " class="yh">请注意，此设置为只作用于数据录入阶段，且保存后立即生效。不会影响报表查询，那边由个人指定时间段进行分析。</div>

<div id="time_select">
	<div style="float:left; height:15px; line-height:15px; margin-top:1px; font-weight:bold; font-family:微软雅黑;">设置时间段：</div>
<?php
$t_arr = array(8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,0,1,2,3,4,5,6,7);
foreach ($t_arr as $v) {
?>
	<a href="javascript:;" class="time_h" onclick="set_h(this, <?php echo $v; ?>);" title="<?php echo $v; ?>点"><?php echo $v; ?></a>
<?php } ?>
</div>


<div style="clear:both; margin-top:10px;">
	<div style="float:left; font-weight:bold; font-family:微软雅黑;">所选时间段：</div>
	<div id="time_select_tips" style="font-family:'Tahoma';"></div>
</div>

<form method="POST" onsubmit="return check_data(this)">
	<input type="hidden" name="hid" value="<?php echo $_hid; ?>">
	<input type="hidden" name="h_set" id="h_set" value="<?php echo $line["h_set"]; ?>">

	<div class="button_line">
		<input type="submit" class="submit" value="保存设置">
	</div>
</form>


<script type="text/javascript">
init_hour_select();
update_select_tips();
</script>


</body>
</html>