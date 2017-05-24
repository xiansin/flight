<?php

/**
 * Created by PhpStorm
 * FileName: Export.php
 * ProjectName:flight
 * User: jianjia.zhou@longmaster.com.cn
 * DateTime: 2017/5/24 10:33
 */

class Export
{
    /**
     * @description        导出csv文件
     * @version            V1.0<增加是否在同一个表格存在多个表格>
     * @author             JianJia.Zhou<jianjia.zhou@longmaster.com.cn>
     * @lastModify         2017-5-19 16:45:25
     * @param string  $fileName       导出文件名
     * @param string  $excelTitle     表格头
     * @param array   $excelCellName  表格第一行名
     * @param array   $excelTableData 导出数据
     * @param boolean $isMany         是否在同一个表格存在多个表格
     */
    public static function exportExcelCsv($fileName, $excelTitle, $excelCellName, $excelTableData, $isMany = false)
    {
        $file = "{$excelTitle}\n";
        $exportDate = date('Y-m-d H:i:s');
        $file .= "导出时间：[{$exportDate}]\n";
        $cellKey = [];
        if ($isMany === true) {
            $cellCount = count($excelCellName);
            for ($i = 0; $i < $cellCount; $i++) {
                $cellKey = [];
                foreach ($excelCellName[$i] as $key => $value) {
                    $cellKey[$key] = $value['key'];
                    $file .= "{$value['value']},";
                }
                $file .= "\n";
                foreach ($excelTableData[$i] as $k => $tableArray) {
                    foreach ($cellKey as $key) {
                        if (strpos($tableArray[$key], "fee") !== false) {
                            $file .= "{$tableArray[$key]},";
                        } else {
                            $file .= "{$tableArray[$key]}\t,";
                        }
                    }
                    $file .= "\n";
                }
                $file .= "\n\n\n\n";
            }
        } else {
            foreach ($excelCellName as $key => $value) {
                $cellKey[$key] = $value['key'];
                $file .= "{$value['value']},";
            }
            $file .= "\n";
            foreach ($excelTableData as $k => $tableArray) {
                foreach ($cellKey as $key) {
                    if (strpos($tableArray[$key], "fee") !== false) {
                        $file .= "{$tableArray[$key]},";
                    } else {
                        $file .= "{$tableArray[$key]}\t,";
                    }
                }
                $file .= "\n";
            }
        }
        header("Content-type:text/csv");
        $fileName = self::mbConvertEncoding($fileName);
        header("Content-Disposition:attachment;filename={$fileName}_" . date('YmdHis') . ".csv");
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        $file = self::mbConvertEncoding($file);
        die($file);
    }


    /**
     * @description        导出excel
     * @version            V1.0
     * @author             JianJia.Zhou<jianjia.zhou@longmaster.com.cn>
     * @lastModify         2017-5-24 10:55:51
     * @param string $fileName       文件名
     * @param string $excelTitle     文件title
     * @param array  $excelCellName  表头
     * @param array  $excelTableData 表格数据
     */
    public static function exportExcel($fileName = '', $excelTitle = '', $excelCellName = [], $excelTableData = [])
    {
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        $cellName = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'];
        $cellNum = count($excelCellName);
        $tableNum = count($excelTableData);
        $pageSize = 10000;///sheet显示条数
        $pageNum = ceil($tableNum / $pageSize);///sheet数
        $excelTableArray = $pageNum > 1 ? array_chunk($excelTableData, $pageSize) : [$excelTableData];

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("longmaster.com");
        $objPHPExcel->getProperties()->setTitle($excelTitle);
        ///分页输出数据
        foreach ($excelTableArray as $page => $tableArray) {
            if ($page != 0) {
                $objPHPExcel->createSheet($page);
            }
            $objPHPExcel->setActiveSheetIndex($page);
            $objPHPExcel->getActiveSheet()->setTitle("第" . ($page + 1) . "页");
            $cover = 0;
            if ($page == 0) {
                $objPHPExcel->getActiveSheet()->mergeCells("A1:{$cellName[$cellNum-1]}1")->getStyle("A1:{$cellName[$cellNum-1]}1")->getFont()->setBold(true);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A1", $excelTitle);
                $cover = 1;
            }
            $cellKey = [];
            foreach ($excelCellName as $key => $value) {
                $cellKey[$key] = $value['key'];
                if (isset($value['width'])) {
                    $objPHPExcel->getActiveSheet()->getColumnDimension($cellName[$key])->setWidth($value['width']);
                } else {
                    $objPHPExcel->getActiveSheet()->getColumnDimension($cellName[$key])->setAutoSize(true);
                }
                if (isset($value['formant'])) {
                    $objPHPExcel->getActiveSheet()->getStyle($cellName[$key])->getNumberFormat()->setFormatCode($value['formant']);
                }
                $objPHPExcel->getActiveSheet()->getStyle($cellName[$key] . (1 + $cover))->getFont()->setBold(true);
                $objPHPExcel->setActiveSheetIndex($page)->setCellValue($cellName[$key] . (1 + $cover), $value['value']);
            }
            foreach ($tableArray as $i => $row) {
                foreach ($cellKey as $j => $key) {
                    $objPHPExcel->setActiveSheetIndex($page)->setCellValue($cellName[$j] . ($i + 2 + $cover), $row[$key]);
                }
            }
            sleep(3);
        }
        ///导出
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $fileName . '.xls"');
        header("Content-Disposition:attachment;filename={$fileName}_" . date('YmdHis') . ".xls");//attachment新窗口打印inline本窗口打印
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }


    /**
     * @description        保存CSV
     * @version            V1.0
     * @author             JianJia.Zhou<jianjia.zhou@longmaster.com.cn>
     * @lastModify         2017-5-24 10:56:45
     * @param  string $excelName      文件名
     * @param  string $excelTitle     文件title
     * @param  array  $excelCallName  表头
     * @param  array  $excelTableData 表格数据
     * @param  null   $fileDir        保存目录
     * @param  array  $screen
     * @return string
     */
    public static function saveExcelCsv($excelName = '', $excelTitle = '', $excelCallName = [], $excelTableData = [], $fileDir = null, $screen = [])
    {
        if (!$fileDir) {
            if (APP_PATH) {
                $fileDir = rtrim(APP_PATH, '/') . 'storage/csv/' . date("YmdHis") . '/';
            } else {
                $fileDir = DIR . "storage/csv/" . date("YmdHis") . "/";
            }
        }
        \Folder::createFolder($fileDir);
        $excelName = $excelName . ".csv";
        $fileName = $fileDir . $excelName;
        $fileName = self::mbConvertEncoding($fileName);
        if (!is_writable($fileName)) {
            @touch($fileName);
        }
        $data = [];
        $cellKey = [];
        foreach ($excelCallName as $key => $value) {
            $cellKey[] = $key;
            $data[$key] = self::mbConvertEncoding($value);
        }
        $file = fopen($fileName, "a");
        fputcsv($file, [self::mbConvertEncoding($excelTitle)]);
        if (is_array($screen)) {
            foreach ($screen as $key => $value) {
                fputcsv($file, [self::mbConvertEncoding($value)]);
            }
        }
        fputcsv($file, [self::mbConvertEncoding("导出时间：") . date("Y-m-d H:i:s")]);
        fputcsv($file, $data);
        foreach ($excelTableData as $key => $value) {
            foreach ($cellKey as $k) {
                if (strpos($k, "price") !== false || strpos($k, 'fee') !== false || strpos($k, "surplus") !== false || preg_match("/_value$/", $k)) {
                    $data[$k] = self::mbConvertEncoding($value[$k]);
                } else {
                    $data[$k] = self::mbConvertEncoding($value[$k] . "\t");
                }
            }
            fputcsv($file, $data);
        }
        fclose($file);
        return $fileName;
    }

    /**
     * @description        遍历文件夹
     * @version            V1.0
     * @author             JianJia.Zhou<jianjia.zhou@longmaster.com.cn>
     * @lastModify         2017-5-24 10:57:25
     * @param string $dir 目录
     * @return array
     */
    public static function listDir($dir = '')
    {
        $result = array();
        if (is_dir($dir)) {
            $file_dir = scandir($dir);
            foreach ($file_dir as $file) {
                if ($file == '.' || $file == '..') {
                    continue;
                } elseif (is_dir($dir . $file)) {
                    $result = array_merge($result, self::listDir($dir . $file . '/'));
                } else {
                    array_push($result, $dir . $file);
                }
            }
        }
        return $result;
    }

    /**
     * @description        文件夹压缩
     * @version            V1.0
     * @author             JianJia.Zhou<jianjia.zhou@longmaster.com.cn>
     * @lastModify         2017-5-24 10:57:45
     * @param string $path    目录
     * @param string $zibName 压缩文件名
     */
    public static function compressedFolders($path = '', $zibName = '')
    {
        $path = rtrim($path, "/") . "/";
        $fileList = self::listDir($path);
        $fileName = $path . $zibName . ".zip";
        \Folder::createFolder($path);
        $zip = new ZipArchive();
        if ($zip->open($fileName, ZipArchive::OVERWRITE) === true) {
            foreach ($fileList as $value) {
                $zip->addFile($value, strtr($value, [$path => ""]));
            }
        }
        $zip->close();
        header("Cache-Control: max-age=0");
        header("Content-Description: File Transfer");
        header('Content-disposition: attachment; filename=' . $zibName . "_" . date("YmdHis") . ".zip"); // 文件名
        header("Content-Type: application/zip"); // zip格式的
        header("Content-Transfer-Encoding: binary"); // 告诉浏览器，这是二进制文件
        header('Content-Length: ' . filesize($fileName));
        @readfile($fileName);
        @unlink($fileName);
    }

    /**
     * @description        文件压缩
     * @version            V1.0
     * @author             JianJia.Zhou<jianjia.zhou@longmaster.com.cn>
     * @lastModify         2017-5-24 10:58:10
     * @param string $fileName 文件名
     * @param array  $data     压缩的文件列表
     * @param null   $fileDir  保存路径
     */
    public static function compressedFile($fileName = '', $data = [], $fileDir = null)
    {
        $fileName = self::mbConvertEncoding($fileName);
        if (!$fileDir) {
            if (APP_PATH) {
                $fileDir = rtrim(APP_PATH, '/') . 'log/csv/' . date("YmdHis") . '/';
            } else {
                $fileDir = __DIR__ . "log/csv/" . date("YmdHis") . "/";
            }
        }
        \Folder::createFolder($fileDir);
        $zip = new ZipArchive();
        $file = $fileDir . $fileName . ".zip";
        if ($zip->open($file, ZipArchive::OVERWRITE) === true) {
            foreach ($data as $name => $filePath) {
                $zip->addFile($filePath, strtr($filePath, [$fileDir => ""]));
            }
            $zip->close();
        }
        header("Cache-Control: max-age=0");
        header("Content-Description: File Transfer");
        header('Content-disposition: attachment; filename=' . $fileName . "_" . date("YmdHis") . ".zip"); // 文件名
        header("Content-Type: application/zip"); // zip格式的
        header("Content-Transfer-Encoding: binary"); // 告诉浏览器，这是二进制文件
        header('Content-Length: ' . filesize($file));
        @readfile($file);
        @unlink($file);
    }

    /**
     * @description        对数据进行编码
     * @version            V1.0
     * @author             JianJia.Zhou<jianjia.zhou@longmaster.com.cn>
     * @lastModify         2017-5-24 10:59:41
     * @param string $string 数据
     * @param string $encode 编码
     * @return string
     */
    public static function mbConvertEncoding($string = '', $encode = "GB2312")
    {
        $encodeN = mb_detect_encoding($string, ["ASCII", "UTF-8", "GB2312", "GBK", "BIG5"]);
        $string = $encodeN == $encode ? $string : mb_convert_encoding($string, $encode, $encodeN);
        return $string;
    }
}