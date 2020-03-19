<?php defined("ROOT") or exit("Error."); ?>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script language="javascript">
var base_src = "m/sys/character.php";
function add(hid) {
	set_high_light('');
	parent.load_src(1, base_src+'?op=add', 1000, 600);
	return false;
}

function edit(id, obj) {
	set_high_light(obj);
	parent.load_src(1, base_src+'?op=edit&id='+id, 1000, 600);
	return false;
}
</script>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center">Ȩ���б�</td><td class="bar_right"></td></tr></table></div>
	<div class="header_center">
<?php if (check_power("add")) { ?>
		<button onclick="add()" class="button">���</button>
<?php } ?>
	</div>
	<div class="headers_oprate"><form name="topform" method="GET">ģ��������<input name="searchword" value="<?php echo $_GET["searchword"]; ?>" class="input" size="12">&nbsp;<input type="submit" class="search" value="����" style="font-weight:bold" title="�������">&nbsp;<input type="button" value="����" onclick="location='?'" class="search" title="�˳�������ѯ"></form></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>

<!-- �����б� begin -->
<form name="mainform">
<table width="100%" align="center" class="list">
<?php
echo $table_header."\r\n";
if (count($table_items) > 0) {
	echo implode("\r\n", $table_items);
} else {
?>
	<tr>
		<td colspan="<?php echo count($list_heads); ?>" align="center" class="nodata">(û������...)</td>
	</tr>
<?php
}
?>
</table>
</form>
<!-- �����б� end -->

<div class="space"></div>

<!-- ��ҳ���� begin -->
<div class="footer_op">
	<div class="footer_op_left"><button onclick="select_all()" class="button">ȫѡ</button>&nbsp;<button onclick="unselect()" class="button">��ѡ</button>&nbsp;
	<?php
		if ($username == "admin" || $debug_mode) {
			echo $power->show_button("close,delete");
		}
	?>
	</div>
	<div class="footer_op_right"><?php echo pagelinkc($page, $pagecount, $count, make_link_info($link_param, "page"), "button"); ?></div>
</div>
<!-- ��ҳ���� end -->

<div class="space"></div>
</body>
</html>