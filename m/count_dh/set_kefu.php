<?php
// --------------------------------------------------------
// - 功能说明 : 项目新增，修改
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2010-10-13 11:40
// --------------------------------------------------------
require "../../core/core.php";

if ($_POST) {

	// 处理客服
	$kefu_arr = explode(",", str_replace("、", ",", str_replace("，", ",", $_POST["kefu"])));
	$new_arr = array();
	foreach ($kefu_arr as $v) {
		$v = trim($v);
		if ($v) $new_arr[] = $v;
	}
	$kefu = @implode(",", $new_arr);

	// 查询是否已经有记录了：
	$line = $db->query("select * from count_dh_type where hid=$hid limit 1", 1);
	if ($line["hid"] > 0) {
		$sql = "update count_dh_type set kefu='$kefu' where hid='$hid' limit 1";
	} else {
		$sql = "insert into count_dh_type set hid='$hid', kefu='$kefu'";
	}

	ob_start();
	$db->query($sql);
	$error = ob_get_clean();

	if (empty($error)) {
		echo '<script> parent.update_content(); </script>';
		echo '<script> parent.msg_box("资料提交成功", 2); </script>';
		echo '<script> parent.load_src(0); </script>';
	} else {
		exit_html("提交出错：".$error);
	}

	exit;
}

$line = $db->query("select * from count_dh_type where hid=$hid limit 1", 1);


?>
<html>
<head>
<title>设置客服</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>

<style>
#rec_part, #rec_user {margin-top:6px; }
.rec_user_b {width:140px; float:left; }
.rec_group_part {clear:both; margin:10px 0 5px 0; font-weight:bold; }

.hide_scroll {overflow-y:hidden; overflow-x:hidden; }
.show_scroll, html {overflow-y:auto; overflow-x:hidden; }

.left {padding:10px 5px !important; }
.right {padding:10px 5px !important; }
</style>

<script language="javascript">
</script>

</head>

<body>

<div class="space"></div>

<form name="mainform" action="" method="POST">
<table width="100%" class="edit">
	<tr>
		<td class="left" valign="top">客服名单：</td>
		<td class="right">
			<input name="kefu" value="<?php echo $line["kefu"]; ?>" class="input" style="width:90%"><br>
			<b>填写说明：</b><br>
			　　1. 请注意名字不要填错，要和系统已登记的人员真实姓名一致。<br>
			　　2. 如果填写的名字在系统里查不到，数据无法进行关联查询（比如到院人数等）<br>
			　　3. 各名字用逗号（大小写逗号均可）隔开。
		</td>
	</tr>
</table>

<input type="hidden" name="op" value="submit">

<br>

<div class="button_line">
	<input type="submit" class="submit" value="提交资料">
</div>

</form>

</body>
</html>