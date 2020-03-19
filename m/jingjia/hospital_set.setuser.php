<?php defined("ROOT") or exit("Error."); ?>
<html>
<head>
<title>设置录入人员</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script type="text/javascript">
function check_data(oForm) {
	return confirm("确定都设置好了吗？           ");
}
</script>
</head>

<body>

<form method="POST" onsubmit="return check_data(this)">
<table width="100%" align="center" class="list">
	<tr>
		<td class="head" align="center" width="50">UID</td>
		<td class="head" align="center" width="80">姓名</td>
		<td class="head" align="left">当前设置</td>
	</tr>

<?php
if (count($all_users) > 0) {
	foreach ($all_users as $_uid => $_uname) {
		// 此人当前勾选过的设置:
		$_ufield = $cur_users[$_uid]["fields"];
		$checked_field = array();
		if (!empty($_ufield)) {
			$checked_field = explode(",", $_ufield);
		}
?>
	<tr class="<?php echo $class; ?>">
		<td align="center" class="item"><?php echo $_uid; ?></td>
		<td align="center" class="item"><?php echo $_uname; ?></td>
		<td align="left" class="item">
<?php
		foreach ($hospital_field_arr as $fn) {
			if (!array_key_exists($fn, $field_name_arr)) {
				continue; //系统已取消了该字段，不输出
			}
			$fn_name = $field_name_arr[$fn]; //字段对应的名称
			$chk = in_array($fn, $checked_field) ? " checked" : ""; //是否被选中
?>
			<input type="checkbox" name="user_field_set[<?php echo $_uid; ?>][]" value="<?php echo $fn; ?>" <?php echo $chk; ?> id="ufd_<?php echo $_uid."_".$fn; ?>"><label for="ufd_<?php echo $_uid."_".$fn; ?>"><?php echo $fn_name; ?></label>&nbsp;
<?php	} ?>
		</td>
	</tr>
<?php } //人员foreach结束 ?>

<?php } else { ?>
	<tr>
		<td colspan="3" align="center" class="nodata">(该医院还没有添加竞价录入人员，请至人员管理添加，部门需选择为“竞价”)</td>
	</tr>
<?php } ?>

</table>

<input type="hidden" name="set_hid" value="<?php echo $set_hid; ?>">
<input type="hidden" name="op" value="setuser_submit">
<div class="button_line"><input type="submit" class="submit" value="提交资料"></div>

</form>

</body>
</html>