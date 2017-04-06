<?php
/**
 * Created by PhpStorm.
 * User: jianjia.zhou@longmaster.com.cn
 * Date: 2017/3/23
 * Time: 16:16
 * 功能描述: 用于记录日志
 */
define("ENTER", "\r\n");

class Log
{
    /**
     * 写日志
     * @param int $level 日志级别
     * @param $msg
     * @param $data
     * @param string $filename
     */
    public static function logWrite($level = 2, $msg, $data, $filename = "error")
    {
        $path = Flight::get("flight.storage.path") . "/logs/" . date("Ymd") . "/";
        $filename = $filename . ".log";
        self::createDir($path);
        $path = $path . $filename;
        if (!is_writable($path)) {
            @touch($path);
        }
        //日志级别
        $log_level = array_flip(Flight::get("LOG_LEVEL"));
        $level_desc = $log_level[$level];
        //每段日志之前插入分隔符
        $head = ENTER . ENTER . date("Y-m-d H:i:s") . " " . substr(number_format(microtime(true), 6, '', ''), 10, 6) . ENTER;
        $head .= '************************************************************' . ENTER;
        $head .= $level_desc . ":";
        $formatData = self::formatData($data);
        if (empty(trim($formatData))) {
            $formatData = 'NULL';
        }
        $write_data = $head . $msg . ENTER . $formatData;
        $handle = @fopen($path, 'a');
        if ($handle) {
            fwrite($handle, $write_data);
            fclose($handle);
        }
    }

    /**
     * 追踪时第几行调用
     * @return string
     */
    public static function trackTrace()
    {
        $str = '';
        $array = debug_backtrace();
        unset ($array[0]);
        foreach ($array as $row) {
            $str .= $row['file'] . ':' . $row['line'] . '行，调用方法：' . $row['function'] . ENTER;
        }
        return $str;
    }

    /**
     * 创建文件夹
     * @param $path 文件夹路径名
     * @param int $mode 目录权限
     * @param int $recursive 是否设置递归模式
     */
    private static function createDir($path, $mode = 0777, $recursive = 1)
    {
        if (!is_dir($path)) {
            Folder::createFolder($path, $mode, $recursive);
        }

    }

    /**
     * 格式转换
     * @param object $data
     * @return string
     */
    public static function formatData($data)
    {
        $return = '';
        //数组和对象都格式化
        if (is_array($data) || is_object($data)) {
            $return .= 'total_count:' . count($data) . ENTER;
            $return .= json_encode($data) . ENTER;
        } else {
            $return .= "$data" . ENTER;
        }
        return $return;
    }
}
