<?php defined("ROOT") or exit("Error."); ?>
<html>
<head>
<title>填写当日竞价消费</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<style>
.h_name {font-family:"微软雅黑"; }
</style>
<script language="javascript">
function check_data(oForm) {
	// 日期安全性校验，防止出现:选了日期，但页面没重新加载数据的情况（防止日期控件不兼容）
	if (byid("date_to_check").value != byid("date_input").value) {
		alert("对不起，日期选择后，没有成功加载系统已录入数据，请刷新页面重试。"); return false;
	}
	if (oForm.xiaofei.value == '') {
		alert("请输入消费额！                "); oForm.xiaofei.focus(); return false;
	}
	return true;
}
function set_date(s) {
	// 页面内容屏蔽掉，防止没重新加载之前就开始修改数据并提交
	document.title = "正在读取数据，请稍候...";
	document.body.innerHTML = "";
	self.location = "?op=add&date="+s;
}
</script>
</head>

<body>
<div class="description">
	<div class="d_title">提示：</div>
	<div class="d_item">　　请录入当前选择医院（科室）的当日竞价消费。</div>
</div>

<div class="space"></div>

<form name="mainform" action="" method="POST" onsubmit="return check_data(this)">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">当日竞价消费额</td>
	</tr>
	<tr>
		<td class="left">当前医院：</td>
		<td class="right"><b class="h_name"><?php echo $h_name; ?></b>&nbsp;&nbsp;</td>
	</tr>
	<tr>
		<td class="left">日期：</td>
		<td class="right">
			<input name="date" id="date_input" onchange="set_date(this.value)" readonly value="<?php echo $_GET["date"] ? $_GET["date"] : date("Y-m-d", strtotime("-1 day")); ?>" class="input" style="width:100px">&nbsp;
			<button onclick="picker({el:'date_input',dateFmt:'yyyy-MM-dd'}); return false;" class="buttonb">选择日期</button>
			<!-- <button onclick="set_date('<?php echo date("Y-m-d"); ?>')" class="button">今天</button>&nbsp;
			<button onclick="set_date('<?php echo date("Y-m-d", strtotime("-1 day")); ?>')" class="button">昨天</button>&nbsp;
			<button onclick="set_date('<?php echo date("Y-m-d", strtotime("-2 day")); ?>')" class="button">前天</button>&nbsp;
			<button onclick="set_date('<?php echo date("Y-m-d", strtotime("-3 day")); ?>')" class="button"><?php echo date("d", strtotime("-3 day")); ?>日</button>&nbsp;
			<button onclick="set_date('<?php echo date("Y-m-d", strtotime("-4 day")); ?>')" class="button"><?php echo date("d", strtotime("-4 day")); ?>日</button>&nbsp;&nbsp; -->
			<span class="intro">日期默认为昨天</span></td>
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
<input type="hidden" name="op" value="add_submit">
<input type="hidden" id="date_to_check" value="<?php echo $_GET["date"] ? $_GET["date"] : date("Y-m-d", strtotime("-1 day")); ?>">
<div class="button_line"><input type="submit" class="submit" value="提交资料"></div>
</form>

</body>
</html>