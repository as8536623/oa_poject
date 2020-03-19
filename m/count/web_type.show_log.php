<?php
// --------------------------------------------------------
// - 功能说明 : 查看日志
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2013-6-24
// --------------------------------------------------------

$line = $db->query("select * from $table where id='$id' limit 1", 1);

?>
<html>
<head>
<title>查看修改日志</title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/base.css" rel="stylesheet" type="text/css">
<script src="/res/base.js" language="javascript"></script>

<style>
* {font-family:"微软雅黑"; }
</style>

<script language="javascript">
</script>

</head>

<body>

<?php echo $line["log"] ? nl2br($line["log"]) : "(无记录)"; ?>

</body>
</html>