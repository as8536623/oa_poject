<?php defined("ROOT") or exit("Error."); ?>
<html>
<head>
<title>�޸ľ�������</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style>
.h_name {font-family:"΢���ź�"; }
</style>
<script language="javascript">
function check_data(oForm) {
	if (oForm.xiaofei.value == '') {
		alert("�����þ������Ѷ�!                   "); oForm.xiaofei.focus(); return false;
	}
	return true;
}
</script>
</head>

<body>
<div class="description">
	<div class="d_title">��ʾ��</div>
	<div class="d_item">������¼�뵱ǰѡ��ҽԺ�����ң��ĵ��վ�������(���ڲ����޸�)��</div>
</div>

<div class="space"></div>

<form name="mainform" action="" method="POST" onsubmit="return check_data(this)">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">���۵������Ѷ�</td>
	</tr>
	<tr>
		<td class="left">���ڣ�</td>
		<td class="right"><b><?php echo int_date_to_date($line["date"]); ?></b></td>
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
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="op" value="edit_submit">

<div class="button_line"><input type="submit" class="submit" value="�ύ����"></div>
</form>
</body>
</html>