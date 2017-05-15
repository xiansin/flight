<?php

/**
 * Created by PhpStorm.
 * User: jianjia.zhou@longmaster.com.cn
 * Date: 2016/8/17
 * Time: 17:08
 * 目录相关操作类
 */
class Folder
{
    /**
     * 创建目录 成功返回-true 失败返回-false
     * @param $dirName              目录名称
     * @param string $authority 目录权限
     * @param bool $recursive
     * @return bool
     */
    public static function createFolder($dirName, $authority = '0777', $recursive = false)
    {
        if (!file_exists($dirName)) {
            if (@mkdir($dirName, $authority, $recursive)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 获取指定目录下所有文件名包含子目录
     * @param $dirName              目录名称
     * @return array
     */
    public static function getFolderFiles($dirName)
    {
        $dirArray = [];
        //获取目录所有文件名
        $fileNames = self::_getFileNamesByDir($dirName);
        foreach ($fileNames as $value) {
            $dirArray[] = $value;
        }
        return $dirArray;
    }

    /**
     * 删除目录及子目录下所有文件
     * @param $dirName              目录名称
     */
    public static function deleteAllFiles($dirName)
    {
        $fileNames = self::_getFileNamesByDir($dirName);
        foreach ($fileNames as $value) {
            unlink($value);
        }
    }

    /**
     * 删除目录及子目录
     * @param $path                     路径
     */
    public static function deleteFolder($path)
    {
        $dp = @dir($path);
        while (false !== ($item = $dp->read())) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (is_dir($dp->path . '/' . $item)) {
                self::deleteFolder($dp->path . '/' . $item);
                @rmdir($dp->path . '/' . $item);
            } else {
                unlink($dp->path . '/' . $item);
            }

        }
    }

    /**
     * 获取目录
     * @param $dirName                  名录名称
     * @return array
     */
    public static function getDir($dirName)
    {
        $dirArray = array();
        if (!$dh = opendir($dirName))
            return $dirArray;
        $i = 0;
        while ($f = readdir($dh)) {
            if ($f == '.' || $f == '..')
                continue;
            //如果只要子目录名, path = $f;
            //$path = $dir.'/'.$f;
            $path = $f;
            $dirArray[$i] = $path;
            $i++;
        }
        return $dirArray;
    }

    /**
     * 获取目录所有文件
     * @param $path                 路径
     * @param $files                返回到指定数组
     */
    private static function _getAllFiles($path, &$files)
    {
        if (is_dir($path)) {
            $dp = dir($path);
            while ($file = $dp->read()) {
                if ($file != "." && $file != "..") {
                    self::_getAllFiles($path . "/" . $file, $files);
                }
            }
            $dp->close();
        }
        if (is_file($path)) {
            $files[] = $path;
        }
    }

    /**
     * 获取目录所有文件名
     * @param $dir
     * @return array
     */
    private static function _getFileNamesByDir($dir)
    {
        $files = array();
        //获取目录所有文件
        self::_getAllFiles($dir, $files);
        return $files;
    }

}