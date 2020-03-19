<?php defined("ROOT") or exit("Error."); ?>
<html>
<head>
<title>查看修改日志</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style>
.h_name {font-family:"微软雅黑"; }
</style>
</head>

<body>

<table width="100%" class="edit">
	<tr>
		<td class="left">数据ID：</td>
		<td class="right"><b><?php echo $id; ?></b></td>
	</tr>
	<tr>
		<td class="left">日期：</td>
		<td class="right"><b><?php echo int_date_to_date($line["date"]); ?></b></td>
	</tr>
	<tr>
		<td class="left">添加记录：</td>
		<td class="right"><?php echo $line["u_name"]." 于 ".date("Y-m-d H:i", $line["addtime"])." 添加"; ?></td>
	</tr>
	<tr>
		<td class="left">修改记录：</td>
		<td class="right"><?php echo $line["log"] ? $line["log"] : "(暂无内容)"; ?></td>
	</tr>

</table>
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="op" value="edit_submit">

<div class="button_line"><input onclick="parent.load_box(0)" type="submit" class="submit" value="关闭并返回"></div>
</form>
</body>
</html>