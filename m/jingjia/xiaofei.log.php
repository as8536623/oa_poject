<?php defined("ROOT") or exit("Error."); ?>
<html>
<head>
<title>�鿴�޸���־</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style>
.h_name {font-family:"΢���ź�"; }
</style>
</head>

<body>

<table width="100%" class="edit">
	<tr>
		<td class="left">����ID��</td>
		<td class="right"><b><?php echo $id; ?></b></td>
	</tr>
	<tr>
		<td class="left">���ڣ�</td>
		<td class="right"><b><?php echo int_date_to_date($line["date"]); ?></b></td>
	</tr>
	<tr>
		<td class="left">��Ӽ�¼��</td>
		<td class="right"><?php echo $line["u_name"]." �� ".date("Y-m-d H:i", $line["addtime"])." ���"; ?></td>
	</tr>
	<tr>
		<td class="left">�޸ļ�¼��</td>
		<td class="right"><?php echo $line["log"] ? $line["log"] : "(��������)"; ?></td>
	</tr>

</table>
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="op" value="edit_submit">

<div class="button_line"><input onclick="parent.load_box(0)" type="submit" class="submit" value="�رղ�����"></div>
</form>
</body>
</html>