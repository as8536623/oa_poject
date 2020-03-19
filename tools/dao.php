<?php
/* --------------------------------------------------------
// ˵��: �� mssql ����
// ����: ���� (weelia@126.com)
// ʱ��: 2009-05-11 13:16
// ----------------------------------------------------- */
set_time_limit(0);
ini_set("mssql.datetimeconvert", "0");


$mssql_db = "xijiao";     // �ɵ������������ݿ���
$hospital_id = 16;           // ҽԺid������д����Ҫ!!!


include "../lib/mysql.php";
$db = new mysql();

$link = mssql_connect("127.0.0.1", "sa", "123456");
mssql_select_db($mssql_db, $link);

//echo '<script language="javascript"> var t = setInterval("scroll(0,999999)", 50); window.onload = function() {scroll(0,999999); clearInterval(t); } </script>';

// step 1 : ������ѯ����:
echo '��ʼ������ѯ��������...<br>';
flush();
//$db->query("delete from disease where hospital_id='$hospital_id' and author='$mssql_db'"); //�����������һ��ʧ�ܵ�����
$rs = mssql_query("select distinct ��ѯ���� from �û���", $link);
$dis_id_name = array();
while ($row = mssql_fetch_assoc($rs)) {
	$name = trim($row["��ѯ����"]);
	if ($name != '') {
		$nid = $db->query("select id from disease where hospital_id='$hospital_id' and name='$name' limit 1", 1, "id");
		if (!$nid) {
			$nid = $db->query("insert into disease set hospital_id='$hospital_id', name='$name', addtime='".time()."', author='$mssql_db'");
		}
		$dis_name_id[$name] = $nid;
	}
}
echo "<pre>";
print_r($dis_name_id);
echo "</pre>";
flush();


// step 2 : ����Ӵ�ҽ������:
echo '��ʼ����Ӵ�ҽ������...<br>';
flush();
$db->query("delete from doctor where hospital_id='$hospital_id' and author='$mssql_db'"); //�����������һ��ʧ�ܵ�����
/*
$rs = mssql_query("select distinct �Ӵ�ҽ�� from �û���", $link);
$doctor_id_name = array();
while ($row = mssql_fetch_assoc($rs)) {
	$name = trim($row["�Ӵ�ҽ��"]);
	if ($name != '') {
		$nid = $db->query("insert into doctor set hospital_id='$hospital_id', name='$name', addtime='".time()."', author='$mssql_db'");
		$doctor_name_id[$name] = $nid;
	}
}
echo "<pre>";
print_r($doctor_name_id);
echo "</pre>";
flush();
*/


// step 3 : ��������б�����:
echo '��ʼ�����б�����...<br>';
flush();

$table = 'patient_'.$hospital_id;
$db->query("CREATE TABLE IF NOT EXISTS `{$table}` (
  `id` int(10) NOT NULL auto_increment,
  `part_id` int(10) NOT NULL default '0',
  `name` varchar(20) NOT NULL,
  `sex` varchar(6) NOT NULL COMMENT '�Ա�',
  `age` int(3) NOT NULL default '0',
  `disease_id` int(10) NOT NULL default '0' COMMENT '��������',
  `tel` varchar(20) NOT NULL,
  `zhuanjia_num` varchar(10) NOT NULL,
  `content` mediumtext NOT NULL,
  `jiedai` varchar(20) NOT NULL,
  `order_date` int(10) NOT NULL default '0',
  `order_date_changes` int(4) NOT NULL default '0' COMMENT 'ԤԼʱ���޸Ĵ���',
  `order_date_log` text NOT NULL,
  `media_from` varchar(20) NOT NULL,
  `memo` mediumtext NOT NULL,
  `status` int(2) NOT NULL default '0',
  `come_date` int(10) NOT NULL default '0',
  `doctor` varchar(32) NOT NULL COMMENT '�Ӵ�ҽ��',
  `xiaofei` int(2) NOT NULL default '0' COMMENT '�Ƿ�����',
  `huifang` mediumtext NOT NULL COMMENT '�طü�¼',
  `addtime` int(10) NOT NULL default '0',
  `author` varchar(32) NOT NULL,
  `edit_log` mediumtext NOT NULL COMMENT '�Ǹ����޸ĵ���־��¼',
  PRIMARY KEY  (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=gbk;"
);

$db->query("delete from $table where author='$mssql_db'"); // �����һ�εĴ�������
$count = $count_ok = 0;
$rs = mssql_query("select * from �û���", $link);
while ($row = mssql_fetch_assoc($rs)) {
	$r = array();
	//$r["hospital_id"] = $hospital_id;
	$r["part_id"] = trim($row["��������"]) == "����ͷ�" ? 2 : (trim($row["��������"]) == "�绰�ͷ�" ? 3 : 4);
	$r["name"] = trim($row["����"]);
	$r["sex"] = trim($row["�Ա�"]);
	$r["age"] = trim($row["����"]);
	$r["disease_id"] = $dis_name_id[trim($row["��ѯ����"])];
	$r["tel"] = num_trans(trim($row["�绰"]));
	$r["zhuanjia_num"] = '';
	$r["content"] = trim($row["��ѯ����"]);
	$r["jiedai"] = '';
	$r["order_date"] = strtotime($row["ԤԼʱ��"]);
	$r["media_from"] = trim($row["ý����Դ"]);
	$r["memo"] = trim($row["��ע"]);
	$r["status"] = trim($row["�Ƿ����"]) == 1 ? 1 : ($r["order_date"] > time() ? 0 : 2);
	$r["come_date"] = 0;
	$r["doctor"] = '';
	$r["huifang"] = trim($row["�绰�ط����"]);
	$r["addtime"] = strtotime($row["����"]);
	$r["author"] = trim($row["�Ӵ�ҽ��"]);

	$sqldata = $db->sqljoin($r);
	if ($db->query("insert into $table set $sqldata")) {
		$count_ok++;
	}
	$count++;
	if ($count % 1000 == 0) {
		echo "����� ".$count." ...<br>";
		flush();
	}
}
echo "�б������ �ܹ�:".$count.", �ɹ�: ".$count_ok.". <br>";
flush();

mssql_free_result($rs);
mssql_close($link);

echo "ȫ��������<br>";


function num_trans($str) {
	$big = explode(' ', '�� �� �� �� �� �� �� �� �� ��');
	if ($str == '') return '';
	foreach ($big as $k => $num) {
		$str = str_replace($num, $k, $str);
	}
	return $str;
}

/*
SQL Server ������ݲο�:

Array
(
    [ID] => 561
    [����] => 2007-06-13 13:17:21
    [����] => ����
    [�Ա�] => Ů
    [����] => 40
    [��ѯ����] => ����������
    [ý����Դ] => ��־
    [�绰] => 15821726964
    [�Ӵ�ҽ��] => ����ޱ
    [ʣ��ʱ��] => -1190
    [ԤԼʱ��] => 2005-06-13 00:00:00
    [��ע] =>
    [�绰�ط����] =>
    [�Ƿ����] => 0
    [��ѯ����] => ����
    [��������] => �ͷ�����
)
*/
?>