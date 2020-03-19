<?php
// --------------------------------------------------------
// - 功能说明 : 网络数据明细
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2013-9-10
// --------------------------------------------------------
require "../../core/core.php";
include "web_config.php";

if ($_GET["dt"] == "") {
	$_GET["dt"] = date("Y-m-d");
}
$dt = str_replace("-", "", $_GET["dt"]);

if ($_GET["sid"] == '') {
	$_GET["sid"] = 1;
}
$sid = intval($_GET["sid"]);

$list = $db->query("select * from count_web where hid='$hid' and date='$dt' and sub_id='$sid' order by kefu asc, sub_id asc", "id");


// 操作的处理:
if ($op) {
	if ($op == "delete") {
		$opid = intval($_GET["id"]);
		$crc = intval($_GET["crc"]);
		if ($opid > 0) {
			$tmp_data = $db->query_first("select * from $table where id='$opid' limit 1");
			if ($tmp_data["addtime"] != $crc) {
				exit("对不起 crc 校验出错 无法删除 ");
			}
			if ($db->query("delete from $table where id='$opid' limit 1")) {
				$op_data[] = $tmp_data;
				$log->add("delete", "删除数据", serialize($op_data));
				msg_box("删除成功", "back", 1);
			}
		} else {
			exit("参数错误...");
		}
	}
}





// 页面开始 ------------------------
?>
<html>
<head>
<title><?php echo $h_name; ?> - 数据明细</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<script src="/res/datejs/picker.js" language="javascript"></script>
<style>
* {font-family:"Tahoma"; }
</style>
<script type="text/javascript">
function del_confirm() {
	return confirm("删除后不能恢复，确定要删除该条数据吗？");
}
</script>
</head>

<body>
<table style="margin:10px 0 0 0px;" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="300" align="left">
		</td>

		<td width="" align="center">
			<form method="GET">
				<input name="dt" value="<?php echo $_GET["dt"]; ?>" class="input" style="width:150px" id="dt"> <img src="/res/img/calendar.gif" id="dt" onClick="picker({el:'dt',dateFmt:'yyyy-MM-dd'})" align="absmiddle" style="cursor:pointer" title="选择日期">&nbsp;
				<select name="sid" class="combo">
					<?php echo list_option($sub_type_arr, "_key_", "_value_", $_GET["sid"]); ?>
				</select>&nbsp;
				<input type="submit" class="button" value="确定">
			</form>
		</td>

		<td width="300" align="right">
		</td>
	</tr>
</table>

<div class="space"></div>

<table width="100%" align="center" class="list">
	<tr>
		<td class="head" align="center">客服</td>
		<td class="head" align="center">分类</td>

		<td class="head" align="center" style="color:red">总点击</td>
		<td class="head" align="center">本地</td>
		<td class="head" align="center">外地</td>
		<td class="head" align="center" style="color:red">总有效</td>
		<td class="head" align="center">本地</td>
		<td class="head" align="center">外地</td>

		<td class="head" align="center" style="color:red">当天约</td>
		<td class="head" align="center">本地</td>
		<td class="head" align="center">外地</td>
		<td class="head" align="center" style="color:red">预计到院</td>
		<td class="head" align="center">本地</td>
		<td class="head" align="center">外地</td>
		<td class="head" align="center" style="color:red">实际到院</td>
		<td class="head" align="center">本地</td>
		<td class="head" align="center">外地</td>

		<td class="head" align="center">添加人</td>
		<td class="head" align="center">操作</td>
	</tr>

<?php
foreach ($list as $li) {
?>

	<tr>
		<td class="item" align="center"><?php echo $li["kefu"]; ?></td>
		<td class="item" align="center"><?php echo $sub_type_arr[$li["sub_id"]]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo $li["click"]; ?></td>
		<td class="item" align="center"><?php echo $li["click_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["click_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["ok_click"]; ?></td>
		<td class="item" align="center"><?php echo $li["ok_click_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["ok_click_other"]; ?></td>

		<td class="item" align="center" style="color:red"><?php echo $li["talk"]; ?></td>
		<td class="item" align="center"><?php echo $li["talk_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["talk_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["orders"]; ?></td>
		<td class="item" align="center"><?php echo $li["order_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["order_other"]; ?></td>
		<td class="item" align="center" style="color:red"><?php echo $li["come"]; ?></td>
		<td class="item" align="center"><?php echo $li["come_local"]; ?></td>
		<td class="item" align="center"><?php echo $li["come_other"]; ?></td>

		<td class="item" align="center" onclick="alert(this.title)" title="<?php echo trim($li["log"]); ?>"><?php echo $li["u_realname"]; ?></td>
		<td class="item" align="center">
			<a href="?op=delete&id=<?php echo $li["id"]; ?>&crc=<?php echo $li["addtime"]; ?>" onclick="return del_confirm();">删除</a>
		</td>
	</tr>

<?php } ?>

</table>

</body>
</html>