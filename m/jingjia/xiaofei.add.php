<?php defined("ROOT") or exit("Error."); ?>
<html>
<head>
<title>��д���վ�������</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<style>
.h_name {font-family:"΢���ź�"; }
</style>
<script language="javascript">
function check_data(oForm) {
	// ���ڰ�ȫ��У�飬��ֹ����:ѡ�����ڣ���ҳ��û���¼������ݵ��������ֹ���ڿؼ������ݣ�
	if (byid("date_to_check").value != byid("date_input").value) {
		alert("�Բ�������ѡ���û�гɹ�����ϵͳ��¼�����ݣ���ˢ��ҳ�����ԡ�"); return false;
	}
	if (oForm.xiaofei.value == '') {
		alert("���������Ѷ                "); oForm.xiaofei.focus(); return false;
	}
	return true;
}
function set_date(s) {
	// ҳ���������ε�����ֹû���¼���֮ǰ�Ϳ�ʼ�޸����ݲ��ύ
	document.title = "���ڶ�ȡ���ݣ����Ժ�...";
	document.body.innerHTML = "";
	self.location = "?op=add&date="+s;
}
</script>
</head>

<body>
<div class="description">
	<div class="d_title">��ʾ��</div>
	<div class="d_item">������¼�뵱ǰѡ��ҽԺ�����ң��ĵ��վ������ѡ�</div>
</div>

<div class="space"></div>

<form name="mainform" action="" method="POST" onsubmit="return check_data(this)">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">���վ������Ѷ�</td>
	</tr>
	<tr>
		<td class="left">��ǰҽԺ��</td>
		<td class="right"><b class="h_name"><?php echo $h_name; ?></b>&nbsp;&nbsp;</td>
	</tr>
	<tr>
		<td class="left">���ڣ�</td>
		<td class="right">
			<input name="date" id="date_input" onchange="set_date(this.value)" readonly value="<?php echo $_GET["date"] ? $_GET["date"] : date("Y-m-d", strtotime("-1 day")); ?>" class="input" style="width:100px">&nbsp;
			<button onclick="picker({el:'date_input',dateFmt:'yyyy-MM-dd'}); return false;" class="buttonb">ѡ������</button>
			<!-- <button onclick="set_date('<?php echo date("Y-m-d"); ?>')" class="button">����</button>&nbsp;
			<button onclick="set_date('<?php echo date("Y-m-d", strtotime("-1 day")); ?>')" class="button">����</button>&nbsp;
			<button onclick="set_date('<?php echo date("Y-m-d", strtotime("-2 day")); ?>')" class="button">ǰ��</button>&nbsp;
			<button onclick="set_date('<?php echo date("Y-m-d", strtotime("-3 day")); ?>')" class="button"><?php echo date("d", strtotime("-3 day")); ?>��</button>&nbsp;
			<button onclick="set_date('<?php echo date("Y-m-d", strtotime("-4 day")); ?>')" class="button"><?php echo date("d", strtotime("-4 day")); ?>��</button>&nbsp;&nbsp; -->
			<span class="intro">����Ĭ��Ϊ����</span></td>
	</tr>
<?php
foreach ($user_field_arr as $fn) {
	if (!array_key_exists($fn, $all_field_arr)) {
		continue; //ϵͳ��ɾ�����ֶ�
	}
	$fn_name = $all_field_arr[$fn]; //�ֶ�����
?>
	<tr>
		<td class="left"><?php echo $fn_name; ?>��</td>
		<td class="right"><input name="xiaofei[<?php echo $fn; ?>]" value="<?php echo $line[$fn] > 0 ? $line[$fn] : ""; ?>" class="input" style="width:100px"> <span class="intro">����д���Ѷ����ж�����˺ţ�����д��Щ�˺ŵ������ѣ�</span></td>
	</tr>
<?php } ?>

</table>
<input type="hidden" name="op" value="add_submit">
<input type="hidden" id="date_to_check" value="<?php echo $_GET["date"] ? $_GET["date"] : date("Y-m-d", strtotime("-1 day")); ?>">
<div class="button_line"><input type="submit" class="submit" value="�ύ����"></div>
</form>

</body>
</html>