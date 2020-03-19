<?php defined("ROOT") or exit("Error."); ?>
<html>
<head>
<title>设置医院竞价搜索引擎</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script language="javascript">
function check_data(oForm) {
	return confirm("确认要提交吗？                       ");
}
</script>
</head>

<body>
<div class="description">
	<div class="d_item">请勾选该医院使用的竞价搜索引擎。(如果全部不勾选，则默认使用全部可用搜索引擎)</div>
</div>

<div class="space"></div>

<form name="mainform" action="" method="POST" onsubmit="return check_data(this)">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">医院竞价搜索引擎</td>
	</tr>
	<tr>
		<td class="left" style="width:35%;">请勾选：</td>
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

<div class="button_line"><input type="submit" class="submit" value="提交资料"></div>
</form>
</body>
</html>