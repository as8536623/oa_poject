<?php defined("ROOT") or exit("Error."); ?>
<html>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title" style="width:45%"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $pinfo["title"]; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="header_center">
		<?php //echo $power->show_button("add", "&sid=".$sid."&back_url=".$back_url); ?>
	</div>
	<div class="headers_oprate"><form name="topform" method="GET"><input type="hidden" name="op" value="<?php echo $op; ?>"><input type="hidden" name="sid" value="<?php echo $sid; ?>">&nbsp;&nbsp;<nobr>ģ��������<input name="key" value="<?php echo $_GET["key"]; ?>" class="input" size="12">&nbsp;<input type="submit" class="search" value="����" style="font-weight:bold" title="�������">&nbsp;<input type="button" value="����" onclick="location='?op=list&sid=<?php echo $sid; ?>'" class="search" title="�˳�������ѯ"><input type="hidden" name="op" value="list"></nobr></form></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>

<!-- �����б� begin -->
<form name="mainform">
<?php echo $t->show(); ?>
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
	<div class="footer_op_right"><?php echo $pagelink; ?></div>
</div>
<!-- ��ҳ���� end -->

<div class="space"></div>
</body>
</html>