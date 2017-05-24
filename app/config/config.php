<?php
/**
 * Created by PhpStorm.
 * User: jianjia.zhou@longmaster.com.cn
 * Date: 2017/3/23
 * Time: 14:20
 */

// 加载全局变量
$globalConfig = include APP_PATH . '/config/global.php';
// 数据库连接配置
$dbConfig = include APP_PATH . '/config/database.php';
// 常量
//$constantConfig = include APP_PATH . '/config/constant.php';

//合并相关配置项
return array_merge($globalConfig);
