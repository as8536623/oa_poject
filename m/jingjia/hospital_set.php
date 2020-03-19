<?php
/* --------------------------------------------------------
// ˵��: ҽԺ����������������
// ����: ���� (weelia@126.com)
// ʱ��: 2011-07-25
// ----------------------------------------------------- */
$table = "jingjia_hospital_set";
require "../../core/core.php";

if ($debug_mode || $username == "admin" || $uinfo["part_id"] == 9 || ($uinfo["part_id"] == 202 && $uinfo["part_admin"])) {
	// �����޸�
} else {
	exit_html("�Բ�����û�в���Ȩ�ޣ�");
}

if ($op) {
	include "hospital_set.op.php";
}

// ����ҽԺ:
$hids = implode(",", $hospital_ids);
$h_list = $db->query("select id,area,name from hospital where id in ($hids) order by area asc, name asc", "id");

$area_count = $area_ids = array();
foreach ($h_list as $v) {
	$area_count[$v["area"]] = intval($area_count[$v["area"]]) + 1;
	$area_ids[$v["area"]][] = $v["id"];
}
arsort($area_count);


// ��ȡ���������б�
$field_name_arr = $db->query("select fieldname, name from jingjia_field_set order by fieldname", "fieldname", "name");


?>
<html>
<head>
<title>����</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script type="text/javascript">
function load_url(s) {
	parent.load_box(1, 'src', s);
}
</script>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center">ҽԺ����������������</td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"><button onclick="history.back()" class="button">����</button></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>
<div class="description">
	<div class="d_item">����ÿ��ҽԺʹ������Щ�������ѵ��������档(������������ã���ʾ�����������������á������õ�ѡ����á�)</div>
</div>

<div class="space"></div>
<form method="POST" onsubmit="return check_data(this)">
<table width="100%" align="center" class="list">
	<tr>
		<td class="head" align="center" width="60">ID</td>
		<td class="head" align="left" width="">&nbsp;ҽԺ����</td>
		<td class="head" align="left" width="">&nbsp;��ǰ��������</td>
		<td class="head" align="left" width="">&nbsp;��ǰ¼����Ա</td>
	</tr>


<?php foreach ($area_count as $k => $v) { ?>
	<tr>
		<td class="group" colspan="4" align="left"><?php echo $k." (".$v.")"; ?></td>
	</tr>

<?php
	foreach ($area_ids[$k] as $id) {
?>
	<tr>
		<td align="center" class="item"><?php echo $id; ?></td>
		<td align="left" class="item">&nbsp;<?php echo $h_list[$id]["name"]; ?></td>
		<td align="left" class="item">
<?php
	$fs = $db->query("select fields from jingjia_hospital_set where hid=$id limit 1", 1, "fields");
	if ($fs != '') {
		$fs = explode(",", $fs);
		$arr = array();
		foreach ($fs as $fsn) {
			if (array_key_exists($fsn, $field_name_arr)) {
				$arr[] = $field_name_arr[$fsn];
			}
		}
		echo "&nbsp;".implode("��", $arr);
	} else {
		//echo "(δ����)";
	}
?>
			&nbsp;<a href="javascript:void(0)" onclick="load_url('m/jingjia/hospital_set.php?op=setfield&hid=<?php echo $id; ?>');">����</a>
		</td>
		<td align="left" class="item">
<?php
	// ��ȡ�����õ�¼����Ա:
	$users = $db->query("select uid,u_name,fields from jingjia_user_set where hid=$id");
	if (count($users) > 0) {
		$arr = array();
		foreach ($users as $us) {
			$fs = explode(",", $us["fields"]);
			$fs_name = array();
			foreach ($fs as $fsn) {
				if (array_key_exists($fsn, $field_name_arr)) {
					$fs_name[] = $field_name_arr[$fsn];
				}
			}

			$arr[] = '<font color="#FF8040">'.$us["u_name"]."</font>".":".implode("��", $fs_name);
		}
		echo "&nbsp;".implode(' <font color="silver">|</font> ', $arr);
	} else {
		//echo "(δ����)";
	}
?>
			&nbsp;<a href="javascript:void(0);" onclick="load_url('m/jingjia/hospital_set.php?op=setuser&hid=<?php echo $id; ?>');">����</a>
		</td>
	</tr>
<?php } ?>

<?php } ?>


</table>

</form>


</body>
</html>