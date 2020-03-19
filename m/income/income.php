<?php
/*
// - 功能说明 : income.php
// - 创建作者 : 幽兰 (weelia@126.com)
// - 创建时间 : 2010-07-07
*/
$mod = $table = "income";
require "../../core/core.php";

if (!$hid) {
	exit_html("对不起，没有选择医院，不能执行该操作！");
}

if (!$hconfig["门诊收费项目"]) {
	exit_html("请先设置医院收费项目（在医院管理中）");
}

if ($op) {
	include $mod.".op.php";
}

include $mod.".index.php";
?>