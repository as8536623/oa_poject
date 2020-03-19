<?php
/*
// 说明: 创建系统表
// 作者: 幽兰 (weelia@126.com)
// 时间: 2009-10-07 16:55
*/

$db_tables = array();

$db_tables["patient"] = "CREATE TABLE IF NOT EXISTS `patient_{hid}` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`part_id` int(10) NOT NULL DEFAULT '0',
	`name` varchar(20) NOT NULL,
	`age` int(3) NOT NULL,
	`sex` varchar(6) NOT NULL COMMENT '性别',
	`disease_id` int(10) NOT NULL DEFAULT '0' COMMENT '病患类型',
	`disease_2` varchar(50) NOT NULL COMMENT '二级疾病',
	`depart` int(10) NOT NULL DEFAULT '0' COMMENT '科室',
	`is_local` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否本地病人',
	`area` varchar(32) NOT NULL COMMENT '病人来源地区',
	`tel` varchar(20) NOT NULL,
	`tel_location` varchar(40) NOT NULL COMMENT '号码归属地',
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
	`from_account` int(10) NOT NULL DEFAULT '0' COMMENT '所属帐户',
	`memo` mediumtext NOT NULL,
	`status` int(2) NOT NULL DEFAULT '0',
	`doctor` varchar(32) NOT NULL COMMENT '接待医生',
	`is_chengjiao` int(2) NOT NULL DEFAULT '0' COMMENT '是否成交',
	`is_xiaofei` int(2) NOT NULL DEFAULT '0' COMMENT '是否消费',
	`xiaofei_count` double(10,1) NOT NULL DEFAULT '0' COMMENT '总消费额',
	`xiaofei_log` mediumtext NOT NULL COMMENT '历次消费记录',
	`is_zhiliao` int(2) NOT NULL DEFAULT '0' COMMENT '是否治疗(包括手术)',
	`xiangmu` varchar(250) NOT NULL COMMENT '治疗项目',
	`huifang_kf` varchar(32) NOT NULL COMMENT '回访客服',
	`huifang` mediumtext NOT NULL COMMENT '回访记录',
	`huifang_nexttime` int(10) NOT NULL DEFAULT '0' COMMENT '下次回访提醒时间',
	`huifang_date` int(8) NOT NULL DEFAULT '0' COMMENT '下次回访提醒日期',
	`huifang_uid` int(10) NOT NULL DEFAULT '0' COMMENT '回访uid',
	`yingxiao_doctor` varchar(32) NOT NULL COMMENT '营销医生',
	`yingxiao_name` varchar(32) NOT NULL COMMENT '营销专员',
	`addtime` int(10) NOT NULL DEFAULT '0',
	`uid` int(10) NOT NULL DEFAULT '0' COMMENT 'UID',
	`author` varchar(32) NOT NULL,
	`edit_log` mediumtext NOT NULL COMMENT '修改记录',
	PRIMARY KEY (`id`),
	KEY `part_id` (`part_id`),
	KEY `order_date` (`order_date`),
	KEY `addtime` (`addtime`),
	KEY `author` (`author`)
	) ENGINE=MyISAM  DEFAULT CHARSET=gbk;";


?>