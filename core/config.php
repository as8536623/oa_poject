<?php
/*
// 配置文件 weelia @ 2012-07-17
*/
//exit("系统维护中...");

@header("Content-type: text/html; charset=gb2312");

// 站点名称:
$global_site_name = "病人统计分析系统 5.0";

// 参数设置:
$global_default_pagesize = 25; //默认分页数(列表未填写时使用此数据)

// 排序表格的表头:
$aOrderTips = array("" => "点击取消按此栏目排序", "asc" => "点击按升序排序", "desc" => "点击按降序排序");
$aOrderFlag = array("" => "", "asc" => '↑', "desc" =>'↓');

// 颜色数组:
$aTitleColor = array("" => "默认", "fuchsia" => "紫红色", "red" => "红色", "green" => "绿色", "blue" => "蓝色",
	"orange" => "橙黄色", "darkviolet" => "紫罗兰色", "silver" => "银色", "maroon" => "栗色", "olive" => "橄榄色",
	"navy" => "海军蓝", "purple" => "紫色", "coral" => "珊瑚色", "crimson" => "深红色", "gold" => "金色", "black" => "黑色");

$button_split = ' <font color="silver">|</font> ';

$status_array = array(0 => '等待', 1 => '已到', 2 => '未到' ,3 => '跟踪',4=>'无效');

$oprate_type = array("add"=>"新增", "delete"=>"删除", "edit"=>"修改", "login"=>"用户登录", "logout"=>"用户退出");


// 2010-07-19 15:22
$shoufei_bumen_array = array(0 => "门诊", 1 => "住院");

$guahao_config_arr = array(
	"patient_add" => "新增病人",
	"patient_edit" => "修改病人",
	"patient_delete" => "删除病人",
	"set_come" => "勾到院",
	"set_huifang_kf" => "设回访客服",
	"huifang" => "回访",
	"set_xiaofei" => "记消费",
);


// 数据授权定义，该处也对应首页的统计数据显示权限
$data_power_arr = array(
	"all" => "总数据",
	"web" => "网络",
	"tel" => "电话",
);


$from_soft_arr = array("swt" => "商务通", "qq" => "QQ", "other" => "其它");


?>