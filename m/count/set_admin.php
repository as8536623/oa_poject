<?php
require "../../core/core.php";

$_hid = intval($_REQUEST["hid"]);
if ($_hid == 0) {
	exit("参数错误 hid");
}
$_hinfo = $db->query("select * from hospital where id=$_hid limit 1", 1);
$admin_arr = $db->query("select * from sys_admin where isshow=1 and concat(',',hospitals,',') like '%,{$_hid},%' order by realname asc", "id");

if ($_POST) {
	$ids = @implode(",", $_POST["admin_ids"]);
	$names = array();
	foreach ($_POST["admin_ids"] as $v) {
		$names[] = $admin_arr[$v]["realname"];
	}
	$names = implode("、", $names);
	echo '<script> parent.update_uids("'.$ids.'", "'.$names.'"); </script>';
	echo '<script> parent.wee_show_box(0); </script>';
	exit;
}

$_tmp = explode(",", $_GET["uids"]);
$cur_uids = array();
foreach ($_tmp as $v) {
	$v = intval($v);
	if ($v > 0) $cur_uids[] = $v;
}

?>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title>设置管理员</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script type="text/javascript">
function update_check_color(o) {
	if (o.nextSibling.tagName.toLowerCase() == "label") {
		o.nextSibling.style.color = o.checked ? "blue" : "";
	}
}
</script>
</head>

<body>
<form name="mainform" action="" method="POST">

<b>医院：<?php echo $_hinfo["name"]; ?></b>
<br>
<br>
<b>请选择管理人员：(按姓名排序)</b>
<div style="padding:20px; ">
<?php
foreach ($admin_arr as $_id => $_u) {
	$checked = in_array($_id, $cur_uids) ? " checked" : "";
?>
	<nobr><input type="checkbox" class="check" name="admin_ids[]" value="<?php echo $_id; ?>" id="u_<?php echo $_id; ?>" <?php echo $checked; ?> onclick="update_check_color(this)"><label for="u_<?php echo $_id; ?>" <?php if ($checked) echo ' style="color:red"'; ?>><?php echo $_u["realname"]; ?></label>　</nobr>
<?php } ?>
</div>

<div class="button_line">
	<input type="submit" class="submit" value="确定">
</div>

<input type="hidden" name="hid" value="<?php echo $_hid; ?>" />
</form>
</body>
</html>