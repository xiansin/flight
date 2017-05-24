<?php
/**
 * Created by PhpStorm
 * FileName: constant.php
 * ProjectName:flight
 * User: jianjia.zhou@longmaster.com.cn
 * DateTime: 2017/5/24 11:01
 */


// 日志级别->正常
define("LOG_LEVEL_NORMAL", 1);
// 日志级别->一般错误:一般性错误
define("LOG_LEVEL_ERROR", 2);
// 日志级别->警告性错误:需要发出警告的错误
define("LOG_LEVEL_WARNING", 3);
// 日志级别->严重错误:导致系统崩溃无法使用
define("LOG_LEVEL_EMERGENCY", 4);

// 接口请求状态->成功
define('RESULT_SUCCESS', 0);
// 接口请求状态->失败
define('RESULT_FAILED', -1);
// 接口请求状态->数据库不存在该条数据
define("RESULT_DB_NOT_EXIST", -102);
// 接口请求状态->请求参数错误
define('RESULT_PARAM_FAILED', -997);
// 接口请求状态->缺少请求参数
define('RESULT_PARAM_LACK', -998);
// 接口请求状态->系统错误
define('RESULT_SYS_FAILED', -999);
