<?php
/**
 * Created by PhpStorm.
 * User: jianjia.zhou@longmaster.com.cn
 * Date: 2017/3/23
 * Time: 15:31
 */
Flight::route("/", array("DemoController", "_index"));
Flight::route("/a", array("DemoController", "_a"));
