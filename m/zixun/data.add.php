<?php
if ($table == '') exit;




if ($_POST) {
	ob_start();


	$error = ob_get_clean();
	if ($error != '') {
		echo "�����ύʧ�ܣ�ϵͳ��æ�����Ժ����ԡ�";
	} else {
		echo '<script> parent.update_content(); </script>';
		//$show_h_set = implode(" &nbsp;", hour_set_to_show(explode(",", $h_set)));
		//echo '<script> parent.update_content_byid("h_set_'.$_hid.'", "'.$show_h_set.'", "innerHTML"); </script>';
		echo '<script> parent.msg_box("����ɹ�", 2); </script>';
		echo '<script> parent.load_src(0); </script>';
	}
	exit;
}

?>
<html>
<head>
<title>¼������</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style type="text/css">
.yh {font-family:"΢���ź�"; }
</style>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $h_name; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>
<form name="mainform" action="" method="POST" onsubmit="return Check()">
<table width="100%" class="edit">
	<tr>
		<td class="left">���ڣ�</td>
		<td class="right">
			<input name="date" value="<?php echo date("Y-m-d"); ?>" class="input" style="width:100px" id="order_date"> <img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'order_date',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="ѡ������">&nbsp; &nbsp;
			ʱ��Σ�
			<select name="h_from_end" class="combo">
				<?php echo list_option($cur_hour_set_arr, '_value_', '_value_'); ?>
			</select>&nbsp; &nbsp;
			�ͷ���
			<select name="kf_name" class="combo">
				<?php echo list_option($kefu_id_names, '_value_', '_value_', $_GET["kefu"]); ?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="left">�ܵ����</td>
		<td class="right"><input name="all_click" value="" class="input" style="width:150px"></td>
	</tr>
	<!-- <tr>
		<td class="left">����ͨԤԼ��</td>
		<td class="right"><input name="swt_order" value="" class="input" style="width:150px"></td>
	</tr> -->
	<!-- <tr>
		<td class="left">QQԤԼ��</td>
		<td class="right"><input name="qq_order" value="" class="input" style="width:150px"></td>
	</tr> -->
</table>
<input type="hidden" name="op" value="add">

<div class="button_line"><input type="submit" class="submit" value="�ύ����"></div>
</form>


</body>
</html>