<?php
/* --------------------------------------------------------
// ˵��: �Һ����ݱ������
// ����: ���� (weelia@126.com)
// ʱ��: 2009-05-21 20:52
// ----------------------------------------------------- */
session_start();

if (!$_POST || !$_REQUEST["hid"]) {
	// û�д���post���ݣ�����û�д���ҽԺ����ʾ����ֱ����������ʣ�ֱ�ӷ���404ҳ��
	header("HTTP/1.0 404 Not Found");
	exit;
}

/*
	���ݲ���˵��: Ҫ�������͵绰�Ǳ�����Ŀ���ҵ绰�����ٰ���7λ����
	"name" => "����"
	"sex" => 1:��, 0:Ů ��ֱ���ύ���С���Ů��
	"phone|tel" => "�绰"
	"email" => "�����ʼ���ַ"
	"city" => "���ڳ���"
	"date_depart|dep" => "����"
	"doctor" => "ԤԼҽ��"

	"date_y" => "ԤԼ��"
	"date_m" => "ԤԼ��"
	"date_d" => "ԤԼ��"
	"date_h" => "ԤԼСʱ"
	"date_i" => "ԤԼ����"
	"date_s" => "ԤԼ��"

	"time2|order_date" => "ֱ��ָ��ԤԼ����" ��ʽ���� strtotime() �����ܽ����ĸ�ʽ �� "2008-12-25 14:30:00"
	"content|title" => "�������"
	"otherInfo|memo" => "������ע"
*/

// ��ʼ��������:
$r = array();

// ����
$r["name"] = trim(format_text($_POST["name"]));
if ($r["name"] == '') {
	exit_js("�Բ�������������д��");
}

// �Ա�
if (in_array(trim($_POST["sex"]), array("1", "��"))) {
	$r["sex"] = "��";
} else if (in_array(trim($_POST["sex"]), array("0", "Ů"))) {
	$r["sex"] = "Ů";
}

// �绰
$r["tel"] = num_to_lower(trim(format_text(trim($_POST["tel"]) ? $_POST["tel"] : $_POST["phone"])));
if ($r["tel"] == '' || !num_check($r["tel"], 7)) {
	exit_js("�Բ�����û��������ϵ�绰�����ߵ绰��ʽ����ȷ (����Ҫ��7λ����)��");
}

$r["email"] = trim(format_text($_POST["email"]));
$r["city"] = trim($_POST["city"]);

// ԤԼʱ��
if ($_POST["date_y"] > 0 || $_POST["date_m"] > 0 || $_POST["date_d"] > 0 || $_POST["date_h"] > 0 || $_POST["date_i"] > 0) {
	$time_str = (isset($_POST["date_y"]) ? intval($_POST["date_y"]) : date("Y"))."-";
	$time_str .= (isset($_POST["date_m"]) ? intval($_POST["date_m"]) : date("m"))."-";
	$time_str .= (isset($_POST["date_d"]) ? intval($_POST["date_d"]) : date("d"))." ";
	$time_str .= (isset($_POST["date_h"]) ? intval($_POST["date_h"]) : "0").":";
	$time_str .= (isset($_POST["date_i"]) ? intval($_POST["date_i"]) : "0").":";
	$time_str .= isset($_POST["date_s"]) ? intval($_POST["date_s"]) : "0";
} else {
	$time_str = $_POST["order_date"] ? $_POST["order_date"] : $_POST["time2"];
	$time_str_ori = $time_str;

	if ($time_str != '' && strlen($time_str) <= 10) {
		$time_str .= ' ';
		$time_str .= (isset($_POST["_hour"]) ? intval($_POST["_hour"]) : '0').":";
		$time_str .= (isset($_POST["_minute"]) ? intval($_POST["_minute"]) : '0').":";
		$time_str .= '0';
	}

	//�޷�����ʱ�䣬���䴮�����ڱ�ע���������
	if (!(strtotime($time_str) > 0)) {
		$_POST["memo"] = $time_str_ori." ".$_POST["memo"];
	}
}
$r["order_date"] = @strtotime($time_str);

$r["depart"] = trim($_POST["date_depart"] ? $_POST["date_depart"] : $_POST["dep"]);
$r["content"] = trim($_POST["content"] ? $_POST["content"] : $_POST["title"]);
$r["doctor"] = trim($_POST["doctor"]);
$r["memo"] = trim($_POST["memo"] ? $_POST["memo"] : $_POST["otherInfo"]);

include "ip/function.ip.php"; //IP������
$r["ip"] = _get_ip();
$r["ip_address"] = ip_area($r["ip"]);

// ������Լ����⹥����������
// GET:
$get_str = '';
foreach ($_GET as $k => $v) {
	$get_str .= $k." => ".$v."<br>";
}
$r["getdata"] = $get_str;

// ����POST�����е����ݴ���:
$post_str = '';
foreach ($_POST as $k => $v) {
	$post_str .= $k." => ".$v."<br>";
}
$r["postdata"] = $post_str;

// ����SERVER������������:
$server_str = '';
foreach ($_SERVER as $k => $v) {
	$server_str .= $k." => ".$v."<br>";
}
$r["serverdata"] = $server_str;


// ҽԺid:
$hospital_id = intval($_REQUEST["hid"]);
$r["hospital_id"] = $hospital_id;

// ͨ��ǰ����ַ�ж���վ��Դ
$site_url = $_SERVER["HTTP_REFERER"];
if ($site_url) {
	list($a, $site_url) = explode("://", $site_url, 2);
	if ($site_url) {
		list($site_url, $b) = explode("/", $site_url, 2);
	}
}
$r["site"] = $site_url;

$r["addtime"] = time();

include "lib/mysql.php";
$db = new mysql();


// �ؼ��ʹ��� 2010-03-31
$f_word = $db->query("select config from guahao_config where name='filter' limit 1", 1, "config");
if ($f_word != '') {
	$f_word_arr = explode(",", str_replace("��", ",", $f_word));
	$check_str = implode(" ", $r);
	foreach ($f_word_arr as $key) {
		if ($key = trim($key)) {
			if (substr_count($check_str, $key) > 0) {
				exit_js("�ύ�ɹ�����������");
			}
		}
	}
}
// ==== end.


// �ȼ�� hospital_id
$hospital_name = $db->query("select name from hospital where id=$hospital_id limit 1", 1, "name");
if (!$hospital_name) {
	exit_js("�Բ��𣬸ü�ҽԺ��δע�᱾ϵͳ���������޷��ύ��");
}

// ��ֹ�ظ��ύ���:
$addtime = $db->query("select addtime from guahao where hospital_id=$hospital_id and name='{$r[name]}' and ip='{$r[ip]}' order by addtime desc limit 1", 1, "addtime");
if ($addtime > 0 && time() - $addtime < 300) { // 5����
	exit_js("�Բ����벻Ҫ�ظ��ύ��");
}

// �ύ����:
ob_start(); //��ʹ����Ҳ����ʾmysql����
$sqldata = $db->sqljoin($r);
$result = $db->query("insert into guahao set $sqldata");
ob_end_clean();

if ($result) {
	exit_js("�Һ������Ѿ��ύ�ɹ���\\n\\n��л��ʹ�����߹Һ�ϵͳ�����������Ѿ��յ������ǻἰʱ����ȡ����ϵ��лл��");
} else {
	exit_js("�Բ��𣬷�������æ�����Ժ����ԣ�");
}

// �������!
// ---------------------------------------------------------------------------------------




// ------------------------------- ����Ϊ��ҳ����Ҫʹ�õĺ��� -----------------------------

// ���alert��ִֹͣ�д���
function exit_js($str) {
	echo '<script language="javascript">';
	if ($str) {
		echo 'alert("'.$str.'");';
	}
	echo 'history.back();';
	echo '</script>';
	exit;
}

// ȥ���ı��е�html����:
function format_text($string) {
	$search = array("'<script[^>]*?>.*?</script>'si", "'<[\/\!]*?[^<>]*?>'si", "'([\r\n])[\s]+'", "'&(quot|#34);'i",
		"'&(amp|#38);'i", "'&(lt|#60);'i", "'&(gt|#62);'i", "'&(nbsp|#160);'i", "'&(iexcl|#161);'i", "'&(cent|#162);'i",
		"'&(pound|#163);'i", "'&(copy|#169);'i", "'&#(\d+);'e");
	$replace = array ("", "", "\\1", "\"", "&", "<", ">", " ", chr(161), chr(162), chr(163), chr(169), "chr(\\1)");
	$string = preg_replace($search, $replace, $string);

	return rtrim($string);
}

// ���ָ��������������
function num_check($str, $num_need=7) {
	$num = explode(" ", "0 1 2 3 4 5 6 7 8 9");
	$strlen = strlen($str);
	if ($strlen == 0) return false;

	$num_count = 0;
	for ($n=0; $n<$strlen; $n++) {
		$char = substr($str, $n, 1);
		if (ord($char) > 128) {
			$char = substr($str, $n, 2);
			$n++;
		}
		if (in_array($char, $num)) {
			$num_count++;
		}
	}

	return $num_count >= $num_need ? 1 : 0;
}

// ��ȫ������ת��Ϊ���:
function num_to_lower($str) {
	$num = explode(" ", "�� �� �� �� �� �� �� �� �� ��");
	foreach ($num as $k => $v) {
		$str = str_replace($v, $k, $str);
	}
	return $str;
}
?>