<?php defined("ROOT") or exit("Error."); ?>
<html>
<head>
<title>修改竞价消费</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style>
.h_name {font-family:"微软雅黑"; }
</style>
<script language="javascript">
function check_data(oForm) {
	if (oForm.xiaofei.value == '') {
		alert("请设置竞价消费额!                   "); oForm.xiaofei.focus(); return false;
	}
	return true;
}
</script>
</head>

<body>
<div class="description">
	<div class="d_title">提示：</div>
	<div class="d_item">　　请录入当前选择医院（科室）的当日竞价消费(日期不能修改)。</div>
</div>

<div class="space"></div>

<form name="mainform" action="" method="POST" onsubmit="return check_data(this)">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">竞价当日消费额</td>
	</tr>
	<tr>
		<td class="left">日期：</td>
		<td class="right"><b><?php echo int_date_to_date($line["date"]); ?></b></td>
	</tr>

<?php 
foreach ($user_field_arr as $fn) {
	if (!array_key_exists($fn, $all_field_arr)) {
		continue; //系统已删除此字段
	}
	$fn_name = $all_field_arr[$fn]; //字段名称
?>
	<tr>
		<td class="left"><?php echo $fn_name; ?>：</td>
		<td class="right"><input name="xiaofei[<?php echo $fn; ?>]" value="<?php echo $line[$fn] > 0 ? $line[$fn] : ""; ?>" class="input" style="width:100px"> <span class="intro">请填写消费额（如果有多个子账号，请填写这些账号的总消费）</span></td>
	</tr>
<?php } ?>

</table>
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="op" value="edit_submit">

<div class="button_line"><input type="submit" class="submit" value="提交资料"></div>
</form>
</body>
</html>