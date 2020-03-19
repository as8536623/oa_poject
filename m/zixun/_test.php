<html>
<head>
<title>测试</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style type="text/css">
.time_h {display:"block"; float:left; width:22px; height:15px; line-height:15px; font-family:"Tahoma"; text-align:center; }
.time_h:hover {color:red; background:url("img/time_round_bg.png") no-repeat; }
.time_h_sel {display:"block"; float:left; color:red; width:22px; height:15px; line-height:15px; font-family:"Tahoma"; font-weight:bold; background:url("img/time_round_bg.png") no-repeat; text-align:center; }

</style>
<script type="text/javascript">
function set_cookie(name, value, time) { //单位秒
	var date = new Date();
	date.setTime(date.getTime() + time * 1000);
	window.document.cookie = name + "=" + escape(value) + ";expires=" + date.toUTCString();
}

function get_cookie(name) {
	var result = "";
	var array = window.document.cookie.split(";");
	var length = array.length;
	for (var i = 0; i < length; i++) {
		var nv = array[i].split("=");
		if (nv[0] == name) {
			result = unescape(nv[1]);
		}
	}
	return result;
}

function set_h(o, hour) {
	if (o.className == "time_h") {
		o.className = "time_h_sel";
	} else {
		o.className = "time_h";
	}
	// 记录已选时间段到cookie:
	var select_h = "";
	var ls = byid("time_select").getElementsByTagName("A");
	for (var i=0; i<ls.length; i++) {
		if (ls[i].className == "time_h_sel") {
			select_h += (select_h != '' ? "," : "") + ls[i].innerHTML;
		}
	}
	set_cookie("time_select_user_set", select_h, 100000);

	update_select_tips();
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
</script>
</head>

<body>

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


<script type="text/javascript">
var s = get_cookie("time_select_user_set");
if (s != '') {
	var s_arr = s.split(",");
	var ls = byid("time_select").getElementsByTagName("A");
	for (var i=0; i<ls.length; i++) {
		if (in_array(ls[i].innerHTML, s_arr)) {
			ls[i].className = "time_h_sel";
		}
	}
}

update_select_tips();
</script>


</body>
</html>