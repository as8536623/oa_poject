<?php
/*
// - ����˵�� : ��ѯ����ʱ������
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2013-4-19
*/
require "../../core/core.php";
require "config.inc.php";
$table = "zixun_hour_set";

if ($hospitals == '') {
	exit("�Բ�����û��ҽԺȨ�ޣ�");
}

// ҽԺ:
$hospital_arr = $db->query("select id,name,area,sort from hospital where id in ($hospitals) order by name asc", 'id');

// ��ѯ��ǰ����:
$hour_set_arr = $db->query("select * from $table", "hid");

if ($op == "edit") {
	include "set.edit.php";
	exit;
}

// ҳ�濪ʼ ------------------------
?>
<html>
<head>
<title>��ѯ����ʱ������</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/sorttable_keep.js" language="javascript"></script>
<style type="text/css">
.column_sortable {cursor:pointer; color:blue; font-family:"΢���ź�"; }
.sorttable_nosort {font-family:"΢���ź�"; }
.hour_set_list {border:1px solid #97e6a5; }
.hour_set_list .head td {border:1px solid #e7e7e7; background:#f2f8f9; padding:4px 3px 3px 3px; font-weight:bold; }
.hour_set_list .data td {border:1px solid #e7e7e7; padding:4px 3px 3px 3px; }
.al {text-align:left; }
.ac {text-align:center; }
.yh {font-family:"΢���ź�"; }
</style>

<script type="text/javascript">
function h_set(hid) {
	var link = "/m/zixun/set.php?op=edit&hid="+hid;
	parent.load_src(1, link, 700, 200);
	return false;
}
</script>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center">��ѯ����ʱ������</td><td class="bar_right"></td></tr></table></div>
	<div class="header_center" style="font-family:'΢���ź�'; color:red; ">
		(��������ã���ϵͳĬ��ʱ��δ���8~12 12~16 16~20 20~23)
	</div>
	<div class="headers_oprate"><button onclick="self.location.reload();return false;" class="button">ˢ��</button></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>

<table id="hour_set" class="round_table hour_set_list sortable" cellpadding="0" cellspacing="0" width="100%">
	<tr class="head">
		<td class="ac column_sortable" width="" title="���������">����</td>
		<td class="ac column_sortable" width="" title="���������">ҽԺ</td>
		<td class="ac column_sortable" width="" title="���������">���ȶ�</td>
		<td class="al column_sortable" width="60%" title="���������">ʱ�������</td>
		<td class="ac sorttable_nosort" width="">����</td>
	</tr>

<?php
foreach ($hospital_arr as $_hid => $_hinfo) {
	$h_set = $hour_set_arr[$_hid];
?>
	<tr class="data" onmouseover="mi(this)" onmouseout="mo(this)">
		<td class="ac"><?php echo $_hinfo["area"]; ?></td>
		<td class="ac"><?php echo $_hinfo["name"]; ?></td>
		<td class="ac"><?php echo $_hinfo["sort"]; ?></td>
		<td class="al yh" id="h_set_<?php echo $_hid; ?>"><?php echo implode(" &nbsp;", hour_set_to_show(explode(",", $h_set["h_set"]))); ?></td>
		<td class="ac"><button onclick="h_set(<?php echo $_hid; ?>)" class="button">�޸�</button></td>
	</tr>
<?php } ?>

</table>

</body>
</html>