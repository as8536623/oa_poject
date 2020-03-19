<?php defined("ROOT") or exit("Error."); ?>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style>
</style>
<script language="javascript">
function update_cnt(o, id_a, id_b, id_c) {
	var a = byid(id_a).value;
	var b = byid(id_b).value;
	var c = byid(id_c).value;

	var cnt = (a != "" ? 1 : 0) + (b != "" ? 1 : 0) + (c != "" ? 1 : 0);

	if (cnt == 2 && (a == "" || b == "" || c == "")) {
		if (a == "") {
			byid(id_a).value = parseInt(b) + parseInt(c);
		} else if (b == "") {
			byid(id_b).value = a - c;
		} else {
			byid(id_c).value = a - b;
		}
	}
	if (cnt == 3) {
		if (o.id == id_a) {
			byid(id_c).value = a - b;
		} else if (o.id == id_b) {
			byid(id_c).value = a - b;
		} else {
			byid(id_b).value = a - c;
		}
	}
}

</script>
</head>

<body>
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $pinfo["title"]." - ".($op == "add" ? "����" : "�޸�"); ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"><input type="button" value="����" onclick="history.back()" class="button"></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>

<div class="description">
	<div class="d_title">��ʾ��</div>
	<!-- <li class="d_item">û�����ô˵</li> -->
</div>

<div class="space"></div>

<form name="mainform" method="POST" onsubmit="return check_data()">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">��������</td>
	</tr>
	<tr>
		<td class="left">ʱ�䣺</td>
		<td class="right"><?php echo $op == "add" ? $_GET["date"] : $line["date"]; ?></td>
	</tr>

	<tr>
		<td class="left">IPͳ�ƣ�</td>
		<td class="right">�ܣ�<input name="ip" id="ip" value="<?php echo $line["ip"]; ?>" class="input" style="width:80px" onchange="update_cnt(this,'ip', 'ip_local', 'ip_other')"> = ���أ�<input name="ip_local" id="ip_local" value="<?php echo $line["ip_local"]; ?>" class="input" style="width:80px" onchange="update_cnt(this,'ip', 'ip_local', 'ip_other')"> + ��أ�<input name="ip_other" id="ip_other" value="<?php echo $line["ip_other"]; ?>" class="input" style="width:80px" onchange="update_cnt(this,'ip', 'ip_local', 'ip_other')"> <span class="intro">��д��������ֵ����һ�����Զ�����</span></td>
	</tr>
	<tr>
		<td class="left">PVͳ�ƣ�</td>
		<td class="right">�ܣ�<input name="pv" id="pv" value="<?php echo $line["pv"]; ?>" class="input" style="width:80px" onchange="update_cnt(this,'pv', 'pv_local', 'pv_other')"> = ���أ�<input name="pv_local" id="pv_local" value="<?php echo $line["pv_local"]; ?>" class="input" style="width:80px" onchange="update_cnt(this,'pv', 'pv_local', 'pv_other')"> + ��أ�<input name="pv_other" id="pv_other" value="<?php echo $line["pv_other"]; ?>" class="input" style="width:80px" onchange="update_cnt(this,'pv', 'pv_local', 'pv_other')"> <span class="intro">��д��������ֵ����һ�����Զ�����</span></td>
	</tr>

	<tr>
		<td class="left">�����</td>
		<td class="right">�ܣ�<input name="click" id="click" value="<?php echo $line["click"]; ?>" class="input" style="width:80px" onchange="update_cnt(this,'click', 'click_local', 'click_other')"> = ���أ�<input name="click_local" id="click_local" value="<?php echo $line["click_local"]; ?>" class="input" style="width:80px" onchange="update_cnt(this,'click', 'click_local', 'click_other')"> + ��أ�<input name="click_other" id="click_other" value="<?php echo $line["click_other"]; ?>" class="input" style="width:80px" onchange="update_cnt(this,'click', 'click_local', 'click_other')"> <span class="intro">��д��������ֵ����һ�����Զ�����</span></td>
	</tr>
	<tr>
		<td class="left">��Ч�����</td>
		<td class="right">�ܣ�<input name="ok_click" id="ok_click" value="<?php echo $line["ok_click"]; ?>" class="input" style="width:80px" onchange="update_cnt(this,'ok_click', 'ok_click_local', 'ok_click_other')"> = ���أ�<input name="ok_click_local" id="ok_click_local" value="<?php echo $line["ok_click_local"]; ?>" class="input" style="width:80px" onchange="update_cnt(this,'ok_click', 'ok_click_local', 'ok_click_other')"> + ��أ�<input name="ok_click_other" id="ok_click_other" value="<?php echo $line["ok_click_other"]; ?>" class="input" style="width:80px" onchange="update_cnt(this,'ok_click', 'ok_click_local', 'ok_click_other')"> <span class="intro">��д��������ֵ����һ�����Զ�����</span></td>
	</tr>

	<tr>
		<td class="left">��Ի���</td>
		<td class="right"><input name="zero_talk" value="<?php echo $line["zero_talk"]; ?>" class="input" style="width:120px"> <span class="intro">��Ի�</span></td>
	</tr>
</table>


<?php
$e_names = $sconfig["engine"] ? explode("|", trim($sconfig["engine"])) : array();

$engine_data = (array) @unserialize($line["engine"]);
foreach ($engine_data as $k => $v) {
	if ($k && !in_array($k, $e_names)) {
		$e_names[] = $k;
	}
}

if (count($e_names) > 0) {
?>
<div class="space"></div>
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">��������������</td>
	</tr>
<?php
	foreach ($e_names as $n) {
?>
	<tr>
		<td class="left"><?php echo $n; ?>��</td>
		<td class="right">�ܣ�<input name="engine_<?php echo $n; ?>_all" id="engine_<?php echo $n; ?>_all" value="<?php echo $engine_data[$n]["all"]; ?>" class="input" style="width:80px" onchange="update_cnt(this,'engine_<?php echo $n; ?>_all', 'engine_<?php echo $n; ?>_local', 'engine_<?php echo $n; ?>_other')"> = ���أ�<input name="engine_<?php echo $n; ?>_local" id="engine_<?php echo $n; ?>_local" value="<?php echo $engine_data[$n]["local"]; ?>" class="input" style="width:80px" onchange="update_cnt(this,'engine_<?php echo $n; ?>_all', 'engine_<?php echo $n; ?>_local', 'engine_<?php echo $n; ?>_other')"> + ��أ�<input name="engine_<?php echo $n; ?>_other" id="engine_<?php echo $n; ?>_other" value="<?php echo $engine_data[$n]["other"]; ?>" class="input" style="width:80px" onchange="update_cnt(this,'engine_<?php echo $n; ?>_all', 'engine_<?php echo $n; ?>_local', 'engine_<?php echo $n; ?>_other')"></td>
	</tr>
<?php
	}
?>
</table>
<?php
}
?>

<div class="space"></div>

<input type="hidden" name="engine_names" value="<?php echo implode("|", $e_names); ?>">

<input type="hidden" name="op" value="<?php echo $op; ?>">
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="site_id" value="<?php echo $site_id; ?>">
<input type="hidden" name="date" value="<?php echo $op == "add" ? $_GET["date"] : $line["date"]; ?>">
<input type="hidden" name="back_url" value="<?php echo $_GET["back_url"]; ?>">
<div class="button_line"><input type="submit" class="submit" value="�ύ����"></div>


</form>

<div class="space"></div>
</body>
</html>