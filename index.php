<?php
/*
// - 功能说明 : index.php
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2010-06-21
*/
@header("Content-type: text/html; charset=gb2312");

// 检查是否在根目录下运行，否则无法运行
// 请勿删除此段代码，否则会出现更严重问题
$need_root_url = "http://".$_SERVER["HTTP_HOST"];
$cur_root_url = rtrim("http://".$_SERVER["HTTP_HOST"].@str_replace("\\", "/", @dirname($_SERVER["REQUEST_URI"])), "/");
if ($need_root_url != $cur_root_url) {
	echo "对不起，该系统必须在域名的根目录下运行，不支持放置在子目录下。<br>";
	echo "如果条件所限，必须放子目录，可采用解析二级域名并绑定到该子目录，然后通过二级域名访问。<br><br>";
	echo "期望路径: ".$need_root_url."<br>";
	echo "实际路径: ".$cur_root_url."<br>";
	exit;
}

require "core/core.php";


$power = load_class("power", $db);
list($menu_stru, $menu_power) = $power->parse_menu($usermenu);
$menu_ids = array_keys($menu_power);
$menu_id_list = implode(",", $menu_ids);

$menu_data = array();
if ($tmp_data = $db->query("select id,title,link,isshow from sys_menu where id in ($menu_id_list) and isshow=1 order by sort")) {
	foreach ($tmp_data as $tmp_line) {
		$menu_data[$tmp_line["id"]] = array($tmp_line["title"], $tmp_line["link"]);
	}
}

// 验证&删除多余mid:
foreach ($menu_stru as $mainid => $mlevel1) {
	if (!array_key_exists($mainid, $menu_data)) {
		unset($menu_stru[$mainid]); continue;
	}
	foreach ($mlevel1 as $key => $itemid) {
		if (!array_key_exists($itemid, $menu_data)) {
			unset($mlevel1[$key]);
		}
	}
	$menu_stru[$mainid] = array_merge($mlevel1);
}

$menu_mids = json(array_keys($menu_stru));
$menu_stru_json = json($menu_stru);
$menu_data_json = json($menu_data);

$menu_shortcut = "";

$is_show_dyn_menu = 1;
$is_show_shortcut = 0;
$close_left_menu = isset($config["close_left_menu"]) ? $config["close_left_menu"] : 0;
$is_show_footer = 0;
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $global_site_name; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=gb2312">
<link href="/res/frame.css" rel="stylesheet" type="text/css">
<script language="javascript">
var menu_mids = <?php echo $menu_mids; ?>;
var menu_stru = <?php echo $menu_stru_json; ?>;
var menu_data = <?php echo $menu_data_json; ?>;
var menu_shortcut = [<?php echo $menu_shortcut; ?>];
var show_dyn_menu = <?php echo $is_show_dyn_menu ? 1 : 0; ?>;
var show_shortcut = <?php echo $is_show_shortcut ? 1 : 0; ?>;

function change_to_hospital(type) {
	var url = byid("sys_frame").contentWindow.location.href;
	url += (url.split("?").length > 1 ? "&" : "?") + "main_hid_to="+type;
	byid("sys_frame").contentWindow.location = url;
}
</script>
<script language="javascript" src="/res/frame.js?20121211"></script>
<script language="javascript" src="/res/menu.js"></script>
<script language="javascript" src="/res/drag.js"></script>
</head>

<body>

<div id="top_border" class="co_top">
	<div class="co_left_top"></div>
	<div class="co_right_top"></div>
	<div class="clear"></div>
</div>

<div id="logo_bar" class="logo">
	<div class="logo_v_line fleft"></div>
	<div class="logo_v_line fright"></div>
	<div class="clear"></div>
	<iframe src="/core/update_data.php " width="0" height="0" scrolling="no"></iframe>
</div>

<div id="menu_bar">
	<div class="tline left"></div>
	<div class="top_menu">
		<div id="sys_top_menu"></div>
		<div id="sys_top_menu_right"><a href="javascript:void(0);" onclick="change_to_hospital('pre'); return false;">上家医院</a> <img src="/res/img/word_spacer.gif" align="absmiddle"> <a href="javascript:void(0);" onclick="change_to_hospital('next'); return false;">下家医院</a> <img src="/res/img/word_spacer.gif" align="absmiddle"> <a href="javascript:void(0);" id="a_show_hide_side" onclick="show_hide_side(); return false;">关闭侧栏</a> <img src="/res/img/word_spacer.gif" align="absmiddle"> <a href="m/logout.php">退出</a></div>

		<div class="clear"></div>
	</div>
	<div class="tline right"></div>
	<div class="clear"></div>
</div>

<div id="main_bar">
	<div id="side_menu" class="left_menu">
		<div id="sys_left_menu"></div>
		<div id="sys_shortcut" style="display:none;"></div>
		<div id="sys_online"></div>
		<div id="sys_notice"></div>
	</div>
	<div id="frame_content"><iframe id="sys_frame" name="main" onload="frame_loaded_do(this)" src="" mid="" framesrc="" frameborder="0" scrolling="auto" width="100%" height="365" onreadystatechange="update_navi()"></iframe></div>
	<div class="clear"></div>
</div>

<div id="bottom_border" class="co_bottom">
	<div class="co_left_bottom"></div>
	
	<div class="co_right_bottom"></div>
	<div class="clear"></div>
</div>


<!-- loading status table -->
<table id="sys_loading" style="display:none; position:absolute; border:1px solid #00D5D5; background:#D9FFFF; line-height:120%"><tr><td style="padding:1px 0 0 6px"><img src='/res/img/loading.gif' width='16' height='16' align='absmiddle' /></td><td id="sys_loading_tip" style="padding:2px 6px 0px 6px"></td></tr>
</table>

<!-- sys dialog box -->
<div id="dl_layer_div" style="position:absolute; filter:Alpha(opacity=70); display:none; background:#404040; z-index:998; opacity:0.7;"></div>
<div id="dl_box_div" onmousedown="handlestart(event, this)" class="obox" style="position:absolute; display:none; z-index:999">
	<table class="dl_table" width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="4" height="4"><img src="/res/img/dl_top_left.gif" width="4" height="4"></td>
			<td height="4" style="background:url('/res/img/dl_top_center.gif') repeat-x;">&nbsp;</td>
			<td width="4" height="4"><img src="/res/img/dl_top_right.gif" width="4" height="4"></td>
		</tr>
	</table>

	<table class="dl_table" width="100%" cellspacing="0" cellpadding="0" style="border:2px solid #00CC66; border-top:0; border-bottom:0;">
		<tr>
			<td width="100%">
				<div id="dl_box_title_box">
					<div id="dl_box_title"></div>
					<div id="dl_box_op"><a href="javascript:load_box(0);">关闭</a></div>
					<div class="clear"></div>
				</div>
				<div id="dl_box_loading" style="position:absolute; display:none;"><img src="res/img/loading.gif" align="absmiddle"> 加载中，请稍候... </div>
				<div id="dl_iframe"><iframe src="about:blank" frameborder="0" scrolling="auto" width="100%" id="dl_set_iframe" onload="update_title(this)"></iframe></div>
				<div id="dl_content" style="display:none;"></div>
			</td>
		</tr>
	</table>

	<table class="dl_table" width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="4" height="4"><img src="/res/img/dl_bottom_left.gif" width="4" height="4"></td>
			<td height="4" style="background:url('/res/img/dl_bottom_center.gif') repeat-x;">&nbsp;</td>
			<td width="4" height="4"><img src="/res/img/dl_bottom_right.gif" width="4" height="4"></td>
		</tr>
	</table>
</div>

<!-- repeatlist dialog box -->
<div id="rl_layer_div" style="position:absolute; filter:Alpha(opacity=70); display:none; background:#404040; z-index:1000; opacity:0.7;"></div>
<div id="rl_box_div" onmousedown="handlestart(event, this)" class="obox" style="position:absolute; display:none; z-index:1001">
	<table class="dl_table" width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="4" height="4"><img src="/res/img/dl_top_left.gif" width="4" height="4"></td>
			<td height="4" style="background:url('/res/img/dl_top_center.gif') repeat-x;">&nbsp;</td>
			<td width="4" height="4"><img src="/res/img/dl_top_right.gif" width="4" height="4"></td>
		</tr>
	</table>

	<table class="dl_table" width="100%" cellspacing="0" cellpadding="0" style="border:2px solid #00CC66; border-top:0; border-bottom:0;">
		<tr>
			<td width="100%">
				<div id="dl_box_title_box">
					<div id="dl_box_title"></div>
					<div id="dl_box_op"><a href="javascript:repeatlist_src(0);">关闭</a></div>
					<div class="clear"></div>
				</div>
				<div id="dl_box_loading" style="position:absolute; display:none;"><img src="res/img/loading.gif" align="absmiddle"> 加载中，请稍候... </div>
				<div id="rl_iframe"><iframe src="about:blank" frameborder="0" scrolling="auto" width="100%" id="rl_set_iframe" onload="update_title(this)"></iframe></div>
				<div id="rl_content" style="display:none;"></div>
			</td>
		</tr>
	</table>

	<table class="dl_table" width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="4" height="4"><img src="/res/img/dl_bottom_left.gif" width="4" height="4"></td>
			<td height="4" style="background:url('/res/img/dl_bottom_center.gif') repeat-x;">&nbsp;</td>
			<td width="4" height="4"><img src="/res/img/dl_bottom_right.gif" width="4" height="4"></td>
		</tr>
	</table>
</div>

<!-- msg_box -->
<div id="sys_msg_box" style="display:none; position:absolute;cursor:pointer;" onclick="msg_box_hide()" onmouseover="msg_box_hold()" onmouseout="msg_box_delay_hide()" title="点击关闭">
	<table cellpadding="0">
		<tr>
			<td class="left_div"></td>
			<td class="center_div"><table><tr><td id="sys_msg_box_content"></td></tr></table></td>
			<td class="right_div"></td>
		</tr>
	</table>
</div>

<?php if ($debug_mode) { ?>
<!-- log -->
<div id="log" style="width:300px; height:600px; position:absolute; right:10px; bottom:10px; z-index:100000; border:2px solid silver; background:white; padding:5px; overflow:auto; display:none;"></div>
<?php } ?>

<script language="JavaScript">
dom_loaded.load(init);
</script>

<?php if ($close_left_menu) { ?>
<script language="javascript">
	show_hide_side();
</script>
<?php } ?>

</body>
</html>