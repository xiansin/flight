<?php

/**
 * Created by PhpStorm
 * FileName: File.php
 * ProjectName:flight
 * User: jianjia.zhou@longmaster.com.cn
 * DateTime: 2017/4/12 16:19
 */
class File
{
    /**
     * 上传文件
     * @param $file
     * @return bool
     */
    public static function uploadFile($file = null)
    {
        $upload = new \UpLoad();
        if (empty($file)) {
            $file = $_FILES['file'];
        }
        $result = $upload->fileUpload($file);
        if(!$result){
            $status = $upload->getStatus();
            \Log::logWrite(Flight::get('LOG_LEVEL')['WARNING'], '上传状态信息:' . $status['message'],json_encode($file),'upload.error');
        }

    }


    /**
     * excel转数组
     * @param $filename
     * @param string $encode
     * @return array|null
     */
    public static function excelToArray($filename, $encode = 'utf-8')
    {
        if (!file_exists($filename)) {
            return null;
        }
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($filename);

        //获取工作表的数目
        $sheetCount = $objPHPExcel->getSheetCount();
        $excelDataAll = array();
        for ($sc = 0; $sc < $sheetCount; $sc++) {
            $objWorksheet = $objPHPExcel->getSheet($sc);
            $highestRow = $objWorksheet->getHighestRow();
            $highestColumn = $objWorksheet->getHighestColumn();
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            $excelData = array();
            for ($row = 1; $row <= $highestRow; $row++) {
                for ($col = 0; $col < $highestColumnIndex; $col++) {
                    $excelData[$row][] = (string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                }
            }
            $excelDataAll[] = array('title' => $objWorksheet->getTitle(), 'data' => $excelData);
        }
        return $excelDataAll;
    }
}