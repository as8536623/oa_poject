<?php
/*
// ˵��: �ύ���߹Һ����� - POST
// ����: ���� (weelia@126.com)
// ʱ��: 2014-03-18
*/
include "../core/db.php";
include "../core/function.php";
include "../core/class.fastjson.php";
include "check_crc.php";

include "log_function.php";
_wee_log("add_patient.php", "POST", "");

$hospital_id = intval($_POST["hospital_id"]);
if ($hospital_id <= 0) {
	exit("-1:����hospital_id����");
}
$hinfo = $db->query("select id,name from hospital where id=$hospital_id limit 1", 1);
if (!is_array($hinfo) || $hinfo["id"] != $hospital_id) {
	exit("-1:ҽԺid=[".$hospital_id."]������");
}
$hname = $hinfo["name"];

$r = array();
$r["name"] = safe_char($_POST["name"]);
$r["sex"] = $_POST["sex"] == "Ů" ? "Ů" : "��";
$r["age"] = intval($_POST["age"]);
$r["tel"] = check_tel($_POST["tel"]);
$r["qq"] = safe_char($_POST["qq"]);
$r["content"] = safe_char($_POST["content"]);
$r["disease_id"] = intval($_POST["disease_id"]);
$r["media_from"] = safe_char($_POST["media_from"]);
$r["zhuanjia_num"] = safe_char($_POST["zhuanjia_num"]);
$r["is_local"] = intval($_POST["is_local"]) > 0 ? 1 : 0;
$r["area"] = safe_char($_POST["area"]);
$r["order_date"] = @strtotime($_POST["order_date"]);
$r["memo"] = safe_char($_POST["memo"]);

/*
$r["from_account"] = $_POST["from_account"];
$r["depart"] = $_POST["depart"];
$r["from_soft"] = $_POST["from_soft"];
*/

$r["tel_location"] = get_mobile_location($r["tel"]);
$r["status"] = 0;
$r["part_id"] = 2;
$r["addtime"] = time();
$r["author"] = "����";


// ��Ч�Լ��
if ($r["name"] == '') {
	exit("-1:��������Ϊ��");
}
if ($r["tel"] == '') {
	exit("-1:�ֻ��Ų���Ϊ��");
}
if ($r["disease_id"] <= 0) {
	exit("-1:�������Ͳ���Ϊ��");
}
if ($r["media_from"] == '') {
	exit("-1:ý����Դ����Ϊ��");
}
if ($r["order_date"] <= 0) {
	exit("-1:ԤԼʱ���ʽ�����δ��д");
}


$table = "patient_".$hospital_id;

// ��ֹ�ظ��ύ�� ����+����������������ظ� (���δ�ύ�ֻ��žͲ����)
if ($r["tel"] != '') {
	$repeat = $db->query("select count(*) as c from $table where name='".$r["name"]."' and tel='".$r["tel"]."'", 1, "c");
	if ($repeat > 0) {
		exit("-1:�����ظ����޷��ύ");
	}
}

// �ύ����:
$sqldata = $db->sqljoin($r);
$ins_id = $db->query("insert into $table set $sqldata");
if ($ins_id > 0) {
	exit($ins_id.":�ύ�ɹ�");
} else {
	exit($ins_id.":�ύ�����д������Ժ�����");
}

// end.



// ------------------------------------- �������� ----------------------------------------
function safe_char($s) {
	$s = strip_tags($s);
	$s = str_replace("\\", "", $s);
	$s = str_replace("/", "", $s);
	$s = str_replace("%", "", $s);
	$s = str_replace("*", "", $s);
	$s = str_replace("#", "", $s);
	$s = str_replace("&", "", $s);
	$s = str_replace("|", "", $s);
	$s = str_replace("'", "", $s);
	$s = str_replace('"', "", $s);
	$s = str_replace("_", "", $s);
	$s = str_replace(";", "", $s);
	$s = str_replace("$", "", $s);
	$s = str_replace("=", "", $s);
	$s = str_replace("union", "", $s);
	$s = str_replace("like", "", $s);
	$s = str_replace("and", "", $s);
	$s = str_replace("or", "", $s);
	$s = str_replace("<", "", $s);
	$s = str_replace(">", "", $s);
	$s = str_replace("{", "", $s);
	$s = str_replace("}", "", $s);
	$s = str_replace("(", "", $s);
	$s = str_replace(")", "", $s);
	$s = str_replace(",", " ", $s);

	return $s;
}

function check_tel($s) {
	$s = trim($s);
	$s = safe_char($s);
	if ($s == '') {
		return '';
	}
	if (substr($s, 0, 1) == "1" && strlen($s) == 11) {
		return $s;
	}
	exit("-1:�ֻ��Ÿ�ʽ����ȷ");
}


?>