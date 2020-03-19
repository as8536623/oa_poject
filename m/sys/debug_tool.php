<?php
header("Content-Type:text/html;charset=gbk");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1970 05:00:00 GMT");
require "../../core/core.php";
if (!$debug_mode) {
	exit_html("��ִ��Ȩ��...");
}

set_time_limit(100);

if ($op == "phpinfo") {
	phpinfo();
	exit;
}

// �����ֶ�
if ($op == "update_field") {
	include_once ROOT."core/function.create_table.php";

	// ��ȡ����µ�ÿ��ҽԺ:
	$hids = $db->query("select id from hospital", "", "id");

	echo "���ڴ������Ժ�...".str_repeat("&nbsp;", 50)."<br>";
	flush();
	ob_flush();
	ob_end_flush();

	// Ҫ����ı�:
	$table_names = array();
	$table_names[] = "patient";
	foreach ($hids as $hid) {
		$table_names[] = "patient_".$hid;
	}

	// ����:
	foreach ($table_names as $table_name) {
		$cs = $db_tables["patient"];

		if (!table_exists($table_name, $db->dblink)) {
			//$db->query(str_replace("{hid}", $hid, $cs));
			//echo "������ ".$table_name." <br>";
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
					echo "�� ".$table_name." ����ֶ� ".$f." ";
				}
			}
		}

		echo $table_name." �����<br>";

		flush();
		ob_flush();
		ob_end_flush();
		usleep(100000);
	}

	echo "<br>ȫ����ɡ�";
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

		// �Ƿ���Ҫ������ʷ��¼��ṹ:
		if (!in_array($backup_table, $tables)) {
			$sql = "create table $backup_table (select * from $table limit 1)";
			$db->query($sql);
			$sql = "truncate table $backup_table";
			$db->query($sql);
		}

		// ת�����ݣ�
		$sql = "insert into $backup_table (select * from $table where order_date<".$the_date.")";
		$db->query($sql);
		$num = mysql_affected_rows();
		if ($num > 0) {
			$sql = "delete from $table where order_date<".$the_date;
			$db->query($sql);
		}

		echo $_hid.":".$_hname." ������ϣ���ת����".intval($num)."������<br>";
		flush();
		ob_flush();
		ob_end_flush();
		sleep(1);
	}

	echo "<br>ȫ��������ɡ�";
	exit;
}


if ($op == "talk_to_file") {

	// ϵͳ�����б�
	$tlist = mysql_query("show tables");
	$tables = array();
	while ($li = mysql_fetch_array($tlist)) {
		$tables[] = $li[0];
	}

	$hid_name_arr = $db->query("select id,name from hospital order by id asc", "id", "name");
	foreach ($hid_name_arr as $_hid => $_hname) {

		// ����ҽԺĿ¼:
		$hdir = ROOT."data/".$_hid."/";
		if (!file_exists($hdir)) {
			@mkdir($hdir, 0777);
		}
		@chmod($hdir, 0777);
		if (!file_exists($hdir)) {
			exit($hdir." �������ɹ��������Ȩ�ޡ�");
		}

		// ����ǰ��
		$table = "patient_{$_hid}";
		$ids = $db->query("select id from $table where length(content)>200", "", "id");
		foreach ($ids as $_id) {
			// ��Ŀ¼��ÿ��Ŀ¼������1000���ļ�
			$dir_name = ceil($_id / 1000);
			$dir = $hdir.$dir_name."/";
			if (!file_exists($dir)) {
				@mkdir($dir, 0777);
			}
			@chmod($dir, 0777);
			if (!file_exists($dir)) {
				exit($dir." �������ɹ��������Ȩ�ޡ�");
			}
			$line = $db->query("select content from $table where id=$_id limit 1", 1);
			$content = $line["content"];
			$len = @file_put_contents($dir.$_id.".txt", $content);

			// �ɹ��󽫸ü�¼�ֶα��
			if ($len > 0) {
				$db->query("update $table set content='@' where id=$_id limit 1");
			}
		}
		$count1 = count($ids);


		// ������ʷ��¼��
		$table = "patient_{$_hid}_history";
		$count2 = 0;
		if (in_array($table, $tables)) {
			$ids = $db->query("select id from $table where length(content)>200", "", "id");
			foreach ($ids as $_id) {
				// ��Ŀ¼��ÿ��Ŀ¼������1000���ļ�
				$dir_name = ceil($_id / 1000);
				$dir = $hdir.$dir_name."/";
				if (!file_exists($dir)) {
					@mkdir($dir, 0777);
				}
				@chmod($dir, 0777);
				if (!file_exists($dir)) {
					exit($dir." �������ɹ��������Ȩ�ޡ�");
				}
				$line = $db->query("select content from $table where id=$_id limit 1", 1);
				$content = $line["content"];
				$len = @file_put_contents($dir.$_id.".txt", $content);

				// �ɹ��󽫸ü�¼�ֶα��
				if ($len > 0) {
					$db->query("update $table set content='@' where id=$_id limit 1");
				}
			}
			$count2 = count($ids);
		}

		echo $_hid.":".$_hname." ������ϣ��洢��".($count1+$count2)."�������¼<br>";

		flush();
		ob_flush();
		ob_end_flush();
		sleep(1);
	}

	echo "<br>ȫ��������ɡ�";
	exit;
}


// ��һ�������������н���ֶ�
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
<title>���Թ���</title>
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
	return confirm("�˲���ʮ��Σ�գ����޿�����Աʹ�á�\r\n\r\n����㲻�ǿ�����Ա����֪������������������ȡ����������������ܷǳ����ء���֮��\r\n\r\n�Ƿ�ȷ��������");
}
</script>
</head>

<body>
<div class="headers">
	<div class="headers_title"><table class="bar"><tr><td class="bar_left"></td><td class="bar_center">���Թ���</td><td class="bar_right"></td></tr></table></div>
	<div class="header_center"></div>
	<div class="headers_oprate"><input type="button" value="����" onclick="history.back()" class="button" title="������һҳ"></div>
</div>

<div class="space"></div>

<table width="100%" align="center" class="list">
	<tr>
		<td class="head" align="center" width="32">���</td>
		<td class="head" align="left" width="100">����</td>
		<td class="head" align="left" width="">���</td>
	</tr>
	<tr>
		<td align="center" class="item">1</td>
		<td align="left" class="item"><a href="?op=phpinfo">�鿴phpinfo()</a></td>
		<td align="left" class="item">�鿴������phpinfo���Դ���ϳ��ֺ������⡣</td>
	</tr>
	<tr>
		<td align="center" class="item">2</td>
		<td align="left" class="item"><a href="?op=update_field">����patient��ṹ</a></td>
		<td align="left" class="item">�� patient �����ṹ���ʱ��ʹ�ô˹��߽����Ӧ�õ�ϵͳ���� patient ���С�</td>
	</tr>
	<tr>
		<td align="center" class="item">3</td>
		<td align="left" class="item"><a href="?op=move_old_data" onclick="return confirm_op()">��һ��ǰ�ľ������Ƶ���ʷ����</a></td>
		<td align="left" class="item">��һ��ǰ�ľ������Ƶ���ʷ���У���ԤԼʱ���㡣</td>
	</tr>
	<tr>
		<td align="center" class="item">4</td>
		<td align="left" class="item"><a href="?op=talk_to_file" onclick="return confirm_op()">���������ݴ�ŵ��ı��ļ���</a></td>
		<td align="left" class="item">���������ݴ�ŵ��ı��ļ��У�����ʹ���ݿ�ѹ����С</td>
	</tr>
</table>


</body>
</html>