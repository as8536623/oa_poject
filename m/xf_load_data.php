<?php
 
require "../core/core.php";
check_power('', $pinfo) or msg_box("没有打开权限...", "back", 1);
$op = $_GET["op"];
$table = "patient_".$hid;

// 生成链接的快捷函数
function a5($arr) {
	return aa($arr, 5);
}
function a4($arr) {
	return aa($arr, 4);
}
function a3($arr) {
	return aa($arr, 3);
}
function aa($arr, $data_len=3) {
	$a = empty($arr["data"]) ? "0" : $arr["data"];
	if (strlen(trim($a)) > $data_len) {
		$a = str_pad("", $data_len, '*'); //数值溢出标记(出现多个星号后，表示数字位数要增加啦)
	} else {
		$a = str_replace(" ", "&nbsp;", str_pad($a, $data_len, " ")); //尾部加空格
	}
	if ($arr["link"]) {
		$a = '<b class="fa"><a href="'.$arr["link"].'" class="fb">'.$a.'</a></b>';
	} else {
		$a = '<b class="fa">'.$a.'</b>';
	}
	return $a;
}
$where = array();

if ($op == "show") {
	$time_ty = "order_date";
	$begin_time = mktime(0,0,0,date("m"),1);
	$end_time = strtotime("+1 month", $begin_time);
	$where[] = $time_ty.'>='.$begin_time;
	$where[] = $time_ty.'<'.$end_time;
		
	$sqlwhere = count($where) ? (implode(" and ", $where)) : "";

	$doctor_arr = $db->query("select name from doctor where  hospital_id =$hid");
	$arrived_arr = $db->query("select doctor,count(doctor) as counts from $table where status = 1 and $sqlwhere group by doctor", "doctor", "counts");
	$xfed_arr = $db->query("select doctor,count(doctor) as counts from $table where status = 1 and is_xiaofei = 1 and $sqlwhere group by doctor", "doctor", "counts");
	$notxf_arr = $db->query("select doctor,count(doctor) as counts from $table where status = 1 and is_xiaofei <> 1 and $sqlwhere group by doctor", "doctor", "counts");
	$d = array();
	$sd = array();
		if($xfed_arr){
			foreach($xfed_arr as $hname => $hcounts){
				$d[$hname."已消费"]["data"] = intval($hcounts);
				$d[$hname."已消费"]["link"] = "/m/patient/xiaofei.php?show=thismonth&xiaofei=1&doctor_name=".$hname;
			}
		}
		if($notxf_arr){
			foreach($notxf_arr as $nname => $ncounts){
				$d[$nname."未消费"]["data"] = intval($ncounts);
				$d[$nname."未消费"]["link"] = "/m/patient/xiaofei.php?show=thismonth&xiaofei=2&doctor_name=".$nname;
			}
		}
		if($arrived_arr){
			foreach($arrived_arr as $aname => $acounts){
				$d[$aname."到诊"]["data"] = intval($acounts);
				$d[$aname."到诊"]["link"] = "/m/patient/xiaofei.php?show=thismonth&doctor_name=".$aname;
			}
		}
		if($doctor_arr){
			foreach($doctor_arr as $n){
				$sd_html = '<tr onMouseOver="mi(this)" onMouseOut="mo(this)">'.
								'<td class="idata" align="center">'.$n["name"].'&nbsp;</td>'.
            					'<td>到诊 '.a5($d[$n["name"]."到诊"]).'</td>'.
            					'<td>已消费 '.a5($d[$n["name"]."已消费"]).'</td>'.
            					'<td>未消费 '.a5($d[$n["name"]."未消费"]).'</td>
							</tr>';
				
				array_push($sd, $sd_html);
				}
			}
	
	$d_all["总数据已消费"]["data"] = $xfed_arr_all = $db->query("select count(*) as counts from $table where status = 1 and is_xiaofei = 1 and $sqlwhere", 1, "counts");
	$d_all["总数据未消费"]["data"] = $notxf_arr_all = $db->query("select count(*) as counts from $table where status = 1 and is_xiaofei <> 1 and $sqlwhere", 1, "counts");
	$d_all["总数据到诊"]["data"] = $arrived_arr_all = $db->query("select count(*) as counts from $table where status = 1 and $sqlwhere", 1, "counts");
	
	$d_all["总数据已消费"]["link"] = "/m/patient/xiaofei.php?show=thismonth&xiaofei=1";
	$d_all["总数据未消费"]["link"] = "/m/patient/xiaofei.php?show=thismonth&xiaofei=2";
	$d_all["总数据到诊"]["link"] = "/m/patient/xiaofei.php?show=thismonth";
	
	$sd_html_all = '<tr onMouseOver="mi(this)" onMouseOut="mo(this)" style="border-top:1px solid #000">'.
								'<td class="idata" align="center"  style="border-top:1px solid #000">总数据&nbsp;</td>'.
            					'<td>到诊 '.a5($d_all["总数据到诊"]).'</td>'.
            					'<td>已消费 '.a5($d_all["总数据已消费"]).'</td>'.
            					'<td>未消费 '.a5($d_all["总数据未消费"]).'</td>
							</tr>';
	array_push($sd, $sd_html_all);
	
	$str = implode(",", $sd);	
    echo $str;
		
}else{
	echo "错咯";
	}
?>
