<?php
/*
// - ����˵�� : ��������
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2011-02-28
*/
require "../../core/core.php";

set_time_limit(0);

if ($hid == 0) {
	exit_html("�Բ���û��ѡ��ҽԺ������ִ�иò�����");
}

$table = "patient_".$hid;
$h_name = $db->query("select name from hospital where id=$hid limit 1", 1, "name");

$time_array = array("order_date"=>"��Ժ����ʱ��", "addtime"=>"ԤԼ����ʱ��", "djsj"=>"�Ǽ�ʱ��");
$status_array = array("all"=>"����", "come"=>"�ѵ�", "not"=>"δ��");
$sort_array = array("order_date"=>"��Ժ����ʱ��", "name"=>"����");
$part_array = array("2"=>"����", "3"=>"�绰");
$depart_array = $db->query("select id,name from disease where hospital_id='$hid'", "id", "name");


$op = $_GET["op"];

// ���嵱ǰҳ��Ҫ�õ��ĵ��ò���:
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
	
);

// ��ȡҳ����ò���:
foreach ($aLinkInfo as $local_var_name => $call_var_name) {
	$$local_var_name = $_GET[$call_var_name];
}


$display = "none";

// ����ʱ��:
if ($op == "show") {
	$where = "";

	
	if ($_GET["ty"]) {
		$time_ty = $_GET["ty"];
		//echo $time_ty;
	}else{
		$time_ty = "order_date";
		}

	if ($_GET["btime"]) {
		$tb = strtotime($_GET["btime"]." 0:0:0");
		$where[] = "$time_ty>=$tb";
	}
	if ($_GET["etime"]) {
		$te = strtotime($_GET["etime"]." 23:59:59");
		$where[] = "$time_ty<$te";
	}

	if ($_GET["status"] == '') $_GET["status"] = "come";
	if ($st = $_GET["status"]) {
		if ($st != "all") {
			$where[] = ($st == "come") ? "status=1" : "status<>1";
		}
	}

	if ($_GET["part"]) {
		$where[] = "part_id=".intval($_GET["part"]);
	}

	if ($_GET["depart"]) {
		$where[] = "disease_id=".intval($_GET["depart"]);
	}

	//�ͷ�ֻ�ܿ����Լ�������
	$data_power = @explode(",", $uinfo["data_power"]);
	$_limit = array();
	if (!in_array("all", $data_power) || $uinfo["part_id"] == 2) {
		$_limit[] = "author='".$realname."'";
		$_limit[] = "w_name='".$realname."'";
		$where[] = "(".implode(" or ", $_limit).")";
	}
	

	$sqlwhere = count($where) ? ("where ".implode(" and ", $where)) : "";
	//echo $sqlwhere;

	$sort = $_GET["sort"] ? $_GET["sort"] : "order_date";
	
	// ��ҳ����:
	$count = $db->query("select count(*) as c from $table $sqlwhere", 1, "c");
	$pagecount = max(ceil($count / $pagesize), 1);
	$page = max(min($pagecount, intval($page)), 1);
	$offset = ($page - 1) * $pagesize;
	$list1 = $db->query("select * from $table $sqlwhere order by $sort asc limit $offset,$pagesize");
	
	
	$list = $db->query("select * from $table $sqlwhere order by $sort asc", "");

	// ���:
	$fields = array(
		"name"=>"����",
		"sex"=>"�Ա�",
		"age"=>"����",
		"vocation"=>"ְҵ",
		"tel"=>"�绰����",
		"ordernum"=>"ԤԼ��",
		"zhuanjia_num"=>"�����",
		"doctor"=>"����ҽ��",
		"disease_id"=>"����",
		"disease_2"=>"��������",
		"content"=>"��ѯ����",
		"media_from"=>"ý��",
		"engine_key"=>"�ؼ���",
		"from_site"=>"������ַ",
		"memo"=>"��ע",
		"area"=>"����",
		"author"=>"�Ǽ���",
		"w_name"=>"ά����",
		"order_date"=>"��Ժ����ʱ��",
		"addtime"=>"ԤԼ����ʱ��",
		"djsj"=>"�Ǽ�ʱ��"
	);

	// ��������ת��:
	if ($fields["disease_id"]) {
		$disease_id_name = $db->query("select id,name from disease", "id", "name");
	}

	$output_name = array();
	foreach ($list as $li) {
		$line = array();
		foreach ($fields as $k=>$v) {
			if ($k == "order_date" || $k == "addtime" || $k == "djsj") {
				$y = @date("Y-m-d H:i:s", $li[$k]);
			} else if ($k == "disease_id") {
				$y = $disease_id_name[$li[$k]];
			} else {
				$y = $li[$k];
			}
			// �滻���лس�����Ϊ�ո�:
			$y = str_replace("\n", " ", str_replace("\r", "", $y));
			// ѹ�����߿ո�
			$y = trim($y);
			// ����ո��滻Ϊһ��:
			while (substr_count($y, "  ") > 0) {
				$y = str_replace("  ", " ", $y);
			}
			
			
			// ��ֵ��ʾ������Ϊռλ
			$line[$k] = (trim($y) == "" ? "-" : $y);
		}
		$output_name[] = $line;
	}
	
	if($export = $_GET["export"]){
		include  "../../core/exportExcel.php";
		$exportexcel = exportExcel($h_name,array_values($fields),$output_name);
		$display  = ($display == "none") ? "none":"block";
		
	}else{
		$display  = "block";
	}
	
	

	
	
	

}



$title = '��������';
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


// �޸� page_param ����ĳ���������
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

// ɾ��ĳ���������
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
<!-- ͷ�� begin -->
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center"><?php echo $h_name." ".$title; ?></td><td class="bar_right"></td></tr></table></div>
	<div class="headers_oprate"><button onClick="history.back()" class="button">����</button></div>
</div>
<!-- ͷ�� end -->

<div class="space"></div>
<div id="tiaojian">
<span>����������</span>
<form id="searchorexport" method="GET">
	<select name="ty" class="combo">
		<option value="" style="color:gray">-ʱ������-</option>
		<?php echo list_option($time_array, "_key_", "_value_", $time_ty); ?>
	</select>&nbsp;
	<input name="btime" id="begin_time" class="input" style="width:80px" value="<?php echo $_GET["btime"] ? $_GET["btime"] : date("Y-m-01"); ?>">
	<img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'begin_time',dateFmt:'yyyy-MM-dd',maxDate:'#F{$dp.$D(\'end_time\')}' })" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��">

	<input name="etime" id="end_time" class="input" style="width:80px" value="<?php echo $_GET["etime"] ? $_GET["etime"] : date("Y-m-d"); ?>">
	<img src="/res/img/calendar.gif" id="order_date" onClick="picker({el:'end_time',dateFmt:'yyyy-MM-dd',minDate:'#F{$dp.$D(\'begin_time\')}' })" align="absmiddle" style="cursor:pointer" title="ѡ��ʱ��">

	<select name="status" class="combo">
		<option value="" style="color:gray">-�Ƿ�Ժ-</option>
		<?php echo list_option($status_array, "_key_", "_value_", $_GET["status"]); ?>
	</select>&nbsp;
	<select name="sort" class="combo">
		<option value="" style="color:gray">-�������-</option>
		<?php echo list_option($sort_array, "_key_", "_value_", $_GET["sort"]); ?>
	</select>&nbsp;
	<select name="part" class="combo">
		<option value="" style="color:gray">-����-</option>
		<?php echo list_option($part_array, "_key_", "_value_", $_GET["part"]); ?>
	</select>&nbsp;
	<select name="depart" class="combo">
		<option value="" style="color:gray">-����-</option>
		<?php echo list_option($depart_array , "_key_", "_value_", $_GET["depart"]); ?>
	</select>&nbsp;
	<br>
    <br>
	<input type="button" class="button" onClick="page_param_update('export','0', 1);" value="��ѯ">&nbsp;
    <input type="button" class="button" onClick="page_param_update('export','1', 1);" value="����">
	<input type="hidden" name="op" value="show"> 
</form>
</div>
<div class="space"></div>
<div style="display:<?php echo $display ?>">
<table id="list" style="" width="100%" align="center" class="list sortable">
	<tr>
    	<?php 
			foreach($fields as $field => $fieldvalue){
				if($field != "")
		?>
        <td class="head" align="center"><?php echo $fieldvalue; ?></td>
        <?php
			}
		?>
    </tr>
    <?php
	if(count($list1) > 0){
		foreach($list1 as $line1){
	?>
    <tr onMouseOver="mi(this)" onMouseOut="mo(this)">
        <td align="center" class="item"><nobr><?php echo $line1["name"]; ?></nobr></td>
        <td align="center" class="item"><nobr><?php echo $line1["sex"]; ?></nobr></td>
        <td align="center" class="item"><nobr><?php echo $line1["age"]; ?></nobr></td>
        <td align="center" class="item"><?php echo $line1["vocation"]; ?></td>
        <td align="center" class="item"><nobr><?php echo $line1["tel"]; ?></nobr></td>
        <td align="center" class="item"><nobr><?php echo $line1["ordernum"]; ?></nobr></td>
        <td align="center" class="item"><nobr><?php echo $line1["zhuanjia_num"]; ?></nobr></td>
        <td align="center" class="item"><nobr><?php echo $line1["doctor"]; ?></nobr></td>
        <td align="center" class="item"><nobr><?php echo $disease_id_name[$line1["disease_id"]]; ?></nobr></td>
        <td align="center" class="item"><nobr><?php echo $line1["disease_2"]; ?></nobr></td>
        <td align="left" class="item" title="<?php echo $line1["content"]; ?>"><?php if(strlen($line1["content"]) > 120) echo cut($line1["content"], 120, "��"); else echo $line1["content"]; ?></td>
        <td align="center" class="item"><?php echo $line1["media_from"]; ?></td>
        <td align="center" class="item"><?php echo $line1["engine_key"]; ?></td>
        <td align="center" class="item"><?php echo $line1["from_site"]; ?></td>
        <td align="center" class="item" style="width:100px;"><?php echo $line1["memo"]; ?></td>
        <td align="center" class="item"><nobr><?php $area = explode(' ',$line1["area"]); echo $area[0]."<br>".$area[1]; ?></nobr></td>
        <td align="center" class="item"><nobr><?php echo $line1["author"]; ?></nobr></td>
        <td align="center" class="item"><nobr><?php echo $line1["w_name"]; ?></nobr></td>
        <td align="center" class="item"><nobr><?php date_default_timezone_set('PRC');  echo nl2br(date("Y-m-d\nH:i", $line1["order_date"])); ?></nobr></td>
        <td align="center" class="item"><nobr><?php echo nl2br(date("Y-m-d\nH:i", $line1["addtime"])); ?></nobr></td>
        <td align="center" class="item"><nobr><?php echo nl2br(date("Y-m-d\nH:i", $line1["djsj"])); ?></nobr></td>
    </tr>
    <?php
		}
	}
	?>
</table>
<!-- �����б� end -->

<div class="space"></div>

<!-- ��ҳ���� begin -->
<div class="footer_op">
	<div class="footer_op_left">&nbsp;�� <b><?php echo $count; ?></b> ������</div>
	<div class="footer_op_right"><?php echo pagelinkc($page, $pagecount, $count, make_link_info($aLinkInfo, "page"), "button"); ?></div>
</div>
<!-- ��ҳ���� end -->
</div>

</body>
</html>