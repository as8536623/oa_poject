<?php
header("Content-Type:text/html;charset=gbk");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1970 05:00:00 GMT");
require "../../core/core.php";
if (!$debug_mode) {
	exit_html("无执行权限...");
}

set_time_limit(100);

if ($op == "phpinfo") {
	phpinfo();
	exit;
}

// 更新字段
if ($op == "update_field") {
	include_once ROOT."core/function.create_table.php";

	// 读取需更新的每家医院:
	$hids = $db->query("select id from hospital", "", "id");

	echo "正在处理，请稍候...".str_repeat("&nbsp;", 50)."<br>";
	flush();
	ob_flush();
	ob_end_flush();

	// 要处理的表:
	$table_names = array();
	$table_names[] = "patient";
	foreach ($hids as $hid) {
		$table_names[] = "patient_".$hid;
	}

	// 处理:
	foreach ($table_names as $table_name) {
		$cs = $db_tables["patient"];

		if (!table_exists($table_name, $db->dblink)) {
			//$db->query(str_replace("{hid}", $hid, $cs));
			//echo "创建表 ".$table_name." <br>";
		} else {
			$fields = parse_fields($cs);
			foreach ($fields as $f => $fs) {
				if (!field_exists($f, $table_name, $db->dblink)) {
					$tm = array_keys($fields);
					$tm2 = array_search($f, $tm);
					if ($tm2 == 0) {
						$pos = " first";
					} else {
						$pos = " after `".$tm[$tm2-1]."`";
					}
					$sql = "alter table `".$table_name."` add ".$fs.$pos;
					$db->query($sql);
					echo "表 ".$table_name." 添加字段 ".$f." ";
				}
			}
		}

		echo $table_name." 已完成<br>";

		flush();
		ob_flush();
		ob_end_flush();
		usleep(100000);
	}

	echo "<br>全部完成。";
	exit;
}

if ($op == "move_old_data") {
	$the_date = strtotime("-1 years");

	$tlist = mysql_query("show tables");
	$tables = array();
	while ($li = mysql_fetch_array($tlist)) {
		$tables[] = $li[0];
	}

	$hid_name_arr = $db->query("select id,name from hospital order by id asc", "id", "name");
	foreach ($hid_name_arr as $_hid => $_hname) {
		$table = "patient_{$_hid}";
		$backup_table = "patient_{$_hid}_history";

		// 是否需要创建历史记录表结构:
		if (!in_array($backup_table, $tables)) {
			$sql = "create table $backup_table (select * from $table limit 1)";
			$db->query($sql);
			$sql = "truncate table $backup_table";
			$db->query($sql);
		}

		// 转移数据：
		$sql = "insert into $backup_table (select * from $table where order_date<".$the_date.")";
		$db->query($sql);
		$num = mysql_affected_rows();
		if ($num > 0) {
			$sql = "delete from $table where order_date<".$the_date;
			$db->query($sql);
		}

		echo $_hid.":".$_hname." 处理完毕，共转移了".intval($num)."条数据<br>";
		flush();
		ob_flush();
		ob_end_flush();
		sleep(1);
	}

	echo "<br>全部处理完成。";
	exit;
}


if ($op == "talk_to_file") {

	// 系统的所有表
	$tlist = mysql_query("show tables");
	$tables = array();
	while ($li = mysql_fetch_array($tlist)) {
		$tables[] = $li[0];
	}

	$hid_name_arr = $db->query("select id,name from hospital order by id asc", "id", "name");
	foreach ($hid_name_arr as $_hid => $_hname) {

		// 建立医院目录:
		$hdir = ROOT."data/".$_hid."/";
		if (!file_exists($hdir)) {
			@mkdir($hdir, 0777);
		}
		@chmod($hdir, 0777);
		if (!file_exists($hdir)) {
			exit($hdir." 建立不成功，请检查好权限。");
		}

		// 处理当前表
		$table = "patient_{$_hid}";
		$ids = $db->query("select id from $table where length(content)>200", "", "id");
		foreach ($ids as $_id) {
			// 分目录：每个目录下最多放1000个文件
			$dir_name = ceil($_id / 1000);
			$dir = $hdir.$dir_name."/";
			if (!file_exists($dir)) {
				@mkdir($dir, 0777);
			}
			@chmod($dir, 0777);
			if (!file_exists($dir)) {
				exit($dir." 建立不成功，请检查好权限。");
			}
			$line = $db->query("select content from $table where id=$_id limit 1", 1);
			$content = $line["content"];
			$len = @file_put_contents($dir.$_id.".txt", $content);

			// 成功后将该记录字段标记
			if ($len > 0) {
				$db->query("update $table set content='@' where id=$_id limit 1");
			}
		}
		$count1 = count($ids);


		// 处理历史记录表
		$table = "patient_{$_hid}_history";
		$count2 = 0;
		if (in_array($table, $tables)) {
			$ids = $db->query("select id from $table where length(content)>200", "", "id");
			foreach ($ids as $_id) {
				// 分目录：每个目录下最多放1000个文件
				$dir_name = ceil($_id / 1000);
				$dir = $hdir.$dir_name."/";
				if (!file_exists($dir)) {
					@mkdir($dir, 0777);
				}
				@chmod($dir, 0777);
				if (!file_exists($dir)) {
					exit($dir." 建立不成功，请检查好权限。");
				}
				$line = $db->query("select content from $table where id=$_id limit 1", 1);
				$content = $line["content"];
				$len = @file_put_contents($dir.$_id.".txt", $content);

				// 成功后将该记录字段标记
				if ($len > 0) {
					$db->query("update $table set content='@' where id=$_id limit 1");
				}
			}
			$count2 = count($ids);
		}

		echo $_hid.":".$_hname." 处理完毕，存储了".($count1+$count2)."条聊天记录<br>";

		flush();
		ob_flush();
		ob_end_flush();
		sleep(1);
	}

	echo "<br>全部处理完成。";
	exit;
}


// 从一个创建表的语句中解读字段
function parse_fields($s) {
	$list = explode("\n", $s);
	$out = array();
	foreach ($list as $k) {
		$k = trim($k);
		if (substr($k, 0, 1) == "`") {
			$fname = ltrim($k, "`");
			list($sa, $sb) = explode(" ", $fname, 2);
			$sa = rtrim($sa, "`");
			$out[$sa] = rtrim(trim($k), ',');
		}
	}

	return $out;
}

?>
<html>
<head>
<title>调试工具</title>
<meta http-equiv="Content-Type" content="text/html;charset=gbk">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>
<style>
.head {padding:5px 10px 3px 10px !important;}
.item {padding:20px 10px 18px 10px !important; }
a {font-weight:bold; color:#FF8040; font-size:14px; }
</style>
<script type="text/javascript">
function confirm_op() {
	return confirm("此操作十分危险，仅限开发人员使用。\r\n\r\n如果你不是开发人员，不知道后果，请立即点击“取消”。否则问题可能非常严重。慎之！\r\n\r\n是否确定继续？");
}
</script>
</head>

<body>
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center">调试工具</td><td class="bar_right"></td></tr></table></div>
	<div class="header_center"></div>
	<div class="headers_oprate"><input type="button" value="返回" onclick="history.back()" class="button" title="返回上一页"></div>
</div>

<div class="space"></div>

<table width="100%" align="center" class="list">
	<tr>
		<td class="head" align="center" width="32">序号</td>
		<td class="head" align="left" width="100">功能</td>
		<td class="head" align="left" width="">简介</td>
	</tr>
	<tr>
		<td align="center" class="item">1</td>
		<td align="left" class="item"><a href="?op=phpinfo">查看phpinfo()</a></td>
		<td align="left" class="item">查看服务器phpinfo，以此诊断出现何种问题。</td>
	</tr>
	<tr>
		<td align="center" class="item">2</td>
		<td align="left" class="item"><a href="?op=update_field">更新patient表结构</a></td>
		<td align="left" class="item">当 patient 基本结构变更时，使用此工具将变更应用到系统所有 patient 表中。</td>
	</tr>
	<tr>
		<td align="center" class="item">3</td>
		<td align="left" class="item"><a href="?op=move_old_data" onclick="return confirm_op()">将一年前的旧数据移到历史表中</a></td>
		<td align="left" class="item">将一年前的旧数据移到历史表中，按预约时间算。</td>
	</tr>
	<tr>
		<td align="center" class="item">4</td>
		<td align="left" class="item"><a href="?op=talk_to_file" onclick="return confirm_op()">将聊天内容存放到文本文件中</a></td>
		<td align="left" class="item">将聊天内容存放到文本文件中，可以使数据库压力减小</td>
	</tr>
</table>


</body>
</html>