<?php
//
// - ����˵�� : ���ñ�ͷ
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2013-5-16
//
require "../core/core.php";

if ($_POST) {
	$f_arr = explode(",", $_POST["save_result"]);
	if (count($f_arr) > 12) {
		exit("�Բ����б�����������12���ֶΣ�����ѡ��̫����...");
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
	echo 'parent.msg_box("���ñ���ɹ�");';
	echo 'parent.update_content();';
	echo '</script>';

	exit;
}

// �����Ȩ��:
$data_power = explode(",", $uinfo["data_power"]);

$power_show = array();

if ($debug_mode || in_array("all", $data_power)) {
	$power_show["��"] = "������";
}
if ($debug_mode || in_array("web", $data_power)) {
	$power_show["����"] = "����";
}
if ($debug_mode || in_array("tel", $data_power)) {
	$power_show["�绰"] = "�绰";
}
// ����:
$z_info = $db->query("select name,type,sum_condition from index_module_set where isshow=1");
foreach ($z_info as $li) {
	if ($debug_mode || in_array($li["name"], $data_power)) {
		$power_show[$li["name"]] = $li["name"];
	}
}

if (count($power_show) == 0) {
	exit("�Բ�����û���κ�Ȩ�ޣ�����ϵ����Ա��");
}


// ��ǰ���ã�
$cur_field_arr = explode(",", $uinfo["list_field"]);



// �������õ��ֶζ���:
$filed = array();
$filed["��"] = array(
	"����:ԤԼ", "����:Ԥ��", "����:ʵ��", "����:����",
	"����:ԤԼ", "����:Ԥ��", "����:ʵ��",
	"����:ԤԼ", "����:Ԥ��", "����:ʵ��",
	"����:ԤԼ", "����:Ԥ��", "����:ʵ��",
	"ͬ��:ԤԼ", "ͬ��:Ԥ��", "ͬ��:ʵ��",
	"����",
);

$filed["����"] = array(
	"����:ԤԼ", "����:Ԥ��", "����:ʵ��",
	"����:ԤԼ", "����:Ԥ��", "����:ʵ��",
	"����:ԤԼ", "����:Ԥ��", "����:ʵ��",
	"����:ԤԼ", "����:Ԥ��", "����:ʵ��",
	"ͬ��:ԤԼ", "ͬ��:Ԥ��", "ͬ��:ʵ��",
	"����",
);

$filed["�绰"] = array(
	"����:ԤԼ", "����:Ԥ��", "����:ʵ��",
	"����:ԤԼ", "����:Ԥ��", "����:ʵ��",
	"����:ԤԼ", "����:Ԥ��", "����:ʵ��",
	"����:ԤԼ", "����:Ԥ��", "����:ʵ��",
	"ͬ��:ԤԼ", "ͬ��:Ԥ��", "ͬ��:ʵ��",
);



// ͨ�ò���:
$common_field = array(
	"����:ԤԼ", "����:Ԥ��", "����:ʵ��",
	"����:ԤԼ", "����:Ԥ��", "����:ʵ��",
	"����:ԤԼ", "����:Ԥ��", "����:ʵ��",
	"����:ԤԼ", "����:Ԥ��", "����:ʵ��",
);




?>
<html>
<head>
<title>�����б��ͷ (��ѡ������·�����˳��)</title>
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
		alert("�빴ѡҪ�鿴�ı�ͷ���ݣ�");
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
			alert("�Բ��������Թ�ѡ12���ֶΣ���ȥ����������Ҫ���ֶ��������£�");
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
		<td align="right" width="20%">����&nbsp;</td>
		<td align="center" width="80%">�ɹ�ѡ��(��๴ѡ12��)</td>
	</tr>

<?php
foreach ($power_show as $pcode => $pname) {
	if (array_key_exists($pcode, $filed)) {
		$f_arr = $filed[$pcode];
	} else {
		$f_arr = $common_field;
	}
	echo '<tr class="line"><td align="right">'.$pname.'��</td><td align="left">';
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

<div style="margin-top:10px;">���϶��Ե�������</div>
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
	<input type="submit" class="submit" value="��������">
</div>


</form>

</body>
</html>