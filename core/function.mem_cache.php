<?php
/*
// 说明: 设置和读取memcache
// 作者: 幽兰 (weelia@126.com)
// 时间: 2012-11-13 => 2012-12-09
// 注意: 本函数只依赖系统Memcache扩展功能，不依赖其他程序
*/

define("gUseMemCache", 1); //如果设置为0，则禁用memcache缓存机制
define("gMemPreFix", "btoa_"); //全局前缀，用于解决同一服务器多个系统使用

function wee_mem_init() {
	if (!gUseMemCache) {
		return false;
	}

	if ($GLOBALS["mem"]) {
		return $GLOBALS["mem"];
	}

	ob_start();
	if (!class_exists("Memcache", false)) {
		echo "Memcache库未安装...<br>";
	} else {
		$GLOBALS["mem"] = $mem = new Memcache();
		$mem->connect("127.0.0.1", 11211);
	}
	$error = ob_get_clean();
	if ($error) {
		echo "";
		return false;
	}
	return $mem;
}

// 将数据存储到memcache:
// 存储的内容有：
// type => array|string  仅内部使用
// save_time => 值的存储时间
// data => 结果
function wee_mem_set_cache($name, $value, $timeout=600) {
	$mem = wee_mem_init();
	if ($mem === false) return false;

	$to_save = array();
	$to_save["type"] = is_array($value) ? "array" : "string";
	$to_save["save_time"] = time();

	if (is_array($value)) {
		$save_value = serialize($value);
	} else {
		$save_value = $value;
	}
	$to_save["data"] = $save_value;

	return $mem->set(gMemPreFix.$name, $to_save, 0, $timeout);
}


// 从memcache读取数据，注意返回值的格式:
function wee_mem_get_cache($name) {
	$mem = wee_mem_init();
	if ($mem === false) return false;

	$arr = $mem->get(gMemPreFix.$name);
	if ($arr === false || !is_array($arr)) {
		return false;
	}
	if ($arr["type"] == "array") {
		$arr["data"] = @unserialize($arr["data"]);
	}

	unset($arr["type"]); //type不返回
	return $arr;
}


// 删除一个缓存
function wee_mem_delete_cache($name) {
	$mem = wee_mem_init();
	if ($mem === false) return false;

	return $mem->delete(gMemPreFix.$name, 0);
}

?>