// --------------------------------------------------------
// - 功能说明 : JavaScript 函数库
// - 创建作者 : zhuwenya (zhuwenya@126.com)
// - 创建时间 : 2008-04-20 08:00 => 2008-06-21 16:28
// --------------------------------------------------------
var nSelCount=0;

function byid(id) {
	return document.getElementById(id);
}


// 检查一个字符串是否在数组中
function in_array(str, arr) {
	if (!arr.length || arr.length == 0) {
		return false;
	}
	for (var i = 0; i < arr.length; i++) {
		if (str == arr[i]) {
			return true;
		}
	}
	return false;
}

// 获取字符串（或任意类型）中包含的数字
function get_num(string) {
	var nums = '';
	string = '' + string;
	for (var i=0; i<string.length; i++) {
		var ch = string.substring(i, i+1);
		if (ch in [0,1,2,3,4,5,6,7,8,9]) {
			nums += ch;
		}
	}
	return nums;
}


function set_center(obj) {
	var objwidth = obj.offsetWidth;
	var objheight = obj.offsetHeight;
	var left = (document.documentElement.clientWidth - objwidth) / 2;
	obj.style.left = left+"px";
	var top = document.documentElement.scrollTop+(document.documentElement.clientHeight - objheight) / 2;
	obj.style.top = top+"px";
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

// json 返回值的解析处理:
function ajax_out(xm) {
	var s = xm.responseText;
	if (s == "") {
		alert("ajax返回结果为空.."); return {};
	}
	try {
		eval("var out="+s+";");
	} catch (e) {
		alert(s);
		return {};
	}

	return out;
}


function reg_event(obj, event_basename, fn) {
	if (document.all) {
		obj.attachEvent("on"+event_basename, fn);
	} else {
		obj.addEventListener(event_basename, fn, false);
	}
}


function msg_box(string, showtime) {
	if (window.parent && window.parent.msg_box) {
		window.parent.msg_box(string, showtime);
	} else {
		alert(string);
	}
}


function set_title(obj, title_id) {
	var oti = byid(title_id);
	if (oti.value=="") {
		var s=obj.value; var isb=false; u="";
		for (ni=s.length; ni>=0; ni--) {c=s.charAt(ni);if(isb){if(c=='/'||c=='\\'||c=='_'||c=='.')break;u=c+u;}if(c=='.')isb=true;}
		oti.value=u;
	}
}


// select_type: all,reverse,none 之一，分别表示:全选，反选，不选
function select(select_type) {
	var f = document.forms["mainform"];
	if (f) {
		var chk = f.getElementsByTagName("INPUT");
		for (var i = 0; i < chk.length; i++) {
			o = chk[i];
			if (o.type == "checkbox" && o.disabled != true) {
				if (select_type == 'all') {
					o.checked = true;
				} else if (select_type == 'reverse') {
					o.checked = !o.checked;
				} else if (select_type == 'none') {
					o.checked = false;
				}
				if (o.onclick) {
					o.onclick(o); //执行点击后操作
				}
			}
		}
	}
}

function select_all() {
	var ofm=document.forms["mainform"]; nSelCount=0;
	for(var i=0; i<ofm.elements.length; i++) {
		var e=ofm.elements[i];
		if(e.type=='checkbox'&&e.disabled!=true){e.checked=true; nSelCount++;}
	}
}

function select_none() {
	ofm=document.forms["mainform"]; nSelCount=0;
	for(var i=0; i<ofm.elements.length; i++) {
		var e=ofm.elements[i];
		if(e.type=='checkbox' && e.disabled!=true) {e.checked=false; nSelCount++;}
	}
}

function unselect() {
	ofm=document.forms["mainform"]; nSelCount = 0;
	for(var i=0; i<ofm.elements.length; i++) {
		var e = ofm.elements[i];
		if(e.type == 'checkbox' && e.disabled != true) {e.checked=!e.checked; nSelCount++;}
	}
}

function get_select() {
	ofm=document.forms["mainform"]; var u=''; nSelCount=0;
	for (var i=0; i<ofm.elements.length; i++) {
		var e = ofm.elements[i];
		if(e.type == 'checkbox' && e.checked == true && e.name != 'group'){if(nSelCount>0)u+=","; u+=e.value; nSelCount++;}
	}
	return u;
}


function get_select_count() {
	var f = document.forms["mainform"];
	var cnt = 0;
	if (f) {
		var chk = f.getElementsByTagName("INPUT");
		for (var i = 0; i < chk.length; i++) {
			o = chk[i];
			if (o.type == "checkbox" && o.disabled != true && o.checked == true) {
				cnt++;
			}
		}
	}
	return cnt;
}

function del() {
	var f = document.forms["mainform"];
	var cnt = get_select_count();
	if (cnt == 0) {
		alert("您没有选择任何一条资料，无法执行删除！");
		return false;
	}
	if (!confirm("共选择了 "+cnt+" 条资料，您确定要删除吗？")) {
		return false;
	}
	f.op.value = "delete";
	f.submit();
}

function set_check(cl, chk) {
	k=chk.checked; al=cl.split(','); ofm=document.forms["mainform"];
	for (ni=0; ni<al.length; ni++) {if (al[ni]) {s="ofm."+al[ni]+".checked="+k+";"; eval(s); }}
}

function set_parent_check(cl, chk) {
	k=chk.checked;
	if (k) {
		al=cl.split(','); ofm=document.forms["mainform"];
		for (ni=0; ni<al.length; ni++) {if (al[ni]) {s="ofm."+al[ni]+".checked="+k+";"; eval(s); }}
	}
}

function mi(o) {
	var tds = o.getElementsByTagName("td");
	for (var i = 0; i < tds.length; i ++) {
		tds[i].style.backgroundColor = "#E0EFEF";
	}
}

function mo(o) {
	var tds = o.getElementsByTagName("td");
	for (var i = 0; i < tds.length; i ++) {
		tds[i].style.backgroundColor = "";
	}
}

function set_item_color(obj) {
	if (obj.checked) {
		obj.parentNode.parentNode.className = "list_tr_checked";
	} else {
		obj.parentNode.parentNode.className = "";
	}
}

window.last_high_obj = '';
function set_high_light(obj) {
	if (last_high_obj) {
		var tr = last_high_obj.parentNode.parentNode;
		tr.className = "line";
	}
	if (obj) {
		var tr = obj.parentNode.parentNode;
		mo(tr);
		tr.className = "line tr_high_light";
		last_high_obj = obj;
	} else {
		last_high_obj = '';
	}
}

function isdel() {
	return confirm("您确定要删除该资料吗？");
}

if (!document.all) {
	HTMLElement.prototype.insertAdjacentHTML = function(where, html) {
		var e = this.ownerDocument.createRange();
		e.setStartBefore(this);
		e = e.createContextualFragment(html);
		switch (where) {
			case 'beforeBegin': this.parentNode.insertBefore(e, this);break;
			case 'afterBegin': this.insertBefore(e, this.firstChild); break;
			case 'beforeEnd': this.appendChild(e); break;
			case 'afterEnd':
				if(!this.nextSibling) this.parentNode.appendChild(e);
				else this.parentNode.insertBefore(e, this.nextSibling); break;
		}
	};
}

function round_top_add_table(o) {
	var html = '<table width="100%" style="border:0;"><tr><td style="height:4px; width:100%;padding:0;" class="t_tc"><div class="t_lt"></div><div class="t_rt"></div><div class="clear"></div></td></tr></table>';
	o.insertAdjacentHTML("beforeBegin", html);
}

function round_bottom_add_table(o) {
	var html = '<table width="100%" style="border:0;"><tr><td style="height:4px; width:100%;padding:0;" class="t_bc"><div class="t_lb"></div><div class="t_rb"></div><div class="clear"></div></td></tr></table>';
	o.insertAdjacentHTML("afterEnd", html);
}


// 兼容IE chrome FF的获取当前样式的方法
// var tbl = getCurrentStyle(t, "borderLeftWidth");
function getCurrentStyle(obj, prop) {
	if (obj.currentStyle) {
		return obj.currentStyle[prop];
	} else if (window.getComputedStyle) {
		prop = prop.replace(/([A-Z])/g, "-$1");
		prop = prop.toLowerCase();
		return window.getComputedStyle(obj, "").getPropertyValue(prop);
	}
	return null;
}


function page_init() {
	if (window.page_is_init == true) {
		return;
	}
	window.page_is_init = true;

	// 获取页面中的表格元素：
	var ts = document.getElementsByTagName("TABLE");
	var len = ts.length;
	if (len > 0) {
		for (var i = len - 1; i >= 0; i--) {
			var t = ts[i];
			var tc = t.className; //样式筛选用
			tc = tc.split(" ")[0];
			var tbl = getCurrentStyle(t, "borderLeftWidth"); //表格边框宽度筛选用
			if (tc != '' && t.style.display != "none" && tbl == "1px" && (in_array(tc, "new_list list edit view round_table".split(" ")) )) {
				round_top_add_table(t);
				round_bottom_add_table(t);
				t.style.borderTop = 0;
				t.style.borderBottom = 0;

				// 表格第一行的border-top设为0:
				var tr = t.getElementsByTagName("TR");
				if (tr.length > 0) {
					var tr1 = tr[0];
					var td = tr1.getElementsByTagName("TD");
					if (td.length > 0) {
						for (var j = 0; j < td.length; j++) {
							td[j].style.borderTop = 0;
							td[j].style.paddingTop = "3px";
							td[j].style.paddingBottom = "5px";
						}
					}
				}

				// 表格最后一行的border-bottom 设为0:
				if (tr.length > 1) {
					var trn = tr[tr.length - 1];
					var td = trn.getElementsByTagName("TD");
					if (td.length > 0) {
						for (var j = 0; j < td.length; j++) {
							td[j].style.borderBottom = 0;
						}
					}
				}

			}
		}
	}

	window.document.onmouseover = function(e) {
		var event = e ? e : (window.event ? window.event : null);
		var o = event.srcElement ? event.srcElement : event.target;
		//parent.document.title = o.tagName;
		while (o.tagName != 'TD' && o.parentNode.tagName != 'HTML') {
			o = o.parentNode;
		}
		if (o.tagName == "TD" && (o.className == "item" || o.parentNode.className == "line")) {
			mi(o.parentNode);
		}
	}

	window.document.onmouseout = function(e) {
		var event = e ? e : (window.event ? window.event : null);
		var o = event.srcElement ? event.srcElement : event.target;
		while (o.tagName != 'TD' && o.parentNode.tagName != 'HTML') {
			o = o.parentNode;
		}
		if (o.tagName == "TD" && (o.className == "item" || o.parentNode.className == "line")) {
			mo(o.parentNode);
		}
	}

	// 表格效果:
	var etable = document.getElementsByTagName("table");
	for (var i=0; i<etable.length; i++) {
		// 对edit页面加动态效果:
		if (etable[i].className == "edit") {
			var etr = etable[i].getElementsByTagName("tr");
			for (var j=0; j<etr.length; j++) {
				var etd = etr[j].getElementsByTagName("td");
				for (var x=0; x<etd.length; x++) {
					etd[x].style.backgroundColor = (j % 2 ? "#FFFFFF" : "#F9F9F9");
					if (in_class("left", etd[x].className)) {
						//etd[x].style.backgroundColor = (j % 2 ? "#FCFCFC" : "#F6F6F6");
					}
					if (in_class("right", etd[x].className)) {
						//etd[x].style.backgroundColor = (j % 2 ? "#FFFFFF" : "#F9F9F9");
					}
				}
			}
		}
	}

	// 按钮,输入框效果
	var eto = [];
	var einput = document.getElementsByTagName("input");
	for (var i=0; i<einput.length; i++) {
		if (einput[i].type == "text" || einput[i].type == "password" || einput[i].type == "file") {
			eto.push(einput[i]);
		}
	}
	var etextarea = document.getElementsByTagName("textarea");
	for (var i=0; i<etextarea.length; i++) {
		eto.push(etextarea[i]);
	}

	var eselect = document.getElementsByTagName("select");
	for (var i=0; i<eselect.length; i++) {
		eto.push(eselect[i]);
	}

	for (var i=0; i<eto.length; i++) {
		reg_event(eto[i], "focus", function(e) {
			var event = e ? e : (window.event ? window.event : null);
			var o = event.srcElement ? event.srcElement : event.target;
			if (o.className == "input") {
				o.className = "input_focus";
			}
		});
		reg_event(eto[i], "blur", function(e) {
			var event = e ? e : (window.event ? window.event : null);
			var o = event.srcElement ? event.srcElement : event.target;
			if (o.className == "input_focus") {
				o.className = "input";
			}
		});
	}

	// 按钮效果:
	var eto = [];
	var einput = document.getElementsByTagName("input");
	for (var i=0; i<einput.length; i++) {
		if (einput[i].type == "button" || einput[i].type == "submit") {
			eto.push(einput[i]);
		}
	}
	var ebutton = document.getElementsByTagName("button");
	for (var i=0; i<ebutton.length; i++) {
		eto.push(ebutton[i]);
	}

	for (var i=0; i<eto.length; i++) {
		var c = eto[i].className;
		if (in_class("button", c) || in_class("buttonb", c) || in_class("search", c) || in_class("submit", c)) {
			eto[i].onmouseover = function() {
				add_class(this, this.className.split(" ")[0]+"_over");
			}
			eto[i].onmouseout = function() {
				remove_class(this, this.className.split(" ")[0]+"_over");
			}
		}
	}

	var s = self.location.href.split("#")[1];
	if (s != '' && !isNaN(s) && byid("#"+s)) {
		byid("#"+s).className = "list_tr_modified";
		byid("#"+s).scrollIntoView(true);
	}
}

function in_class(class_name, obj_class) {
	var obj_class_s = obj_class.split(" ");
	for (var i=0; i<obj_class_s.length; i++) {
		if (obj_class_s[i] == class_name) {
			return true;
		}
	}
	return false;
}

function add_class(o, new_class) {
	var s = o.className;
	o.className = s ? s+" "+new_class : new_class;
}

function remove_class(o, class_name) {
	var s = o.className;
	if (s == class_name) {
		o.className = '';
	} else {
		var s_s = s.split(" ");
		var new_class = [];
		for (var i=0; i<s_s.length; i++) {
			if (s_s[i] != class_name) {
				new_class.push(s_s[i]);
			}
		}
		o.className = new_class.join(" ");
	}
}

function reg_event(obj, type, fn) {
	if(obj.attachEvent) {
		obj['e'+type+fn] = fn;
		obj[type+fn] = function(){obj['e'+type+fn](window.event);}
		obj.attachEvent('on'+type, obj[type+fn]);
	}else {
		obj.addEventListener(type, fn, false);
	}
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


dom_loaded.load(page_init);