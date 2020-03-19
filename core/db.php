<?php
/*
// 说明: 初始化数据库 不依赖config文件
// 作者: 幽兰 (934834734@qq.com)
// 时间: 2012-12-13
*/

$path = str_replace("\\", "/", dirname(__FILE__))."/";

include_once $path."class.mysql.php";

//$db = new mysql(explode("|", base64_decode("bG9jYWxob3N0fGJ0b2FjbnxiNFpoam5HVGxmTHBzVVBMfGJ0b2FjbnxnYms=")));
//$db = new mysql(explode("|", base64_decode("bG9jYWxob3N0fGJ0b2FjbnxiNFpoam5HVGxmTHBzVVBMfGJ0b2FjbnxnYms=")));

$db = new mysql(array('localhost','oa_byby','bybynjcj2018ao','oa_byby','gbk'));
$db->show_error = false;

$GLOBALS["db"] = $db; //注册为全局变量

?>