<?php
/* --------------------------------------------------------
// 说明: 表数据转换 (临时任务)
// 作者: 幽兰 (weelia@126.com)
// 时间: 2009-05-11 19:46
// ----------------------------------------------------- */
set_time_limit(0);

include "../lib/mysql.php";
$db = new mysql();

$list = $db->query("select * from patient");
foreach ($list as $li) {
	$id = $li["id"];
	$hid = $li["hospital_id"];
	$table = create_patient_table($hid);
	unset($li["hospital_id"]);
	unset($li["id"]);

	$sqldata = $db->sqljoin($li);
	if ($db->query("insert into $table set $sqldata")) {
		$db->query("delete from patient where id=$id limit 1");
	}
}

echo "done...";


function create_patient_table($hospital_id) {
	global $db;

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

	return $table;
}


?>