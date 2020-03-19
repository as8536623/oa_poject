<?php
/*
// ����: ���� (weelia@126.com)
*/

$zixun_edit_timeout = 1800; //��ѯ�༭���� ��ʱʱ��

$default_hour_set = "8,12,16,20,23"; //ϵͳĬ��ʱ���


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


// ��������Сʱ֮���ʱ���
function get_hour_count($h1, $h2) {
	if ($h2 < $h1) {
		$h2 = 24 + $h2;
	}
	return $h2 - $h1;
}

// ��ȡ����Сʱ֮�����������:
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