<?php
/*
// ˵��: ������uKey
// ����: ���� (weelia@126.com)
// ʱ��: 2011-07-07
*/
require "../core/core.php";

if ($uinfo["ukey_sn"] != '') {
	exit_html("�Բ��������˺��Ѿ��󶨹�uKey���кţ�".substr($uinfo["ukey_sn"], 0, 4)."********".substr($uinfo["ukey_sn"], -4, 4));
}

if ($_POST) {
	$ukey_sn = $_POST["ukey_sn"];
	if (strlen($ukey_sn) == 16) {
		if ($db->query("select count(*) as c from sys_admin where ukey_sn='".$ukey_sn."'", 1, "c") > 0) {
			exit_html("��uKey��š�{$ukey_sn}���Ѿ�������ʹ�ã��󶨲��ɹ���");
		}
		$db->query("update sys_admin set use_ukey=1, ukey_sn='".$ukey_sn."', ukey_no='������' where name='$username' limit 1");
		msg_box("uKey�󶨳ɹ���", "/m/main.php", 3);
		exit;
	} else {
		exit_html("uKey��ų��Ȳ���ȷ��������16λ��");
	}
}



?>
<html>
<head>
<title>������uKey</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="../res/base.css" rel="stylesheet" type="text/css">
<script src="../res/base.js" language="javascript"></script>
<script type="text/javascript">
function write_cur_ukey_sn() {
	et99 = byid("ET99");
	if (et99) {
		window.onerror = function() {
			//alert("��ȡET99�豸���ִ���");
			return true;
		}
		var count = et99.FindToken("FFFFFFFF");
		if (count > 0) {
			et99.OpenToken("FFFFFFFF", 1)
			sn = et99.GetSN();
			if (sn != '') {
				byid("ukey_sn").value = sn;
				byid("ukey_sn_show").innerHTML = sn.substring(0,6)+"****"+sn.substring(10,16);
				return;
			}
		}
	}
}

function check_data(f) {
	if (f.ukey_sn.value == '') {
		alert("���ȡuKey���֮�����ύ��");
		return false;
	}
	if (!confirm("�ύ����ȡ����Ҳ���������޸İ󶨡�ȷ��Ҫ�ύ��")) {
		return false;
	}
	return true;
}


</script>
</head>

<body>

<form method="POST" onsubmit="return check_data(this)">
	<div style="padding:20px 0 0 40px;">
		<b>Ҫ�󶨵�uKey���</b>��<span id="ukey_sn_show" style="padding:0 10px; font-family:Tahoma; font-weight:bold;">(δ��ȡ)</span><br />
		<input type="hidden" name="ukey_sn" id="ukey_sn" value="">
		<br />
		<br />
		ע�⣺<br />
		��1���������İ�ť��ȡuKey��š�<br />
		��2�������ܻ�ȡ������ <a href="/ukey/" title="���߰�װ��������" target="_blank">[����]</a> ���߰�װ����������ɺ󣬱�ҳ����Զ����²���ȡuKey��<br />
		��3��uKey�ύ���´ε�¼����ʹ��uKey�������ظ��ύ����ȷ�ϲ����uKeyû�кͱ��˵Ļ�����<br />
		<br />
		<br />
		<input type="submit" class="submit" value="�ύ">
	</div>
</form>

<!-- ukey -->
<object classid="clsid:e6bd6993-164f-4277-ae97-5eb4bab56443" id="ET99" name="ET99" style="left:0px; top:0px" width="0" height="0"></object>


<script type="text/javascript">
var Timer = setTimeout("self.location.reload()", 3000);
write_cur_ukey_sn();
if (byid("ukey_sn").value != '') {
	clearTimeout(Timer);
}
</script>

</body>
</html>