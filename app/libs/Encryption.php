<?php

/**
 * Created by PhpStorm
 * FileName: Encryption.php
 * ProjectName:flight
 * User: jianjia.zhou@longmaster.com.cn
 * DateTime: 2017/4/6 11:33
 * 常见加密/解密方式
 */

class Encryption
{
    /**
     * @description         哈希加密
     * @version             V1.0
     * @author              JianJia.Zhou<jianjia.zhou@longmaster.com.cn>
     * @lastModify          2017-5-24 10:29:00
     * @param string $string 待加密串
     * @return bool|null|string
     */
    public static function passwordHash($string = '')
    {
        if (empty($string)) {
            return null;
        }
        return password_hash($string, PASSWORD_DEFAULT);
    }

    /**
     * @description        验证密码
     * @version            V1.0
     * @author             JianJia.Zhou<jianjia.zhou@longmaster.com.cn>
     * @lastModify         2017-5-24 10:29:33
     * @param string  $string 验证密码
     * @param  string $hash   对比的hash串
     * @return bool
     */
    public static function passwordVerify($string = '', $hash = '')
    {
        if (empty($string) || empty($hash)) {
            return false;
        }
        return password_verify($string, $hash);
    }

    /**
     *
     * @param string $string
     * @return bool|string
     */
    /**
     * @description        MD5加密
     * @version            V1.0
     * @author             JianJia.Zhou<jianjia.zhou@longmaster.com.cn>
     * @lastModify         2017-5-24 10:31:07
     * @param string $string 带加密的字符串
     * @return bool|string
     */
    public static function passwordMd5($string = '')
    {
        if (empty($string) || empty($hash)) {
            return false;
        }
        return md5($string);
    }
}