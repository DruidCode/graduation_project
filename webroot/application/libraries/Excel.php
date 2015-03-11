<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once dirname(__FILE__).'/excel/PHPExcel.php';
require_once dirname(__FILE__).'/excel/PHPExcel/IOFactory.php';
class Excel {
function read($filePath){
        $PHPExcel = new PHPExcel(); 
        /**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/ 
        $PHPReader = new PHPExcel_Reader_Excel2007(); 
        if(!$PHPReader->canRead($filePath)){ 
                $PHPReader = new PHPExcel_Reader_Excel5(); 
                if(!$PHPReader->canRead($filePath)){ 
                        echo 'no Excel'; 
                        return ; 
                } 
        } 
        $PHPExcel = $PHPReader->load($filePath); 
        /**读取excel文件中的第一个工作表*/ 
        $currentSheet = $PHPExcel->getSheet(0); 
        /**取得最大的列号*/ 
        $allColumn = $currentSheet->getHighestColumn(); 
        /**取得一共有多少行*/ 
        $allRow = $currentSheet->getHighestRow(); 
        /**从第二行开始输出，因为excel表中第一行为列名*/ 
        $arr = array();
        $i = 0;
		$file = fopen('ok.csv', 'ab');
        for($currentRow = 2;$currentRow <= $allRow;$currentRow++){ 
                
                $j = 0;
                /**从第A列开始输出*/ 
                for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++){ 
                        $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();/**ord()将字符转为十进制数*/ 
						$val = str_replace(array("\r\n", "\r", "\n"), "", $val);
						if (empty($val)) continue;
						$val = $val . "^";
						fwrite($file, $val);
                        $arr[$i][$j] = $val; 
                        $j ++;
                } 
						fwrite($file, "\r\n");
                $i ++;
        } 
		fclose($file);
        return $arr;
}

function write($filename, $header, $datas){

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                ->setLastModifiedBy("Maarten Balliauw")
                ->setTitle("Office 2007 XLSX Zhisland Document")
                ->setSubject("Office 2007 XLSX Zhisland Document")
                ->setDescription("Zhisland document for Office 2007 XLSX")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Zhisland result file");

        $objPHPExcel->setActiveSheetIndex(0);
        $start = ord('A');
        $line = 1;
        foreach($header as $key=>$val){
                 $index = $key;
                 $objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($start + $index).$line, $val);
        }
    
        // Miscellaneous glyphs, UTF-8
        foreach($datas as $key=>$data){
               ++$line ;
               foreach($data as $key=>$val){
                    $index = $key;
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($start + $index).$line, $val);
               }
        }

        // Rename worksheet
        //$objPHPExcel->getActiveSheet()->setTitle('Zhisland');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
}
}
