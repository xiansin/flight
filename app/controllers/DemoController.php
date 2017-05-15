<?php
/**
 * Created by PhpStorm
 * FileName: DemoController.php
 * ProjectName:flight
 * User: jianjia.zhou@longmaster.com.cn
 * DateTime: 2017/4/6 14:50
 */

class DemoController extends Controller{
    public function index(){
        $a = self::ajaxPage(100,5);
        echo $a['pageInfo'];
    }
    public function a(){
        header("Content-type: image/gif");
        $image_code = new \VerifyCode();
        $image_code->imageOut();
    }


}