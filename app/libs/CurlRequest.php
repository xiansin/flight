<?php

/**
 * Created by PhpStorm
 * FileName: CurlRequest.php
 * ProjectName:flight
 * User: jianjia.zhou@longmaster.com.cn
 * DateTime: 2017/4/6 13:54
 * 功能描述:HTTP相关请求
 */
class CurlRequest
{
    /**
     * HTTP POST 请求
     * @param $url          请求URL
     * @param $data         请求数据
     * @param null $header HTTP请求头
     * @return mixed        返回结果
     */
    public static function curlPost($url, $data, $header = null)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//将curl_exec()获取的信息以字符串返回
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);   //只需要设置一个秒的数量就可以
        curl_setopt($curl, CURLOPT_POST, 80);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1)');
        if (!empty($header) && isset($header)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    /**
     * HTTP GET 请求
     * @param $url          请求URL
     * @param null $header HTTP请求头
     * @return mixed        返回结果
     */
    public static function curlGet($url, $header = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//将curl_exec()获取的信息以字符串返回
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);   //只需要设置一个秒的数量就可以
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1)');
        if (!empty($header) && isset($header)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    /**
     * HTTP PUT 请求
     * @param $url          请求URL
     * @param $data         请求数据
     * @param null $header HTTP请求头
     * @return mixed        返回结果
     */
    public static function curlPut($url, $data, $header = null)
    {
        $curl = curl_init(); //初始化CURL句柄
        curl_setopt($curl, CURLOPT_URL, $url); //设置请求的URL
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //设为TRUE把curl_exec()结果转化为字串，而不是直接输出
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT'); //设置请求方式

        curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-HTTP-Method-Override: PUT"));//设置HTTP头信息
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);//设置提交的字符串
        if (!empty($header) && isset($header)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }
        $result = curl_exec($curl);//执行预定义的CURL
        curl_close($curl);

        return $result;
    }

}