<?php
/*
// - 功能说明 : visit.php
// - 创建作者 : 幽兰 (weelia@126.com)
// - 创建时间 : 2010-07-07
*/
$mod = "visit";
require "../../core/core.php";

if (!$hid) {
	exit_html("对不起，没有选择医院，不能执行该操作！");
}

$table = "visit";

if ($op) {
	include $mod.".op.php";
}

include $mod.".index.php";
?>