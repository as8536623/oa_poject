<?php
/*
// - 功能说明 : 更新病种消费数据
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2012-08-25
*/
header("Content-Type:text/html;charset=GB2312");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
require "../../core/core.php";
require "../../core/class.fastjson.php";

$table = "disease_xiaofei";

$out = array();
$out["status"] = "bad";
$out["tips"] = '';

$data_name = $_GET["data_name"];
$data = $_GET["data"];

// 允许修改的字段：
$allow_edit = explode(" ", "touru_queding touru_buqueding mubiao_renjun mubiao_renshu memo");

if (substr_count($data_name, "@") == 3) {
	list($_hid, $_month, $_disease_id, $_edit_type) = explode("@", $data_name);

	$_hid = intval($_hid);
	$_month = intval($_month);
	$_disease_id = intval($_disease_id);

	// 有效性检查:
	if ($_hid > 0 && $_month > 0 && $_disease_id > 0 && in_array($_edit_type, $allow_edit)) {
		if (in_array($_edit_type, explode(" ", "touru_queding touru_buqueding mubiao_renjun mubiao_renshu"))) {
			$data = floatval($data);
		}

		//insert 还是 update
		$old_line = $db->query("select * from $table where hid=$_hid and month=$_month and disease_id=$_disease_id limit 1", 1);
		$old_id = @intval($old_line["id"]);

		if ($old_id > 0) {
			$db->query("update $table set $_edit_type='$data' where id=$old_id limit 1");
			$new_id = $old_id;
		} else {
			$h_name = $db->query("select name from hospital where id=$_hid limit 1", 1, "name");
			$dis_name = $db->query("select name from disease where id=$_disease_id limit 1", 1, "name");
			$new_id = $db->query("insert into $table set hid=$_hid, h_name='$h_name', month=$_month, disease_id=$_disease_id, disease_name='$dis_name', $_edit_type='$data', author='$realname'");
		}

		// 更新总投入:
		if ($_edit_type == "touru_queding" || $_edit_type == "touru_buqueding") {
			$db->query("update $table set touru_zong=0+touru_queding+touru_buqueding where id=$new_id limit 1");
		}

		// 记录修改日志:
		if ($old_id > 0 && $old_line[$_edit_type] != $data) {
			$logs = trim($old_line["logs"]."\r\n".date("Y-m-d H:i")." ".$realname." 将[".$_edit_type."] 由 [".$old_line[$_edit_type]."] 修改为 [".$data."]");
			$db->query("update $table set logs='$logs' where id=$new_id limit 1");
		}


		// 重新计算需更新的数据:
		$t_month_begin = strtotime(substr($_month, 0, 4)."-".substr($_month, 4, 2)."-01 0:0:0");
		$t_month_end = strtotime("+1 month", $t_month_begin) - 1;

		// 该病种预约人数和到诊人数
		$yuyue_count = $db->query("select count(id) as c from patient_{$_hid} where addtime>=$t_month_begin and addtime<=$t_month_end and disease_id=$_disease_id", 1, "c");
		$come_count = $db->query("select count(id) as c from patient_{$_hid} where status=1 and order_date>=$t_month_begin and order_date<=$t_month_end and disease_id=$_disease_id", 1, "c");

		// 消费数据:
		$line = $db->query("select * from $table where id=$new_id limit 1", 1);
		$out["to_update"][$_disease_id."@touru_zong"] = $line["touru_zong"];

		$out["to_update"][$_disease_id."@yuyue_renjun"] = '';
		if ($line["touru_zong"] > 0 && $yuyue_count > 0) {
			$out["to_update"][$_disease_id."@yuyue_renjun"] = round($line["touru_zong"] / $yuyue_count, 1);
		}

		$out["to_update"][$_disease_id."@daozhen_renjun"] = '';
		if ($line["touru_zong"] > 0 && $come_count > 0) {
			$out["to_update"][$_disease_id."@daozhen_renjun"] = round($line["touru_zong"] / $come_count, 1);
		}
	}

	$out["status"] = "ok";
	$out["update_id"] = $_disease_id."@".$_edit_type;
	$out["value"] = $data;
}

//print_r($out);

echo FastJSON::convert($out);
?>