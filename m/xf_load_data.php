<?php
 
require "../core/core.php";
check_power('', $pinfo) or msg_box("û�д�Ȩ��...", "back", 1);
$op = $_GET["op"];
$table = "patient_".$hid;

// �������ӵĿ�ݺ���
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
		$a = str_pad("", $data_len, '*'); //��ֵ������(���ֶ���Ǻź󣬱�ʾ����λ��Ҫ������)
	} else {
		$a = str_replace(" ", "&nbsp;", str_pad($a, $data_len, " ")); //β���ӿո�
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
				$d[$hname."������"]["data"] = intval($hcounts);
				$d[$hname."������"]["link"] = "/m/patient/xiaofei.php?show=thismonth&xiaofei=1&doctor_name=".$hname;
			}
		}
		if($notxf_arr){
			foreach($notxf_arr as $nname => $ncounts){
				$d[$nname."δ����"]["data"] = intval($ncounts);
				$d[$nname."δ����"]["link"] = "/m/patient/xiaofei.php?show=thismonth&xiaofei=2&doctor_name=".$nname;
			}
		}
		if($arrived_arr){
			foreach($arrived_arr as $aname => $acounts){
				$d[$aname."����"]["data"] = intval($acounts);
				$d[$aname."����"]["link"] = "/m/patient/xiaofei.php?show=thismonth&doctor_name=".$aname;
			}
		}
		if($doctor_arr){
			foreach($doctor_arr as $n){
				$sd_html = '<tr onMouseOver="mi(this)" onMouseOut="mo(this)">'.
								'<td class="idata" align="center">'.$n["name"].'&nbsp;</td>'.
            					'<td>���� '.a5($d[$n["name"]."����"]).'</td>'.
            					'<td>������ '.a5($d[$n["name"]."������"]).'</td>'.
            					'<td>δ���� '.a5($d[$n["name"]."δ����"]).'</td>
							</tr>';
				
				array_push($sd, $sd_html);
				}
			}
	
	$d_all["������������"]["data"] = $xfed_arr_all = $db->query("select count(*) as counts from $table where status = 1 and is_xiaofei = 1 and $sqlwhere", 1, "counts");
	$d_all["������δ����"]["data"] = $notxf_arr_all = $db->query("select count(*) as counts from $table where status = 1 and is_xiaofei <> 1 and $sqlwhere", 1, "counts");
	$d_all["�����ݵ���"]["data"] = $arrived_arr_all = $db->query("select count(*) as counts from $table where status = 1 and $sqlwhere", 1, "counts");
	
	$d_all["������������"]["link"] = "/m/patient/xiaofei.php?show=thismonth&xiaofei=1";
	$d_all["������δ����"]["link"] = "/m/patient/xiaofei.php?show=thismonth&xiaofei=2";
	$d_all["�����ݵ���"]["link"] = "/m/patient/xiaofei.php?show=thismonth";
	
	$sd_html_all = '<tr onMouseOver="mi(this)" onMouseOut="mo(this)" style="border-top:1px solid #000">'.
								'<td class="idata" align="center"  style="border-top:1px solid #000">������&nbsp;</td>'.
            					'<td>���� '.a5($d_all["�����ݵ���"]).'</td>'.
            					'<td>������ '.a5($d_all["������������"]).'</td>'.
            					'<td>δ���� '.a5($d_all["������δ����"]).'</td>
							</tr>';
	array_push($sd, $sd_html_all);
	
	$str = implode(",", $sd);	
    echo $str;
		
}else{
	echo "��";
	}
?>
