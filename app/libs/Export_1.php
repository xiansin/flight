<?php

/**
 * Created by PhpStorm
 * FileName: Export.php
 * User: JianJia.Zhou<jianjia.zhou@longmaster.com.cn>
 * DateTime: 2017/8/15 9:12
 */
class Export
{
    private $_handle;
    private $_cellKey = array();
    private $_filePath;
    private $_filename;
    private $_title;

    /**
     * Export constructor.
     * @param $filename
     * @param $header
     */
    public function __construct($filename, $header)
    {
        $this->_filename = $this->mbConvertEncoding($filename . "_" .date("YmdHis") . ".csv");
        $this->_title = $filename;
        // 创建文件
        $this->create();
        // 取得句柄
        $this->_getHandle();
        // 设置表头
        $this->setHeader($header);
    }

    /**
     * 设置主体
     * @version             v1.0
     * @author              JianJia.Zhou<jianjia.zhou@longmaster.com.cn>
     * @changeTime          2017/8/15 9:39
     * @param $data
     */
    public function setBody($data)
    {
        foreach ($data as $arr) {
            $file = "";
            foreach ($this->_cellKey as $key) {
                if (strpos($arr[$key], "fee") !== false) {
                    $file .= "{$arr[$key]},";
                } else {
                    $file .= "{$arr[$key]}\t,";
                }
            }
            $file .= "\n";
            $file = $this->mbConvertEncoding($file);
            fwrite($this->_handle, $file);
        }
    }

    /**
     * 取得文件句柄
     * @version             v1.0
     * @author              JianJia.Zhou<jianjia.zhou@longmaster.com.cn>
     * @changeTime          2017/8/15 9:39
     */
    protected function _getHandle()
    {
        $this->_handle = @fopen($this->_filePath, 'r+');
    }

    /**
     * 设置表头
     * @version             v1.0
     * @author              JianJia.Zhou<jianjia.zhou@longmaster.com.cn>
     * @changeTime          2017/8/15 9:40
     * @param $header
     */
    protected function setHeader($header)
    {
        $file = $this->_title . "\n";
        $exportDate = date('Y-m-d H:i:s');
        $file .= "导出时间：[{$exportDate}]\n";
        // 设置表头
        foreach ($header as $key => $row) {
            $file .= $row["value"] . ",";
            $this->_cellKey[$key] = $row['key'];
        }
        $file .= "\n";
        $file = $this->mbConvertEncoding($file);
        fwrite($this->_handle, $file);
    }

    /**
     * 下载文件
     * @version             v1.0
     * @author              JianJia.Zhou<jianjia.zhou@longmaster.com.cn>
     * @changeTime          2017/8/15 9:40
     */
    protected function downloadFile()
    {
        Header("Content-type:text/html;charset=utf-8");
        //首先要判断给定的文件存在与否
        if (!file_exists($this->_filePath)) {
            echo "没有该文件文件";
            return;
        }
        $fp = fopen($this->_filePath, "r");
        $fileSize = filesize($this->_filePath);
        //下载文件需要用到的头
        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges: bytes");
        Header("Accept-Length:" . $fileSize);
        Header("Content-Disposition: attachment; filename=" . $this->_filename);
        $buffer = 1024;
        $fileCount = 0;
        //向浏览器返回数据
        while (!feof($fp) && $fileCount < $fileSize) {
            $fileCon = fread($fp, $buffer);
            $fileCount += $buffer;
            echo $fileCon;
        }
        fclose($fp);
        // 下载完成后 删除文件
        unlink($this->_filePath);
    }

    /**
     * 创建文件
     * @version             v1.0
     * @author              JianJia.Zhou<jianjia.zhou@longmaster.com.cn>
     * @changeTime          2017/8/15 9:40
     */
    protected function create()
    {
        $path = Flight::get("filePath") . "/";
        $path = $path . $this->_filename;
        if (!is_writable($this->_filename)) {
            @touch($path);
        }
        $this->_filePath = $path;
    }

    /**
     * 转换编码
     * @version             v1.0
     * @author              JianJia.Zhou<jianjia.zhou@longmaster.com.cn>
     * @changeTime          2017/8/15 9:40
     * @param        $string
     * @param string $encode
     * @return string
     */
    protected function mbConvertEncoding($string, $encode = "GB2312")
    {
        $encodeN = mb_detect_encoding($string, ["ASCII", "UTF-8", "GB2312", "GBK", "BIG5"]);
        $string = $encodeN == $encode ? $string : mb_convert_encoding($string, $encode, $encodeN);
        return $string;
    }

    /**
     * 关闭句柄并下载文件
     */
    public function __destruct()
    {
        fclose($this->_handle);
        $this->downloadFile();
    }
}
