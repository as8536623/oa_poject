<?php
 
require "../core/core.php";
check_power('', $pinfo) or msg_box("没有打开权限...", "back", 1);
$op = $_GET["op"];
$table_ku = "ku_list";

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
	$time_ty = "addtime";
	$time_ty1 = "order_date";
	if ($_GET["smb"] == ""){
		$tb = "";
	}else{
		$smb = $_GET["smb"];
		$tb = strtotime($smb." 0:0:0");
		$where[] = "and $time_ty>=$tb";
		$where1[] = "and $time_ty1>=$tb";
		}
	
	if ($_GET["sme"] == ""){
		$te = "";
	}else{
		$sme = $_GET["sme"];
		$te = strtotime($sme." 23:59:59");
		$where[] = " and $time_ty<$te";
		$where1[] = " and $time_ty1<$te";
		}
		
	$sqlwhere = count($where) ? (implode(" ", $where)) : "";
	$sqlwhere1 = count($where1) ? (implode(" ", $where1)) : "";
	
	$betime = "btime=".$smb."&etime=".$sme;

	$author_arr = $db->query("select realname from sys_admin where isshow = 1 and part_id in (2,209) and hospitals like '%".$hid."%'");
	$zixun_arr = $db->query("select u_name,count(u_name) as counts from $table_ku where hid = $hid $sqlwhere group by u_name", "u_name", "counts");
	$havehf_arr = $db->query("select u_name,count(u_name) as counts from $table_ku where hf_log <> '' and hid = $hid $sqlwhere group by u_name", "u_name", "counts");
	$nothf_arr = $db->query("select u_name,count(u_name) as counts from $table_ku where hf_log = '' and hid = $hid $sqlwhere group by u_name", "u_name", "counts");
	$arrived_arr = $db->query("select author,count(author) as counts from patient_$hid where status = 1 $sqlwhere1 group by author", "author", "counts");
	$d = array();
	$sd = array();
		if($zixun_arr){	
			foreach($zixun_arr as $zname => $zcounts){
				$d[$zname."咨询"]["data"] = intval($zcounts);
				$d[$zname."咨询"]["link"] = "/m/ziliaoku/ku_list1.php?".$betime."&kefu_23_name=".$zname;
			}
		}
		if($havehf_arr){
			foreach($havehf_arr as $hname => $hcounts){
				$d[$hname."回访"]["data"] = intval($hcounts);
				$d[$hname."回访"]["link"] = "/m/ziliaoku/ku_list1.php?".$betime."&kefu_23_name=".$hname."&hf_log=1";
			}
		}
		if($nothf_arr){
			foreach($nothf_arr as $nname => $ncounts){
				$d[$nname."未回访"]["data"] = intval($ncounts);
				$d[$nname."未回访"]["link"] = "/m/ziliaoku/ku_list1.php?".$betime."&kefu_23_name=".$nname."&hf_log=2";
			}
		}
		if($arrived_arr){
			foreach($arrived_arr as $aname => $acounts){
				$d[$aname."实到"]["data"] = intval($acounts);
				$d[$aname."实到"]["link"] = "/m/patient/patient.php?".$betime."&come=1&kefu_23_name=".$aname;
			}
		}
		if($author_arr){
			foreach($author_arr as $n){
				$sd_html = '<tr onMouseOver="mi(this)" onMouseOut="mo(this)">'.
								'<td class="idata" align="center">'.$n["realname"].'&nbsp;</td>'.
            					'<td>咨询 '.a5($d[$n["realname"]."咨询"]).'</td>'.
            					'<td>回访 '.a5($d[$n["realname"]."回访"]).'</td>'.
            					'<td>未回访 '.a5($d[$n["realname"]."未回访"]).'</td>'.
								'<td>实到 '.a5($d[$n["realname"]."实到"]).'</td>
							</tr>';
				
				array_push($sd, $sd_html);
				}
			}
	
	$d_all["总数据咨询"]["data"] = $zixun_arr_all = $db->query("select count(*) as counts from ku_list where hid = $hid $sqlwhere", 1, "counts");
	$d_all["总数据回访"]["data"] = $havehf_arr_all = $db->query("select count(*) as counts from ku_list where hf_log <> '' and hid = $hid $sqlwhere", 1, "counts");
	$d_all["总数据未回访"]["data"] = $nothf_arr_all = $db->query("select count(*) as counts from ku_list where hf_log = '' and hid = $hid $sqlwhere", 1, "counts");
	$d_all["总数据实到"]["data"] = $arrived_arr_all = $db->query("select count(*) as counts from patient_$hid where status = 1 $sqlwhere1", 1, "counts");
	
	$d_all["总数据咨询"]["link"] = "/m/ziliaoku/ku_list1.php?".$betime;
	$d_all["总数据回访"]["link"] = "/m/ziliaoku/ku_list1.php?".$betime."&hf_log=1";
	$d_all["总数据未回访"]["link"] = "/m/ziliaoku/ku_list1.php?".$betime."&hf_log=2";
	$d_all["总数据实到"]["link"] = "/m/patient/patient.php?".$betime."&come=1";
	
	$sd_html_all = '<tr onMouseOver="mi(this)" onMouseOut="mo(this)" style="border-top:1px solid #000">'.
								'<td class="idata" align="center"  style="border-top:1px solid #000">总数据&nbsp;</td>'.
            					'<td>咨询 '.a5($d_all["总数据咨询"]).'</td>'.
            					'<td>回访 '.a5($d_all["总数据回访"]).'</td>'.
            					'<td>未回访 '.a5($d_all["总数据未回访"]).'</td>'.
								'<td>实到 '.a5($d_all["总数据实到"]).'</td>
							</tr>';
	array_push($sd, $sd_html_all);
	
	$str = implode(",", $sd);	
    echo $str;
		
}else{
	echo "错咯";
	}
?>
