<?php
/*
// 说明: 提交患者挂号数据 - POST
// 作者: 幽兰 (weelia@126.com)
// 时间: 2014-03-18
*/
include "../core/db.php";
include "../core/function.php";
include "../core/class.fastjson.php";
include "check_crc.php";

include "log_function.php";
_wee_log("add_patient.php", "POST", "");

$hospital_id = intval($_POST["hospital_id"]);
if ($hospital_id <= 0) {
	exit("-1:参数hospital_id错误");
}
$hinfo = $db->query("select id,name from hospital where id=$hospital_id limit 1", 1);
if (!is_array($hinfo) || $hinfo["id"] != $hospital_id) {
	exit("-1:医院id=[".$hospital_id."]不存在");
}
$hname = $hinfo["name"];

$r = array();
$r["name"] = safe_char($_POST["name"]);
$r["sex"] = $_POST["sex"] == "女" ? "女" : "男";
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
$r["author"] = "章翔";


// 有效性检查
if ($r["name"] == '') {
	exit("-1:姓名不能为空");
}
if ($r["tel"] == '') {
	exit("-1:手机号不能为空");
}
if ($r["disease_id"] <= 0) {
	exit("-1:疾病类型不能为空");
}
if ($r["media_from"] == '') {
	exit("-1:媒体来源不能为空");
}
if ($r["order_date"] <= 0) {
	exit("-1:预约时间格式错误或未填写");
}


$table = "patient_".$hospital_id;

// 防止重复提交： 姓名+号码组合起来不能重复 (如果未提交手机号就不检查)
if ($r["tel"] != '') {
	$repeat = $db->query("select count(*) as c from $table where name='".$r["name"]."' and tel='".$r["tel"]."'", 1, "c");
	if ($repeat > 0) {
		exit("-1:数据重复，无法提交");
	}
}

// 提交数据:
$sqldata = $db->sqljoin($r);
$ins_id = $db->query("insert into $table set $sqldata");
if ($ins_id > 0) {
	exit($ins_id.":提交成功");
} else {
	exit($ins_id.":提交过程有错误，请稍后重试");
}

// end.



// ------------------------------------- 函数部分 ----------------------------------------
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
	exit("-1:手机号格式不正确");
}


?>