<?php
/*
// - 功能说明 : 切换医院
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2011-07-23
*/
define("WEE_MAIN", "1");
require "../core/core.php";
$hospital_changed = 0;

if ($_GET["do"] == 'change') {
	$hospital_changed = 1;
	$_SESSION["hospital_id"] = intval($_GET["hospital_id"]);
	$hid = $hid = $_SESSION["hospital_id"];
	$h_name = $db->query("select name from hospital where id=$hid limit 1", 1, "name");

	// 切换成功提示
	echo '<script type="text/javascript">';
	echo 'parent.update_content();';
	echo 'parent.load_box(0);';
	echo 'parent.msg_box("切换至：'.$h_name.'");';
	echo '</script>';
	exit;
}

// 切换医院下拉列表:
$options = array();
$hids = implode(",", $hospital_ids);
$h_list = $db->query("select id,name,sort from hospital where id in ($hids) order by sort desc, name asc", "id");

?>
<html>
<head>
<title>切换医院 (红色标记为当前医院)</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style>
.linered {color:red; }
.linered td {background:; font-weight:bold; }
.linered a {color:red; }
.head, .item, .group {border:1px solid #ebebeb !important; border-left:0 !important; border-right:0 !important; }
.group {background-color:#EAEAEA !important; }

<?php if (count($h_list) <= 4) { ?>
#hospital_list {height:200px; min-height:200px; }
<?php } ?>
<?php if (count($h_list) > 20) { ?>
#hospital_list {border:2px solid #E6E6E6; }
#hospital_list {height:500px; max-height:500px; overflow-y:scroll; }
.list {border:0; }
<?php } ?>
</style>
<script language="javascript">

</script>
</head>

<body>

<div id="hospital_list" style="display:none;">
<table width="100%" align="center" class="list" id="hospital_table">
	<tr>
		<td class="head" align="center" width="10"></td>
		<td class="head" align="center" width="50">ID</td>
		<td class="head" align="left">&nbsp;医院名称</td>
		<td class="head" align="left" width="80">&nbsp;优先度</td>
		<td class="head" align="center" width="80">操作</td>
	</tr>


<?php
	foreach ($h_list as $id => $v) {
		$class = $hid == $id ? "linered" : "";
?>
	<tr class="<?php echo $class; ?>">
		<td align="center" class="item"></td>
		<td align="center" class="item"><?php echo $id; ?></td>
		<td align="left" class="item">&nbsp;<a href='?do=change&hospital_id=<?php echo $id; ?>' class='op' title="点击切换到此医院"><?php echo $v["name"]; ?></a></td>
		<td align="left" class="item">&nbsp;<?php echo $v["sort"]; ?></td>
		<td align="center" class="item"><a id="hid_<?php echo $id; ?>" href='?do=change&hospital_id=<?php echo $id; ?>' class='op'>进入</a></td>
	</tr>
<?php } ?>

</table>

</div>

<script type="text/javascript">
function do_init() {
	var cur_id = "<?php echo intval($hid); ?>";
	byid("hospital_list").style.display = "block";
	if (cur_id > 0) {
		byid('hid_'+cur_id).scrollIntoView();
	}
}
setTimeout("do_init()", 100);
</script>


</body>
</html>