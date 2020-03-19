<?php 
require "../../core/core.php";
$table = "ku_list";
		$ids = explode(",", $_GET["id"]);
		$del_fail = $del_ok = 0;
		foreach ($ids as $_id) {
			$_id = intval($_id);
			if ($_id > 0) {
				$db->query("delete from $table where id='$_id' limit 1");
			}
		}
	
  echo"<script>alert('·µ»Ø');history.go(-1);</script>"; 