<?php
require "../../core/core.php";

$uid = intval($_REQUEST["uid"]);
$cur_menu = '';
if ($uid > 0) {
	$line = $db->query("select * from sys_admin where id=$uid limit 1", 1);
	$cur_menu = $line["menu"];
}

if (!empty($_SESSION["global_user_menu"])) {
	$cur_menu = $_SESSION["global_user_menu"];
}

if ($_POST) {
	$menu = $power->get_power_from_post();
	$_SESSION["global_user_menu"] = $menu;

	echo '<script> parent.set_detail_power(0); </script>';
	//echo '<script> alert("权限已保存！"); </script>';
	exit;
}

?>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title>独立设置权限</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
</head>

<body>
<form name="mainform" action="" method="POST" onsubmit="return Check()">

<?php echo $power->show_power_table($usermenu, $cur_menu); ?>

<div class="button_line">
	<input type="submit" class="submit" value="确定">
</div>

<input type="hidden" name="uid" value="<?php echo $uid; ?>" />
</form>
</body>
</html>