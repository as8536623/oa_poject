<?php
require "../../core/core.php";
$table = "sys_admin";

if ($debug_mode || $username == "admin") {
	// yeah
} else {
	exit("�Բ�����û�в���Ȩ�޹�...");
}

if ($_GET["type"] == '') {
	$_GET["type"] = "character_id";
}

$part_arr = $db->query("select id,name from sys_part", "id", "name");
$character_arr = $db->query("select id,name from sys_character", "id", "name");

$worklog_arr = array(
	"zixun_view" => "��ѯ�鿴",
	"zixun_edit" => "��ѯ�޸�",
	"zhixing_view" => "ִ�в鿴",
	"zhixing_edit" => "ִ���޸�",
	"zhuguan_view" => "���ܲ鿴",
	"zhuguan_edit" => "�����޸�",
	"zhuren_view" => "���β鿴",
	"zhuren_edit" => "�����޸�",
);


if ($_POST) {
	$set_users = array();
	$error_count = 0;

	// ��¼��־:
	@file_put_contents("piliang.log", date("Y-m-d H:i:s")." ".$username." ".serialize($_POST)."\r\n\r\n", FILE_APPEND);

	$sql_where = '';
	if ($_POST["type"] == "part_id") {
		$part_id = intval($_POST["part_id"]);
		if ($part_id <= 0) {
			exit("����[part_id]����...");
		}
		$sql_where = "part_id='".$part_id."'";
	}
	if ($_POST["type"] == "character_id") {
		$character_id = intval($_POST["character_id"]);
		if ($character_id <= 0) {
			exit("����[character_id]����...");
		}
		$sql_where = "character_id='".$character_id."'";
	}

	if ($sql_where == '') {
		exit("��������ȷ...");
	}

	if ($_POST["op_type"] == '') {
		exit("����[op_type]����...");
	}

	$op_type = $_POST["op_type"] == "add" ? "add" : "remove";

	$list = $db->query("select * from sys_admin where $sql_where", "id");

	$to_set = $_POST["set"];
	foreach ($list as $id => $li) {
		$sql_update = array();

		// �޸�Ȩ��:
		if (intval($to_set["character_id"]) > 0) {
			$sql_update[] = "character_id='".intval($to_set["character_id"])."'";
		}

		// �޸ĹҺź�������:
		if (count($to_set["guahao_config"]) > 0) {
			$now_set = explode(",", $li["guahao_config"]);
			foreach ($to_set["guahao_config"] as $v) {
				if ($op_type == "add") {
					if (!in_array($v, $now_set)) {
						$now_set[] = $v;
					}
				} else {
					$_key = array_search($v, $now_set);
					if ($_key !== false) {
						unset($now_set[$_key]);
					}
				}
			}

			$sql_update[] = "guahao_config='".implode(",", $now_set)."'";
		}

		// �޸����ݲ鿴Ȩ��:
		if (count($to_set["data_power"]) > 0) {
			$now_set = explode(",", $li["data_power"]);
			foreach ($to_set["data_power"] as $v) {
				if ($op_type == "add") {
					if (!in_array($v, $now_set)) {
						$now_set[] = $v;
					}
				} else {
					$_key = array_search($v, $now_set);
					if ($_key !== false) {
						unset($now_set[$_key]);
					}
				}
			}

			$sql_update[] = "data_power='".implode(",", $now_set)."'";
		}

		// ������־
		if (count($to_set["worklog"]) > 0) {
			$now_set = explode(",", $li["worklog"]);
			foreach ($to_set["worklog"] as $v) {
				if ($op_type == "add") {
					if (!in_array($v, $now_set)) {
						$now_set[] = $v;
					}
				} else {
					$_key = array_search($v, $now_set);
					if ($_key !== false) {
						unset($now_set[$_key]);
					}
				}
			}

			$sql_update[] = "worklog='".implode(",", $now_set)."'";
		}

		// �绰Ȩ��:
		if ($to_set["show_tel"]) {
			if ($op_type == "add") {
				$sql_update[] = "show_tel=1";
			} else {
				$sql_update[] = "show_tel=0";
			}
		}

		// �����ֻ���:
		if ($to_set["allow_mobile_login"]) {
			if ($op_type == "add") {
				$sql_update[] = "allow_mobile_login=1";
			} else {
				$sql_update[] = "allow_mobile_login=0";
			}
		}

		// 2013-7-24
		if ($to_set["show_talk"]) {
			if ($op_type == "add") {
				$sql_update[] = "show_talk=1";
			} else {
				$sql_update[] = "show_talk=0";
			}
		}

		if (count($sql_update) > 0) {
			$to_update = implode(", ", $sql_update);
			ob_start();
			$db->query("update sys_admin set $to_update where id=$id limit 1");
			$error = ob_get_clean();

			$set_users[] = $li["realname"];

			if ($error != '') {
				$error_count++;
				echo $error."<br>";
			}
		} else {
			exit("��������û����Ҫ���µ�����...");
		}

	}

	echo "<br>������ <b>".count($list)."</b> ����Ա���˻���<br><br>";
	echo implode("��", $set_users)."<br><br>";
	echo '<a href="javascript:history.go(-1);">����</a>';
	exit;

}

?>
<html xmlns=http://www.w3.org/1999/xhtml>
<head>
<title>��������Ȩ��</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style type="text/css">
#type_select {height:25px; overflow:hidden; margin-top:10px; background:url("/res/img/tab_bg.jpg") repeat-x; }

.hs_tab_cur {margin-left:5px; float:left; }
.hs_tab_cur .hs_tab_left {float:left; width:3px; height:25px; background:url("/res/img/tab_cur_left.jpg") no-repeat; }
.hs_tab_cur .hs_tab_center {float:left; height:25px; background:url("/res/img/tab_cur_center.jpg") repeat-x; }
.hs_tab_cur .hs_tab_right {float:left; width:3px; height:25px; background:url("/res/img/tab_cur_right.jpg") no-repeat; }
.hs_tab_cur a {font-weight:bold; text-decoration:none; display:block; line-height:25px; padding:0 3px; color:red; }

.hs_tab_nor {margin-left:5px; float:left; }
.hs_tab_nor .hs_tab_left {float:left; width:3px; height:25px; background:url("/res/img/tab_nor_left.jpg") no-repeat; }
.hs_tab_nor .hs_tab_center {float:left; height:25px; background:url("/res/img/tab_nor_center.jpg") repeat-x; }
.hs_tab_nor .hs_tab_right {float:left; width:3px; height:25px; background:url("/res/img/tab_nor_right.jpg") no-repeat; }
.hs_tab_nor a {font-weight:normal; text-decoration:none; display:block; line-height:25px; padding:0 3px; }
</style>
</head>

<body>
<div id="type_select">
<?php
$type_arr = array(
	"part_id" => "�������š���������",
	"character_id" => "����Ȩ�ޡ���������",
);
foreach ($type_arr as $_x => $_y) {
	$tab_class = $_x == $_GET["type"] ? "hs_tab_cur" : "hs_tab_nor";
?>
	<div class="<?php echo $tab_class; ?>">
		<div class="hs_tab_left"></div>
		<div class="hs_tab_center"><a href="?type=<?php echo $_x; ?>" onfocus="this.blur()"><?php echo $_y; ?></a></div>
		<div class="hs_tab_right"></div>
		<div class="clear"></div>
	</div>
<?php
	}
?>
	<div class="clear"></div>
</div>

<form method="POST" action="" onsubmit="" style="margin-top:10px;">
<table width="100%" class="edit">
<?php if ($_GET["type"] == "part_id") { ?>
	<tr>
		<td class="left">Ҫ���õĲ��ţ�</td>
		<td class="right">
			<select name="part_id" class="combo">
				<option value="" style="color:gray">--��ѡ��--</option>
				<?php echo list_option($part_arr, "_key_", "_value_"); ?>
			</select>
		</td>
	</tr>
<?php } ?>

<?php if ($_GET["type"] == "character_id") { ?>
	<tr>
		<td class="left">Ҫ���õ�Ȩ�ޣ�</td>
		<td class="right">
			<select name="character_id" class="combo">
				<option value="" style="color:gray">--��ѡ��--</option>
				<?php echo list_option($character_arr, "_key_", "_value_"); ?>
			</select>
		</td>
	</tr>
<?php } ?>

</table>

<div class="space"></div>

<table width="100%" class="edit">
	<tr>
		<td class="left">������</td>
		<td class="right">
			<select name="op_type" class="combo">
				<option value="add">����</option>
				<option value="remove">��ȥ</option>
			</select>
		</td>
	</tr>

	<tr>
		<td class="left">�µ�Ȩ�ޣ�</td>
		<td class="right">
			<select name="set[character_id]" class="combo">
				<option value="" style="color:gray">--������ԭ������--</option>
				<?php echo list_option($character_arr, "_key_", "_value_"); ?>
			</select>
		</td>
	</tr>

	<tr>
		<td class="left">�Һź��ģ�</td>
		<td class="right">
<?php
$line_config = @explode(",", $line["guahao_config"]);
foreach ($guahao_config_arr as $k => $v) {
	$checked = in_array($k, $line_config) ? "checked" : "";
?>
			<span><input type="checkbox" name="set[guahao_config][]" value="<?php echo $k; ?>" <?php echo $checked; ?> id="chk_<?php echo $k; ?>"><label for="chk_<?php echo $k; ?>"<?php if ($checked) echo ' style="color:red"'; ?>><?php echo $v; ?></label></span>
<?php } ?>
		</td>
	</tr>

	<tr>
		<td class="left">���ݹ���</td>
		<td class="right">
<?php

// 2013-5-13
$index_module = $db->query("select name from index_module_set where isshow='1'");
foreach ($index_module as $_v) {
	$data_power_arr[$_v["name"]] = $_v["name"];
}

$cur_data_power = @explode(",", $line["data_power"]);
foreach ($data_power_arr as $k => $v) {
	$chk = @in_array($k, $cur_data_power) ? " checked" : "";
?>
			<input type="checkbox" name="set[data_power][]" value="<?php echo $k; ?>" id="dp_<?php echo $k; ?>" <?php echo $chk; ?>><label for="dp_<?php echo $k; ?>"><?php echo $v; ?></label> &nbsp;
<?php } ?>
		</td>
	</tr>

	<tr>
		<td class="left">��ʾ���룺</td>
		<td class="right"><input type="checkbox" name="set[show_tel]" value="1" id="chk0001"><label for="chk0001">��ʾ�������˵ĵ绰����</label></td>
	</tr>

	<tr>
		<td class="left">��¼�ֻ��棺</td>
		<td class="right">
			<input type="checkbox" name="set[allow_mobile_login]" id="allow_mobile_login"><label for="allow_mobile_login">�����¼�ֻ���</label>
		</td>
	</tr>

	<tr>
		<td class="left">�����¼��</td>
		<td class="right">
			<input type="checkbox" name="set[show_talk]" id="show_talk"><label for="show_talk">������ʾ�����¼</label>
		</td>
	</tr>

	<tr>
		<td class="left">������־��</td>
		<td class="right">
<?php
foreach ($worklog_arr as $k => $v) {
	$checked = '';
?>
			<span><input type="checkbox" name="set[worklog][]" value="<?php echo $k; ?>" <?php echo $checked; ?> id="chk_<?php echo $k; ?>"><label for="chk_<?php echo $k; ?>"<?php if ($checked) echo ' style="color:red"'; ?>><?php echo $v; ?></label></span>
<?php } ?>
		</td>
	</tr>
</table>

<div class="space"></div>

<div class="button_line">
	<input type="submit" class="submit" value="�ύ�޸�">
</div>

<input type="hidden" name="type" value="<?php echo $_GET["type"]; ?>">

</form>




</body>
</html>