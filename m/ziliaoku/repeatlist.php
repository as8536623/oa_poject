<?php
// --------------------------------------------------------
// - ����˵�� : �������Ͽ�
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2013-7-11
// --------------------------------------------------------
require "../../core/core.php";
$table = "ku_list";

// ���嵱ǰҳ��Ҫ�õ��ĵ��ò���:
$aLinkInfo = array(
	"mobile" => "mobile",
	"name" => "name",
	"qq" => "qq",
	"weixin" => "weixin"
);

// ��ȡҳ����ò���:
foreach ($aLinkInfo as $local_var_name => $call_var_name) {
	$$local_var_name = $_GET[$call_var_name];
}

// ���嵥Ԫ���ʽ:
$aOrderType = array(0 => "", 1 => "asc", 2 => "desc");
$aTdFormat = array(
	0=>array("title"=>"��Ժ", "width"=>"12", "align"=>"center"),
	2=>array("title"=>"�Ǽ�����", "width"=>"", "align"=>"center", "sort"=>"addtime", "defaultorder"=>2),
	1=>array("title"=>"�Ǽ���", "width"=>"40", "align"=>"center", "sort"=>"u_name", "defaultorder"=>1),
	6=>array("title"=>"ά����", "width"=>"40", "align"=>"center", "sort"=>"w_name", "defaultorder"=>1),
	3=>array("title"=>"����", "width"=>"40", "align"=>"center", "sort"=>"name", "defaultorder"=>1),
	4=>array("title"=>"�Ա�", "width"=>"20", "align"=>"center", "sort"=>"sex", "defaultorder"=>1),
	5=>array("title"=>"����", "width"=>"20", "align"=>"center", "sort"=>"age", "defaultorder"=>1),
	7=>array("title"=>"����", "width"=>"40", "align"=>"center", "sort"=>"area", "defaultorder"=>1),
	8=>array("title"=>"�ֻ�", "width"=>"70", "align"=>"center", "sort"=>"mobile", "defaultorder"=>1),
	9=>array("title"=>"QQ", "width"=>"18", "align"=>"center", "sort"=>"qq", "defaultorder"=>1),
	10=>array("title"=>"΢��", "width"=>"80", "align"=>"center", "sort"=>"weixin", "defaultorder"=>1),
	11=>array("title"=>"��������", "width"=>"50", "align"=>"center", "sort"=>"jblx", "defaultorder"=>1),
	12=>array("title"=>"��������", "width"=>"", "align"=>"center", "sort"=>"intention", "defaultorder"=>1),
	13=>array("title"=>"�ؼ���", "width"=>"60", "align"=>"center", "sort"=>"laiyuan", "defaultorder"=>1),
	14=>array("title"=>"��ѯ����", "width"=>"80", "align"=>"left", "sort"=>"", "defaultorder"=>1),
	18=>array("title"=>"��ǰ�ط�ʱ��", "width"=>"60", "align"=>"center", "sort"=>"huifang_nowtime", "defaultorder"=>1),
	15=>array("title"=>"�طô���", "width"=>"", "align"=>"center", "sort"=>"", "defaultorder"=>1),
	16=>array("title"=>"�´λط�ʱ��", "width"=>"60", "align"=>"center", "sort"=>"huifang_nexttime", "defaultorder"=>1),
);

// Ĭ������ʽ:
$defaultsort = 2;
$defaultorder = 2;


// ��ѯ����:
// $where = array();
// if ($username != 'admin' && !$debug_mode) {
// 	$where[] = "hid in (".implode(",", $hospital_ids).")";
// }
// $where[] = "hid=".$hid;

if($mobile){
	$where[] = "(mobile = $mobile)";
}
if($name){
	$where[] = "(name = '$name')";
}
if($qq){
	$where[] = "(qq = '$qq')";
}
if($weixin){
	$where[] = "(weixin = '$weixin')";
}

$sqlwhere = count($where) > 0 ? ("where ".implode(" and ", $where)) : " ";

//��������
$disease_id_name = $db->query("select id,name from disease", "id", "name");


// ������Ĵ���
if ($sortid > 0) {
	$sqlsort = "order by ".$aTdFormat[$sortid]["sort"]." ";
	if ($sorttype > 0) {
		$sqlsort .= $aOrderType[$sorttype];
	} else {
		$sqlsort .= $aOrderType[$aTdFormat[$sortid]["defaultorder"]];
	}
} else {
	if ($defaultsort > 0 && array_key_exists($defaultsort, $aTdFormat)) {
		$sqlsort = "order by ".$aTdFormat[$defaultsort]["sort"]." ".$aOrderType[$defaultorder];
	} else {
		$sqlsort = "";
	}
}

// ��ҳ����:
$count = $db->query("select count(*) as c from $table $sqlwhere", 1, "c");
$pagecount = max(ceil($count / $pagesize), 1);
$page = max(min($pagecount, intval($page)), 1);
$offset = ($page - 1) * $pagesize;

// ��ѯ:
$data = $db->query("select * from $table $sqlwhere $sqlsort limit $offset,$pagesize");


// ҳ�濪ʼ ------------------------
?>
<html>
<head>
<title>�ظ�����</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<style>
.column_sortable {color:blue !important; cursor:pointer;}
.sorttable_nosort {color:gray; }
.tr_high_light td {background:#FFE1D2; }
.hf_line {border-top:1px dotted silver; height:0; line-height:0; padding:3px 0 3px 10px; }
.tr_red {color:red; }
.tr_red td {color:red; }
.tr_purple {color:#8000FF; }
.tr_purple td {color:#8000FF; }
.tr_green{color:#FFFF80}
.tr_green td{color:#FFFF80}
</style>
<script language="javascript">

function add() {
	set_high_light('');
	parent.load_src(1,'/m/ziliaoku/ku_edit.php', 800, 550);
	return false;
}
</script>
</head>

<body>
<!-- ͷ�� begin -->
<table class="headers" width="100%">
	<tr>
		<td class="headers_title" style="width:100px">
			<nobr><table class="bar"><tr><td class="bar_left"></td><td class="bar_center">�������ظ���</td><td class="bar_right"></td></tr></table></nobr>
		</td>
	</tr>
</table>
<!-- ͷ�� end -->

<div class="space"></div>

<!-- �����б� begin -->
<form name="mainform">
<table id="list" width="100%" align="center" class="list sortable">
	<!-- ��ͷ���� begin -->
	<tr>
<?php
// ��ͷ����:
foreach ($aTdFormat as $tdid => $tdinfo) {
	list($tdalign, $tdwidth, $tdtitle) = make_td_head($tdid, $tdinfo);
?>
		<td class="head" align="<?php echo $tdalign; ?>" width="<?php echo $tdwidth; ?>"><?php echo $tdtitle; ?></td>
<? } ?>
	</tr>
	<!-- ��ͷ���� end -->

	<!-- ��Ҫ�б����� begin -->
<?php
if (count($data) > 0) {
	foreach ($data as $line) {
		$id = $line["id"];

		$content = cut(strip_tags($line["zx_content"]), 30, "��");
		if ($line["hf_log"]) {
			$content .= '<hr class="hf_line">';
			$content .= '<span style="color:#8000FF">'.text_show($line["hf_log"]).'</span>';
		}

		$class = "";
		if ($line["is_yuyue"]) {
			$class = "tr_red";
		}
		
		//�Ƿ���
		$repeatItemHid = $db->query("select hid from $table where id = $id limit 1",1);
		$ptable = "patient_".$repeatItemHid['hid'];
		$dzcheck = $db->query("select status from $ptable where lid = $id limit 1",1);

?>
	<tr onMouseOver="mi(this)" onMouseOut="mo(this)" class="<?php echo $class; ?>">
		<td align="center" class="item"><input name="dzcheck" type="checkbox" value="<?php echo $dzcheck["status"]; ?>" <?php if($dzcheck["status"]) echo "checked" ; ?>  onclick="return false;"></td>
		<td align="center" class="item"><nobr><?php date_default_timezone_set('PRC');  echo nl2br(date("Y-m-d\nH:i", $line["addtime"])); ?></nobr></td>
		<td align="center" class="item"><?php echo $line["u_name"]; ?></td>
        <td align="center" class="item"><?php echo $line["w_name"]; ?></td>
		<td align="center" class="item"><nobr><?php echo $line["name"]; ?></nobr></td>
		<td align="center" class="item"><?php echo $line["sex"]; ?></td>
		<td align="center" class="item"><?php echo $line["age"]; ?></td>
		<td align="center" class="item"><nobr><?php $area =explode(" ",$line["area"]); echo $area[0]."<br>".$area[1]  ?></nobr></td>
		<td align="center" class="item"><?php echo $line["mobile"]; ?></td>
		<td align="center" class="item"><?php echo $line["qq"]; ?></td>
		<td align="center" class="item"><?php echo $line["weixin"]; ?></td>
		<td align="center" class="item"><nobr><?php $jblx = $line["jblx"]; $jblx1 = explode(",",$jblx); if($jblx1[0]) echo $disease_id_name[$jblx1[0]]."<br>".$jblx1[1] ?></nobr></td>
		<td align="center" class="item"><?php echo $line["intention"]; ?></td>
		<td align="center" class="item"><?php echo $line["laiyuan"]; ?></td>
		<td align="left" class="item" title='<?php  echo $line["zx_content"] ?>'><?php echo $content; ?></td>
        <td align="center" class="item" style=" color:#F00"><nobr><?php  echo nl2br(date("Y-m-d", $line["huifang_nowtime"])); ?></nobr></td>
		<td align="center" class="item" style=" color:#F00" ><?php echo hftimes($line["hf_log"]); ?></td>
		<td align="center" class="item" style=" color:#F00"><nobr><?php echo $line["huifang_nexttime"]; ?></nobr></td>
	</tr>
<?php
	}
} else {
?>
	<tr>
		<td colspan="<?php echo count($aTdFormat); ?>" align="center" class="nodata">(û������...)</td>
	</tr>
<?php } ?>
	<!-- ��Ҫ�б����� end -->
</table>
</form>
<!-- �����б� end -->

<div class="space"></div>

<!-- ��ҳ���� begin -->
<div class="footer_op">
	<div class="footer_op_left">&nbsp;�� <b><?php echo $count; ?></b> ������</div>
	<div class="footer_op_right"><?php echo pagelinkc($page, $pagecount, $count, make_link_info($aLinkInfo, "page"), "button"); ?></div>
</div>
<!-- ��ҳ���� end -->

</body>
</html>