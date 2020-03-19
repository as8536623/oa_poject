<?php
/*
// - ����˵�� : get_online.php
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2009-05-26 11:53
*/
ob_start();
require "../core/core.php";
require "../core/class.fastjson.php";
ob_end_clean();

/*
@file_put_contents(ROOT."data/get_online_data.txt", ($uid."#".date("Y-m-d H:i:s")."\r\n"), FILE_APPEND);
if (@filesize(ROOT."data/get_online_data.txt") > 10*1024*1024) {
	@rename(ROOT."data/get_online_data.txt", ROOT."data/get_online_data_".date("Ymd_His").".txt");
}
*/

ob_start();
error_reporting(0);
set_time_limit(5);

$out = array();
$out["status"] = "bad";

$time = time();


// ���浱ǰ�û��������ߵļ�¼:
$db->query("update sys_admin set lastactiontime='$time',online=1 where name='$username' limit 1");

// �����û�����״̬:
$time_file = ROOT."data/get_online.txt";
$t = intval(@file_get_contents($time_file));
if ((time() - $t) > 60) {
	@file_put_contents($time_file, time());
	$db->query("update sys_admin set online=if($time-lastactiontime>150, '0', '1') where online=1");
	//$db->query("optimize table sys_admin");
}


// ��ȡ�����û�:
$aUser = array();
//$aUser[intval($uid)] = array("name"=>$username, "realname"=>$realname, "isowner"=>1);

$count = $db->query("select count(id) as c from sys_admin where online=1", 1, "c");
$aUser[intval($uid)] = array("name"=>$username, "realname"=>"�� ".$count." ������", "isowner"=>1);

/*
$sqlwhere = '';
if ($hid > 0) {
	$sqlwhere = "and concat(',',hospitals,',') like '%,{$hid},%'";
}

$data = $db->query("select id,name,realname,hospitals from sys_admin where name!='$username' and online='1' $sqlwhere order by thislogin desc limit 10");

foreach ($data as $line) {
	// 2009-05-09 16:55 �޸ģ���ȡͬһҽԺ�������û�
	$aUser[$line["id"]] = array("name"=>$line["name"], "realname"=>$line["realname"], "isowner"=>0);
}
*/

$out["online_list"] = $aUser;


// ��ȡ֪ͨ:
$cur_pid = intval($uinfo["part_id"]);
$cur_is_m = $uinfo["part_manage"] ? 1 : 0;
$time = time();

//$_begin = now();
$data = $db->query("select * from sys_notice where (reader_type='all' or (reader_type='part' and concat(',',part_ids,',') like '%,{$cur_pid},%') or (reader_type='user' and concat(',',uids,',') like '%,{$uid},%') or (reader_type='manager' and 1='$cur_is_m')) and isshow=1 and (begintime=0 or (begintime>0 and begintime<=$time)) and (endtime=0 or (endtime>0 and endtime>=$time)) and concat(',', read_uids, ',') not like '%,{$uid},%' order by addtime desc limit 4");
//$_tuse = round(now() - $_begin, 4);
//@file_put_contents("../data/notice.log", ($_tuse." ".$db->sql."\r\n"), FILE_APPEND);


foreach ($data as $line) {
	$messid = $line["id"];
	$aMessInfo[$messid] = array(
		"type"=>"notice",
		"url"=>"/m/sys/notice.php?id=".$messid,
		"title"=>cut(("֪ͨ: ".my_replace(text_show($line["title"]))), 22, ".."),
	);
}


// ��ȡ��Ϣ:
/*
$data = $db->query("select * from sys_message where to_uid='$uid' and readtime=0 order by addtime desc limit 8");

foreach ($data as $line) {
	$messid = $line["id"];
	$aMessInfo[$messid] = array(
		"type"=>"message",
		"url"=>"/m/sys/message.php?id=".$messid,
		"title"=>cut(($line["from_realname"].": ".my_replace(text_show($line["content"]))), 22, ".."),
	);
	//$db->query("update sys_message set readtime=$time where id=$messid limit 1");
}
*/
$out["online_notice"] = $aMessInfo;

$out["status"] = "ok";

ob_end_clean();


// --------- ��� ---------
echo 'var _online_data = '.FastJSON::convert($out)."\n";
echo 'get_online_do(_online_data);'."\n";

//echo '//'.$sql2;

function my_replace($s) {
	$s = str_replace("'", "", $s);
	$s = str_replace('"', "", $s);
	$s = str_replace("\r", "", $s);
	$s = str_replace("\n", " ", $s);
	return $s;
}
?>