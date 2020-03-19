<?php
/*
// - ����˵�� : ����
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2009-05-02 15:47
*/
require "../../core/core.php";
$table = "ku_list";

if ($hid == 0) {
	exit_html("�Բ���û��ѡ��ҽԺ������ִ�иò�����");
}

if ($_POST) {
	$where = array();
	if($smb = $_POST["smb"]){
		$tb = strtotime($smb." 0:0:0");
		$where[] = "and addtime>=$tb";
	}
	if($sme = $_POST["sme"]){
		$te = strtotime($sme." 23:59:59");
		$where[] = "and addtime<=$te";
	}
	$sqlwhere = count($where) ? (implode(" ", $where)) : "";
	echo $sqlwhere;
	$fromuid = $_POST["fromuid"];
	$toid = $_POST["toid"];
	$fromname = $db->query("select realname from sys_admin where id=$fromuid limit 1", 1, "realname");
	$toname = $db->query("select realname from sys_admin where id=$toid limit 1", 1, "realname");
	
	if ($fromuid != '' && $toid != '') {
		if ($db->query("update $table set u_name='$toname',uid=$toid where binary u_name='$fromname' and uid=$fromuid and hid=$hid $sqlwhere")) {
			msg_box("����ɹ���", "?", 1);
			$log->add("����ת��", $realname."�� ".$fromname." ������ת�Ƹ� ".$toname, "", "");
		}
	}
}


$title = '�Ǽ���Ϣת��';

$fromname_list = $db->query("select uid,u_name,count(u_name) as acount from $table where u_name!='' and hid = $hid group by u_name order by binary u_name");
foreach ($fromname_list as $k => $li) {
	$fromname_list[$k]["from_value"] = $li["u_name"]." (".$li["acount"].")";
}

$toname_list = $db->query("select id,realname from sys_admin where isshow = 1 and (character_id = 2 or character_id = 12) and hospitals like '%$hid%'");

?>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<script language="javascript">
function Check() {
	var oForm = document.mainform;
	if (oForm.fromname.value == "") {
		alert("�����롰ԭ���֡���"); oForm.fromname.focus(); return false;
	}
	if (oForm.toname.value == "") {
		alert("�����롰�����֡���"); oForm.toname.focus(); return false;
	}
	return true;
}
</script>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers" >
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $title; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"><button onClick="history.back()" class="button">����</button></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>

<div class="description">
	<div class="d_title">��ʾ��</div>
	<div class="d_item">����ת������������ύ��ť��ʼת��</div>
</div>

<div class="space"></div>

<form name="mainform" action="?action=move" method="POST" onSubmit="return Check()">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">ת������</td>
	</tr>
	<tr>
		<td class="left red">��ʼʱ�䣺</td>
		<td class="right">
			<input name="smb" id="smb" class="input" onClick="picker({el:'smb',dateFmt:'yyyy-MM-dd',maxDate:'#F{$dp.$D(\'sme\')}' })" value="" >
		</td>
	</tr>
	<tr>
		<td class="left red">����ʱ�䣺</td>
		<td class="right">
		<input name="sme" id="sme" class="input" onClick="picker({el:'sme',dateFmt:'yyyy-MM-dd',minDate:'#F{$dp.$D(\'smb\')}' })" value="" >
		</td>
	</tr>
	<tr>
		<td class="left red">ԭ���֣�</td>
		<td class="right">
			<select name="fromuid" class="combo">
				<option value='' style="color:gray">--��ѡ��--</option>
				<?php echo list_option($fromname_list, 'uid', 'from_value', ''); ?>
			</select>
			<span class="intro">ԭ���֣�����Ϊ��</span>
		</td>
	</tr>
	<tr>
		<td class="left red">�����֣�</td>
		<td class="right">
			<select name="toid" class="combo">
				<option value='' style="color:gray">--��ѡ��--</option>
				<?php echo list_option($toname_list, 'id', 'realname', ''); ?>
			</select>
			<span class="intro">�µ����֣�����Ϊ��</span>
		</td>
	</tr>
</table>

<div class="button_line"><input type="submit" class="submit" value="�ύ"></div>

</form>
</body>
</html>