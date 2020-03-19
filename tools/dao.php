<?php
/* --------------------------------------------------------
// 说明: 导 mssql 数据
// 作者: 幽兰 (weelia@126.com)
// 时间: 2009-05-11 13:16
// ----------------------------------------------------- */
set_time_limit(0);
ini_set("mssql.datetimeconvert", "0");


$mssql_db = "xijiao";     // 旧的数据所在数据库名
$hospital_id = 16;           // 医院id必须填写且重要!!!


include "../lib/mysql.php";
$db = new mysql();

$link = mssql_connect("127.0.0.1", "sa", "123456");
mssql_select_db($mssql_db, $link);

//echo '<script language="javascript"> var t = setInterval("scroll(0,999999)", 50); window.onload = function() {scroll(0,999999); clearInterval(t); } </script>';

// step 1 : 处理咨询类型:
echo '开始处理咨询类型数据...<br>';
flush();
//$db->query("delete from disease where hospital_id='$hospital_id' and author='$mssql_db'"); //清除可能是上一次失败的数据
$rs = mssql_query("select distinct 咨询类型 from 用户表", $link);
$dis_id_name = array();
while ($row = mssql_fetch_assoc($rs)) {
	$name = trim($row["咨询类型"]);
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


// step 2 : 处理接待医生数据:
echo '开始处理接待医生数据...<br>';
flush();
$db->query("delete from doctor where hospital_id='$hospital_id' and author='$mssql_db'"); //清除可能是上一次失败的数据
/*
$rs = mssql_query("select distinct 接待医生 from 用户表", $link);
$doctor_id_name = array();
while ($row = mssql_fetch_assoc($rs)) {
	$name = trim($row["接待医生"]);
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


// step 3 : 处理核心列表数据:
echo '开始处理列表数据...<br>';
flush();

$table = 'patient_'.$hospital_id;
$db->query("CREATE TABLE IF NOT EXISTS `{$table}` (
  `id` int(10) NOT NULL auto_increment,
  `part_id` int(10) NOT NULL default '0',
  `name` varchar(20) NOT NULL,
  `sex` varchar(6) NOT NULL COMMENT '性别',
  `age` int(3) NOT NULL default '0',
  `disease_id` int(10) NOT NULL default '0' COMMENT '病患类型',
  `tel` varchar(20) NOT NULL,
  `zhuanjia_num` varchar(10) NOT NULL,
  `content` mediumtext NOT NULL,
  `jiedai` varchar(20) NOT NULL,
  `order_date` int(10) NOT NULL default '0',
  `order_date_changes` int(4) NOT NULL default '0' COMMENT '预约时间修改次数',
  `order_date_log` text NOT NULL,
  `media_from` varchar(20) NOT NULL,
  `memo` mediumtext NOT NULL,
  `status` int(2) NOT NULL default '0',
  `come_date` int(10) NOT NULL default '0',
  `doctor` varchar(32) NOT NULL COMMENT '接待医生',
  `xiaofei` int(2) NOT NULL default '0' COMMENT '是否消费',
  `huifang` mediumtext NOT NULL COMMENT '回访记录',
  `addtime` int(10) NOT NULL default '0',
  `author` varchar(32) NOT NULL,
  `edit_log` mediumtext NOT NULL COMMENT '非个人修改的日志记录',
  PRIMARY KEY  (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=gbk;"
);

$db->query("delete from $table where author='$mssql_db'"); // 清除上一次的错误数据
$count = $count_ok = 0;
$rs = mssql_query("select * from 用户表", $link);
while ($row = mssql_fetch_assoc($rs)) {
	$r = array();
	//$r["hospital_id"] = $hospital_id;
	$r["part_id"] = trim($row["所属部门"]) == "网络客服" ? 2 : (trim($row["所属部门"]) == "电话客服" ? 3 : 4);
	$r["name"] = trim($row["姓名"]);
	$r["sex"] = trim($row["性别"]);
	$r["age"] = trim($row["年龄"]);
	$r["disease_id"] = $dis_name_id[trim($row["咨询类型"])];
	$r["tel"] = num_trans(trim($row["电话"]));
	$r["zhuanjia_num"] = '';
	$r["content"] = trim($row["咨询内容"]);
	$r["jiedai"] = '';
	$r["order_date"] = strtotime($row["预约时间"]);
	$r["media_from"] = trim($row["媒体来源"]);
	$r["memo"] = trim($row["备注"]);
	$r["status"] = trim($row["是否就诊"]) == 1 ? 1 : ($r["order_date"] > time() ? 0 : 2);
	$r["come_date"] = 0;
	$r["doctor"] = '';
	$r["huifang"] = trim($row["电话回访情况"]);
	$r["addtime"] = strtotime($row["日期"]);
	$r["author"] = trim($row["接待医生"]);

	$sqldata = $db->sqljoin($r);
	if ($db->query("insert into $table set $sqldata")) {
		$count_ok++;
	}
	$count++;
	if ($count % 1000 == 0) {
		echo "已完成 ".$count." ...<br>";
		flush();
	}
}
echo "列表处理完成 总共:".$count.", 成功: ".$count_ok.". <br>";
flush();

mssql_free_result($rs);
mssql_close($link);

echo "全部结束！<br>";


function num_trans($str) {
	$big = explode(' ', '０ １ ２ ３ ４ ５ ６ ７ ８ ９');
	if ($str == '') return '';
	foreach ($big as $k => $num) {
		$str = str_replace($num, $k, $str);
	}
	return $str;
}

/*
SQL Server 表的数据参考:

Array
(
    [ID] => 561
    [日期] => 2007-06-13 13:17:21
    [姓名] => 宣向华
    [性别] => 女
    [年龄] => 40
    [咨询内容] => 阴道紧缩术
    [媒体来源] => 杂志
    [电话] => 15821726964
    [接待医生] => 王玮薇
    [剩余时间] => -1190
    [预约时间] => 2005-06-13 00:00:00
    [备注] =>
    [电话回访情况] =>
    [是否就诊] => 0
    [咨询类型] => 妇科
    [所属部门] => 客服中心
)
*/
?>