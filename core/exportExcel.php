<?php

/**
 *
 * execl数据导出
 * 应用场景：数据导出
 * @param string $title 模型名（如Member），用于导出生成文件名的前缀
 * @param array $cellName 表头及字段名
 * @param array $data 导出的表数据
 *
 * 特殊处理：合并单元格需要先对数据进行处理
 */
function exportExcel($title,$cellName,$data)
{    
    //引入核心文件
	require ROOT."ToExcel/PHPExcel.php";
    $objPHPExcel = new PHPExcel();

    //定义配置
    $xlsTitle = iconv('gb2312', 'utf-8', $title);//文件名称
    $fileName = $title.date('_YmdHis');//文件名称
    $letter = array(
            'A','B','C','D','E','F','G','H','I','J','K','L','M',
            'N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
            'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM',
            'AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ'
    );
    $objActSheet = $objPHPExcel->getActiveSheet();
    //写在处理的前面（了解表格基本知识，已测试）
	
	 $objPHPExcel->getProperties()->setCreator("NJCJYY");//创建人
	 $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//水平居中
	 $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
	 $objPHPExcel->getDefaultStyle()->getFont()->setSize(11);//默认字体大小
     $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(12);//所有单元格（行）默认高度
	 $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(18);//所有单元格（列）默认宽度
    //处理表头
    for($i = 0;$i < count($cellName);$i++) {
            //单元宽度自适应,1.8.1版本phpexcel中文支持勉强可以，自适应后单独设置宽度无效
            //$objActSheet->getColumnDimension("$letter[$i]")->setAutoSize(true); 
            $objActSheet->setCellValue("$letter[$i]1",iconv('gbk', 'utf-8', $cellName[$i])); 
        }

    //处理数据
    for($i = 2;$i <= count($data)+1;$i++){
		$j = 0;
		foreach($data[$i-2] as $k=>$v){
			$objActSheet->setCellValue("$letter[$j]$i",iconv('gbk', 'utf-8', $v));
			$j++;
		}	
	}

    //导出execl
    //创建Excel输入对象
	$objWrite = new PHPExcel_Writer_Excel5($objPHPExcel);
	ob_end_clean();
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
	header("Content-Type:application/force-download");
	header("Content-Type:application/vnd.ms-execl");
	header("Content-Type:application/octet-stream");
	header("Content-Type:application/download");;
	header('Content-Disposition:attachment;filename="'.$fileName.'.xls"');
	header("Content-Transfer-Encoding:binary");
	$objWrite->save('php://output');
	//$objWriter->save("D:/OA/xls/".$filename);
}


?>