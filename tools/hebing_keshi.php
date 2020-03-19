<?php
/* --------------------------------------------------------
// 幽兰 @ 2012-07-09
// ----------------------------------------------------- */
set_time_limit(0);
include "../core/core.php";

// 武友好医院 [耳鼻喉科、皮肤科、妇科、精神科、男科]  合并为一个。

$hebing_mubiao = 140; //合并到这里

$to_hebing = array();
$to_hebing[] = 116;
$to_hebing[] = 122;
$to_hebing[] = 174;
$to_hebing[] = 196;

$db->query("update patient_140 set depart=101");
$db->query("update patient_116 set depart=102");
$db->query("update patient_122 set depart=103");
$db->query("update patient_174 set depart=104");
$db->query("update patient_196 set depart=105");

$to_table = "patient_".$hebing_mubiao;
// 获取该表所有字段：
$flist = mysql_query("show columns from ".$to_table);
$fields = array();
while ($li = mysql_fetch_array($flist)) {
	$fields[] = $li[0];
}
if ($fields[0] == "id") {
	unset($fields[0]);
} else {
	echo "<pre>";
	print_r($fields);
	exit("程序有问题，请检查获取字段是否正常！");
}

$f_str = implode(",", $fields);
echo $f_str."<br><br>";


// step1: 合并病人:
foreach ($to_hebing as $h) {
	$table = "patient_".$h;
	$sql = "insert into $to_table ($f_str) select $f_str from $table ";
	//echo $sql."<br>";

	$db->query($sql);
	echo $table." 数据已成功转移到：".$to_table."<br>";
}

echo "<br><br>";

// step2: 处理人员权限：
foreach ($to_hebing as $h) {
	$users = $db->query("select * from sys_admin where concat(',', hospitals, ',') like '%,{$h},%'", "id");
	foreach ($users as $uid => $u) {
		$hs = explode(",", $u["hospitals"]);
		// 删去该医院的授权:
		foreach ($hs as $k => $v) {
			if ($v == $h) {
				unset($hs[$k]);
			}
		}
		// 添加目标医院的授权，如果没有的话:
		if (!in_array($hebing_mubiao, $hs)) {
			$hs[] = $hebing_mubiao;
		}
		$new_hs = implode(",", $hs);
		$db->query("update sys_admin set hospitals='$new_hs' where id=$uid limit 1");
		echo $u["realname"].": ".$new_hs."<br>";
	}
	echo "<br>";
}

echo "用户已经处理好了。<br>";

// step3: 处理疾病
foreach ($to_hebing as $h) {
	$db->query("update disease set hospital_id=$hebing_mubiao where hospital_id=$h");
	echo $h." 疾病转到 ".$hebing_mubiao."<br>";
}


echo "<br><br>";

echo "接下来还需要做的事情：<br>";
echo "1. 确认数据导入无误后，删除后面编号的表: ".implode(", ", $to_hebing)." <br>";
echo "2. 删除医院ID: ".implode(", ", $to_hebing)."  用户权限已经处理好了，可以直接删除<br>";
echo "3. 修改目标医院名称 不要用科室了。<br>";




?>