<?php

/**
 * Created by PhpStorm
 * FileName: VerifyCodeController.php
 * ProjectName:flight
 * User: ZhouJianJia<jianjia.zhou@longmaster.com.cn>
 * DateTime: 2017/6/5 14:03
 */

namespace common;

use \Controller;

class VerifyCodeController extends Controller
{
    /**
     * 验证码图片
     * @version             V1.0<新建>
     * @author              JianJia.Zhou<jianjia.zhou@longmaster.com.cn>
     * @changeTime          2017-6-5 15:03:32
     */
    public function verifyCodePic()
    {
        $verifyCode = new \VerifyCode();
        $verifyCode->imageOut();
    }

    /**
     * 验证验证码是否正确
     * @version             V1.0<新建>
     * @author              JianJia.Zhou<jianjia.zhou@longmaster.com.cn>
     * @changeTime          2017-6-5 15:04:00
     * @param string $code  验证码
     * @return bool
     */
    public static function verifyCode($code = "")
    {
        return \VerifyCode::verifyCode($code);
    }
}