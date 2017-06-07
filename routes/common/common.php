<?php
/**
 * Created by PhpStorm
 * FileName: common.php
 * ProjectName:flight
 * User: ZhouJianJia<jianjia.zhou@longmaster.com.cn>
 * DateTime: 2017/6/5 14:45
 */
// 验证码
Flight::route("GET /verify/code", array("\\common\\VerifyCodeController", "_verifyCodePic"));
