<?php
require "../../core/core.php";
$table = "order_num";

if($_GET["op"] == "getonum"){
	
	$ordernum = $db->query("select ordernum from $table", 1, "ordernum"); 
	$ordernum+=1;
	$db->query("update $table set ordernum = $ordernum");
	echo $ordernum;

	}


?>