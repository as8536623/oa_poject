<?php
// --------------------------------------------------------
// - ����˵�� : �������ϲ鿴
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2009-05-02 17:28
// --------------------------------------------------------

$title = $line["name"]." ����";

$disease_id_name = $db->query("select id,name from disease where hospital_id='$hid'", 'id', 'name');
$part_id_name = $db->query("select id,name from sys_part", 'id', 'name');
$depart_id_name = $db->query("select id,name from depart where hospital_id='$hid'", "id", "name");

if ($uinfo["show_talk"]) {
	// ��ȡ�����¼��
	$line["talk_content"] = get_talk_content($hid, $line["lid"]);
} else {
	$line["talk_content"] = '(��Ȩ��)';
}


// --------- ���� -----------
function _talk_text_show($s) {
	$s = str_replace(" ", "&nbsp;", $s);
	$s = str_replace("\r", "", $s);
	$s = str_replace("\n", "<br>", $s);
	for ($i=0; $i<5; $i++) {
		$s = str_replace("<br><br>", "<br>", $s);
	}
	$s = "<br>".$s;
	$s = preg_replace("/<br>([^>]*?\d{2}:\d{2}:\d{2})/", "<br><br><font color=blue>[\\1]</font>", $s);
	while (substr($s, 0, 4) == "<br>") {
		$s = substr($s, 4);
	}
	return $s;
}


?>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style>
.view {border:2px solid #aedfbb; }
.view td {padding:5px 3px 3px 8px; border:1px solid #d3efdb; }
.view .h {font-weight:bold; background:#eaf7ed; text-align:left; padding-left:15px; }
.view .l {text-align:right; color:#000000; background:#f4fbf7; }
.view .r {text-align:left; }
.fo_line {margin:15px 0 auto; text-align:center; }
</style>
<script type="text/javascript">

</script>
</head>

<body>

<table width="100%" align="center" class="view">
	<tr>
		<td colspan="4" class="h">��������</td>
	</tr>
	<tr>
		<td class="l">������</td>
		<td class="r" colspan="3"><b><?php echo $line["name"]; ?></b></td>
	</tr>
	<tr>
		<td class="l" width="15%">�Ա�</td>
		<td class="r" width="30%"><?php echo $line["sex"]; ?></td>
		<td class="l" width="15%">���䣺</td>
		<td class="r" width="40%"><?php echo $line["age"] > 0 ? $line["age"] : ""; ?></td>
	</tr>
	<tr>
		<td class="l">�绰��</td>
		<td class="r"><?php echo ($show_tel || $line["author"] == $realname) ? $line["tel"] : "<font color='gray' title='��Ȩ��'>*</font>"; ?> <?php echo $line["tel_location"]; ?></td>
        <?php if($uinfo["part_id"] != 4){?>
		<td class="l">�Ǽ���&amp;ά���ˣ�</td>
		<td class="r"><?php echo $line["author"]; ?> &amp; <?php echo $line["w_name"]; ?> @ <?php echo date("Y-m-d H:i", $line["addtime"]); ?></td>
        <?php } ?>
	</tr>
	<tr>
		<td class="l">��ѯ���ݣ�</td>
		<td class="r" colspan="3"><?php echo text_show(rtrim($line["content"])); ?></td>
	</tr>
	<tr>
    	<td class="l">�������ң�</td>
		<td class="r"><?php echo $disease_id_name[$line["disease_id"]]; ?></td>
		<td class="l">�������ͣ�</td>
		<td class="r"><?php echo $line["disease_2"] ? $line["disease_2"] : _show_disease($line["disease_id"]); ?></td>
	</tr>
	<tr>
		<td class="l">ԤԼ�ţ�</td>
		<td class="r"><?php echo $line["ordernum"]; ?></td>
        <td class="l">����ţ�</td>
		<td class="r"><?php echo $line["zhuanjia_num"]; ?></td>
	</tr>
	<tr>
		<td class="l">������</td>
		<td class="r"><?php echo $line["area"]; ?></td>
		<td class="l">ý����Դ��</td>
		<td class="r"><?php echo $line["media_from"]; ?>  <?php echo $line["engine_key"]; ?></td>
	</tr>
	<tr>
		<td class="l">����ʱ�䣺</td>
		<td class="r"><?php echo @date("Y.n.j G:i", $line["order_date"]); ?></td>
        <td class="l">������ַ��</td>
		<td class="r"><?php echo $line["from_site"]; ?></td>
        
	</tr>
	<tr>
		<td class="l">��Լ״̬��</td>
		<td class="r"><?php echo $status_array[$line["status"]]; ?></td>
		<td class="l">IP��</td>
		<td class="r"><?php echo $line["IP"]; ?></td>
	</tr>
    <tr>
		<td class="l"></td>
		<td class="r"></td>
		<td class="l">ҽ����</td>
		<td class="r"><?php echo $line["doctor"] ? $line["doctor"] : '<font color="gray">(δ����)</font>'; ?></td>
	</tr>
    <?php if($uinfo["part_id"] != 4){?>
	<tr>
		<td class="l">��ע��</td>
		<td class="r" colspan="3"><?php echo text_show(rtrim($line["memo"])); ?></td>
	</tr>
	<tr>
		<td class="l">�޸ļ�¼��</td>
		<td class="r" colspan="3"><?php echo text_show($line["edit_log"]); ?></td>
	</tr>
	<tr>
		<td colspan="4" class="h">�طü�¼</td>
	</tr>
	<tr>
		<td class="l">�ط����ݣ�</td>
		<td class="r" colspan="3"><?php echo $line["huifang"] ? text_show(strip_tags($line["huifang"])) : '<font color="gray">(��)</font>'; ?></td>
	</tr>
	<tr>
		<td class="l">�طÿͷ���</td>
		<td class="r" colspan="3"><?php echo $line["huifang_kf"] ? $line["huifang_kf"] : "<font color='gray'>(��)</font>"; ?> <?php echo $line["huifang_nexttime"] ? ("&nbsp; �´λط����ѣ�".int_date_to_date($line["huifang_nexttime"])) : ""; ?></td>
	</tr>
	<tr>
		<td colspan="4" class="h">��������</td>
	</tr>
	<tr>
		<td class="l">�������ݣ�</td>
		<td class="r" colspan="3"><?php echo $line["talk_content"] ? _talk_text_show($line["talk_content"]) : '<font color="gray">(��)</font>'; ?></td>
	</tr>
    <?php } ?>
</table>

<div class="fo_line"> <button onClick="parent.load_src(0)" class="buttonb">�ر�</button> </div>

</body>
</html>