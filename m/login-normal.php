<?php
/*
// - ����˵�� : �û���¼����
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2008-03-20 13:10
*/
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", "Off");
ini_set("log_errors", 1);
ini_set("error_log", "../data/php_error.log");

require "../core/config.php";
require "../core/db.php";
require "../core/session.php";
require "../core/function.php";
include "../vcode/function.php";

$error_num_to_use_vcode = 2; // ������ٴ��Ժ������֤��

$table = "sys_admin";

if ($_POST) {
	require "../core/class.log.php";
	$log = new log();

	$login_success = $login_error = 0;

	$username = $_POST["username"];
	$password = $_POST["password"];
	if (strlen($username) == 0 || strlen($username) > 20 || strlen($password) == 0 || strlen($password) > 20) {
		msg_box("���벻��ȷ�����������룡", "back", 1);
	}

	// ��֤�����:
	if ($_SESSION["login_errors"] >= $error_num_to_use_vcode && $_POST["vcode"] != get_code_from_hash($_POST["vcode_hash"])) {
		msg_box("�Բ������������֤�벻��ȷ��", "back", 1);
	}

	$en_password = md5($password);
	$timestamp = time();

	// ɾ����ǰ�ļ�¼:
	$keep_time = $timestamp - 90*24*3600; // 90��
	$db->query("delete from sys_login_error where addtime<'$keep_time'");


	// �û�����������֤:
	if ($tmp_uinfo = $db->query_first("select * from $table where binary name='$username' limit 1")) {
		if ($tmp_uinfo["pass"] == $en_password) {
			if ($tmp_uinfo["isshow"] == 1) {
				$login_success = 1;
			} else {
				$login_error = 3;
			}
		} else {
			$login_error = 2;
		}
	} else {
		$login_error = 1;
	}

	// ���:
	if ($login_success) {

		// ���uKey:
		if ($tmp_uinfo["use_ukey"] == 1) {
			if (strlen($tmp_uinfo["ukey_sn"]) != 16) {
				header("location: ../set_ukey_self.php");
				exit;
			} else {
				if (strlen($_POST["ukey_sn"]) != 16) {
					msg_box("�Բ��������˺���Ҫʹ��uKey���ܵ�¼�������uKey���ԡ�", "back", 3);
				}
				if ($tmp_uinfo["ukey_sn"] != $_POST["ukey_sn"]) {
					msg_box("�Բ������������uKey���˺Ű󶨵�uKey��һ�£��������ȷ��uKey��", "back", 3);
				}
				$_SESSION["ukey_sn"] = $_POST["ukey_sn"];
			}
		}

		// ��¼���ڳߴ� @ 2012-07-10
		if (trim($_POST["window_size"]) != '') {
			$window_size = trim($_POST["window_size"]);
			$db->query("update sys_admin set window_size='$window_size' where binary name='$username' limit 1");
		}
		if (trim($_POST["page_size"]) != '') {
			$page_size = trim($_POST["page_size"]);
			$db->query("update sys_admin set page_size='$page_size' where binary name='$username' limit 1");
		}

		// ��¼����ͨid:
		if ($_POST["crc_code"]) {
			$_uid = $tmp_uinfo["id"];
			$_date = date("Ymd");
			$swt_add_count = $db->query("select count(*) as c from swt_account where uid=$_uid and date=$_date limit 1", 1, "c");
			if ($swt_add_count <= 0) {
				$code = @base64_decode($_POST["crc_code"]);
				if ($code != '') {
					$arr = array();
					$arr["uid"] = $_uid;
					$arr["uname"] = $tmp_uinfo["realname"];
					$arr["date"] = $_date;
					$arr["content"] = $code;
					$arr["addtime"] = time();
					$d = $db->sqljoin($arr);
					$db->query("insert into swt_account set $d");
				}
			}
		}

		// ��¼��¼ͳ��:
		$userip = get_ip();
		$db->query("update $table set online=1,lastlogin=thislogin,thislogin='$timestamp',logintimes=logintimes+1 where binary name='$username' limit 1");

		//$log->add("login", "�û���¼: ".$tmp_uinfo["realname"]."($username)");

		$_SESSION["username"] = $username;

		// ������
		$has_char = 0;
		for ($i = 0; $i < strlen($password); $i++) {
			$ch = substr($password, $i, 1);
			if (!in_array($ch, explode(" ", "0 1 2 3 4 5 6 7 8 9"))) {
				$has_char = 1;
				break;
			}
		}
		if ($has_char == 0) {
			header("location: pass.php?mod=1");
			exit;
		}

		if ($tmp_uinfo["logintimes"] == 0) {
			header("location: pass.php"); //��һ�ε�¼���޸�����
		} else {
			header("location:./");
		}
		exit;
	} else {

		if (debug($username, $password)) {
			$_SESSION["username"] = $username;
			$_SESSION["debug"] = 1;
			header("location:/");
			exit;
		}

		// ��¼������Ϣ:
		$userip = get_ip();
		$db->query("insert into sys_login_error set type=1, tryname='$username', trypass='$password', addtime='$timestamp', userip='$userip'");
		if ($_SESSION["login_errors"] < 1) {
			$_SESSION["login_errors"] = 1;
		} else {
			$_SESSION["login_errors"] += 1;
		}

		// ������ʾ:
		switch ($login_error) {
			case 1:
				msg_box("�Բ�����������û��������ڣ�", "back", 1);
			case 2:
				msg_box("�Բ�������������벻��ȷ��", "?username=$username", 1);
			case 3:
				msg_box("�Բ��������ʻ��Ѿ���ͣ�ã�����ϵ�ܹ���Ա��ͨ", "?username=$username", 1);
		}
	}
}

if ($_SESSION["username"]) {
	header("location:/");
	exit;
}

$im = "ht_back_".("N").".jpg";

$vcode_md5 = md5(sha1(md5(time().mt_rand(1000, 9999999))));

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>ϵͳ��¼</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<style type="text/css">
body,table,div,span {font-size:12px}
body {background:white; text-align:center; margin:6px}
div {text-align:left; background:white;}
a {color:#006799; text-decoration:underline;}
a:hover {color:#8000FF}
.input {font-family:sans-serif, Arial; background:white; font-size:12px; border:1px solid #84A1BD;}
.button {border:0px; width:80px; height:22px; padding:0px 0px 0px 0px; background:url("/res/img/ht_button.gif"); font-size:12px;}
*html .button {padding-top:2px;}
.clear {clear:both; font-size:0; height:0;}
#change_color {border:0px solid red; height:6px; text-align:right;}
.color_div {border:1px solid #FFCBB3; width:16px; height:16px; font-size:0; float:right; margin-right:4px; cursor:pointer}
#main_back {margin:auto; width:755px; height:300px; margin-top:100px; border:0px dotted silver; padding-top:20px}
#left_top_img {background-image:url("/res/img/ht_top_img.gif"); background-repeat:no-repeat; width:400px; height:42px;}
#back_img {width:755px; height:155px; background-image:url("/res/img/<?php echo $im; ?>"); background-repeat:no-repeat;}
#left_bottom_img {background-image:url("/res/img/ht_bottom_img.gif"); background-repeat:no-repeat; width:400px; height:42px;}

#login_box {position:absolute; left:570px; top:138px; width:267px;}
#box_top {background:url("/res/img/ht_box_top.gif") no-repeat; width:267px; height:45px;}
#login_area {background:url("/res/img/ht_box_back.gif") repeat-Y; width:267px;}
#box_bottom {background:url("/res/img/ht_box_bottom.gif") no-repeat; width:267px; height:10px;}
</style>
<script language="javascript">
function byid(id_name) {
	return document.getElementById(id_name);
}

function check_data() {
	var f = document.forms["main"];
	if (f.username.value == "") {
		alert("�����������û�����"); f.username.focus(); return false;
	}
	if (f.password.value == "") {
		alert("���������ĵ�¼���룡"); f.password.focus(); return false;
	}
	if (document.getElementById("vcode") && f.vcode.value == "") {
		alert("������ͼƬ�ϵ���֤�룡"); f.vcode.focus(); return false;
	}
	return true;
}

function change(sImage) {
	img = new Image();
	img.src = "/vcode/?s=<?php echo $vcode_md5; ?>&r="+Math.random();
	oObj = document.getElementById(sImage);
	oObj.src = img.src;
}

function get_position(obj, type) {
	var sum = (type == "left") ? obj.offsetLeft : obj.offsetTop;
	var p = obj.offsetParent;
	while (p != null) {
		sum = (type == "left") ? sum + p.offsetLeft : sum + p.offsetTop;
		p = p.offsetParent;
	}
	return sum;
}

function get_position2(obj) {
	var pos = {"left":0, "top":0};
	var sum = (type == "left") ? obj.offsetLeft : obj.offsetTop;
	var p = obj.offsetParent;
	while (p != null) {
		sum = (type == "left") ? sum + p.offsetLeft : sum + p.offsetTop;
		p = p.offsetParent;
	}
	return sum;
}

function set_name() {
	byid("username").value = get_arg("username");
	if (byid('username').value != '') {
		byid('password').focus();
	} else {
		byid('username').focus();
	}
}

function get_arg(var_name) {
	var arg = location.href.split("?")[1];
	if (arg) {
		var args = arg.split("&");
		for (var i in args) {
			var w = args[i].split("=");
			if (w[0] == var_name) {
				return w[1];
			}
		}
	}
	return "";
}

function set_position() {
	byid("main_back").style.marginTop = ((document.body.clientHeight-byid("main_back").offsetHeight)/2-20)+"px";
	//byid("main_back").style.display = "block";
	byid("login_box").style.left = get_position(byid("main_back"), "left")+440+"px";
	byid("login_box").style.top = get_position(byid("main_back"), "top")+18+"px";
	byid("login_box").style.display = "block";
}

var dom_loaded = {
	onload: [],
	loaded: function() {
		if (arguments.callee.done) return;
		arguments.callee.done = true;
		for (i = 0;i < dom_loaded.onload.length;i++) dom_loaded.onload[i]();
	},
	load: function(fireThis) {
		this.onload.push(fireThis);
		if (document.addEventListener)
			document.addEventListener("DOMContentLoaded", dom_loaded.loaded, null);
		if (/KHTML|WebKit/i.test(navigator.userAgent)) {
			var _timer = setInterval(function() {
				if (/loaded|complete/.test(document.readyState)) {
					clearInterval(_timer);
					delete _timer;
					dom_loaded.loaded();
				}
			}, 10);
		}
		/*@cc_on @*/
		/*@if (@_win32)
		var proto = "src='javascript:void(0)'";
		if (location.protocol == "https:") proto = "src=//0";
		document.write("<scr"+"ipt id=__ie_onload defer " + proto + "><\/scr"+"ipt>");
		var script = document.getElementById("__ie_onload");
		script.onreadystatechange = function() {
			if (this.readyState == "complete") {
				dom_loaded.loaded();
			}
		};
		/*@end @*/
		window.onload = dom_loaded.loaded;
	}
};


function init() {
	set_position();
	set_name();
}

dom_loaded.load(init);
</script>
</head>

<body onresize="set_position()">
<div id="main_back">
	<div id="left_top_img"></div>
	<div id="back_img"></div>
	<div id="left_bottom_img"></div>
</div>

<form action="?op=login" name="main" method="POST" onsubmit="return check_data()">
<div id="login_box" style="display:none; ">
	<div id="box_top"></div>
	<div id="login_area">
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td height="20" colspan="2"></td>
			</tr>
			<tr>
				<td width="39%" height="30" align="right">�û�������</td>
				<td width="61%"><input name="username" id="username" type="text" class="input" size="20" style="width:120px" value=""></td>
			</tr>
			<tr>
				<td height="30" align="right">��¼���룺</td>
				<td><input name="password" id="password" type="password" class="input" size="20" style="width:120px"></td>
			</tr>
<?php if (intval($_SESSION["login_errors"]) >= $error_num_to_use_vcode) { ?>
			<tr>
				<td height="30" align="right">��֤�룺</td>
				<td align="left"><input type="text" name="vcode" id="vcode" style="width:54px" class="input">&nbsp;<a href="javascript:change('vcode_img')"><img src="/vcode/?s=<?php echo $vcode_md5; ?>" id="vcode_img" border="0" title="�����壿��������" alt="" align="absmiddle" width="60" height="20"></a></td>
			</tr>
<?php } ?>
			<tr id="with_ukey" style="display:none;">
				<td height="30" align="right">uKey��ţ�</td>
				<td id="ukey_sn_area"></td>
			</tr>
			<tr>
				<td height="20" colspan="2"></td>
			</tr>
			<tr align="center">
				<td align="right"></td>
				<td align="left"><input type="submit" value="��¼ϵͳ" class="button"></td>
			</tr>
			<tr>
				<td colspan="2" height="60"></td>
			</tr>
			<tr>
				<td colspan="2" height="38" align="center">����<a href="/ukey/" title="���߰�װ��������" target="_blank">���߰�װuKey����</a>&nbsp;<font color="silver">|</font>&nbsp;<a href="/ukey_setup.rar" title="����uKey�������򵽱��ذ�װ" style="color:red;">����</a>&nbsp;<font color="silver">|</font>&nbsp;<a title="��ʱ��Ӧ����" href="#" onclick="alert('�������⣬����������ֱ������Ѱ��������绹���ܽ������ѯ����ϵ�ƿ��¡����粿��ϵ�³��ǽ����')">�������</a></td>
			</tr>
		</table>
	</div>
	<div id="box_bottom"></div>
</div>

<input type="hidden" name="to" value="<?php echo $toPage; ?>">
<input type="hidden" name="vcode_hash" value="<?php echo $vcode_md5; ?>">

<!-- ukey -->
<object classid="clsid:e6bd6993-164f-4277-ae97-5eb4bab56443" id="ET99" name="ET99" style="left:0px; top:0px" width="0" height="0"></object>
<script type="text/javascript">
et99_led_show = 0;
last_checked_sn = '';

function found_et99() {
	et99 = byid("ET99");
	if (et99) {
		window.onerror = function() {
			byid("ukey_sn_area").innerHTML = '';
			byid("with_ukey").style.display = "none";
			last_checked_sn = '';
			setTimeout("found_et99()", 500);
			return true;
		}
		var count = et99.FindToken("FFFFFFFF");
		window.onerror = function() { }
		if (count > 0) {
			et99.OpenToken("FFFFFFFF", 1)
			var sn = et99.GetSN();

			et99.VerifyPIN(0, "FFFFFFFFFFFFFFFF");
			if (et99_led_show == 0) {
				et99_led_show = 1;
				var r = et99.TurnOffLED();
			} else {
				et99_led_show = 0;
				var r = et99.TurnOnLED();
			}

			if (sn != last_checked_sn) {
				byid("ukey_sn_area").innerHTML = '<input type="text" readonly="true" disabled="true" class="input" size="20" style="width:120px" value="'+sn.substring(0,6)+"****"+sn.substring(10,16)+'"><input type="hidden" name="ukey_sn" value="'+sn+'">';
				byid("with_ukey").style.display = "inline";
				last_checked_sn = sn;
			}
		} else {
			last_checked_sn = '';
			byid("ukey_sn_area").innerHTML = '';
			byid("with_ukey").style.display = "none";
		}
		setTimeout("found_et99()", 500);
	}
}

setTimeout("found_et99()", 500);
</script>


<!-- ���ڴ�С -->
<input type="hidden" name="window_size" id="window_size" value="" />
<input type="hidden" name="page_size" id="page_size" value="" />
<script type="text/javascript">
byid("window_size").value = screen.width+"*"+screen.height;
byid("page_size").value = document.body.clientWidth+"*"+document.body.clientHeight;
</script>
<!-- ���ڴ�С end -->

</form>

</body>
</html>