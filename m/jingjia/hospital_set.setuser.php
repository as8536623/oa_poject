<?php defined("ROOT") or exit("Error."); ?>
<html>
<head>
<title>����¼����Ա</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script type="text/javascript">
function check_data(oForm) {
	return confirm("ȷ�������ú�����           ");
}
</script>
</head>

<body>

<form method="POST" onsubmit="return check_data(this)">
<table width="100%" align="center" class="list">
	<tr>
		<td class="head" align="center" width="50">UID</td>
		<td class="head" align="center" width="80">����</td>
		<td class="head" align="left">��ǰ����</td>
	</tr>

<?php
if (count($all_users) > 0) {
	foreach ($all_users as $_uid => $_uname) {
		// ���˵�ǰ��ѡ��������:
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
				continue; //ϵͳ��ȡ���˸��ֶΣ������
			}
			$fn_name = $field_name_arr[$fn]; //�ֶζ�Ӧ������
			$chk = in_array($fn, $checked_field) ? " checked" : ""; //�Ƿ�ѡ��
?>
			<input type="checkbox" name="user_field_set[<?php echo $_uid; ?>][]" value="<?php echo $fn; ?>" <?php echo $chk; ?> id="ufd_<?php echo $_uid."_".$fn; ?>"><label for="ufd_<?php echo $_uid."_".$fn; ?>"><?php echo $fn_name; ?></label>&nbsp;
<?php	} ?>
		</td>
	</tr>
<?php } //��Աforeach���� ?>

<?php } else { ?>
	<tr>
		<td colspan="3" align="center" class="nodata">(��ҽԺ��û����Ӿ���¼����Ա��������Ա������ӣ�������ѡ��Ϊ�����ۡ�)</td>
	</tr>
<?php } ?>

</table>

<input type="hidden" name="set_hid" value="<?php echo $set_hid; ?>">
<input type="hidden" name="op" value="setuser_submit">
<div class="button_line"><input type="submit" class="submit" value="�ύ����"></div>

</form>

</body>
</html>