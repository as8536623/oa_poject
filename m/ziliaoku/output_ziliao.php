<?php
/*
// - 功能说明 : 导出病人
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2011-02-28
*/
require "../../core/core.php";

set_time_limit(0);

if ($hid == 0) {
	exit_html("对不起，没有选择医院，不能执行该操作！");
}

$table = "ku_list";
$h_name = $db->query("select name from hospital where id=$hid limit 1", 1, "name");

$time_array = array("addtime"=>"登记时间", "huifang_nowtime"=>"回访时间");
$status_array = array("all"=>"不限", "come"=>"已预约", "not"=>"未预约");
$sort_array = array("addtime"=>"登记时间", "huifang_nowtime"=>"回访时间", "name"=>"名字");
$part_array = array("pall"=>"不限", "dianhua"=>"电话", "wxqq"=>"微信/QQ");
$depart_array = $db->query("select id,name from depart where hospital_id='$hid'", "id", "name");


$op = $_GET["op"];

// 定义当前页需要用到的调用参数:
$aLinkInfo = array(
	"page" => "page",
	"op" => "op",
	"ty" => "ty",
	"btime" => "btime",
	"etime" => "etime",
	"status" => "status",
	"part" => "part",
	"depart" => "depart",
	"sort" => "sort",
	"export" => "export",
	"lastHfTime" => "lastHfTime",
	"searchtype1" => "searchtype1",
	"searchword1" => "searchword1",
	
);

// 读取页面调用参数:
foreach ($aLinkInfo as $local_var_name => $call_var_name) {
	$$local_var_name = $_GET[$call_var_name];
}

$display = "none";

// 处理时间:
if ($op == "show") {
	$where = "";
	$where[] = "hid = $hid";
	
	if ($_GET["ty"] == '') $time_ty = "addtime";
	else $time_ty = $_GET["ty"];
	

	if ($_GET["btime"]) {
		$tb = strtotime($_GET["btime"]." 0:0:0");
		$where[] = "$time_ty>=$tb";
	}
	if ($_GET["etime"]) {
		$te = strtotime($_GET["etime"]." 23:59:59");
		$where[] = "$time_ty<$te";
	}

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

	if ($lastHfTime) {
		$thf = strtotime($lastHfTime." 23:59:59");
		$where[] = "huifang_nowtime<=$thf";
	}

	if ($_GET["status"] == '') $_GET["status"] = "not";
	if ($st = $_GET["status"]) {
		if ($st != "all") {
			$where[] = ($st == "come") ? "is_yuyue=1" : "is_yuyue=0";
		}
	}

	if ($_GET["part"] == '') $_GET["part"] = "dianhua"; 
	if ($part = $_GET["part"]){
		if ($part != "pall") {
			$where[] = ($part == "dianhua") ? "LENGTH(mobile)>3" : "LENGTH(mobile)<3";
		}
	}

	//客服只能看到自己的数据
	$data_power = @explode(",", $uinfo["data_power"]);
	$_limit = array();
	if (!in_array("all", $data_power) || $uinfo["part_id"] == 2) {
		$_limit[] = "u_name='".$realname."'";
		$_limit[] = "w_name='".$realname."'";
		$where[] = "(".implode(" or ", $_limit).")";
	}
	
	
	$sqlwhere = count($where) ? ("where ".implode(" and ", $where)) : "";

	$sort = $_GET["sort"] ? $_GET["sort"] : "addtime";
	
	// 分页数据:
	$count = $db->query("select count(*) as c from $table $sqlwhere", 1, "c");
	$pagecount = max(ceil($count / $pagesize), 1);
	$page = max(min($pagecount, intval($page)), 1);
	$offset = ($page - 1) * $pagesize;
	$list1 = $db->query("select * from $table $sqlwhere order by $sort asc limit $offset,$pagesize");

	$list = $db->query("select * from $table $sqlwhere order by $sort asc", "");
	
	
	

	// 输出:
	$fields = array(
		"addtime"=>"登记时间",
		"u_name"=>"登记人",
		"w_name"=>"维护人",
		"name"=>"姓名",
		"sex"=>"性别",
		"age"=>"年龄",
		"vocation"=>"职业",
		"area"=>"地区",
		"mobile"=>"电话号码",
		"qq"=>"QQ",
		"weixin"=>"微信",
		"jblx"=>"疾病类型",
		"intention"=>"就诊意向",
		"laiyuan"=>"关键词",
		"media_from"=>'媒体来源',
		"from_site"=>'渠道网址',
		"zx_content"=>"咨询内容",
		"hf_log"=>"回访内容",
		"huifang_nowtime"=>"当前回访时间",
		"is_yuyue"=>"是否预约",
		"is_arrive"=>"是否到院"		
	);
	
	// 疾病类型转换:
	if ($fields["jblx"]) {
		$disease_id_name = $db->query("select id,name from disease", "id", "name");
	}
	

	
	
	if($export = $_GET["export"]){
		include  "../../core/exportExcel.php";
		$output_name = array();
	foreach ($list as $li) {
		
		//是否到院
		$ptable = "patient_".$li["hid"];
		$dzcheck = $db->query("select status from $ptable where lid = ".$li["id"]." limit 1",1);
		$li["is_arrive"] = $dzcheck["status"];
		
		$line = array();
		foreach ($fields as $k=>$v) {
			if ($k == "addtime") {
				$y = @date("Y-m-d H:i:s", $li[$k]);
			} else if ($k == "is_yuyue" || $k == "is_arrive") {
				$y = ($li[$k] == 1) ? "是":"否";
			} else if ($k == "jblx") {
				$jblx = explode(',',$li[$k]);
				$y = $disease_id_name[$jblx[0]]." ".$jblx[1];
			} else if ($k == "huifang_nowtime"){
				$y = @date("Y-m-d", $li[$k]);
			} else {
				$y = $li[$k];
			}
			// 替换所有回车换行为空格:
			$y = str_replace("\n", " ", str_replace("\r", "", $y));
			// 压缩两边空格
			$y = trim($y);
			// 多个空格替换为一个:
			while (substr_count($y, "  ") > 0) {
				$y = str_replace("  ", " ", $y);
			}
			
			// 空值显示横线作为占位
			$line[] = (trim($y) == "" ? "-" : $y);
			
		}
		$output_name[] = $line;
	}
		$exportexcel = exportExcel($h_name,array_values($fields),$output_name);
		$display  = ($display == "none") ? "none":"block";
	}else{
		$display  = "block";
	}
	

	
	
	

}



$title = '导出数据';
?>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<style>
#tiaojian {margin:10px 0 0 30px; }
form {display:inline; }

#result {margin-left:30px; margin-top:10px; }
.h_name {font-weight:bold; margin-top:20px; }
.h_kf {margin-left:20px; }
.kf_li {border-bottom:0px dotted silver; }
</style>
<script type="text/javascript">
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


// 修改 page_param 表单的某个请求参数
function page_param_update(name, value, is_submit) {
	var is_found = 0;
	var el = byid("searchorexport").getElementsByTagName("INPUT");
	for (var i = 0; i < el.length; i++) {
		if (el[i].name == name) {
			el[i].value = value;
			is_found = 1;
			break;
		}
	}
	if (!is_found) {
		var s = '<input type="hidden" name="'+name+'" value="'+value+'" />';
		byid("searchorexport").insertAdjacentHTML("beforeEnd", s);
	}
	if (is_submit) {
		page_param_submit();
	}
}

// 删除某个请求参数
function page_param_del(name) {
	var el = byid("searchorexport").getElementsByTagName("INPUT");
	for (var i = 0; i < el.length; i++) {
		if (el[i].name == name) {
			el[i].value = '';
			el[i].parentNode.removeChild(el[i]);
			return true;
		}
	}
}

function page_param_submit() {
	byid("searchorexport").submit();
}
</script>
</head>

<body>
<!-- 头部 begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $h_name." ".$title; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"><button onClick="history.back()" class="button">返回</button></div>
</div>
<!-- 头部 end -->

<div class="space"></div>
<div id="tiaojian">
<span>设置条件：</span>
<form method="GET" id="searchorexport">
	<select name="ty" class="combo">
		<option value="" style="color:gray">-时间类型-</option>
		<?php echo list_option($time_array, "_key_", "_value_", $time_ty); ?>
	</select>&nbsp;
	<input name="btime" id="begin_time" class="input" style="width:80px" value="<?php echo $_GET["btime"] ? $_GET["btime"] : date("Y-m-01"); ?>">
	<img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'begin_time',dateFmt:'yyyy-MM-dd',maxDate:'#F{$dp.$D(\'end_time\')}' })" align="absmiddle" style="cursor:pointer" title="选择时间">

	<input name="etime" id="end_time" class="input" style="width:80px" value="<?php echo $_GET["etime"] ? $_GET["etime"] : date("Y-m-d"); ?>">
	<img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'end_time',dateFmt:'yyyy-MM-dd',minDate:'#F{$dp.$D(\'begin_time\')}' })" align="absmiddle" style="cursor:pointer" title="选择时间">
	<select name="searchtype1" class="combo" style="width:100px">
		<?php echo list_option(array("所有", "年龄",  "地区", "疾病类型", "就诊意向", "关键词"), "_value_", "_value_", $_GET["searchtype1"]); ?>
	</select>
	<input name="searchword1" value="<?php echo $_GET["searchword1"]; ?>" class="input" size="12" placeholder="搜索关键词">&nbsp;
	回访时间：
	<input name="lastHfTime" id="lastHfTime" class="input" style="width:80px" value="<?php echo $_GET["lastHfTime"] ? $_GET["lastHfTime"] : ''; ?>" placeholder="最后回访时间">
	<img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'lastHfTime',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="选择时间">
	<select name="status" class="combo">
		<option value="" style="color:gray">-是否预约-</option>
		<?php echo list_option($status_array, "_key_", "_value_", $_GET["status"]); ?>
	</select>&nbsp;
	<select name="sort" class="combo">
		<option value="" style="color:gray">-结果排序-</option>
		<?php echo list_option($sort_array, "_key_", "_value_", $_GET["sort"]); ?>
	</select>&nbsp;
	<select name="part" class="combo">
		<option value="" style="color:gray">-联系方式-</option>
		<?php echo list_option($part_array, "_key_", "_value_", $_GET["part"]); ?>
	</select>&nbsp;<br><br>
	
	<input type="button" class="button" onClick="page_param_update('export','0', 1);" value="查询">&nbsp;
    <input type="button" class="button" onClick="page_param_update('export','1', 1);" value="导出">
	<input type="hidden" name="op" value="show">
</form>
</div>
<div class="space"></div>
<div style="display:<?php echo $display ?>">
<table id="list" style="" width="100%" align="center" class="list sortable">
	<tr>
    	<?php 
			foreach($fields as $field => $fieldvalue){
				if($field != "" && $field != "hf_log"){
		?>
        <td class="head" align="center"><?php echo $fieldvalue; ?></td>
        <?php
			}
		}
		?>
    </tr>
    <?php
	if(count($list1) > 0){
		foreach($list1 as $line1){
			//回访内容
			$content = cut(strip_tags($line1["zx_content"]), 200, "…");
			if ($line1["hf_log"]) {
				$content .= '<hr class="hf_line">';
				$content .= '<span style="color:#8000FF">'.text_show($line1["hf_log"]).'</span>';
			}
			
			//是否到院
			$ptable = "patient_".$line1["hid"];
			$dzcheck = $db->query("select status from $ptable where lid = ".$line1["id"]." limit 1",1);
			$dzcheck["status"];
	?>
    <tr onMouseOver="mi(this)" onMouseOut="mo(this)">
        <td align="center" class="item"><nobr><?php date_default_timezone_set('PRC'); echo nl2br(date("Y-m-d\nH:i", $line1["addtime"])); ?></nobr></td>
        <td align="center" class="item"><nobr><?php echo $line1["u_name"]; ?></nobr></td>
        <td align="center" class="item"><nobr><?php echo $line1["w_name"]; ?></nobr></td>
        <td align="center" class="item"><nobr><?php echo $line1["name"]; ?></nobr></td>
        <td align="center" class="item"><?php echo $line1["sex"]; ?></td>
        <td align="center" class="item"><nobr><?php echo $line1["age"]; ?></nobr></td>
        <td align="center" class="item"><?php echo $line1["vocation"]; ?></td>
        <td align="center" class="item"><nobr><?php $area = explode(' ',$line1["area"]); echo $area[0]."<br>".$area[1]; ?></nobr></nobr></td>
        <td align="center" class="item"><?php echo $line1["mobile"]; ?></td>
        <td align="center" class="item"><?php echo $line1["qq"]; ?></td>
        <td align="center" class="item"><?php echo $line1["weixin"]; ?></td>
        <td align="center" class="item"><nobr><?php $jblx = $line1["jblx"]; $jblx1 = explode(",",$jblx); if($jblx1[0]) echo $disease_id_name[$jblx1[0]]."<br>".$jblx1[1] ?></nobr></td>
        <td align="center" class="item"><?php echo $line1["intention"]; ?></td>
        <td align="center" class="item"><?php echo $line1["laiyuan"]; ?></td>
        <td align="center" class="item"><?php echo $line1["media_from"]; ?></td>
        <td align="center" class="item"><?php echo $line1["from_site"]; ?></td>
        <!--<td align="left" class="item" title="<?php echo $line1["zx_content"]; ?>"><?php if(strlen($line1["zx_content"]) > 130) echo cut($line1["zx_content"], 130, "…"); else echo $line1["zx_content"]; ?></td>-->
        <td align="left" class="item"><?php echo $content; ?></td>
		<td align="center" class="item"><nobr><?php date_default_timezone_set('PRC'); echo nl2br(date("Y-m-d", $line1["huifang_nowtime"])); ?></nobr></td>
        <td align="center" class="item"><?php echo ($line1["is_yuyue"] == 0)? "否":"是"; ?></td>
        <td align="center" class="item"><?php echo ($dzcheck["status"] == 1)? "是":"否"; ?></td>
    </tr>
    <?php
		}
	}
	?>
</table>
<!-- 数据列表 end -->

<div class="space"></div>

<!-- 分页链接 begin -->
<div class="footer_op">
	<div class="footer_op_left">&nbsp;共 <b><?php echo $count; ?></b> 条资料</div>
	<div class="footer_op_right"><?php echo pagelinkc($page, $pagecount, $count, make_link_info($aLinkInfo, "page"), "button"); ?></div>
</div>
<!-- 分页链接 end -->
</div>

</body>
</html>