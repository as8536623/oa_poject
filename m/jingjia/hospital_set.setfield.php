<?php defined("ROOT") or exit("Error."); ?>
<html>
<head>
<title>����ҽԺ������������</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script language="javascript">
function check_data(oForm) {
	return confirm("ȷ��Ҫ�ύ��                       ");
}
</script>
</head>

<body>
<div class="description">
	<div class="d_item">�빴ѡ��ҽԺʹ�õľ����������档(���ȫ������ѡ����Ĭ��ʹ��ȫ��������������)</div>
</div>

<div class="space"></div>

<form name="mainform" action="" method="POST" onsubmit="return check_data(this)">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">ҽԺ������������</td>
	</tr>
	<tr>
		<td class="left" style="width:35%;">�빴ѡ��</td>
		<td class="right" style="width:65%;">
<?php
	foreach ($all_field_arr as $k => $v) {
		$chk = 0;
		if (in_array($k, $cur_field_arr)) {
			$chk = 1;
		}
?>
			<input type="checkbox" name="field_set[]" value="<?php echo $k; ?>" id="fd_<?php echo $k; ?>" <?php echo $chk ? "checked" : ""; ?>><label for="fd_<?php echo $k; ?>"><?php echo $v; ?></label><br>
<?php } ?>
		</td>
	</tr>

</table>
<input type="hidden" name="set_hid" value="<?php echo $set_hid; ?>">
<input type="hidden" name="op" value="setfield_submit">

<div class="button_line"><input type="submit" class="submit" value="�ύ����"></div>
</form>
</body>
</html>