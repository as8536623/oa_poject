<?php
// --------------------------------------------------------
// - 功能说明 : 病人资料库
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2013-7-11
// --------------------------------------------------------
require "../../core/core.php";
$table = "ku_list";

/*
if (!$debug_mode && $username != "admin" && $config["show_ziliaoku"] != 1) {
	exit("对不起，您没有开通此权限！");
}
*/

// 操作的处理:
if ($op = $_GET["op"]) {
	if ($op == "delete") {
		$id = $_GET["id"];
		$del_fail = $del_ok = 0;
			$id = intval($id);
			$line = $db->query("select * from $table where id=$id limit 1", 1);
			if ($id > 0) {
				if($db->query("delete from $table where id=$id limit 1")){
					$del_ok = 1;
					
				}
				
			}
		if ($del_ok > 0) {
			$log->add("删除", "删除登记病人: ".$line["name"], $line, $table);
			echo "<script>alert('删除成功！');history.go(-1);</script>";
			
		} else {
			echo "<script>alert('删除失败！');history.go(-1);</script>";
		}
	}

	if ($op == "set_yuyue") {

		$line = $db->query("select * from $table where id=$id limit 1", 1);
		if (!$line || $line["id"] != $id) {
			exit("参数id对应的资料不存在...");
		}

		$to_hid = $line["hid"];
		$_SESSION["hospital_id"] = $to_hid;

		$r = array();
		$r["part_id"] = $uinfo["part_id"];
		$r["lid"] = $line["id"];
		$r["name"] = $line["name"];
		$r["u_name"] = $line["u_name"];
		$r["uid"] = $line["uid"];
		$r["w_name"] = $line["w_name"];
		$r["sex"] = $line["sex"];
		$r["age"] = $line["age"];
		$r["vocation"] = $line["vocation"];
		$r["area"] = $line["area"];
		$r["IP"] = $line["IP"];
		$r["tel"] = $line["mobile"];
		$r["media_from"] = $line["media_from"];;
		$r["engine_key"] = $line["laiyuan"];
		$r["from_site"] = $line["from_site"];
		$r["jblx"] = $line["jblx"];
		$content = $line["zx_content"];
		if ($line["qq"] != '') {
			$content .= " QQ:".$line["qq"];
		}
		if ($line["order_qq"] != '') {
			$content .= " 预约人QQ:".$line["order_qq"];
		}
		if ($line["weixin"] != '') {
			$content .= " 微信:".$line["weixin"];
		}
		if ($line["order_weixin"] != '') {
			$content .= " 预约人微信:".$line["order_weixin"];
		}

		$memo = "从资料库转入";

		$r["content"] = $content;
		$r["memo"] = $memo;
		$r["from"] = "ku";
		$r["ku_id"] = $id;

		foreach ($r as $k => $v) {
			$r[$k] = $k."=".urlencode($v);
		}

		$link = "/m/patient/patient.php?op=add&".implode("&", $r);
		header("location:".$link);

		exit;
	}
	

}

// 定义当前页需要用到的调用参数:
$aLinkInfo = array(
	"page" => "page",
	"hospital" => "hospital",
	"sortid" => "sort",
	"sorttype" => "sorttype",
	"searchword1" => "searchword1",
	"searchtype1" => "searchtype1",
	"searchword2" => "searchword2",
	"searchtype2" => "searchtype2",
    "hf_time" => "hf_time",
	"show" => "show",
	"kefu_23_name" => "kefu_23_name",
    "btime" => "btime",
	"etime" => "etime",
	"hf_log" => "hf_log",
	"lastHfTime" => "lastHfTime",
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
	//6=>array("title"=>"职业", "width"=>"", "align"=>"center", "sort"=>"vocation", "defaultorder"=>1),
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
	17=>array("title"=>"操作", "width"=>"", "align"=>"center"),
);

// 默认排序方式:
$defaultsort = 2;
$defaultorder = 2;


// 查询条件:
$where = array();
if ($username != 'admin' && !$debug_mode) {
	$where[] = "hid in (".implode(",", $hospital_ids).")";
}
$where[] = "hid=".$hid;

//客服只能看到自己的数据
$data_power = @explode(",", $uinfo["data_power"]);
$_limit = array();
if (!in_array("all", $data_power) || $uinfo["part_id"] == 2) {
	$_limit[] = "u_name='".$realname."'";
	$_limit[] = "w_name='".$realname."'";
	$where[] = "(".implode(" or ", $_limit).")";
}




//搜索
if ($searchword1) {
	switch ($searchtype1){
		case "所有":
			$where[] = "(concat(h_name,'_',name,'_',mobile,'_',qq,'_',zx_content,'_',weixin,'_',u_name,'_',w_name) like '%{$searchword1}%')";
			break;
		case "年龄":
			$where[] = "(age like '%{$searchword1}%')";
			break;
		case "地区":
			$where[] = "(area like '%{$searchword1}%')";
			break;
		case "疾病类型":
			$where[] = "(jblx like '%{$searchword1}%')";
			break;
		case "就诊意向":
			$where[] = "(intention like '%{$searchword1}%')";
			break;
		case "关键词":
			$where[] = "(laiyuan like '%{$searchword1}%')";
			break;
	}
	
}

if ($searchword2) {
	switch ($searchtype2){
		case "所有":
			$where[] = "(concat(h_name,'_',name,'_',mobile,'_',qq,'_',zx_content,'_',weixin,'_',u_name,'_',w_name) like '%{$searchword2}%')";
			break;
		case "年龄":
			$where[] = "(age like '%{$searchword2}%')";
			break;
		case "职业":
			$where[] = "(vocation like '%{$searchword2}%')";
			break;
		case "疾病类型":
			$where[] = "(jblx like '%{$searchword2}%')";
			break;
		case "就诊意向":
			$where[] = "(intention like '%{$searchword2}%')";
			break;
		case "关键词":
			$where[] = "(laiyuan like '%{$searchword2}%')";
			break;
	}
	
}

//已回访
if($hf_log==1){
	$where[] = "hf_log <> ''";
	}

//未回访
if($hf_log==2){
	$where[] = "hf_log = ''";
	}

if ($btime) {
		$tb = strtotime($btime." 0:0:0");
		$where[] = "addtime>=$tb";
	}
if ($etime) {
		$te = strtotime($etime." 23:59:59");
		$where[] = "addtime<=$te";
		if($pinfo["title"] == $ztitle){
			//微信QQ有效电话
			$sqlwhere = count($where) > 0 ? ("where ".implode(" and ", $where)) : " ";
			}
	}
if ($lastHfTime) {
		$thf = strtotime($lastHfTime." 23:59:59");
		$where[] = "huifang_nowtime<=$thf";
	}

$ztitle = "电话/微信/QQ";
$time_type = "addtime";
if($show || $hf_time || $kefu_23_name || $etime){
	//判断时间
	if($show){
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
		}
		$where[] = $time_type.'>='.$begin_time;
		$where[] = $time_type.'<'.$end_time;
	}
	//判断回访时间
	if($hf_time){
		
		if($uinfo["character_id"] == 71 || $username == "admin" || $uinfo["character_id"] == 73){
			$where[] = 	"huifang_nexttime = '$hf_time'";
		}
		else{
			$where[] = 	"(u_name = '$realname' or w_name = '$realname') and huifang_nexttime = '$hf_time'";
		}
	}
	//判断客服名字
	if($kefu_23_name){
		$where[] = 	"u_name = '$kefu_23_name'";
	}
	
	//内页访问
	if($from_inside = $_GET["from_inside"]){
		//有效电话
		$sqlwhere = count($where) > 0 ? ("where LENGTH(mobile)>3 and ".implode(" and ", $where)) : "where LENGTH(mobile)>3 ";
	}else{
		$pinfo["title"] = $ztitle;
		//微信QQ有效电话
		$sqlwhere = count($where) > 0 ? ("where ".implode(" and ", $where)) : " ";
		}
	
	
}else{
	//有效电话
	$sqlwhere = count($where) > 0 ? ("where LENGTH(mobile)>3 and ".implode(" and ", $where)) : "where LENGTH(mobile)>3 ";
}

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
<title>资料列表</title>
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
window.last_high_obj = '';
function set_high_light(obj) {
	if (last_high_obj) {
		last_high_obj.parentNode.parentNode.parentNode.className = "";
	}
	if (obj) {
		obj.parentNode.parentNode.parentNode.className = "tr_high_light";
		last_high_obj = obj;
	} else {
		last_high_obj = '';
	}
}

function add() {
	set_high_light('');
	parent.load_src(1,'/m/ziliaoku/ku_edit.php', 800, 550);
	return false;
}

function edit(id, obj) {
	set_high_light(obj);
	parent.load_src(1,'/m/ziliaoku/ku_edit.php?id='+id, 800, 550);
	return false;
}

function huifang(id, obj) {
	set_high_light(obj);
	parent.load_src(1,'/m/ziliaoku/ku_huifang.php?id='+id, 800, 500);
	return false;
}

function set_yuyue(id, obj) {
	set_high_light(obj);
	if (confirm("请在接下来打开的窗口中完善资料并提交，提交后才算成功。是否继续？")) {
		parent.load_src(1, '/m/ziliaoku/ku_list.php?op=set_yuyue&id='+id);
	}
	return false;
}
</script>
</head>

<body>
<!-- 头部 begin -->
<table class="headers" width="100%">
	<tr>
		<td class="headers_title" style="width:100px">
			<nobr><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $pinfo["title"]; ?></td><td class="bar_right"></td></tr></table></nobr>
		</td>
		<td class="headers_cneter" align="center">
			<button onClick="add()" class="button">添加</button>&nbsp;
			<button onClick="location.reload()" class="button" title="刷新页面">刷新</button>
		</td>
		<td class="headers_oprate">
			<nobr>
			<form name="topform" method="GET" style="display:inline;">
				搜索：
				<select name="searchtype1" class="combo" style="width:100px">
					<?php echo list_option(array("所有", "年龄",  "地区", "疾病类型", "就诊意向", "关键词"), "_value_", "_value_", $_GET["searchtype1"]); ?>
				</select>
				<input name="searchword1" value="<?php echo $_GET["searchword1"]; ?>" class="input" size="12" placeholder="搜索关键词">&nbsp;
                <select name="searchtype2" class="combo" style="width:100px">
					<?php echo list_option(array("所有", "年龄",  "地区", "疾病类型", "就诊意向", "关键词"), "_value_", "_value_", $_GET["searchtype2"]); ?>
				</select>
				<input name="searchword2" value="<?php echo $_GET["searchword2"]; ?>" class="input" size="12" placeholder="搜索关键词">&nbsp;
                <input name="btime" id="begin_time" class="input" style="width:80px" value="<?php echo $_GET["btime"] ? $_GET["btime"] : ''; ?>" placeholder="开始时间">
				<img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'begin_time',dateFmt:'yyyy-MM-dd',maxDate:'#F{$dp.$D(\'end_time\')}' })" align="absmiddle" style="cursor:pointer" title="选择时间">
				<input name="etime" id="end_time" class="input" style="width:80px" value="<?php echo $_GET["etime"] ? $_GET["etime"] : date("Y-m-d"); ?>">
				<img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'end_time',dateFmt:'yyyy-MM-dd',minDate:'#F{$dp.$D(\'begin_time\')}' })" align="absmiddle" style="cursor:pointer" title="选择时间">
				回访时间：
				<input name="lastHfTime" id="lastHfTime" class="input" style="width:80px" value="<?php echo $_GET["lastHfTime"] ? $_GET["lastHfTime"] : ''; ?>" placeholder="最后回访时间">
				<img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'lastHfTime',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="选择时间">
				<input type="submit" class="search" value="搜索" style="font-weight:bold" title="点击搜索">&nbsp;
				
			</form>
            <button onClick="location='?'" class="search" title="退出条件查询">重置</button>
			</nobr>
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

		$content = cut(strip_tags($line["zx_content"]), 200, "…");
		if ($line["hf_log"]) {
			$content .= '<hr class="hf_line">';
			$content .= '<span style="color:#8000FF">'.text_show($line["hf_log"]).'</span>';
		}

		$op = array();
		$op[] = "<button class='button' onclick='huifang(".$id.", this); return false;' class='op'>回访</button>";

		$op[] = "<button class='button' onclick='edit(".$id.", this); return false;' class='op'>修改</button>";

		if ($debug_mode || $username == "admin" || $realname == $line["u_name"] || $realname == $line["w_name"] || $uinfo["character_id"]==71 || $uinfo["character_id"] == 73) {
			$op[] = "<button class='button' onclick='set_yuyue(".$id.", this); return false;' class='op' title='添加到预约系统'>已约</button>";
		}

		
		if ($debug_mode || $username == "admin" || $uinfo["character_id"]==71 || $uinfo["character_id"] == 73) {
			$op[] = "<a href='/m/ziliaoku/ku_list.php?op=delete&id=$id'>删除</a>";
		}
		
		$op_button = implode("&nbsp;", $op);


		$class = "";
		if ($line["is_yuyue"]) {
			$class = "tr_red";
		}
		
		//是否到诊
		$ptable = "patient_".$hid;
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
		<!--<td align="center" class="item"><?php echo $line["vocation"]; ?></td>-->
		<td align="center" class="item"><nobr><?php $area =explode(" ",$line["area"]); echo $area[0]."<br>".$area[1]  ?></nobr></td>
		<td align="center" class="item"><?php echo $line["mobile"]; ?></td>
		<td align="center" class="item"><?php echo $line["qq"]; ?></td>
		<td align="center" class="item"><?php echo $line["weixin"]; ?></td>
		<td align="center" class="item"><nobr><?php $jblx = $line["jblx"]; $jblx1 = explode(",",$jblx); if($jblx1[0]) echo $disease_id_name[$jblx1[0]]."<br>".$jblx1[1] ?></nobr></td>
		<td align="center" class="item"><?php echo $line["intention"]; ?></td>
		<td align="center" class="item"><?php echo $line["laiyuan"]; ?></td>
		<td align="left" class="item"><?php echo $content; ?></td>
        <td align="center" class="item" style=" color:#F00"><nobr><?php  echo nl2br(date("Y-m-d", $line["huifang_nowtime"])); ?></nobr></td>
		<td align="center" class="item" style=" color:#F00" ><?php echo hftimes($line["hf_log"]); ?></td>
		<td align="center" class="item" style=" color:#F00"><nobr><?php echo $line["huifang_nexttime"]; ?></nobr></td>
		<td align="center" class="item"><nobr><?php echo $op_button; ?></nobr></td>
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