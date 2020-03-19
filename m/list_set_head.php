<?php
//
// - 功能说明 : 设置表头
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2013-5-16
//
require "../core/core.php";

if ($_POST) {
	$f_arr = explode(",", $_POST["save_result"]);
	if (count($f_arr) > 12) {
		exit("对不起，列表最多可以设置12个字段，您勾选的太多了...");
	}
	foreach ($f_arr as $k => $v) {
		$f_arr[$k] = trim($v);
		if ($f_arr[$k] == '') {
			unset($f_arr[$k]);
		}
	}
	$f_str = implode(",", $f_arr);
	$db->query("update sys_admin set list_field='{$f_str}' where name='$username' limit 1");

	echo '<script type="text/javascript">';
	echo 'parent.load_box(0);';
	echo 'parent.msg_box("设置保存成功");';
	echo 'parent.update_content();';
	echo '</script>';

	exit;
}

// 允许的权限:
$data_power = explode(",", $uinfo["data_power"]);

$power_show = array();

if ($debug_mode || in_array("all", $data_power)) {
	$power_show["总"] = "总数据";
}
if ($debug_mode || in_array("web", $data_power)) {
	$power_show["网络"] = "网络";
}
if ($debug_mode || in_array("tel", $data_power)) {
	$power_show["电话"] = "电话";
}
// 其它:
$z_info = $db->query("select name,type,sum_condition from index_module_set where isshow=1");
foreach ($z_info as $li) {
	if ($debug_mode || in_array($li["name"], $data_power)) {
		$power_show[$li["name"]] = $li["name"];
	}
}

if (count($power_show) == 0) {
	exit("对不起，您没有任何权限，请联系管理员。");
}


// 当前设置：
$cur_field_arr = explode(",", $uinfo["list_field"]);



// 可以设置的字段定义:
$filed = array();
$filed["总"] = array(
	"今日:预约", "今日:预到", "今日:实到", "今日:跟踪",
	"昨日:预约", "昨日:预到", "昨日:实到",
	"本月:预约", "本月:预到", "本月:实到",
	"上月:预约", "上月:预到", "上月:实到",
	"同比:预约", "同比:预到", "同比:实到",
	"增幅",
);

$filed["网络"] = array(
	"今日:预约", "今日:预到", "今日:实到",
	"昨日:预约", "昨日:预到", "昨日:实到",
	"本月:预约", "本月:预到", "本月:实到",
	"上月:预约", "上月:预到", "上月:实到",
	"同比:预约", "同比:预到", "同比:实到",
	"增幅",
);

$filed["电话"] = array(
	"今日:预约", "今日:预到", "今日:实到",
	"昨日:预约", "昨日:预到", "昨日:实到",
	"本月:预约", "本月:预到", "本月:实到",
	"上月:预约", "上月:预到", "上月:实到",
	"同比:预约", "同比:预到", "同比:实到",
);



// 通用部分:
$common_field = array(
	"今日:预约", "今日:预到", "今日:实到",
	"昨日:预约", "昨日:预到", "昨日:实到",
	"本月:预约", "本月:预到", "本月:实到",
	"上月:预约", "上月:预到", "上月:实到",
);




?>
<html>
<head>
<title>设置列表表头 (勾选后可在下方调整顺序)</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="../res/base.css?ver=20130126" rel="stylesheet" type="text/css">
<script src="../res/base.js?ver=20130126" language="javascript"></script>
<style type="text/css">
.set_filed {border:1px solid #b2b2b2; }
.set_filed .head td {background-color:#f5f5f5; font-weight:bold; border-bottom:1px solid #c5c5c5; padding:4px 3px 2px 3px;}
.set_filed .line td {border-bottom:1px solid #c5c5c5; padding:4px 3px 2px 3px; }
#sort_area li {background:url("/res/img/can_sort.png") no-repeat left middle; padding-left:18px;  list-style-type:none;  }
</style>
<style type="text/css">
</style>
<script type="text/javascript">
function check_data(oform) {
	var d = byid("sort_area").getElementsByTagName("LI");
	if (d.length == 0) {
		alert("请勾选要查看的表头数据！");
		return false;
	}
	var s = '';
	for (var i=0; i<d.length; i++) {
		s += (s ? "," : "") + d[i].innerHTML;
	}
	//alert(s);
	byid("save_result").value = s;
	return true;
}

function check_num(obj) {
	if (obj.checked) {
		var count = 0;
		var rs = document.getElementsByTagName("INPUT");
		for (var i=0; i<rs.length; i++) {
			var o = rs[i];
			if (o.type == "checkbox" && o.checked) {
				count++;
			}
		}
		if (count > 12) {
			alert("对不起，最多可以勾选12个字段，请去掉几个不重要的字段再试试呗！");
			obj.checked = false;
		} else {
			add_to_checked(obj.value);
			obj.nextSibling.style.color = "red";
		}
	} else {
		remove_checked(obj.value);
		obj.nextSibling.style.color = "";
	}
}

function add_to_checked(s) {
	var cur = byid("cur_select").value;
	byid("cur_select").value = cur + (cur ? "," : "") + s;
	update_ul();
}

function remove_checked(s) {
	var arr = byid("cur_select").value.split(",");
	var new_arr = new Array();
	for (var i=0; i<arr.length; i++) {
		if (arr[i] != s) {
			new_arr[new_arr.length] = arr[i];
		}
	}
	byid("cur_select").value = new_arr.join(",");
	update_ul();
}

function update_ul() {
	var arr = byid("cur_select").value.split(",");
	var s = '';
	for (var i=0; i<arr.length; i++) {
		s += '<li>'+arr[i]+'</li>';
	}
	byid("sort_area").innerHTML = s;
}
</script>
</head>

<body>

<form name="mainform" method="POST" onSubmit="return check_data(this)">
<table class="set_filed" width="100%">
	<tr class="head">
		<td align="right" width="20%">名称&nbsp;</td>
		<td align="center" width="80%">可勾选项(最多勾选12个)</td>
	</tr>

<?php
foreach ($power_show as $pcode => $pname) {
	if (array_key_exists($pcode, $filed)) {
		$f_arr = $filed[$pcode];
	} else {
		$f_arr = $common_field;
	}
	echo '<tr class="line"><td align="right">'.$pname.'：</td><td align="left">';
	$index = 0;
	foreach ($f_arr as $v) {
		$fn = $pcode.":".$v;
		$fshow = str_replace(":", "", $v);
		$chk = in_array($fn, $cur_field_arr) ? " checked" : "";
		echo '<input onclick="check_num(this);" type="checkbox" name="set[]" value="'.$fn.'" id="'.$fn.'"'.$chk.'><label for="'.$fn.'">'.$fshow.'</label>&nbsp; ';
		$index++;
		if ($index % 3 == 0 && $index % 6 != 0) {
			echo "&nbsp;&nbsp;&nbsp;&nbsp;";
		}
		if ($index % 6 == 0) {
			echo '<br>';
		}
	}
	echo '</td></tr>';
}

?>
</table>

<script type="text/javascript" src="/res/jquery.min.js"></script>
<script type="text/javascript" src="/res/jquery.dragsort-0.5.1.min.js"></script>

<input type="hidden" id="cur_select" value="<?php echo $uinfo["list_field"]; ?>">

<div style="margin-top:10px;">请拖动以调整排序：</div>
<ul id="sort_area">
<?php
$arr = explode(",", $uinfo["list_field"]);
foreach ($arr as $v) {
?>
	<li><?php echo $v; ?></li>
<?php } ?>
</ul>

<script type="text/javascript">
	$("ul:first").dragsort();
</script>


<input type="hidden" name="save_result" id="save_result" value="">

<div class="button_line">
	<input type="submit" class="submit" value="保存设置">
</div>


</form>

</body>
</html>