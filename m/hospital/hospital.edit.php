<?php
defined("ROOT") or exit;

if ($_POST) {
	$r = array();
	$r["name"] = $_POST["name"];
	$r["intro"] = $_POST["intro"];
	$r["sort"] = $_POST["sort"];

	$r["area"] = $_POST["area"];
	$r["sname"] = $_POST["sname"];
	$r["depart"] = $_POST["depart"];

	$r["full_name"] = $r["area"].$r["sname"].$r["depart"];

	$r["swt_ids"] = $_POST["swt_ids"];
	$r["set_huifang_kf"] = $_POST["set_huifang_kf"] ? 1 : 0;

	// 本数组已经加载（定义）过，其他值不会丢失
	$line["config"]["门诊收费项目"] = str_replace("\n", "|", str_replace("\r", "", $_POST["menzhen_fei"]));
	$line["config"]["住院收费项目"] = str_replace("\n", "|", str_replace("\r", "", $_POST["zhuyuan_fei"]));

	$r["config"] = serialize($line["config"]);

	if ($op == "add") {
		$r["addtime"] = time();
		$r["author"] = $username;
	}

	$sqldata = $db->sqljoin($r);
	if ($op == "edit") {
		$sql = "update $table set $sqldata where id='$id' limit 1";
	} else {
		$sql = "insert into $table set $sqldata";
	}

	if ($hid = $db->query($sql)) {
		$hid = ($op == "edit" ? $id : $hid);
		create_patient_table($hid);

		// 弹出窗口的处理方式:
		if ($mode == "add") {
			echo '<script> parent.update_content(); </script>';
		}
		echo '<script> parent.msg_box("资料提交成功", 2); </script>';
		echo '<script> parent.load_src(0); </script>';
	} else {
		echo "提交失败，请稍后再试！";
	}
	exit;
}

?>
<html>
<head>
<title><?php echo $pinfo["title"]; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script language="javascript">
function check_data(oForm) {
	if (oForm.name.value == "") {
		alert("请输入“医院名称”！");
		oForm.name.focus();
		return false;
	}
	return true;
}
</script>
</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $pinfo["title"]." - ".($op == "add" ? "新增" : "修改"); ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"></div>
</div>
<!-- 头部 end -->

<div class="space"></div>
<form method="POST" onsubmit="return check_data(this)">
<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">医院资料</td>
	</tr>
	<tr>
		<td class="left">项目名称：</td>
		<td class="right"><input name="name" value="<?php echo $line["name"]; ?>" class="input" size="30" style="width:200px"> <span class="intro">名称必须填写</span></td>
	</tr>
	<tr>
		<td class="left">所在地区：</td>
		<td class="right">
			<input name="area" value="<?php echo $line["area"]; ?>" class="input" style="width:60px">
			医院名：<input name="sname" value="<?php echo $line["sname"]; ?>" class="input" style="width:150px">
			科室名：<input name="depart" value="<?php echo $line["depart"]; ?>" class="input" style="width:80px">
		</td>
	</tr>
	<tr>
		<td class="left">医院简介：</td>
		<td class="right"><textarea class="input" name="intro" style="width:60%; height:80px; vertical-align:middle;"><?php echo $line["intro"]; ?></textarea> <span class="intro">医院简介，选填</span></td>
	</tr>
	<tr>
		<td class="left">商务通ID：</td>
		<td class="right"><input name="swt_ids" value="<?php echo $line["swt_ids"]; ?>" class="input" style="width:200px"> <span class="intro">关联的商务通帐号ID</span></td>
	</tr>
	<tr>
		<td class="left">回访设定：</td>
		<td class="right"><input type="checkbox" name="set_huifang_kf" value="1" <?php if ($line["set_huifang_kf"]) echo "checked"; ?> id="chk_set_huifang_kf"><label for="chk_set_huifang_kf">勾选：该医院回访需<b>回访主管</b>指定客服（不勾选则无需指定，回访客服可自由回访）</label></td>
	</tr>
	<tr>
		<td class="left">优先度：</td>
		<td class="right"><input name="sort" value="<?php echo $line["sort"]; ?>" class="input" style="width:80px"> <span class="intro">优先度越大,排序越靠前</span></td>
	</tr>
</table>

<div class="space"></div>

<table width="100%" class="edit">
	<tr>
		<td colspan="2" class="head">医院设置</td>
	</tr>
	<tr>
		<td class="left">门诊收费项目：</td>
		<td class="right"><textarea class="input" name="menzhen_fei" style="width:200px; height:70px; vertical-align:middle;"><?php echo str_replace("|", "\r\n", $line["config"]["门诊收费项目"]); ?></textarea> <span class="intro">门诊收费项目，每行一个</span></td>
	</tr>
	<tr>
		<td class="left">住院收费项目：</td>
		<td class="right"><textarea class="input" name="zhuyuan_fei" style="width:200px; height:70px; vertical-align:middle;"><?php echo str_replace("|", "\r\n", $line["config"]["住院收费项目"]); ?></textarea> <span class="intro">住院收费项目，每行一个</span></td>
	</tr>
</table>

<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="op" value="<?php echo $op; ?>">
<input type="hidden" name="pre_page" value="<?php echo $_SERVER["HTTP_REFERER"]; ?>">

<div class="button_line"><input type="submit" class="submit" value="提交资料"></div>
</form>
</body>
</html>