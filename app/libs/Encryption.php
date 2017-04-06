<?php
/**
 * Created by PhpStorm
 * FileName: Encryption.php
 * ProjectName:flight
 * User: jianjia.zhou@longmaster.com.cn
 * DateTime: 2017/4/6 11:33
 * 常见加密方式
 */
class Encryption{
    /**
     * 哈希加密
     * @param null $string
     * @return bool|null|string
     */
    public static function passwordHash($string = null){
        if(empty($string)){
            return null;
        }
        return password_hash($string,PASSWORD_DEFAULT);
    }

    /**
     * 验证密码
     * @param null $string
     * @param $hash
     * @return bool
     */
    public static function passwordVerify($string = null,$hash){
        if(empty($string) || empty($hash)){
            return false;
        }
        return password_verify($string,$hash);
    }

    /**
     * MD5加密
     * @param null $string
     * @return bool|string
     */
    public static function passwordMd5($string = null){
        if(empty($string) || empty($hash)){
            return false;
        }
        return md5($string);
    }
}