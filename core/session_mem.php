<?php
/*
// 说明: session 保存到 mem_cache
// 作者: 幽兰 (934834734@qq.com)
// 时间: 2009-10-28 20:00
*/

// 初始化 session:
function ses_open($save_path, $sid) {
	$data = ses_read($sid);
	ses_write($sid, $data); //更新过期时间 防止过期
	return true;
}

// session 关闭:
function ses_close() {
	return true;
}

// session 读操作:
function ses_read($sid) {
	if ($sid) {
		$tmp = wee_mem_get_cache("ses_".$sid);
		if (is_array($tmp)) {
			return $tmp["data"];
		}
	}

	return true;
}

// session 写操作:
function ses_write($sid, $sess_data) {
	global $uid, $username, $realname;

	$data = ($sess_data);
	$time = time();

	if ($sid) {
		wee_mem_set_cache("ses_".$sid, $data, 300);
	}

	return true;
}

// 注销 session:
function ses_destroy($sid) {
	wee_mem_delete_cache("ses_".$sid);
	return true;
}

// gc 压缩:
function ses_gc($maxlifetime) {
	return true;
}

// gc 压缩: 该功能由系统进行周期性调用
function ses_gc_by_core() {
	return true;
}


// 注册 session handler:
session_set_save_handler("ses_open", "ses_close", "ses_read", "ses_write", "ses_destroy", "ses_gc");

// 初始 session:
session_start();

?>