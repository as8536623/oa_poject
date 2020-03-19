<?php
/* --------------------------------------------------------
// 说明: 挂号数据保存程序
// 作者: 幽兰 (weelia@126.com)
// 时间: 2009-05-21 20:52
// ----------------------------------------------------- */
session_start();

if (!$_POST || !$_REQUEST["hid"]) {
	// 没有传递post数据，或者没有传递医院，表示可能直接浏览器访问，直接返回404页面
	header("HTTP/1.0 404 Not Found");
	exit;
}

/*
	兼容参数说明: 要求姓名和电话是必填项目，且电话中至少包含7位数字
	"name" => "姓名"
	"sex" => 1:男, 0:女 或直接提交“男”“女”
	"phone|tel" => "电话"
	"email" => "电子邮件地址"
	"city" => "所在城市"
	"date_depart|dep" => "科室"
	"doctor" => "预约医生"

	"date_y" => "预约年"
	"date_m" => "预约月"
	"date_d" => "预约日"
	"date_h" => "预约小时"
	"date_i" => "预约分钟"
	"date_s" => "预约秒"

	"time2|order_date" => "直接指定预约日期" 格式需是 strtotime() 函数能解析的格式 如 "2008-12-25 14:30:00"
	"content|title" => "病情简述"
	"otherInfo|memo" => "其他备注"
*/

// 开始处理数据:
$r = array();

// 姓名
$r["name"] = trim(format_text($_POST["name"]));
if ($r["name"] == '') {
	exit_js("对不起，姓名必须填写！");
}

// 性别
if (in_array(trim($_POST["sex"]), array("1", "男"))) {
	$r["sex"] = "男";
} else if (in_array(trim($_POST["sex"]), array("0", "女"))) {
	$r["sex"] = "女";
}

// 电话
$r["tel"] = num_to_lower(trim(format_text(trim($_POST["tel"]) ? $_POST["tel"] : $_POST["phone"])));
if ($r["tel"] == '' || !num_check($r["tel"], 7)) {
	exit_js("对不起，您没有输入联系电话，或者电话格式不正确 (至少要有7位数字)！");
}

$r["email"] = trim(format_text($_POST["email"]));
$r["city"] = trim($_POST["city"]);

// 预约时间
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

	//无法解析时间，将其串保存于备注中以作检查
	if (!(strtotime($time_str) > 0)) {
		$_POST["memo"] = $time_str_ori." ".$_POST["memo"];
	}
}
$r["order_date"] = @strtotime($time_str);

$r["depart"] = trim($_POST["date_depart"] ? $_POST["date_depart"] : $_POST["dep"]);
$r["content"] = trim($_POST["content"] ? $_POST["content"] : $_POST["title"]);
$r["doctor"] = trim($_POST["doctor"]);
$r["memo"] = trim($_POST["memo"] ? $_POST["memo"] : $_POST["otherInfo"]);

include "ip/function.ip.php"; //IP函数库
$r["ip"] = _get_ip();
$r["ip_address"] = ip_area($r["ip"]);

// 程序调试及恶意攻击备查数据
// GET:
$get_str = '';
foreach ($_GET as $k => $v) {
	$get_str .= $k." => ".$v."<br>";
}
$r["getdata"] = $get_str;

// 保存POST数组中的数据待查:
$post_str = '';
foreach ($_POST as $k => $v) {
	$post_str .= $k." => ".$v."<br>";
}
$r["postdata"] = $post_str;

// 保存SERVER环境变量待查:
$server_str = '';
foreach ($_SERVER as $k => $v) {
	$server_str .= $k." => ".$v."<br>";
}
$r["serverdata"] = $server_str;


// 医院id:
$hospital_id = intval($_REQUEST["hid"]);
$r["hospital_id"] = $hospital_id;

// 通过前向网址判断网站来源
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


// 关键词过滤 2010-03-31
$f_word = $db->query("select config from guahao_config where name='filter' limit 1", 1, "config");
if ($f_word != '') {
	$f_word_arr = explode(",", str_replace("，", ",", $f_word));
	$check_str = implode(" ", $r);
	foreach ($f_word_arr as $key) {
		if ($key = trim($key)) {
			if (substr_count($check_str, $key) > 0) {
				exit_js("提交成功。哈哈哈。");
			}
		}
	}
}
// ==== end.


// 先检查 hospital_id
$hospital_name = $db->query("select name from hospital where id=$hospital_id limit 1", 1, "name");
if (!$hospital_name) {
	exit_js("对不起，该家医院尚未注册本系统，数据暂无法提交！");
}

// 防止重复提交检查:
$addtime = $db->query("select addtime from guahao where hospital_id=$hospital_id and name='{$r[name]}' and ip='{$r[ip]}' order by addtime desc limit 1", 1, "addtime");
if ($addtime > 0 && time() - $addtime < 300) { // 5分钟
	exit_js("对不起，请不要重复提交！");
}

// 提交数据:
ob_start(); //即使出错，也不显示mysql错误
$sqldata = $db->sqljoin($r);
$result = $db->query("insert into guahao set $sqldata");
ob_end_clean();

if ($result) {
	exit_js("挂号数据已经提交成功！\\n\\n感谢您使用在线挂号系统，资料我们已经收到，我们会及时和您取得联系。谢谢。");
} else {
	exit_js("对不起，服务器繁忙，请稍候再试！");
}

// 处理完毕!
// ---------------------------------------------------------------------------------------




// ------------------------------- 以下为本页面需要使用的函数 -----------------------------

// 输出alert并停止执行代码
function exit_js($str) {
	echo '<script language="javascript">';
	if ($str) {
		echo 'alert("'.$str.'");';
	}
	echo 'history.back();';
	echo '</script>';
	exit;
}

// 去除文本中的html代码:
function format_text($string) {
	$search = array("'<script[^>]*?>.*?</script>'si", "'<[\/\!]*?[^<>]*?>'si", "'([\r\n])[\s]+'", "'&(quot|#34);'i",
		"'&(amp|#38);'i", "'&(lt|#60);'i", "'&(gt|#62);'i", "'&(nbsp|#160);'i", "'&(iexcl|#161);'i", "'&(cent|#162);'i",
		"'&(pound|#163);'i", "'&(copy|#169);'i", "'&#(\d+);'e");
	$replace = array ("", "", "\\1", "\"", "&", "<", ">", " ", chr(161), chr(162), chr(163), chr(169), "chr(\\1)");
	$string = preg_replace($search, $replace, $string);

	return rtrim($string);
}

// 检查指定串中数字数量
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

// 将全角数字转换为半角:
function num_to_lower($str) {
	$num = explode(" ", "０ １ ２ ３ ４ ５ ６ ７ ８ ９");
	foreach ($num as $k => $v) {
		$str = str_replace($v, $k, $str);
	}
	return $str;
}
?>