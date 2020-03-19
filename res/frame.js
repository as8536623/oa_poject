/*
 * @Description: 
 * @Author: bigc
 * @LastEditors: bigc
 * @Date: 2019-04-02 15:37:52
 * @LastEditTime: 2019-04-02 15:40:27
 */
// --------------------------------------------------------

// - 功能说明 : Frame框架函数

// - 创建作者 : zhuwenya (zhuwenya@126.com)

// - 创建时间 : 2008-05-14 => 2012-07-12

// --------------------------------------------------------

var s_split = "<img src='/res/img/word_spacer.gif' width='7' height='15' align='absmiddle'>";

var click_link = "javascript:void(0);";

var navi_pre = "<font color='red'>您的位置:</font> ";

var loading_pre = "<img src='/res/img/loading.gif' width='16' height='16' align='absmiddle'> ";



var menu_max_char = Array();



function byid(id) {

	return document.getElementById(id);

}



function byname(name) {

	return document.getElementsByTagName(name);

}



function preload(image_list) {

	var im_count = image_list.length;

	var im = new Array();

	for (var ni=0; ni<im_count; ni++) {

		im[ni] = new Image();

		im[ni].src = "/res/img/" + image_list[ni];

	}

}



function init_top_menu() {

	var a_menu = Array();

	var ni = 0;

	for (var i in menu_mids) {

		var mid = menu_mids[i];

		if (menu_data[mid]) {

			var has_sub_menu = menu_stru[mid] != "";

			if (has_sub_menu) {

				a_menu[ni] = "<a id='mt"+mid+"' href='"+click_link+"' onclick='load("+menu_stru[mid][0]+");return false'"; //加载第一个子菜单的链接

			} else {

				a_menu[ni] = "<a id='mt"+mid+"' href='"+click_link+"' onclick='load("+mid+");return false'";

			}

			if (show_dyn_menu && has_sub_menu) {

				a_menu[ni] += " onmouseover='dropdownmenu(this, event, menu"+mid+", \"150px\", "+mid+")' onmouseout='delayhidemenu()'";

			}

			a_menu[ni] += " onfocus='this.blur();'>"+menu_data[mid][0]+"</a>";

			ni++;



			if (show_dyn_menu && has_sub_menu) {

				eval("menu"+mid+"=Array();");

				var cnt = 0;

				var max_char = 0;

				for (var nm in menu_stru[mid]) {

					eval("menu"+mid+"["+cnt+"]=\"<a href='"+click_link+"' onclick='load("+menu_stru[mid][nm]+");return false'>"+menu_data[menu_stru[mid][nm]][0]+"</a>\";");

					cnt++;

					if (menu_data[menu_stru[mid][nm]][0].length > max_char) {

						max_char = menu_data[menu_stru[mid][nm]][0].length;

					}

				}

				menu_max_char[mid] = max_char;

			}

		}

	}

	byid("sys_top_menu").innerHTML = a_menu.join(s_split);

}



function load(mid) {

	var is_load_url = (arguments.length == 1 ? 1 : arguments[1]);



	top_level_mid = get_parent_mid(mid);

	mid_is_top = !(top_level_mid > 0);

	top_level_mid = mid_is_top ? mid : top_level_mid;



	// 顶部当前菜单加红显示:

	var e = byid("sys_top_menu").getElementsByTagName("a");

	for (var i in e) {

		e[i].className = e[i].id == ("mt"+top_level_mid) ? "red" : "";

		if (e[i].id == ("mt"+top_level_mid)) {

			high_light_obj = e[i];

		}

	}



	// 建立左侧链接:

	var has_sub_menu = menu_stru[top_level_mid].length;

	if (has_sub_menu && menu_data[top_level_mid]) {

		var left_menu = "<table width='100%' style='border:0;'><tr><td style='height:4px; width:100%;padding:0;' class='menu_top_center'><div class='menu_left_top'></div><div class='menu_right_top'></div><div class='clear'></div></td></tr></table><table class='leftmenu_1' style='border-width:0 1px;'><tr><td class='head' style='border-top:0'>"+menu_data[top_level_mid][0]+"</td></tr>";

		for (var nm in menu_stru[top_level_mid]) {

			left_menu += "<tr><td class='item' onmouseover='mi(this)' onmouseout='mo(this)'><a id='ml"+menu_stru[top_level_mid][nm]+"' href='"+click_link+"' onclick='load("+menu_stru[top_level_mid][nm]+");return false' class=''>"+menu_data[menu_stru[top_level_mid][nm]][0]+"</a></td></tr>";

		}

		left_menu += "</table><table width='100%' style='border:0;'><tr><td style='height:4px; width:100%;padding:0;' class='menu_bottom_center'><div class='menu_left_bottom'></div><div class='menu_right_bottom'></div><div class='clear'></div></td></tr></table>";

		byid("sys_left_menu").innerHTML = left_menu;

		byid("sys_left_menu").style.display = "block";

		if (!mid_is_top) {

			byid("ml"+mid).className = "red";

		}

	} else {

		byid("sys_left_menu").innerHTML = '';

		byid("sys_left_menu").style.display = "none";

	}



	// 建立快捷菜单:

	if (show_shortcut && menu_shortcut) {

		var shortcut_tmp = "<table width='100%' style='border:0;'><tr><td style='height:4px; width:100%;padding:0;' class='menu2_top_center'><div class='menu2_left_top'></div><div class='menu2_right_top'></div><div class='clear'></div></td></tr></table><table class='leftmenu_2' style='border-width:0 1px;'><tr><td class='head' style='border-top:0'>快捷方式</td></tr>";

		for (var ni in menu_shortcut) {

			item_mid = menu_shortcut[ni];

			if (!menu_data[item_mid] || (get_parent_mid(item_mid) == top_level_mid)) {

				continue;

			}

			shortcut_tmp += "<tr><td class='item' onmouseover='mi(this)' onmouseout='mo(this)'><a id='ms"+item_mid+"' href='"+click_link+"' onclick='load("+item_mid+","+get_parent_mid(item_mid)+");return false' class=''>"+menu_data[item_mid][0]+"</a></td></tr>";

		}

		shortcut_tmp += "</table><table width='100%' style='border:0;'><tr><td style='height:4px; width:100%;padding:0;' class='menu2_bottom_center'><div class='menu2_left_bottom'></div><div class='menu2_right_bottom'></div><div class='clear'></div></td></tr></table>";

		byid("sys_shortcut").innerHTML = shortcut_tmp;

		byid("sys_shortcut").style.display = "block";

	} else {

		byid("sys_shortcut").innerHTML = '';

		byid("sys_shortcut").style.display = "none";

	}



	// 加载当前页面:

	if (is_load_url && menu_data[mid][1]) {

		if (mid_is_top) {

			make_navi(menu_data[mid][0]);

		} else {

			make_navi(menu_data[top_level_mid][0]+','+menu_data[mid][0]);

		}



		show_status("加载中，请稍候...");

		byid("sys_frame").mid = mid;

		byid("sys_frame").src = menu_data[mid][1];

		byid("sys_frame").framesrc = menu_data[mid][1];

		location.replace(location.href.split('#')[0]+"#"+mid);

		msg_box_hide();



		//oAjax = new ajax();

		//oAjax.connect("http/menu_click_count.php", "GET", "mid="+mid+"&r="+Math.random(), function(){});

	}

}



function load_url(url, navi) {

	show_status("页面加载中，请稍候...");



	make_navi(navi);

	byid("sys_frame").mid = 0;

	byid("sys_frame").src = url;

	byid("sys_frame").framesrc = url;

	msg_box_hide();

	byid("sys_frame").onreadystatechange = function() {update_navi(1);}

}



function get_parent_mid(mid) {

	for (var pmid in menu_stru)

		for (var nm in menu_stru[pmid])

			if (menu_stru[pmid][nm] == mid)

				return pmid;

	return 0;

}



function show_status(string) {

	var o = byid("sys_loading");

	if (string != '') {

		byid("sys_loading_tip").innerHTML = string;

		o.style.display = "block";

		byid("sys_loading").style.left = get_position(byid("logo_bar"), "left") + byid("logo_bar").offsetWidth - byid("sys_loading").offsetWidth - 3 + "px";

		byid("sys_loading").style.top = get_position(byid("logo_bar"), "top") + byid("logo_bar").offsetHeight - byid("sys_loading").offsetHeight - 1 + "px";

	} else {

		o.style.display = "none";

		byid("sys_loading_tip").innerHTML = '';

	}

}



function frame_loaded_do(oframe) {

	if (window.frame_base_height) {

		oframe.style.height = window.frame_base_height+"px";

	}

	show_status('');

}



function clk(obj) {

	// ...

}



function frame_auto_height() {

	var iframe = document.getElementById("sys_frame");

	try {

		var bHeight = iframe.contentWindow.document.body.scrollHeight;

		var dHeight = iframe.contentWindow.document.documentElement.scrollHeight;

		var height = Math.max(bHeight, dHeight);

		iframe.style.height = height+"px";



		// make message box always in center 2009-04-06 13:03

		if (byid("sys_msg_box").style.display == "block") {

			set_center(byid("sys_msg_box"));

		}

	} catch (ex) {

		//...

	}

}





function str_replace(search, replace, subject, count) {

	 var i = 0, j = 0, temp = '', repl = '', sl = 0, fl = 0,

		f = [].concat(search),

		r = [].concat(replace),

		s = subject,

		ra = r instanceof Array, sa = s instanceof Array;

	s = [].concat(s);

	if(count) {

		this.window[count] = 0;

	}



	for(i=0, sl=s.length; i < sl; i++) {

		if(s[i] === ''){

			continue;

		}

		for(j=0, fl=f.length; j < fl; j++) {

			temp = s[i]+'';

			repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];

			s[i] = (temp).split(f[j]).join(repl);

			if(count && s[i] !== temp) {

				this.window[count] += (temp.length-s[i].length)/f[j].length;

			}

		}

	}

	return sa ? s : s[0];

}





function update_navi(is_focus) {

	var base_url = "http://"+location.hostname+"/";

	if (byid("sys_frame").readyState == "complete") {

		var real_src = byid("sys_frame").contentWindow.location.href;

		var frame_src = byid("sys_frame").framesrc;

		if (typeof(real_src) == typeof('')) {

			real_src = str_replace(base_url, "", real_src.split("?")[0]);

			if (is_focus || real_src != frame_src) {

				var local_findit = false;

				for (var mid in menu_data) {

					if (menu_data[mid][1] == real_src) {

						byid("sys_frame").mid = mid;

						var findit = false;

						for (var main_id in menu_stru) {

							if (main_id == mid) {

								update_navi_status(main_id, 0, menu_data[main_id][0]);

								local_findit = true;

								break;

							} else {

								for (var nm in menu_stru[main_id]) {

									item_id = menu_stru[main_id][nm];

									if (item_id == mid) {

										update_navi_status(main_id, item_id, menu_data[main_id][0]+","+menu_data[item_id][0]);

										local_findit = findit = true;

										break;

									}

								}

								if (findit) {

									break;

								}

							}

						}

						break;

					}

				}

			}

		}

	}

}





function update_navi_status(top_mid, left_mid, navi_string) {

	if (top_mid > 0 || left_mid > 0) {

		var base_url = "http://"+location.hostname+"/";

		var now_url = str_replace(base_url, "", byid("sys_frame").contentWindow.location.href);

		byid("sys_frame").framesrc = now_url;

		load(((left_mid > 0 && menu_data[left_mid]) ? left_mid : top_mid), 0);

	}

}



// 此功能已弃用

function make_navi(string) {

	return '';

}



function load_js_file(src, id, loaded_fn) {

	var headerDom = document.getElementsByTagName('head').item(0);

	var jsDom = document.createElement('script');

	jsDom.type = 'text/javascript';

	jsDom.src = src;

	if (id) {

		jsDom.id = id;

	}



	headerDom.appendChild(jsDom);



	if (loaded_fn) {

		if (!document.all) {

			jsDom.onload = function () {

				loaded_fn();

			}

		} else {

			jsDom.onreadystatechange = function () {

				if (jsDom.readyState == 'loaded' || jsDom.readyState == 'complete') {

					loaded_fn();

				}

			}

		}

	}

}





function get_int_time() {

	var d = new Date();

	return Math.round(d.getTime() / 1000, 0);

}



function get_display_time() {

	var t = new Date();

	var y = t.getYear();

	var m = t.getMonth() + 1;

	var d = t.getDate();

	var h = t.getHours();

	var i = t.getMinutes();

	var s = t.getSeconds();

	var ms = t.getMilliseconds();

	m = (m < 10 ? '0' : '') + m;

	d = (d < 10 ? '0' : '') + d;

	h = (h < 10 ? '0' : '') + h;

	i = (i < 10 ? '0' : '') + i;

	s = (s < 10 ? '0' : '') + s;

	ms = (ms < 10 ? '00' : (ms < 100 ? '0' : '')) + ms;

	return y+"-"+m+"-"+d+" "+h+":"+i+":"+s+' '+ms;

}



function log(s) {

	if (debugOnline && byid("log")) {

		if (byid("log").style.display == "none") {

			byid("log").style.display = "block";

		}

		byid("log").innerHTML = get_display_time() + " "+s+"<br>"+byid("log").innerHTML;

	}

}





// 时间周期设置

var getOnlineInterval = 60; //请求周期 s

var getOnlineTimeout = 5; //请求后过多少时间算超时 s

var getOnlineInterval_small = 5; //循环间隔时间

var debugOnline = 0; //是否开启调试



// 变量初始化

var getOnlineLastRequest = 0;

var getOnlineTimer = 0;

var onlineErrorTimer = 0;



var headerDom = document.head || document.getElementsByTagName("head")[0] || document.documentElement;



// 在线信息:

function get_online() {

	if (getOnlineTimer) clearInterval(getOnlineTimer);



	if (get_int_time() - getOnlineLastRequest >= getOnlineInterval) {

		if (byid("js_online_info")) {

			byid("js_online_info").parentNode.removeChild(byid("js_online_info"));

		}



		log('开始请求 get_online.php');

		jsGetOnline = document.createElement('script');

		jsGetOnline.type = 'text/javascript';

		jsGetOnline.src = "http/get_online.php?r="+Math.random();

		jsGetOnline.id = "js_online_info";

		headerDom.insertBefore(jsGetOnline, headerDom.firstChild);



		onlineLastSendTime = get_int_time();

		onlineErrorTimer = setTimeout("get_online_error()", getOnlineTimeout*1000); //超时

	} else {

		log('周期未到，稍后继续');

		getOnlineTimer = setInterval("get_online()", getOnlineInterval_small*1000); //时间没到，一会再试试

	}

}



// 在线信息处理结果:

function get_online_do(out) {

	document.title = ori_doc_title;

	log('请求成功，费时：'+(get_int_time() - onlineLastSendTime)+"s ----");

	clearTimeout(onlineErrorTimer); //立即停止调用无响应函数

	clearTimeout(getOnlineTimer); //立即其它加载

	getOnlineLastRequest = get_int_time(); //更新上次请求时间



	log("请求间隔；"+(getOnlineInterval));

	getOnlineTimer = setInterval("get_online()", getOnlineInterval_small*1000); //稍后继续下一次请求



	if (out["status"] == 'ok') {

		if (out["online_list"]) {

			show_online_list(out["online_list"]);

		}

		// 包括了在线通知和在线消息

		if (out["online_notice"]) {

			show_online_notice(out["online_notice"]);

		} else {

			byid("sys_notice").innerHTML = '';

		}

		if (out["alert"]) {

			msg_box(out["alert"], 3); //显示消息

		}

	}



	if (out["status"] == 'logout') {

		top.location = "/m/logout.php";

		return;

	}

}



// 获取在线消息错误:

function get_online_error() {

	document.title = "请求超时";

	log('请求超时，稍后继续');

	if (byid("js_online_info")) {

		if (headerDom && jsGetOnline.parentNode) {

			headerDom.removeChild(jsGetOnline);

		}



		jsGetOnline = undefined;

	}

	clearInterval(getOnlineTimer);

	getOnlineLastRequest = get_int_time() + 10; //可能这会服务器卡，时间长点再试

	log("请求间隔；"+(getOnlineInterval + 10));

	getOnlineTimer = setInterval("get_online()", getOnlineInterval_small*1000); //继续请求

}





// 现在在线用户列表:

function show_online_list(aOnline) {

	if (typeof(aOnline) == typeof(window)) {

		var string = "<table width='100%' style='border:0;'><tr><td style='height:4px; width:100%;padding:0;' class='menu2_top_center'><div class='menu2_left_top'></div><div class='menu2_right_top'></div><div class='clear'></div></td></tr></table><table width='100%' class='leftmenu_online' style='border-width:0 1px;'>";

		string += "<tr><td class='head' style='border-top:0;'><div style='float:left;'>在线用户</div><div style='float:right;'><a href='javascript:void(0);' onclick=\"load_url('m/sys/online_all.php')\" title='显示所有在线用户'>更多>></a></div><div class='clear'></div></td></tr>";

		for (var n in aOnline) {

			string += "<tr><td class='item' onmouseover='mi(this)' onmouseout='mo(this)'>";

			if (n > 0) {

				//string += "<a href='#"+n+":"+aOnline[n]["name"]+"' onclick=\"load_box(1, 'src', 'm/sys/admin.php?op=viewweb&name="+aOnline[n]["name"]+"'); return false;\" title='"+(aOnline[n]["isowner"] != 1 ? "查看用户资料" : "查看我的资料")+"'>";

			}

			string += aOnline[n]["realname"];

			if (n > 0) {

				//string += "</a>";

			}

			if (aOnline[n]["isowner"] != 1) {

				//string += "&nbsp;<a href='javascript:void(0)' onclick=\"load_box(1, 'src', 'm/sys/talk.php?to="+n+"');\" class='talk' title='点击发送消息'>[交谈]</a>";

			}

			string + "</td></tr>";

		}

		string += "</table><table width='100%' style='border:0;'><tr><td style='height:4px; width:100%;padding:0;' class='menu2_bottom_center'><div class='menu2_left_bottom'></div><div class='menu2_right_bottom'></div><div class='clear'></div></td></tr></table>";

	} else {

		var string = "<div class='online_tips'>没有其他用户在线</div>";

	}

	if (typeof(byid("sys_online")) == typeof(window) && string) {

		byid("sys_online").innerHTML = string;

	}

}



function show_online_notice(arr) {

	if (typeof(arr) == typeof(window)) {

		var s = '';

		if (arr) {

			s += '<table width="100%" class="leftmenu_online">';

			s += '<tr><td class="head">通知和消息</td></tr>';

			for (var k in arr) {

				var li = arr[k];

				s += '<tr><td class="item" onmouseover="mi(this)" onmouseout="mo(this)">';

				if (li["type"] == "notice") {

					li["title"] = '<font color=red>'+li["title"]+'</font>';

					// 处理某些东西

				} else if (li["type"] == "message") {

					// 处理某些东西

				}

				s += '<a href="javascript:void(0);" onclick="load_box(1, \'src\', \''+li["url"]+'\');">'+li["title"]+'</a>';

				s += '</td></tr>';

			}

			s += '</table>';

		}



		byid("sys_notice").innerHTML = s;

	}

}





// xmlhttp函数的封装

function ajax() {

	var xm,bC=false;

	try{xm=new ActiveXObject("Msxml2.XMLHTTP")}catch(e){try{xm=new ActiveXObject("Microsoft.XMLHTTP")}catch(e){try{xm=new XMLHttpRequest()}catch(e){xm=false}}}

	if(!xm)return null;this.connect=function(sU,sM,sV,fn){if(!xm)return false;bC=false;sM=sM.toUpperCase();

	try{if(sM=="GET"){xm.open(sM,sU+"?"+sV,true);sV=""}else{xm.open(sM,sU,true);

	xm.setRequestHeader("Method","POST "+sU+" HTTP/1.1");

	xm.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8")}

	xm.onreadystatechange=function(){if(xm.readyState==4&&!bC){bC=true;if(xm.status==200){fn(xm)}else{window.status="ajax error status code: "+xm.status}}};

	xm.send(sV)}catch(z){return false}return true};return this;

}



function get_position(what, offsettype) {

	var pos = {"left":what.offsetLeft, "top":what.offsetTop};

	var parentEl = what.offsetParent;

	while (parentEl != null) {

		pos.left += parentEl.offsetLeft;

		pos.top += parentEl.offsetTop;

		parentEl = parentEl.offsetParent;

	}

	if (offsettype) {

		return offsettype == "left" ? pos.left : pos.top;

	} else {

		return pos;

	}

}



function msg_box(string, showtime) {

	omsg = byid("sys_msg_box");

	if (string == undefined || string == "") {

		return true;

	}

	if (typeof(showtime) == "undefined") {

		var showtime = 5;

	} else {

		if (typeof(showtime) != typeof(0)) showtime *= 1;

		showtime = Math.min(20, Math.max(1, showtime));

	}

	byid("sys_msg_box_content").innerHTML = string;

	omsg.style.display = "block";

	set_center(omsg);

	sys_msg_box_timer = setTimeout("msg_box_hide()", showtime*1000);

}



function msg_box_hold() {

	clearInterval(sys_msg_box_timer);

}



function msg_box_delay_hide(time) {

	clearInterval(sys_msg_box_timer);

	if (typeof(time) == "undefined") {

		time = 1;

	} else {

		if (typeof(time) != typeof(0)) time *= 1;

		time = Math.min(20, Math.max(1, time));

	}

	sys_msg_box_timer = setTimeout("msg_box_hide()", time*1000);

}



function msg_box_hide() {

	omsg = byid("sys_msg_box");

	omsg.style.display = "none";

}



function set_center(obj) {

	var objw = obj.offsetWidth;

	var objh = obj.offsetHeight;

	var pscroll = get_scroll();

	var psize = get_size();

	var left = (psize[0] - objw) / 2;

	var top = pscroll[1] + (psize[3] - objh) / 2;

	obj.style.left = left < 0 ? "0px" : left+"px";

	obj.style.top = top < 0 ? "0px" : top+"px";

}



function mi(o) {

	o.style.backgroundColor = "#edfaf2";

}



function mo(o) {

	o.style.backgroundColor = "";

}



function set_body_height() {

	var all = get_size()[3];

	var main_bar_height = all - byid("top_border").offsetHeight - byid("logo_bar").offsetHeight - byid("menu_bar").offsetHeight - byid("bottom_border").offsetHeight - 6; // 6 是上下padding值



	var frame_base_height = main_bar_height - 0; //iframe的基准高度(刚好填充页面的高度)

	byid("frame_content").style.height = frame_base_height+"px";

	byid("sys_frame").style.height = frame_base_height+"px";



	// debug:

	//document.title = all + ", "+main_bar_height + ", "+frame_base_height+", "+byid("bottom_border").offsetHeight;

}



function get_size() {

	var xScroll, yScroll;

	if (window.innerHeight && window.scrollMaxY) {

		xScroll = document.body.scrollWidth;

		yScroll = window.innerHeight + window.scrollMaxY;

	} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac

		xScroll = document.body.scrollWidth;

		yScroll = document.body.scrollHeight;

	} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari

		xScroll = document.body.offsetWidth;

		yScroll = document.body.offsetHeight;

	}



	var windowWidth, windowHeight;

	if (self.innerHeight) {	// all except Explorer

		windowWidth = self.innerWidth;

		windowHeight = self.innerHeight;

	} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode

		windowWidth = document.documentElement.clientWidth;

		windowHeight = document.documentElement.clientHeight;

	} else if (document.body) { // other Explorers

		windowWidth = document.body.clientWidth;

		windowHeight = document.body.clientHeight;

	}



	// for small pages with total height less then height of the viewport

	if(yScroll < windowHeight){

		pageHeight = windowHeight;

	} else {

		pageHeight = yScroll;

	}



	if(xScroll < windowWidth){

		pageWidth = windowWidth;

	} else {

		pageWidth = xScroll;

	}



	arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight)

	return arrayPageSize;

}



function get_scroll() {

	var yScroll;

	if (self.pageYOffset) {

		yScroll = self.pageYOffset;

	} else if (document.documentElement && document.documentElement.scrollTop){	 // Explorer 6 Strict

		yScroll = document.documentElement.scrollTop;

	} else if (document.body) {// all other Explorers

		yScroll = document.body.scrollTop;

	}



	arrayPageScroll = new Array('',yScroll)

	return arrayPageScroll;

}



function show_hide_side() {

	if (byid("side_menu").style.display == "none") {

		byid("side_menu").style.display = "";

		byid("frame_content").style.marginLeft = 196;

		byid("a_show_hide_side").innerHTML = "关闭侧栏";

	} else {

		byid("side_menu").style.display = "none";

		byid("frame_content").style.marginLeft = 0;

		byid("a_show_hide_side").innerHTML = "展开侧栏";

	}

}





function st(str) {

	str = window.status + " ==> "+str;

	if (str.length > 100) {

		str = str.substring(str.length - 100, str.length);

	}

	window.status = str;

}



var dom_loaded = {

	onload: [],

	loaded: function() {

		if (arguments.callee.done) return;

		arguments.callee.done = true;

		for (i = 0;i < dom_loaded.onload.length;i++) dom_loaded.onload[i]();

	},

	load: function(fireThis) {

		this.onload.push(fireThis);

		if (document.addEventListener)

			document.addEventListener("DOMContentLoaded", dom_loaded.loaded, null);

		if (/KHTML|WebKit/i.test(navigator.userAgent)) {

			var _timer = setInterval(function() {

				if (/loaded|complete/.test(document.readyState)) {

					clearInterval(_timer);

					delete _timer;

					dom_loaded.loaded();

				}

			}, 10);

		}

		/*@cc_on @*/

		/*@if (@_win32)

		var proto = "src='javascript:void(0)'";

		if (location.protocol == "https:") proto = "src=//0";

		document.write("<scr"+"ipt id=__ie_onload defer " + proto + "><\/scr"+"ipt>");

		var script = document.getElementById("__ie_onload");

		script.onreadystatechange = function() {

			if (this.readyState == "complete") {

				dom_loaded.loaded();

			}

		};

		/*@end @*/

		window.onload = dom_loaded.loaded;

	}

};



function swap_node(node1_name, node2_name) {

	var node1 = byid(node1_name);

	var node2 = byid(node2_name);



	var _parent = node1.parentNode;



	var o = _parent.childNodes;

	var _t1 = null, _t2 = null;

	for (var i=0; i<o.length; i++) {

		if (o[i].id == node1_name && i < o.length-1) {

			_t1 = o[i+1];

		}

		if (o[i].id == node2_name && i < o.length-1) {

			_t2 = o[i+1];

		}

	}



	if (_t1) {

		_parent.insertBefore(node2, _t1);

	} else {

		_parent.appendChild(node2);

	}

	if (_t2) {

		_parent.insertBefore(node1, _t2);

	} else {

		_parent.appendChild(node1);

	}

}



function co(obj, ty) {

	obj.style.backgroundColor = ty == 1 ? "#28D067" : "";

}





// 显示 div:

// type = "src|str"  "src":iframe.src, "str":string, innerHTML

function load_box(isshow, type, src_or_str, params_or_title) {

	if (isshow) {

		var wsize = get_size();

		var width = wsize[0];

		var height = Math.max(wsize[1], wsize[3]);



		byid("dl_layer_div").style.top = byid("dl_layer_div").style.left = "0px";

		byid("dl_layer_div").style.width = width+"px";

		byid("dl_layer_div").style.height = height+"px";

		byid("dl_layer_div").style.display = "block";



		byid("dl_box_div").style.left = (width-584)/2;

		byid("dl_box_div").style.top = 150;

		byid("dl_box_div").style.width = 600 + "px";

		byid("dl_box_div").style.display = "block";



		//byid("dl_iframe").style.display = byid("dl_content").style.display = "none";

		if (type == "src") {

			setTimeout(function() {byid("dl_set_iframe").src = src_or_str+(params_or_title ? "?"+params_or_title : '');}, 20);

			//byid("dl_box_loading").style.display = "block";

			//byid("dl_box_title").innerHTML = "加载中...";

			//byid("dl_box_div").style.height = 8 + byid("dl_box_title_box").offsetHeight + byid("dl_box_loading").offsetHeight + "px";

			timer_box = setInterval("reset_iframe_size()", 300);

			//timer_box = setTimeout("reset_iframe_size()", 1000);

		} else {

			byid("dl_content").innerHTML = src_or_str;

			byid("dl_content").style.display = "block";

			byid("dl_box_loading").style.display = "none";

			byid("dl_box_title").innerHTML = params_or_title;

			byid("dl_box_div").style.height = 8 + byid("dl_box_title_box").offsetHeight + byid("dl_content").offsetHeight + "px";

		}



		set_center(byid("dl_box_div"));



	} else {

		byid("dl_layer_div").style.display = "none";

		byid("dl_box_div").style.display = "none";

		byid("dl_set_iframe").src = "about:blank";

		try {

			clearInterval(timer_box);

		} catch (e) {

			return;

		}

	}

}





function load_src(isshow, src, w, h) {

	if (isshow) {

		var wsize = get_size();

		var width = wsize[0];

		var height = Math.max(wsize[1], wsize[3]);

		var wh = Math.min(wsize[1], wsize[3]);



		if (!w) {

			if (width > 1280) {

				w = width - 300;

			} else if (width > 1024) {

				w = width - 150;

			} else {

				w = width - 60;

			}

		}

		if (!h) {

			h = wh - 60;

		}



		var ow = Math.max(200, w); //弹出的宽度

		var oh = Math.max(100, h); //弹出的高度



		byid("dl_content").style.display = "none";



		byid("dl_layer_div").style.top = byid("dl_layer_div").style.left = "0px";

		byid("dl_layer_div").style.width = width+"px";

		byid("dl_layer_div").style.height = height+"px";

		byid("dl_layer_div").style.display = "block";



		byid("dl_box_div").style.left = (width-ow-16)/2;

		byid("dl_box_div").style.top = 30;

		byid("dl_box_div").style.width = ow + "px";

		byid("dl_box_div").style.height = oh + "px";

		byid("dl_box_div").style.display = "block";





		byid("dl_iframe").style.display = "block";

		byid("dl_set_iframe").src = src;

		byid("dl_set_iframe").style.height = oh - 38 + "px";

		byid("dl_iframe").style.height = oh - 38 + "px";

		//timer_box = setInterval("reset_iframe_size()", 100);



		set_center(byid("dl_box_div"));



	} else {

		byid("dl_layer_div").style.display = "none";

		byid("dl_box_div").style.display = "none";

		byid("dl_set_iframe").src = "about:blank";

		try {

			clearInterval(timer_box);

		} catch (e) {

			return;

		}

	}

}

function repeatlist_src(isshow, src, w, h) {

	if (isshow) {

		var wsize = get_size();

		var width = wsize[0];

		var height = Math.max(wsize[1], wsize[3]);

		var wh = Math.min(wsize[1], wsize[3]);



		if (!w) {

			if (width > 1280) {

				w = width - 300;

			} else if (width > 1024) {

				w = width - 150;

			} else {

				w = width - 60;

			}

		}

		if (!h) {

			h = wh - 60;

		}



		var ow = Math.max(200, w); //弹出的宽度

		var oh = Math.max(100, h); //弹出的高度



		byid("rl_content").style.display = "none";



		byid("rl_layer_div").style.top = byid("dl_layer_div").style.left = "0px";

		byid("rl_layer_div").style.width = width+"px";

		byid("rl_layer_div").style.height = height+"px";

		byid("rl_layer_div").style.display = "block";



		byid("rl_box_div").style.left = (width-ow-16)/2;

		byid("rl_box_div").style.top = 30;

		byid("rl_box_div").style.width = ow + "px";

		byid("rl_box_div").style.height = oh + "px";

		byid("rl_box_div").style.display = "block";





		byid("rl_iframe").style.display = "block";

		byid("rl_set_iframe").src = src;

		byid("rl_set_iframe").style.height = oh - 38 + "px";

		byid("rl_iframe").style.height = oh - 38 + "px";

		//timer_box = setInterval("reset_iframe_size()", 100);



		set_center(byid("rl_box_div"));



	} else {

		byid("rl_layer_div").style.display = "none";

		byid("rl_box_div").style.display = "none";

		byid("rl_set_iframe").src = "about:blank";

		try {

			clearInterval(timer_box);

		} catch (e) {

			return;

		}

	}

}



global_box_last_height = 0;



function reset_iframe_size(obj) {

	if (!obj) {

		obj = byid("dl_set_iframe");

	}

	var id = obj.id;

	var subWeb = document.frames ? document.frames[id].document : obj.contentDocument;

	try {

		byid("dl_iframe").style.display = "block";

		byid("dl_box_loading").style.display = "none";

	} catch (e) {

		return;

	}

	if(obj && subWeb) {

		var height = subWeb.body.scrollHeight;

		obj.height = height;

		byid("dl_box_title").innerHTML = subWeb.title;

		byid("dl_iframe").style.height = height+"px";

		byid("dl_set_iframe").style.height = height + "px";

		byid("dl_box_div").style.height = 8 + byid("dl_iframe").offsetHeight + byid("dl_box_title_box").offsetHeight + "px";

		//if (global_box_last_height == undefined) global_box_last_height = 0;

		if (global_box_last_height != height) {

			set_center(byid("dl_box_div"));

			global_box_last_height = height;

		}

	}

}





function update_title(obj) {

	if (!obj) {

		obj = byid("dl_set_iframe");

	}

	var id = obj.id;

	var subWeb = document.frames ? document.frames[id].document : obj.contentDocument;

	if(obj != null && subWeb != null) {

		byid("dl_box_title").innerHTML = subWeb.title;

	}

}



function reg_event(obj, event_basename, fn) {

	if (document.all) {

		obj.attachEvent("on"+event_basename, fn);

	} else {

		obj.addEventListener(event_basename, fn, false);

	}

}





function init() {

	// 原始页面标题:

	ori_doc_title = document.title;



	set_body_height();

	reg_event(window, "resize", set_body_height);

	init_top_menu();



	get_online();



	// 加载中图标:

	preload("loading.gif".split(","));



	if ((guess_mid = location.href.split("#")[1]) && menu_data[guess_mid]) {

		load(guess_mid);

	} else {

		var is_load = false;

		for (var i in menu_mids) {

			tmid = menu_mids[i];

			if (menu_data[tmid] && menu_data[tmid][1]) {

				load(tmid); break;

			}

			for (var nm in menu_stru[tmid]) {

				tiid = menu_stru[tmid][nm];

				if (menu_data[tiid] && menu_data[tiid][1]) {

					load(tiid); is_load = true; break;

				}

			}

			if (is_load) break;

		}

	}

}



// 刷新 sys_frame 内的内容:

function update_content() {

	byid("sys_frame").contentWindow.location.reload();

}



// 更新内容页面的局部ID:

// type:  innerHTML | value

function update_content_byid(id, value, type) {

	var o = byid("sys_frame").contentWindow.document.getElementById(id);

	if (o) {

		if (type == "value") {

			o.value = value;

		} else {

			o.innerHTML = value;

		}

	}

}





