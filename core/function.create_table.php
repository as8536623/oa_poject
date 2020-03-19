<?php
/*
// ˵��: ����ϵͳ��
// ����: ���� (weelia@126.com)
// ʱ��: 2009-10-07 16:55
*/

$db_tables = array();

$db_tables["patient"] = "CREATE TABLE IF NOT EXISTS `patient_{hid}` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`part_id` int(10) NOT NULL DEFAULT '0',
	`name` varchar(20) NOT NULL,
	`age` int(3) NOT NULL,
	`sex` varchar(6) NOT NULL COMMENT '�Ա�',
	`disease_id` int(10) NOT NULL DEFAULT '0' COMMENT '��������',
	`disease_2` varchar(50) NOT NULL COMMENT '��������',
	`depart` int(10) NOT NULL DEFAULT '0' COMMENT '����',
	`is_local` tinyint(1) NOT NULL DEFAULT '1' COMMENT '�Ƿ񱾵ز���',
	`area` varchar(32) NOT NULL COMMENT '������Դ����',
	`tel` varchar(20) NOT NULL,
	`tel_location` varchar(40) NOT NULL COMMENT '���������',
	`qq` varchar(20) NOT NULL,
	`zhuanjia_num` varchar(10) NOT NULL,
	`content` mediumtext NOT NULL,
	`jiedai` varchar(20) NOT NULL,
	`jiedai_content` text NOT NULL,
	`order_date` int(10) NOT NULL DEFAULT '0',
	`media_from` varchar(20) NOT NULL,
	`engine` varchar(32) NOT NULL,
	`engine_key` varchar(32) NOT NULL,
	`from_site` varchar(255) NOT NULL,
	`from_soft` varchar(10) NOT NULL,
	`from_account` int(10) NOT NULL DEFAULT '0' COMMENT '�����ʻ�',
	`memo` mediumtext NOT NULL,
	`status` int(2) NOT NULL DEFAULT '0',
	`doctor` varchar(32) NOT NULL COMMENT '�Ӵ�ҽ��',
	`is_chengjiao` int(2) NOT NULL DEFAULT '0' COMMENT '�Ƿ�ɽ�',
	`is_xiaofei` int(2) NOT NULL DEFAULT '0' COMMENT '�Ƿ�����',
	`xiaofei_count` double(10,1) NOT NULL DEFAULT '0' COMMENT '�����Ѷ�',
	`xiaofei_log` mediumtext NOT NULL COMMENT '�������Ѽ�¼',
	`is_zhiliao` int(2) NOT NULL DEFAULT '0' COMMENT '�Ƿ�����(��������)',
	`xiangmu` varchar(250) NOT NULL COMMENT '������Ŀ',
	`huifang_kf` varchar(32) NOT NULL COMMENT '�طÿͷ�',
	`huifang` mediumtext NOT NULL COMMENT '�طü�¼',
	`huifang_nexttime` int(10) NOT NULL DEFAULT '0' COMMENT '�´λط�����ʱ��',
	`huifang_date` int(8) NOT NULL DEFAULT '0' COMMENT '�´λط���������',
	`huifang_uid` int(10) NOT NULL DEFAULT '0' COMMENT '�ط�uid',
	`yingxiao_doctor` varchar(32) NOT NULL COMMENT 'Ӫ��ҽ��',
	`yingxiao_name` varchar(32) NOT NULL COMMENT 'Ӫ��רԱ',
	`addtime` int(10) NOT NULL DEFAULT '0',
	`uid` int(10) NOT NULL DEFAULT '0' COMMENT 'UID',
	`author` varchar(32) NOT NULL,
	`edit_log` mediumtext NOT NULL COMMENT '�޸ļ�¼',
	PRIMARY KEY (`id`),
	KEY `part_id` (`part_id`),
	KEY `order_date` (`order_date`),
	KEY `addtime` (`addtime`),
	KEY `author` (`author`)
	) ENGINE=MyISAM  DEFAULT CHARSET=gbk;";


?>