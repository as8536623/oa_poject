<?php
// --------------------------------------------------------
// - 功能说明 : 病人资料库
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2013-7-11
// --------------------------------------------------------
require "../../core/core.php";
$table = "ku_list";

// 定义当前页需要用到的调用参数:
$aLinkInfo = array(
	"mobile" => "mobile",
	"name" => "name",
	"qq" => "qq",
	"weixin" => "weixin"
);

// 读取页面调用参数:
foreach ($aLinkInfo as $local_var_name => $call_var_name) {
	$$local_var_name = $_GET[$call_var_name];
}

// 定义单元格格式:
$aOrderType = array(0 => "", 1 => "asc", 2 => "desc");
$aTdFormat = array(
	0=>array("title"=>"到院", "width"=>"12", "align"=>"center"),
	2=>array("title"=>"登记日期", "width"=>"", "align"=>"center", "sort"=>"addtime", "defaultorder"=>2),
	1=>array("title"=>"登记人", "width"=>"40", "align"=>"center", "sort"=>"u_name", "defaultorder"=>1),
	6=>array("title"=>"维护人", "width"=>"40", "align"=>"center", "sort"=>"w_name", "defaultorder"=>1),
	3=>array("title"=>"姓名", "width"=>"40", "align"=>"center", "sort"=>"name", "defaultorder"=>1),
	4=>array("title"=>"性别", "width"=>"20", "align"=>"center", "sort"=>"sex", "defaultorder"=>1),
	5=>array("title"=>"年龄", "width"=>"20", "align"=>"center", "sort"=>"age", "defaultorder"=>1),
	7=>array("title"=>"地区", "width"=>"40", "align"=>"center", "sort"=>"area", "defaultorder"=>1),
	8=>array("title"=>"手机", "width"=>"70", "align"=>"center", "sort"=>"mobile", "defaultorder"=>1),
	9=>array("title"=>"QQ", "width"=>"18", "align"=>"center", "sort"=>"qq", "defaultorder"=>1),
	10=>array("title"=>"微信", "width"=>"80", "align"=>"center", "sort"=>"weixin", "defaultorder"=>1),
	11=>array("title"=>"疾病类型", "width"=>"50", "align"=>"center", "sort"=>"jblx", "defaultorder"=>1),
	12=>array("title"=>"就诊意向", "width"=>"", "align"=>"center", "sort"=>"intention", "defaultorder"=>1),
	13=>array("title"=>"关键词", "width"=>"60", "align"=>"center", "sort"=>"laiyuan", "defaultorder"=>1),
	14=>array("title"=>"咨询内容", "width"=>"80", "align"=>"left", "sort"=>"", "defaultorder"=>1),
	18=>array("title"=>"当前回访时间", "width"=>"60", "align"=>"center", "sort"=>"huifang_nowtime", "defaultorder"=>1),
	15=>array("title"=>"回访次数", "width"=>"", "align"=>"center", "sort"=>"", "defaultorder"=>1),
	16=>array("title"=>"下次回访时间", "width"=>"60", "align"=>"center", "sort"=>"huifang_nexttime", "defaultorder"=>1),
);

// 默认排序方式:
$defaultsort = 2;
$defaultorder = 2;


// 查询条件:
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

//疾病类型
$disease_id_name = $db->query("select id,name from disease", "id", "name");


// 对排序的处理：
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

// 分页数据:
$count = $db->query("select count(*) as c from $table $sqlwhere", 1, "c");
$pagecount = max(ceil($count / $pagesize), 1);
$page = max(min($pagecount, intval($page)), 1);
$offset = ($page - 1) * $pagesize;

// 查询:
$data = $db->query("select * from $table $sqlwhere $sqlsort limit $offset,$pagesize");


// 页面开始 ------------------------
?>
<html>
<head>
<title>重复患者</title>
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
<!-- 头部 begin -->
<table class="headers" width="100%">
	<tr>
		<td class="headers_title" style="width:100px">
			<nobr><table class="bar"><tr><td class="bar_left"></td><td class="bar_center">患者已重复！</td><td class="bar_right"></td></tr></table></nobr>
		</td>
	</tr>
</table>
<!-- 头部 end -->

<div class="space"></div>

<!-- 数据列表 begin -->
<form name="mainform">
<table id="list" width="100%" align="center" class="list sortable">
	<!-- 表头定义 begin -->
	<tr>
<?php
// 表头处理:
foreach ($aTdFormat as $tdid => $tdinfo) {
	list($tdalign, $tdwidth, $tdtitle) = make_td_head($tdid, $tdinfo);
?>
		<td class="head" align="<?php echo $tdalign; ?>" width="<?php echo $tdwidth; ?>"><?php echo $tdtitle; ?></td>
<? } ?>
	</tr>
	<!-- 表头定义 end -->

	<!-- 主要列表数据 begin -->
<?php
if (count($data) > 0) {
	foreach ($data as $line) {
		$id = $line["id"];

		$content = cut(strip_tags($line["zx_content"]), 30, "…");
		if ($line["hf_log"]) {
			$content .= '<hr class="hf_line">';
			$content .= '<span style="color:#8000FF">'.text_show($line["hf_log"]).'</span>';
		}

		$class = "";
		if ($line["is_yuyue"]) {
			$class = "tr_red";
		}
		
		//是否到诊
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
		<td colspan="<?php echo count($aTdFormat); ?>" align="center" class="nodata">(没有数据...)</td>
	</tr>
<?php } ?>
	<!-- 主要列表数据 end -->
</table>
</form>
<!-- 数据列表 end -->

<div class="space"></div>

<!-- 分页链接 begin -->
<div class="footer_op">
	<div class="footer_op_left">&nbsp;共 <b><?php echo $count; ?></b> 条资料</div>
	<div class="footer_op_right"><?php echo pagelinkc($page, $pagecount, $count, make_link_info($aLinkInfo, "page"), "button"); ?></div>
</div>
<!-- 分页链接 end -->

</body>
</html>