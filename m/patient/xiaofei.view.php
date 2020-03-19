<?php
// --------------------------------------------------------
// - 功能说明 : 病人资料查看
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2009-05-02 17:28
// --------------------------------------------------------

$title = $line["name"]." 资料";

$disease_id_name = $db->query("select id,name from disease where hospital_id='$hid'", 'id', 'name');
$part_id_name = $db->query("select id,name from sys_part", 'id', 'name');
$depart_id_name = $db->query("select id,name from depart where hospital_id='$hid'", "id", "name");



// --------- 函数 -----------
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
		<td colspan="4" class="h">基本资料</td>
	</tr>
	<tr>
		<td class="l">姓名：</td>
		<td class="r" colspan="3"><b><?php echo $line["name"]; ?></b></td>
	</tr>
	<tr>
		<td class="l" width="15%">性别：</td>
		<td class="r" width="30%"><?php echo $line["sex"]; ?></td>
		<td class="l" width="15%">年龄：</td>
		<td class="r" width="40%"><?php echo $line["age"] > 0 ? $line["age"] : ""; ?></td>
	</tr>
	<tr>
		<td class="l">电话：</td>
		<td class="r"><?php echo ($show_tel || $line["author"] == $realname) ? $line["tel"] : "<font color='gray' title='无权限'>*</font>"; ?> <?php echo $line["tel_location"]; ?></td>
        <td class="l">医生：</td>
		<td class="r"><?php echo $line["doctor"] ? $line["doctor"] : '<font color="gray">(未设置)</font>'; ?></td>
	</tr>
	<tr>
    	<td class="l">所属科室：</td>
		<td class="r"><?php echo $disease_id_name[$line["disease_id"]]; ?></td>
		<td class="l">疾病类型：</td>
		<td class="r"><?php echo $line["disease_2"] ? $line["disease_2"] : _show_disease($line["disease_id"]); ?></td>
	</tr>
	<tr>
        <td class="l">门诊号：</td>
		<td class="r"><?php echo $line["zhuanjia_num"]; ?></td>
        <td class="l">就诊时间：</td>
		<td class="r"><?php echo @date("Y.n.j G:i", $line["order_date"]); ?></td>
	</tr>
	<tr>
		<td class="l">地区：</td>
		<td class="r"><?php echo $line["area"]; ?></td>
        <?php if($username == "admin"){ ?>
        <td class="l">登记人：</td>
		<td class="r"><?php echo $line["author"]; ?></td>
        <?php }?>
		
	</tr>

	<tr>
		<td class="l">备注：</td>
		<td class="r" colspan="3"><?php echo text_show(rtrim($line["xf_memo"])); ?></td>
	</tr>
    <tr>
		<td colspan="4" class="h">消费信息</td>
	</tr>
	<tr>
		<td class="l">消费总额：</td>
		<td class="r" colspan="3"><?php echo $line["xiaofei_count"] ? $line["xiaofei_count"] : '<font color="gray">(未消费)</font>'; ?></td>
	</tr>
	<tr>
		<td class="l">消费记录：</td>
		<td class="r" colspan="3"><?php echo $line["xiaofei_log"] ? text_show($line["xiaofei_log"]) : '<font color="gray">(无)</font>'; ?></td>
	</tr>
	<tr>
		<td colspan="4" class="h">回访记录</td>
	</tr>
	<tr>
		<td class="l">回访内容：</td>
		<td class="r" colspan="3"><?php echo $line["xf_huifang"] ? text_show(strip_tags($line["xf_huifang"])) : '<font color="gray">(无)</font>'; ?></td>
	</tr>
	<tr>
		<td class="l">下次回访提醒：</td>
		<td class="r" colspan="3"><?php echo $line["xf_huifang_nexttime"] ? int_date_to_date($line["xf_huifang_nexttime"]) : ""; ?></td>
	</tr>
</table>

<div class="fo_line"> <button onClick="parent.load_src(0)" class="buttonb">关闭</button> </div>

</body>
</html>