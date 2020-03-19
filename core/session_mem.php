<?php
/*
// ˵��: session ���浽 mem_cache
// ����: ���� (934834734@qq.com)
// ʱ��: 2009-10-28 20:00
*/

// ��ʼ�� session:
function ses_open($save_path, $sid) {
	$data = ses_read($sid);
	ses_write($sid, $data); //���¹���ʱ�� ��ֹ����
	return true;
}

// session �ر�:
function ses_close() {
	return true;
}

// session ������:
function ses_read($sid) {
	if ($sid) {
		$tmp = wee_mem_get_cache("ses_".$sid);
		if (is_array($tmp)) {
			return $tmp["data"];
		}
	}

	return true;
}

// session д����:
function ses_write($sid, $sess_data) {
	global $uid, $username, $realname;

	$data = ($sess_data);
	$time = time();

	if ($sid) {
		wee_mem_set_cache("ses_".$sid, $data, 300);
	}

	return true;
}

// ע�� session:
function ses_destroy($sid) {
	wee_mem_delete_cache("ses_".$sid);
	return true;
}

// gc ѹ��:
function ses_gc($maxlifetime) {
	return true;
}

// gc ѹ��: �ù�����ϵͳ���������Ե���
function ses_gc_by_core() {
	return true;
}


// ע�� session handler:
session_set_save_handler("ses_open", "ses_close", "ses_read", "ses_write", "ses_destroy", "ses_gc");

// ��ʼ session:
session_start();

?>