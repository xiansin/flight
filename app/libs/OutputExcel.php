<?php

/**
 * Created by PhpStorm
 * FileName: OutputExcel.php
 * ProjectName:flight
 * User: jianjia.zhou@longmaster.com.cn
 * DateTime: 2017/4/6 14:26
 * 导出表格
 */
class OutputExcel
{
    protected $_excel;

    public function __construct()
    {
        $this->_excel = new PHPExcel();
    }

    /**
     * 导出excel
     * @param $header       头部
     * @param $data         数据
     * @param $filename     表格名
     * @param string $type 导出类型 -execl -csv
     */
    public function outputExcel($header, $data, $filename, $type = 'excel')
    {
        //设置第一个工作表为活动工作表
        $this->_excel->setActiveSheetIndex(0);
        //设置工作表名称
        $this->_excel->getActiveSheet()->setTitle($filename);
        //Excel表格式
        for ($i = 0; $i < count($header); $i++) {
            $letter[$i] = PHPExcel_Cell::stringFromColumnIndex($i);
        }
        //填充表头信息
        for ($i = 0; $i < count($header); $i++) {
            $this->_excel->getActiveSheet()->setCellValue("$letter[$i]1", "$header[$i]");
        }

        //填充表格信息
        for ($i = 2; $i <= count($data) + 1; $i++) {
            $j = 0;
            foreach ($data[$i - 2] as $key => $value) {
                $this->_excel->getActiveSheet()->setCellValue("$letter[$j]$i", "$value");
                $j++;
            }
        }
        //创建Excel输入对象
        if ($type == 'excel') {
            $write = new PHPExcel_Writer_Excel2007($this->_excel);
            $suffix = '.xlsx';
        } elseif ($type == 'csv') {
            $write = new PHPExcel_Writer_CSV($this->_excel);
            $suffix = '.csv';
        }

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header("Content-Disposition:attachment;filename='$filename$suffix");
        header("Content-Transfer-Encoding:binary");
        $write->save('php://output');
    }

    /**
     * 导出excel 多个活动工作表
     * @param $header       头部
     * @param $data         数据
     * @param $filename     表格名
     * @param int $sheetMax 每个活动工作表最大数据量
     * @param string $type 导出类型 -execl -csv
     */
    public function outputExcelSlice($header, $data, $filename, $sheetMax = 500, $type = 'excel')
    {
        $sheetNum = ceil(count($data) / $sheetMax);
        //Excel表格式
        for ($i = 0; $i < count($header); $i++) {
            $letter[$i] = PHPExcel_Cell::stringFromColumnIndex($i);
        }
        for ($active = 0; $active < $sheetNum; $active++) {
            //创建一个工作表
            $this->_excel->createSheet();
            //设置当前工作表为活动工作表
            $this->_excel->setActiveSheetIndex($active);
            //设置工作表名称
            $this->_excel->getActiveSheet()->setTitle($filename . '_page_' . $active);
            //填充表头信息
            for ($i = 0; $i < count($header); $i++) {
                $this->_excel->getActiveSheet()->setCellValue("$letter[$i]1", "$header[$i]");
            }
            $sheetData = array_slice($data, $active * $sheetMax, $sheetMax);
            //填充表格信息
            for ($i = 2; $i <= count($sheetData) + 1; $i++) {
                $j = 0;
                foreach ($sheetData[$i - 2] as $key => $value) {
                    $this->_excel->getActiveSheet()->setCellValue("$letter[$j]$i", "$value");
                    $j++;
                }
            }
        }
        //创建Excel输入对象
        if ($type == 'excel') {
            $write = new PHPExcel_Writer_Excel2007($this->_excel);
            $suffix = '.xlsx';
        } elseif ($type == 'csv') {
            $write = new PHPExcel_Writer_CSV($this->_excel);
            $suffix = '.csv';
        }

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header("Content-Disposition:attachment;filename='$filename$suffix");
        header("Content-Transfer-Encoding:binary");
        $write->save('php://output');
    }
}

