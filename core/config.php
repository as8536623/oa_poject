<?php
/*
// �����ļ� weelia @ 2012-07-17
*/
//exit("ϵͳά����...");

@header("Content-type: text/html; charset=gb2312");

// վ������:
$global_site_name = "����ͳ�Ʒ���ϵͳ 5.0";

// ��������:
$global_default_pagesize = 25; //Ĭ�Ϸ�ҳ��(�б�δ��дʱʹ�ô�����)

// ������ı�ͷ:
$aOrderTips = array("" => "���ȡ��������Ŀ����", "asc" => "�������������", "desc" => "�������������");
$aOrderFlag = array("" => "", "asc" => '��', "desc" =>'��');

// ��ɫ����:
$aTitleColor = array("" => "Ĭ��", "fuchsia" => "�Ϻ�ɫ", "red" => "��ɫ", "green" => "��ɫ", "blue" => "��ɫ",
	"orange" => "�Ȼ�ɫ", "darkviolet" => "������ɫ", "silver" => "��ɫ", "maroon" => "��ɫ", "olive" => "���ɫ",
	"navy" => "������", "purple" => "��ɫ", "coral" => "ɺ��ɫ", "crimson" => "���ɫ", "gold" => "��ɫ", "black" => "��ɫ");

$button_split = ' <font color="silver">|</font> ';

$status_array = array(0 => '�ȴ�', 1 => '�ѵ�', 2 => 'δ��' ,3 => '����',4=>'��Ч');

$oprate_type = array("add"=>"����", "delete"=>"ɾ��", "edit"=>"�޸�", "login"=>"�û���¼", "logout"=>"�û��˳�");


// 2010-07-19 15:22
$shoufei_bumen_array = array(0 => "����", 1 => "סԺ");

$guahao_config_arr = array(
	"patient_add" => "��������",
	"patient_edit" => "�޸Ĳ���",
	"patient_delete" => "ɾ������",
	"set_come" => "����Ժ",
	"set_huifang_kf" => "��طÿͷ�",
	"huifang" => "�ط�",
	"set_xiaofei" => "������",
);


// ������Ȩ���壬�ô�Ҳ��Ӧ��ҳ��ͳ��������ʾȨ��
$data_power_arr = array(
	"all" => "������",
	"web" => "����",
	"tel" => "�绰",
);


$from_soft_arr = array("swt" => "����ͨ", "qq" => "QQ", "other" => "����");


?>