<?php
/*
// ˵��: �����б�
// ����: ���� (weelia@126.com)
// ʱ��: 2010-09-07 11:01
*/
date_default_timezone_set('PRC'); 
if ($_GET["btime"]) {
	$_GET["begin_time"] = strtotime($_GET["btime"]." 0:0:0");
}
if ($_GET["etime"]) {
	$_GET["end_time"] = strtotime($_GET["etime"]." 23:59:59");
}

// ���嵱ǰҳ��Ҫ�õ��ĵ��ò���:
$link_param = explode(" ", "page sort order key searchtype begin_time end_time time_type show come kefu_23_name kefu_4_name doctor_name xiaofei disease part_id from depart names date list_huifang media callid hf_time index_module searchtype ttype btimes etimes");

$param = array();
foreach ($link_param as $s) {
	$param[$s] = $_GET[$s];
}
extract($param);

$sort_type_arr = array(
	"order_date" => array("��Ժ����ʱ��", "order_date {ascdesc} "),
	"addtime" => array("ԤԼ����ʱ��", "addtime {ascdesc}"),
	"djsj" => array("�Ǽ�ʱ��", "djsj {ascdesc}"),
	"name" => array("����", "name {ascdesc}"),
	"age" => array("����", "age {ascdesc}"),
	"vocation" => array("ְҵ", "vocation {ascdesc}"),
	"tel" => array("�绰", "tel {ascdesc}"),
	"qq" => array("QQ", "qq {ascdesc}"),
	"remain_time" => array("ʣ������", "remain_time {ascdesc}"),
	"disease_id" => array("��������", "disease_id {ascdesc}"),
	"media_from" => array("ý����Դ", "media_from {ascdesc}"),
	"engine_key" => array("�ؼ���", "engine_key {ascdesc}"),
	"author" => array("�Ǽ���", "author {ascdesc}"),
	"area" => array("����", "area {ascdesc}"),
);

$sort_type_name_arr = array();
foreach ($sort_type_arr as $_k => $_v) {
	$sort_type_name_arr[$_k] = $_v[0];
}

$sort_order_arr = array("asc" => "С����", "desc" => "��С");


// ���嵥Ԫ���ʽ:
$list_heads = array(
	"����" => array("width"=>"", "align"=>"center"),
	"�Ա�" => array("align"=>"center"),
	"����" => array("align"=>"center"),
	"�绰" => array("align"=>"center"),
	"ԤԼ��" => array("align"=>"center"),
	"�����" => array("align"=>"center"),
	"ҽ��" => array("align"=>"center"),
	"����ѯ����|��ע|�ط�" => array("align"=>"left", "width"=>"270"),
	"��Ժ����ʱ��" => array("width"=>"", "align"=>"center"),
	"����" => array("align"=>"center"),
	"��������" => array("align"=>"center","width"=>""),
	"ý��" => array("align"=>"center"),
	"�ؼ���" => array("align"=>"center"),
	"������ַ" => array("align"=>"center","width"=>"80"),
	//"����" => array("align"=>"center"),
	//"����" => array("align"=>"center"),
	"����" => array("align"=>"center"),
	//"��ע" => array("align"=>"center"),
	"�Ǽ���" => array("width"=>"", "align"=>"center"),
	"ά����" => array("width"=>"", "align"=>"center"),
	//"�ط�" => array("width"=>"24", "align"=>"center"),
	//"��Լ���" => array("align"=>"center"),
	"ԤԼ����ʱ��" => array("width"=>"", "align"=>"center"),
	"�Ǽ�ʱ��" => array("width"=>"", "align"=>"center"),
	"����" => array("width"=>"60", "align"=>"center"),
);

// Ĭ������ʽ:
if ($uinfo["part_id"] == 4) {
	$default_sort = "addtime"; // ��ҽ�ȽϹ�ע���쵽�Ĳ���
	$default_order = "desc";
} else {
	$default_sort = "addtime"; //�ͷ������Ա���ע�����������˶��ٲ���
	$default_order = "desc";
}

if ($show == 'today') {
	$begin_time = mktime(0, 0, 0);
	$end_time = mktime(23, 59, 59);
} else if ($show == 'yesterday') {
	$begin_time = mktime(0, 0, 0) - 24 * 3600;
	$end_time = mktime(0, 0, 0);
}
else if ($show == 'tomorrow') {
	$begin_time = mktime(23, 59, 59);
	$end_time = mktime(23, 59, 59) + 24 * 3600;
}
 else if ($show == "thismonth") {
	$begin_time = mktime(0,0,0,date("m"),1);
	$end_time = strtotime("+1 month", $begin_time);
} else if ($show == "lastmonth") {
	$end_time = mktime(0,0,0,date("m"),1);
	$begin_time = strtotime("-1 month", $end_time);
} else if ($show == "willarrive") {
	$begin_time = mktime(0, 0, 0);
}

// ���������� 2010-09-29:
if ($_GET["date"]) {
	$begin_time = strtotime($_GET["date"]." 0:0:0");
	$end_time = strtotime($_GET["date"]." 23:59:59");
}


// �б���ʾ��:
$t = load_class("table");
$t->set_head($list_heads, $default_sort, $default_order);
$t->set_sort($_GET["sort"], $_GET["order"]);
$t->param = $param;
$t->table_class = "new_list";


// ����������ʼ:
$where = array();

/*
// ȫ��δ�ã�ȡ���ط��鳤�Ĺ��ܣ�������Ҫ�ٵ���
// �ǻطÿͷ���Ա
$is_huifang_zuyuan = 0;
if ($uinfo["part_id"] == 12 && in_array("huifang", $guahao_config) && $uinfo["part_admin"] != 1) {
	$is_huifang_zuyuan = 1;
}

if ($hinfo["set_huifang_kf"] && $is_huifang_zuyuan) {
	$where[] = "(huifang_kf='{$realname}' or author='{$realname}')"; //ֻ��ʾ������Լ��Ĳ���(���Լ���ӵ�)
}
*/


// ��ȡȨ��:
$data_power = @explode(",", $uinfo["data_power"]);
$_limit = array();
if ($debug_mode || in_array("all", $data_power)) {
	if ($uinfo["part_id"] == 2) {
		$_limit[] = "author='".$realname."'";
		$_limit[] = "w_name='".$realname."'";
	}else{
		$_limit[] = "part_id>=0";
	}
} else {
	if (in_array("web", $data_power)) {
		$_limit[] = "part_id=2"; //��webȨ��
	}
	if (in_array("tel", $data_power)) {
		$_limit[] = "part_id=3"; //��telȨ��
	}
	if ($uinfo["part_admin"]) {
		$_limit[] = "part_id=".$uinfo["part_id"]; //���Ź���Ա
	}

	$z_info = $db->query("select name,type,sum_condition from index_module_set where isshow=1");
	foreach ($z_info as $li) {
		if (in_array($li["name"], $data_power)) {
			if (substr_count($li["sum_condition"], "+") > 0) {
				$_arr = explode("+", $li["sum_condition"]);
				foreach ($_arr as $_s) {
					$_limit[] = $li["type"]."='".$_s."'";
				}
			} else {
				$_limit[] = $li["type"]."='".$li["sum_condition"]."'";
			}
		}
	}

	// �����Լ���ӵ�:
	$_limit[] = "author='".$realname."'";
	
	
}


$where[] = "(".implode(" or ", $_limit).")";


if ($index_module) {
	$z_info = $db->query("select * from index_module_set where name='{$index_module}' limit 1", 1);
	if (substr_count($z_info["sum_condition"], "+") > 0) {
		$_arr = explode("+", $z_info["sum_condition"]);
		$z_limit = array();
		foreach ($_arr as $_s) {
			$z_limit[] = $z_info["type"]."='".$_s."'";
		}
		$where[] = "(".implode(" or ", $z_limit).")";
	} else {
		$where[] = $z_info["type"]."='".$z_info["sum_condition"]."'";
	}
}

//��Ŀ����

if($uinfo["part_id"] != 4){
$st_arr = array(
	"all"=>"����",
	"name"=>"����",
	"tel"=>"�绰",
	"ordernum"=>"ԤԼ��",
	"zhuanjia_num"=>"�����",
	"disease_2"=>"��������",
	"content"=>"��ѯ����",
	"memo"=>"��ע",
	"media_from"=>"ý����Դ",
	"engine_key"=>"�ؼ���",
	"from_site"=>"������ַ",
	"area"=>"����",
	"author"=>"�Ǽ���",
	"w_name"=>"ά����",
);
}else{
	$st_arr = array(
	"all"=>"����",
	"name"=>"����",
	"tel"=>"�绰",
	"ordernum"=>"ԤԼ��",
	"zhuanjia_num"=>"�����",
	"disease_2"=>"��������",
	"content"=>"��ѯ����",
	"memo"=>"��ע",
	"media_from"=>"ý����Դ",
	"engine_key"=>"�ؼ���",
	"from_site"=>"������ַ",
	"area"=>"����",
);
	}
    
$t_type = array(
	"order_date"=>"��Ժ����ʱ��",
	"djsj"=>"�Ǽ�ʱ��",
	"addtime"=>"ԤԼ����ʱ��",
);

if ($key = trim(stripslashes($key))) {
	$sk = "%{$key}%";
	if($searchtype == "all"){
		$sfield = array();
		foreach ($st_arr as $st => $sv) {
			if($st == 'all'){
				
			}else{
				$sfield[] = "binary $st like '{$sk}'";
			}
		}
		$where[] = "(".implode(" or ", $sfield).")";
	}else{
		$where[] = "(binary $searchtype like '{$sk}')";
	}
}

if ($btimes > 0) {
	$tbs = strtotime($btimes." 0:0:0");
	$where[] = $ttype.'>='.$tbs;
}
if ($etimes > 0) {
	$tes = strtotime($etimes." 23:59:59");
	$where[] = $ttype.'<'.$tes;
}


$time_type = empty($time_type) ? 'order_date' : $time_type;
if ($begin_time > 0) {
	$where[] = $time_type.'>='.$begin_time;
}
if ($end_time > 0) {
	$where[] = $time_type.'<'.$end_time;
}
if ($come != '') {
	if ($come == 1) {
		$where[] = "status=1";
	} else {
		$where[] = "status in (0,2)";
	}
}
if ($kefu_23_name != '') {
	$where[] = "author='$kefu_23_name'";
}
if ($kefu_4_name != '') {
	$where[] = "jiedai='$kefu_4_name'";
}
if ($doctor_name != '') {
	$where[] = "doctor='$doctor_name'";
}
if ($disease != '') {
	$where[] = "disease_id=$disease";
}
if ($part_id != '') {
	$where[] = "part_id=$part_id";
}
if ($depart != '') {
	$where[] = "depart=$depart";
}
if ($list_huifang) {
	$where[] = "huifang like '%[".$realname."]%'";
}
if ($media) {
	if ($media == "�ֻ�") {
		$where[] = "(media_from='�ֻ�����' or media_from='�ֻ��ƹ�' or media_from='�ֻ��ƹ�绰' or media_from='�ٶ�����')";
	} else {
		$where[] = "media_from='".trim($media)."'";
	}
}
if ($callid) {
	$where[] = "(id=".intval($callid)." and addtime=".intval($_GET["crc"]).")";
}
if ($hf_time > 0) {
	$where[] = "huifang_nexttime='".$hf_time."'";
}

$sqlwhere = $db->make_where($where);
//$sqlsort = $db->make_sort($list_heads, $sort, $order, $default_sort, $default_order);
if ($sort) {
	$order = $order ? $order : "asc";
	if (array_key_exists($sort, $sort_type_arr)) {
		$sqlsort = $sort_type_arr[$sort][1];
		if (substr_count($sqlsort, "{ascdesc}") > 0) {
			$sqlsort = str_replace("{ascdesc}", $order, $sqlsort);
		} else {
			$sqlsort .= " ".$order;
		}
	} else {
		exit_html("δ֪����ʽ��".$sort);
	}
} else {
	$sqlsort = $sort_type_arr[$default_sort][1];
	$default_order = $default_order ? $default_order : "asc";
	if (substr_count($sqlsort, "{ascdesc}") > 0) {
		$sqlsort = str_replace("{ascdesc}", $default_order, $sqlsort);
	} else {
		$sqlsort .= " ".$default_order;
	}
}

$sqlsort = " order by ".$sqlsort;

// ��ҳ����:
$count = $db->query("select count(*) as count from $table $sqlwhere $sqlgroup", 1, "count");
$pagecount = max(ceil($count / $pagesize), 1);
$page = max(min($pagecount, intval($page)), 1);
$offset = ($page - 1) * $pagesize;

// ��ѯ:
$time = time();
$today_begin = mktime(0,0,0);
$today_end = $today_begin + 24 * 3600;
$list_data = $db->query("select *,(order_date-$time) as remain_time, if(order_date<$today_begin, 1, if(order_date>$today_end, 2, 3)) as order_sort, if(status=1,2, if(status=2,1,0)) as status_1 from $table $sqlwhere $sqlgroup $sqlsort limit $offset,$pagesize");

$s_sql = $db->sql." (".$uinfo["data_power"].")";


// id => name:
$hospital_id_name = $db->query("select id,name from hospital", 'id', 'name');
$part_id_name = $db->query("select id,name from sys_part", 'id', 'name');
$disease_id_name = $db->query("select id,name from disease", 'id', 'name');
$depart_id_name = $db->query("select id,name from depart where hospital_id=$hid", 'id', 'name');

$use_depart = 1;
if (count($depart_id_name) == 0) {
	$use_depart = 0;
	unset($list_heads["����"]); //û�п���
}


// ������ͳ������ 2009-05-13 16:46
$res_report = '';
//if ($_GET["from"] == "search") {
	$sqlwhere_s = $sqlwhere ? ($sqlwhere." and status=1") : "where status=1";
	$count_come = $db->query("select count(*) as count from $table $sqlwhere_s $sqlgroup order by id desc", 1, "count");

	$sqlwhere_s = $sqlwhere ? ($sqlwhere." and status!=1") : "where status!=1";
	$count_not = $db->query("select count(*) as count from $table $sqlwhere_s $sqlgroup order by id desc", 1, "count");
	//echo "<br>".$db->sql;

	$count_all = $count_come + $count_not;

	$res_report = "�ܹ�: <b>".$count_all."</b> &nbsp; �ѵ�: <b>".$count_come."</b> &nbsp; δ��: <b>".$count_not."</b>";
//}

// ͳ�ƽ�������:
$t_time_type = "order_date";

$today_where = ($today_where ? ($today_where." and") : "")." $t_time_type>=".$today_begin;
$today_where .= " and $t_time_type<".$today_end;
$sqlwhere_s = "where ".($today_where ? ($today_where." and status=1") : "status=1");
$count_today_come = $db->query("select count(*) as count from $table $sqlwhere_s order by id desc", 1, "count");

$sqlwhere_s = "where ".($today_where ? ($today_where." and status!=1") : "status!=1");
$count_today_not = $db->query("select count(*) as count from $table $sqlwhere_s order by id desc", 1, "count");

$count_today_all = $count_today_come + $count_today_not;

$today_report = "<a href='?show=today'>�ܹ�: <b>".$count_today_all."</b></a> &nbsp; <a href='?show=today&come=1'>�ѵ�: <b>".$count_today_come."</b></a> &nbsp; <a href='?show=today&come=0'>δ��: <b>".$count_today_not."</b></a>&nbsp;";

// ��������ͳ��(����):
if (in_array($uinfo["part_id"], array(2,3))) {
	$basewhere = "part_id=".$uinfo["part_id"];
	$part_today_all = $db->query("select count(*) as count from $table where $basewhere and order_date>=$today_begin and order_date<$today_end", 1, "count");
	$part_today_come = $db->query("select count(*) as count from $table where $basewhere and order_date>=$today_begin and order_date<$today_end and status=1", 1, "count");
	$part_today_not = $part_today_all - $part_today_come;

	$part_report = "�ܹ�: <b>".$part_today_all."</b>  �ѵ�: <b>".$part_today_come."</b>  δ��: <b>".$part_today_not."</b>&nbsp;";
}


// ���б����ݷ���:
if ($sort == "addtime" || ($sort == "" && $default_sort == "addtime") ) {
	if ($order == "desc" || $default_order == "desc") {
		$today_begin = mktime(0,0,0);
		$today_end = $today_begin + 24*3600;
		$yesterday_begin = $today_begin - 24*3600;
        $tomorrow_end = $today_end+ 24*3600;
		$list_data_part = array();
		foreach ($list_data as $line) {
			if ($line["addtime"] < $yesterday_begin) {
				$list_data_part[3][] = $line;
			} else if ($line["addtime"] < $today_begin) {
				$list_data_part[2][] = $line;
			} else if ($line["addtime"] < $today_end) {
				$list_data_part[1][] = $line;
			}
			 else if ($line["addtime"] < $tomorrow_end) {
				$list_data_part[4][] = $line;
			}
		}

		$list_data = array();
		if (count($list_data_part[1]) > 0) { //�н��������:
			$list_data[] = array("id"=>0, "name"=>"���� [".count($list_data_part[1])."]");
			$list_data = array_merge($list_data, $list_data_part[1]);
		}
		if (count($list_data_part[2]) > 0) { //�н��������:
			$list_data[] = array("id"=>0, "name"=>"���� [".count($list_data_part[2])."]");
			$list_data = array_merge($list_data, $list_data_part[2]);
		}
		if (count($list_data_part[3]) > 0) { //�н��������:
			$list_data[] = array("id"=>0, "name"=>"ǰ������ [".count($list_data_part[3])."]");
			$list_data = array_merge($list_data, $list_data_part[3]);
		}
		unset($list_data_part);
	}
} else if ($sort == "status") {
	$list_data_part = array();
	foreach ($list_data as $line) {
		if ($line["status_1"] == 2) { //�ѵ�
			$list_data_part[1][] = $line;
		} else if ($line["status_1"] == 1) { //δ��
			$list_data_part[2][] = $line;
		} else if ($line["status_1"] == 0) { //�ȴ�
			$list_data_part[3][] = $line;
		}
	}

	$list_data = array();
	if (count($list_data_part[1]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"�ѵ� (�Ѹ�Լ) [".count($list_data_part[1])."]");
		$list_data = array_merge($list_data, $list_data_part[1]);
	}
	if (count($list_data_part[2]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"δ�� (ȷ�ϲ��ḰԼ) [".count($list_data_part[2])."]");
		$list_data = array_merge($list_data, $list_data_part[2]);
	}
	if (count($list_data_part[3]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"�ȴ� (��δ��Լ�������ܻḰԼ) [".count($list_data_part[3])."]");
		$list_data = array_merge($list_data, $list_data_part[3]);
	}
	unset($list_data_part);

} else if ($sort == "order_date" || ($sort == "" && $default_sort == "order_date") ) {
	$today_begin = mktime(0,0,0);
	$today_end = $today_begin + 24*3600;
	$yesterday_begin = $today_begin - 24*3600;

	$list_data_part = array();
	foreach ($list_data as $line) {
		if ($line["order_date"] < $yesterday_begin) {
			$list_data_part[1][] = $line;
		} else if ($line["order_date"] < $today_begin) {
			$list_data_part[2][] = $line;
			
		}
		 else if ($line["order_date"] < $tomorrow_end) {
			$list_data_part[4][] = $line;
		} 
		else if ($line["order_date"] < $today_end) {
			if ($line["status"] == 0) {
				$list_data_part[31][] = $line;
			} else if ($line["status"] == 1) {
				$list_data_part[32][] = $line;
			} else {
				$list_data_part[33][] = $line;
			}
			$list_data_part[3][] = $line;
		} else {
			$list_data_part[5][] = $line;
		}
	}

	$list_data = array();
	if (count($list_data_part[31]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"���� (�ȴ���) [".count($list_data_part[31])."]");
		$list_data = array_merge($list_data, $list_data_part[31]);
	}
	if (count($list_data_part[32]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"���� (�ѵ�) [".count($list_data_part[32])."]");
		$list_data = array_merge($list_data, $list_data_part[32]);
	}
	if (count($list_data_part[33]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"���� (������) [".count($list_data_part[33])."]");
		$list_data = array_merge($list_data, $list_data_part[33]);
	}
	if (count($list_data_part[4]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"������Ժ� (ʱ��δ��) [".count($list_data_part[4])."]");
		$list_data = array_merge($list_data, $list_data_part[4]);
	}
	if (count($list_data_part[2]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"���� [".count($list_data_part[2])."]");
		$list_data = array_merge($list_data, $list_data_part[2]);
	}
	if (count($list_data_part[1]) > 0) {
		$list_data[] = array("id"=>0, "name"=>"ǰ������ [".count($list_data_part[1])."]");
		$list_data = array_merge($list_data, $list_data_part[1]);
	}
	unset($list_data_part);
}

$back_url = make_back_url();

// �������:
foreach ($list_data as $li) {
	$id = $li["id"];
	if ($id == 0) {
		$t->add_tip_line($li["name"]);
	} else {
		$crc = $li["addtime"];

		$r = array();

		// ��������д���ŵĴ���취 @ 2012-07-10
		/*
		if (strlen($li["name"]) > 6) {
			if (substr_count($li["name"], "��") > 0) {
				$li["name"] = str_replace("��", "<br>��", $li["name"]);
			}
			if (substr_count($li["name"], "(") > 0) {
				$li["name"] = str_replace("(", "<br>(", $li["name"]);
			}
		}
		*/

		$r["����"] = "<b><nobr>".wee_wrap($li["name"], 6, '<br>')."</nobr></b>";
		$r["�Ա�"] = $li["sex"];
		$r["����"] = $li["age"] > 0 ? $li["age"] : "";
		//$r["ְҵ"] = $li["vocation"];
		if (($show_tel || $li["author"] == $realname || $li["w_name"] == $realname) && $uinfo["part_id"] != 4) {
			$r["�绰"] = '<nobr>'.$li["tel"].'</nobr><br>'.$li["tel_location"];
			if ($li["qq"] != '') {
				$r["�绰"] .= ($li["tel"] != '' ? "<br>" : "")."<nobr>Q:".trim($li["qq"])."</nobr>";
			}
		} else{
			$r["�绰"] = '<font color="gray" title="��Ȩ��">*</font><br>'.$li["tel_location"];
			if ($li["qq"] != '') {
				$r["�绰"] .= ($li["tel"] != '' ? "<br>" : "").'<nobr>Q:<font color="gray" title="��Ȩ��">*</font></nobr>';
			}
		}

		$r["ԤԼ��"] = $li["ordernum"];
		$r["�����"] = $li["zhuanjia_num"];
		$r["ҽ��"] = $li["doctor"];
		$dis_text = array();
		foreach (explode(",", $li["disease_id"]) as $dis_id) {
			if ($dis_id > 0) $dis_text[] = $disease_id_name[$dis_id];
			
		}

		$content_arr = array();
		$li["content"] = ($dis_text ? ("(".implode("|", $dis_text).") ") : "").$li["content"];
		if ($li["content"]) {
			$content_arr[] = '<div class="ct_1"><div class="ct_td_a c_1"><nobr>����</nobr></div><div class="ct_td_b c_1">'.text_show(cut(str_replace("\n", " ", trim($li["content"])), 150, "��")).'</div><div class="clear"></div></div>';
		}
		if ($li["memo"]) {
			$li["memo"] = strip_tags(trim($li["memo"]));
			if (substr_count(trim($li["memo"]), "\n") >= 4) {
				$_arr = @explode("\n", str_replace("\n\n", "\n", str_replace("\r", "", trim($li["memo"]))));
				$_arr = @array_slice($_arr, -4);
				$s_memo = '<font color="gray" style="cursor:help;" title="Ҫ�鿴���࣬��������Ĳ鿴ͼ��">����(�����ز��ֱ�ע)����</font>'.text_show("\r\n".implode("\r\n", $_arr));
			} else {
				$s_memo = text_show($li["memo"]);
			}
			//if($uinfo["part_id"] != 4){
				$content_arr[] = '<div class="ct_1"><div class="ct_td_a c_2"><nobr>��ע</nobr></div><div class="ct_td_b c_2">'.$s_memo.'</div><div class="clear"></div></div>';
			//}
			
		}
		if ($li["huifang"]) {
			$li["huifang"] = strip_tags(trim($li["huifang"]));
			if (substr_count($li["huifang"], "\n") >= 4) {
				$_arr = @explode("\n", str_replace("\n\n", "\n", str_replace("\r", "", trim($li["huifang"]))));
				$_arr = @array_slice($_arr, -4);
				$s_huifang = '<font color="gray" style="cursor:help;" title="Ҫ�鿴���࣬��������Ĳ鿴ͼ��">����(�����ز��ֽ���Ļطü�¼)����</font>'.text_show("\r\n".implode("\r\n", $_arr));
			} else {
				$s_huifang = text_show($li["huifang"]);
			}
				$content_arr[] = '<div class="ct_1"><div class="ct_td_a c_3"><nobr>�ط�</nobr></div><div class="ct_td_b c_3">'.$s_huifang.'</div><div class="clear"></div></div>';
			
		}
		$r["����ѯ����|��ע|�ط�"] = '<div class="ct_0">'.implode('<div class="hr_line"></div>', $content_arr).'</div>';

		//$r["��ѯ����"] = cut($li["content"], 22, "��");


		$r["������ַ"] = $li["from_site"];
		$r["��Ժ����ʱ��"] = "<nobr>".str_replace('|', '<br>', @date((date("Y") == date("Y", $li["order_date"]) ? "Y-m-d|G:i" : "Y-m-d|G:i"), $li["order_date"]))."</nobr>";
		$r["����"] = ($li["order_date"]-time() > 0 ? ceil(($li["order_date"]-time())/24/3600) : '0');

		$r["��������"] = "<nobr>".$disease_id_name[$li["disease_id"]]."<br>".$li["disease_2"]."</nobr>";
		$r["ý��"] = $li["media_from"];
		$r["�ؼ���"] = $li["engine_key"];
		$r["����"] = str_replace("�ͷ�", "", $part_id_name[$li["part_id"]]);
		$r["����"] = $li["depart"] > 0 ? $depart_id_name[$li["depart"]] : "";
		$area =explode(" ",$li["area"]);
		$r["����"] = "<nobr>".$area[0]."<br>".$area[1]."</nobr>";
		//$r["��ע"] = cut($li["memo"], 22, "��");
		//$r["�ͷ�"] = $li["author"]. ($li["edit_log"] ? ("<br><a href='javascript:;' onclick='alert(this.title)' title='".str_replace("<br>", "&#13", strim($li["edit_log"], '<br>'))."' style='color:#8080C0'>��</a>") : '');
		
		//��ҽ���ܿ����ͷ�����
		//if($uinfo["part_id"] != 4){
			$r["�Ǽ���"] = $li["author"];
			$r["ά����"] = $li["w_name"];
		//}
		
		$r["��Լ���"] = $status_array[$li["status"]];
		//$r["�ط�"] = $li["huifang"] != '' ? ('<a href="javascript:;" onclick="alert(this.title)" title="'.trim(strip_tags($li["huifang"])).'">��</a>') : '';
		$r["ԤԼ����ʱ��"] = "<nobr>".str_replace('|', '<br>', @date((date("Y") == date("Y", $li["addtime"]) ? "Y-m-d|G:i" : "Y-m-d|G:i"), $li["addtime"]))."</nobr>";
		$r["�Ǽ�ʱ��"] = "<nobr>".str_replace('|', '<br>', @date((date("Y") == date("Y", $li["djsj"]) ? "Y-m-d|G:i" : "Y-m-d|G:i"), $li["djsj"]))."</nobr>";

		// ����
		$op = array();
		$op[] = "<button class='button_op' onclick='view(".$id.",".$crc.", this); return false;'><img src='/res/img/b_detail.gif' align='absmiddle' title='�鿴'></button>";

		// ����Ժ
		if (@in_array("set_come", $guahao_config)) {
			$op[] = "<button class='button_op' onclick='set_come(".$id.",".$crc.", this); return false;'><img src='/res/img/b_pass.gif' align='absmiddle' title='����Ժ'></button>";
		}

		// �趨�طÿͷ�
		if ($hinfo["set_huifang_kf"] > 0) {
			if (@in_array("set_huifang_kf", $guahao_config)) {
				$op[] = "<button class='button_op' onclick='set_huifang_kf(".$id.",".$crc.", this); return false;'><img src='/res/img/b_user.gif' align='absmiddle' title='���ûطÿͷ�'></button>";
			}
		}

		// �ط�
		if (@in_array("huifang", $guahao_config)) {
			$can_huifang = 1;
			if ($hinfo["set_huifang_kf"] && $uinfo["part_admin"] != 1 && $li["huifang_kf"] != $realname) {
				$can_huifang = 0;
			}
			//if ($can_huifang) {

				$op[] = "<button class='button_op' onclick='set_huifang(".$id.",".$crc.", this); return false;'><img src='/res/img/b_tel.gif' align='absmiddle' title='�ط�'></button>";
			//}
		}

		// ��¼���Ѻ�������Ŀ
		if (@in_array("set_xiaofei", $guahao_config)) {
			$op[] = "<button class='button_op' onclick='set_xiaofei(".$id.",".$crc.", this); return false;'><img src='/res/img/b_check_good.gif' align='absmiddle' title='�������Ѷ��������Ŀ'></button>";
		}

		// �޸�Ȩ��
		$can_edit = 0;
		if (@in_array("patient_edit", $guahao_config)) {
			if ($username == "admin" || $uinfo["part_id"] == 9 || $username == "����") { //������Ա
				$can_edit = 1;
			} else {
				if ($uinfo["part_id"] == 2 || $uinfo["part_id"] == 3) { //�ͷ�
					if ($li["author"] == $realname || $li["w_name"] == $realname) { //�Լ���ӵ�
						$can_edit = 1; //�����޸�
					}
				} else { //���ǿͷ�
					if ($li["status"] != 1) { //δ��Ժ
						$can_edit = 1; // �����޸�
					}
				}
			}
		}
		if ($can_edit || $debug_mode) {
			$op[] = "<button class='button_op' onclick='edit(".$id.",".$crc.", this); return false;' class='op'><img src='/res/img/b_edit.gif' align='absmiddle' title='�޸�' alt=''></button>";
		}

		//ɾ��Ȩ��:
		$can_delete = 0;
		if (@in_array("patient_delete", $guahao_config)) {
			// �����ύ�߱��ˣ���û���޸ĵ�����£�����ɾ��
			if ($li["author"] == $realname) {
				if ($li["status"] == 0 && $li["edit_log"] == '') {
					$can_delete = 1;
				}
			} else {
				// ���Ǳ��ˣ�����ǹ���Ա�Ļ����Ҿ���ɾ��Ȩ�ޣ�����ɾ��:
				if (in_array($uinfo["part_id"], array(9))) {
					$can_delete = 1;
				}
			}
		}
		if ($can_delete || $username == "admin" || $debug_mode) {
			$op[] = "<button class='button_op' id='?op=delete&id=$id&crc=$crc' onclick='if (isdel()) location=this.id;'><img src='/res/img/b_delete.gif' align='absmiddle' title='ɾ��' alt=''></button>";
		}

		$r["����"] = implode("&nbsp;", $op);

		// �и�������;
		$_tr = ' id="line_'.$li["id"].'"';
		$color_status = $li["status"];
		if ($color_status == 0 && date("Ymd", $li["order_date"]) < date("Ymd")) {
			$color_status =5;
		}
		if ($color_status == 0 && $li["huifang"] != '') {
			$color_status = 6;
		}
		$color = $line_color[$color_status];

		// 2010-12-17 �޸ģ�������֮��Ĳ��ˣ���ɫ��һ��
		if ($li["order_date"] > strtotime("+2 month")) {
			$color = "#FF00FF";
		}

		$_tr .= ' style="color:'.$color.'"';
		$r["_tr_"] = $_tr;

		$t->add($r);
	}
}

$pagelink = pagelinkc($page, $pagecount, $count, make_link_info($link_param, "page"), "button");

?>
<html>
<head>
<meta charset="gbk" />
<title><?php echo $pinfo["title"]; ?></title>
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<style>
#color_tips {padding:0 0 8px 12px; }
#float_box {border:2px solid #408080; position:absolute; z-index:99999; background:#f4fbf7; }
.num {font-family:"Tahoma"; }
.hr_line {margin:5px 20px 5px 32px; padding:0; border-top:1px dotted silver; overflow:hidden; }

.button_op {height:22px; line-height:22px; width:22px; font-size:12px; border:0px none; background:url("/res/img/button_op.png") no-repeat; cursor:pointer; color:black; font-weight:normal; padding:1px 0 0 0; }
.button_op_over {height:22px; line-height:22px; width:22px; font-size:12px; border:0px none; background:url("/res/img/button_op.png") no-repeat; background-position:0px -22px;  cursor:pointer; color:black; font-weight:normal; padding:1px 0 0 0; }

/* �б�������ʾ��������� @ 2013-01-21 */
.ct_0 {width:100%; max-width:400px; min-width:200px; margin:3px 0px; }
.ct_1 {width:100%; }
.ct_td_a {width:32px; vertical-align:top; float:left; }
.ct_td_b {margin-left:32px; }
.c_1 {color:#A773CE; }
.c_2 {color:#525EAD; }
.c_3 {color:#A75860; }

.line td {border-color:#abdcbb !important; }
</style>

<script language="javascript">
var base_url = "/m/patient/patient.php";

function add() {
	set_high_light('');
	parent.load_src(1, base_url+'?op=add&from=list');
	return false;
}

function edit(id, crc, obj) {
	set_high_light(obj);
	parent.load_src(1,base_url+'?op=edit&id='+id+'&crc='+crc);
	return false;
}

function view(id, crc, obj) {
	set_high_light(obj);
	parent.load_src(1,base_url+'?op=view&id='+id+"&crc="+crc, 900, 600);
	return false;
}

function close_divs() {
	byid("float_box").innerHTML = '';
	byid("float_box").style.display = "none";
}

function set_come(id, crc, obj) {
	if (id > 0) {
		set_high_light(obj);
		var left = get_position(obj, "left");
		var top = get_position(obj, "top");
		var src = base_url+"?op=set_come&id="+id+"&crc="+crc;
		var w = 300;
		var h = 300;
		byid("float_box").innerHTML = '<iframe id="wee_frame" src="about:blank" width="'+w+'" height="'+h+'" border="0" frameborder="0"></iframe>';
		byid("float_box").style.display = "block";
		byid("float_box").style.left = (left - w - 10) + "px";
		byid("float_box").style.top = top - (h / 2) + 8 + "px";
		byid("wee_frame").src = src; //���IE6�в���ʾ������
	}
	event.cancelBubble = true;
}

function set_huifang(id, crc, obj) {
	set_high_light(obj);
	parent.load_src(1,base_url+'?op=huifang&id='+id+'&crc='+crc, 900, 600);
	return false;
}

function set_huifang_kf(id, crc, obj) {
	if (id > 0) {
		set_high_light(obj);
		var left = get_position(obj, "left");
		var top = get_position(obj, "top");
		var src = base_url+"?op=set_huifang_kf&id="+id+'&crc='+crc;
		var w = 300;
		var h = 100;

		byid("float_box").innerHTML = '<iframe id="wee_frame" src="about:blank" width="'+w+'" height="'+h+'" border="0" frameborder="0"></iframe>';
		byid("float_box").style.display = "block";
		byid("float_box").style.left = (left - w - 10) + "px";
		byid("float_box").style.top = top - (h / 2) + 8 + "px";
		byid("wee_frame").src = src;
	}
	event.cancelBubble = true;
}

function set_xiaofei(id, crc, obj) {
	set_high_light(obj);
	parent.load_src(1,base_url+'?op=set_xiaofei&id='+id+"&crc="+crc, 600, 400);
	return false;
}

function search() {
	parent.load_src(1,base_url+'?op=search&from=list');
	return false;
}


if (!document.all) {
	HTMLElement.prototype.insertAdjacentHTML = function(where, html) {
		var e = this.ownerDocument.createRange();
		e.setStartBefore(this);
		e = e.createContextualFragment(html);
		switch (where) {
			case 'beforeBegin': this.parentNode.insertBefore(e, this);break;
			case 'afterBegin': this.insertBefore(e, this.firstChild); break;
			case 'beforeEnd': this.appendChild(e); break;
			case 'afterEnd':
				if(!this.nextSibling) this.parentNode.appendChild(e);
				else this.parentNode.insertBefore(e, this.nextSibling); break;
		}
	};
}


// �޸� page_param ����ĳ���������
function page_param_update(name, value, is_submit) {
	var is_found = 0;
	var el = byid("page_param").getElementsByTagName("INPUT");
	for (var i = 0; i < el.length; i++) {
		if (el[i].name == name) {
			el[i].value = value;
			is_found = 1;
			break;
		}
	}
	if (!is_found) {
		var s = '<input type="hidden" name="'+name+'" value="'+value+'" />';
		byid("page_param").insertAdjacentHTML("beforeEnd", s);
	}
	if (is_submit) {
		page_param_submit();
	}
}

// ɾ��ĳ���������
function page_param_del(name) {
	var el = byid("page_param").getElementsByTagName("INPUT");
	for (var i = 0; i < el.length; i++) {
		if (el[i].name == name) {
			el[i].value = '';
			el[i].parentNode.removeChild(el[i]);
			return true;
		}
	}
}

function page_param_submit() {
	byid("page_param").submit();
}

function tongyuansou() {
	var key = byid("search_key").value;
	if (key == '') {
		alert("��������Ҫ������������绰���루�绰��������Ǻ���λ��");
		byid("search_key").focus();
		return false;
	}
	var url = base_url+"?op=tongyuansou&code=utf8&key="+encodeURIComponent(key)+"&r="+Math.random();
	parent.load_src(1, url);
}
</script>
</head>

<body onclick="close_divs()">
<form id="page_param" method="GET" action="" style="display:none;">
<?php
foreach ($link_param as $_p) {
?>
		<input type="hidden" name="<?php echo $_p; ?>" value="<?php echo $_GET[$_p] ?>" />
<?php
	}
?>
</form>

<div id="float_box" style="display:none;"><!-- ���ڵ���С�� --></div>

<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title" style="width:40%"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><nobr><?php echo $hospital_id_name[$hid]; ?> - ԤԼ�б�</nobr></td><td class="bar_right"></td></tr></table></div>
	<div class="header_center">
<!--<?php if (@in_array("patient_add", $guahao_config)) { ?>
		<button onclick="add(); return false;" class="button">���</button>&nbsp;
<?php } ?>
		<button onClick="search(); return false;" class="buttonb">�߼�����</button>-->
		<form action="?" method="GET" style="display:inline;">
			<input name="date" id="ch_date" onChange="this.form.submit();" value="<?php echo $_GET["date"]; ?>" style="width:0px; overflow:hidden; padding:0; margin:0; border:0;">
			<button onClick="picker({el:'ch_date',dateFmt:'yyyy-MM-dd'}); return false;" class="buttonb">���ղ鿴</button>
		</form>
	</div>
	<div class="headers_oprate">
    	<nobr>
        <form name="topform" method="GET" style="display:inline">
        	������<select name="searchtype" class="combo">
						<?php echo list_option($st_arr, "_key_", "_value_", $_GET["searchtype"]); ?>
						</select>
                        <input name="key" id="search_key" value="<?php echo $_GET["key"]; ?>" class="input" size="12" placeholder="�����ؼ���">
                        <select name="ttype" class="combo">
						<?php echo list_option($t_type, "_key_", "_value_", $_GET["ttype"]); ?>
						</select>
                        <input name="btimes" id="begin_times" class="input" style="width:70px" value="<?php echo $_GET["btimes"] ? $_GET["btimes"] : ''; ?>" placeholder="��ʼʱ��">
						<img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'begin_times',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��">
						<input name="etimes" id="end_times" class="input" style="width:70px" value="<?php echo $_GET["etimes"] ? $_GET["etimes"] : date("Y-m-d"); ?>">
						<img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'end_times',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��">
                        <input type="submit" class="search" value="����" style="font-weight:bold" title="�������">&nbsp;
                        
        </form>
        <button onClick="location='?'" class="search" title="�˳�������ѯ">����</button>
        </nobr>
    </div>
	<div class="clear"></div>
</div>
<!-- ͷ�� end -->

<!-- ͳ������ begin -->
<div class="space"></div>
<table class="bar" width="100%">
	<tr>
		<td class="bar_left"></td>
		<td class="bar_center">
			<table width="100%" style="padding:0; line-height:12px;">
				<tr>
					<td><nobr>&nbsp;<b>ͳ������:</b> <?php echo $res_report; ?></nobr></td>
					<td align="center"><nobr><?php if (in_array($uinfo["part_id"], array(2,3))) { ?><b>���Ž���:</b> <?php echo $part_report; ?><?php } ?></nobr></td>
					<td align="right"><nobr><b>��������: </b> <?php echo $today_report; ?></nobr></td>
				</tr>
			</table>
		</td>
		<td class="bar_right"></td>
	</tr>
</table>
<!-- ���������ͳ������ end -->

<div class="space"></div>

<div id="color_tips">
��ɫ��ǣ�
<?php foreach ($line_color_tip as $k => $v) { ?>
<font color="<?php echo $line_color[$k]; ?>"><?php echo $v; ?></font>&nbsp;
<?php } ?>

<?php if ($use_depart) { ?>
	<span style="margin-left:30px"><b>���ң�</b>
<?php foreach ($depart_id_name as $_id => $_name) { ?>
	<?php if ($_id == $_GET["depart"]) { ?>
		<b style="color:red">[<?php echo $_name ?>]</b>&nbsp;
	<?php } else { ?>
		<a href="#" onClick="page_param_update('depart','<?php echo $_id; ?>', 1);" onFocus="this.blur()"><?php echo $_name ?></a>&nbsp;
	<?php } ?>
<?php } ?>
		<a href="#" onClick="page_param_update('depart','', 1);" onFocus="this.blur()">[����]</a>
	</span>
<?php } ?>

	<span style="margin-left:30px"><b>����</b>
		<select name="sort" class="combo" onChange="page_param_update('sort', this.value, 1);">
			<option value="" style="color:gray">-Ĭ��-</option>
			<?php echo list_option($sort_type_name_arr, "_key_", "_value_", $_GET["sort"]); ?>
		</select>&nbsp;
		<select name="order" class="combo" onChange="page_param_update('order', this.value, 1);">
			<option value="" style="color:gray">-Ĭ��-</option>
			<?php echo list_option($sort_order_arr, "_key_", "_value_", $_GET["order"]); ?>
		</select>
	</span>

	<span style="margin-left:30px">
	<a href="#" onClick="page_param_update('show','tomorrow');page_param_update('time_type','order_date');page_param_update('come','', 1);"><?php echo ($_GET["show"] == "tomorrow" && empty($_GET["come"]) ) ? ('<font color=red><b>[����Ԥ��]</b></font>') : "����Ԥ��"; ?></a>&nbsp;
		<a href="#" onClick="page_param_update('show','today');page_param_update('time_type','order_date');page_param_update('come','', 1);"><?php echo ($_GET["show"] == "today" && empty($_GET["come"])  && $_GET["time_type"] != "addtime" ) ? ('<font color=red><b>[����]</b></font>') : "����"; ?></a>&nbsp;
		<a href="#" onClick="page_param_update('show','today');page_param_update('time_type','order_date');page_param_update('come','1', 1);"><?php echo ($_GET["show"] == "today" && $come == 1) ? ('<font color=red><b>[�����ѵ�]</b></font>') : "�����ѵ�"; ?></a>&nbsp;
		<a href="#" onClick="page_param_update('show','yesterday');page_param_update('time_type','order_date');page_param_update('come','', 1);"><?php echo ($_GET["show"] == "yesterday" && empty($come) && $_GET["time_type"] != "addtime" ) ? ('<font color=red><b>[����]</b></font>') : "����"; ?></a>&nbsp;
		<a href="#" onClick="page_param_update('show','yesterday');page_param_update('time_type','order_date');page_param_update('come','1', 1);"><?php echo ($_GET["show"] == "yesterday" && $come == 1) ? ('<font color=red><b>[�����ѵ�]</b></font>') : "�����ѵ�"; ?></a>&nbsp;
		<a href="#" onClick="page_param_update('show','willarrive');page_param_update('time_type','order_date');page_param_update('come','0', 1)"><?php echo ($_GET["show"] == "willarrive" && $come == 0) ? ('<font color=red><b>[Ԥ��]</b></font>') : "Ԥ��"; ?></a>&nbsp;
	</span>


</div>


<!-- �����б� begin -->
<?php echo $t->show(); ?>
<!-- �����б� end -->

<!-- ��ҳ���� begin -->
<div class="space"></div>
<div class="footer_op">
	<div class="footer_op_left"><button onClick="select_all()" class="button" disabled="true">ȫѡ</button></div>
	<div class="footer_op_right"><?php echo $pagelink; ?></div>
</div>
<!-- ��ҳ���� end -->

<!-- <?php echo $s_sql; ?> -->

</body>
</html>