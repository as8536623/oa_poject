<?php
/* --------------------------------------------------------
// ���� @ 2012-07-09
// ----------------------------------------------------- */
set_time_limit(0);
include "../core/core.php";

// ���Ѻ�ҽԺ [���Ǻ�ơ�Ƥ���ơ����ơ�����ơ��п�]  �ϲ�Ϊһ����

$hebing_mubiao = 140; //�ϲ�������

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
// ��ȡ�ñ������ֶΣ�
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
	exit("���������⣬�����ȡ�ֶ��Ƿ�������");
}

$f_str = implode(",", $fields);
echo $f_str."<br><br>";


// step1: �ϲ�����:
foreach ($to_hebing as $h) {
	$table = "patient_".$h;
	$sql = "insert into $to_table ($f_str) select $f_str from $table ";
	//echo $sql."<br>";

	$db->query($sql);
	echo $table." �����ѳɹ�ת�Ƶ���".$to_table."<br>";
}

echo "<br><br>";

// step2: ������ԱȨ�ޣ�
foreach ($to_hebing as $h) {
	$users = $db->query("select * from sys_admin where concat(',', hospitals, ',') like '%,{$h},%'", "id");
	foreach ($users as $uid => $u) {
		$hs = explode(",", $u["hospitals"]);
		// ɾȥ��ҽԺ����Ȩ:
		foreach ($hs as $k => $v) {
			if ($v == $h) {
				unset($hs[$k]);
			}
		}
		// ���Ŀ��ҽԺ����Ȩ�����û�еĻ�:
		if (!in_array($hebing_mubiao, $hs)) {
			$hs[] = $hebing_mubiao;
		}
		$new_hs = implode(",", $hs);
		$db->query("update sys_admin set hospitals='$new_hs' where id=$uid limit 1");
		echo $u["realname"].": ".$new_hs."<br>";
	}
	echo "<br>";
}

echo "�û��Ѿ�������ˡ�<br>";

// step3: ������
foreach ($to_hebing as $h) {
	$db->query("update disease set hospital_id=$hebing_mubiao where hospital_id=$h");
	echo $h." ����ת�� ".$hebing_mubiao."<br>";
}


echo "<br><br>";

echo "����������Ҫ�������飺<br>";
echo "1. ȷ�����ݵ��������ɾ�������ŵı�: ".implode(", ", $to_hebing)." <br>";
echo "2. ɾ��ҽԺID: ".implode(", ", $to_hebing)."  �û�Ȩ���Ѿ�������ˣ�����ֱ��ɾ��<br>";
echo "3. �޸�Ŀ��ҽԺ���� ��Ҫ�ÿ����ˡ�<br>";




?>