<?php
/*
// - ����˵�� : ��ȡ�ļ��д�С
// - �������� : zhuwenya (zhuwenya@126.com)
// - ����ʱ�� : 2008-07-08 22:11
*/
require "../core/core.php";
header("Content-Type:text/html;charset=GB2312");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// ��ȡȫվ�ļ���С:
$path_web = dirname(__FILE__)."/../../";
$web_size = get_dir_size($path_web);
if ($web_size > 0) {
	$web_disp_size = "ȫվ: <b>".display_size($web_size)."</b>";
} else {
	$web_disp_size = "ȫվ: �޷���ȡ";
}

// �ϴ���Ŀ��С:
$path_file = dirname(__FILE__)."/../../file/";
$file_size = get_dir_size($path_file);
if ($file_size > 0) {
	$file_disp_size = "�ϴ�: <b>".display_size($file_size)."</b>";
} else {
	$file_disp_size = "�ϴ�: �޷���ȡ";
}

// ��̨ռ�ô�С:
$path_admin = dirname(__FILE__)."/../";
$admin_size = get_dir_size($path_admin);
if ($admin_size > 0) {
	$admin_disp_size = "��̨: <b>".display_size($admin_size)."</b>";
} else {
	$admin_disp_size = "��̨: �޷���ȡ";
}

echo $web_disp_size."��".$file_disp_size."��".$admin_disp_size;
?>