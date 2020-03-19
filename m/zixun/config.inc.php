<?php
/*
// 作者: 幽兰 (weelia@126.com)
*/

$zixun_edit_timeout = 1800; //咨询编辑数据 超时时间

$default_hour_set = "8,12,16,20,23"; //系统默认时间段


function hour_set_to_show($arr) {
	$last_hour = '';
	$res = array();
	foreach ($arr as $v) {
		if ($last_hour != '') {
			$res[] = $last_hour."~".$v;
		}
		$last_hour = $v;
	}
	return $res;
}


// 计算两个小时之间的时间差
function get_hour_count($h1, $h2) {
	if ($h2 < $h1) {
		$h2 = 24 + $h2;
	}
	return $h2 - $h1;
}

// 获取两个小时之间的所有整点:
function get_between_hour($h1, $h2) {
	if ($h2 < $h1) {
		$h2 = 24 + $h2;
	}
	$r = array();
	for ($i = $h1; $i < $h2; $i++) {
		$h = $i > 23 ? ($i - 24) : $i;
		$r[] = $h;
	}
	return $r;
}

?>