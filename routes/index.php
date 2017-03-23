<?php
/**
 * Created by PhpStorm.
 * User: jianjia.zhou@longmaster.com.cn
 * Date: 2017/3/23
 * Time: 15:31
 */
Flight::route("/", function () {
    Flight::get("flight.controllers.path");
});

