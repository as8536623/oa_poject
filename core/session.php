<?php
/*
// 说明: 决定session 采用何种机制
// 作者: 幽兰 (weelia@126.com)
// 时间: 2012-12-13
*/

define("gSessionUseMemCache", 0);

$path = str_replace("\\", "/", dirname(__FILE__))."/";

if (!defined("gUseMemCache")) {
	include_once $path."function.mem_cache.php";
}

if (gUseMemCache && gSessionUseMemCache) {
	include_once $path."session_mem.php"; //采用memcache存储
} else {
	include_once $path."db.php";
	include_once $path."session_db.php"; //采用数据库存储，可能会慢
}

?>