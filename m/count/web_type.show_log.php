<?php
// --------------------------------------------------------
// - ����˵�� : �鿴��־
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2013-6-24
// --------------------------------------------------------

$line = $db->query("select * from $table where id='$id' limit 1", 1);

?>
<html>
<head>
<title>�鿴�޸���־</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>

<style>
* {font-family:"΢���ź�"; }
</style>

<script language="javascript">
</script>

</head>

<body>

<?php echo $line["log"] ? nl2br($line["log"]) : "(�޼�¼)"; ?>

</body>
</html>