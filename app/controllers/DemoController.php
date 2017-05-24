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
        $header = $data =[];
        for($i =0;$i<10;$i++){
            array_push($header,["key"=>"cell{$i}","value"=>"第{$i}"]);
        }
        for($j =0;$j<1500;$j++){
            for($i =0;$i<10;$i++){
                $data[$j]["cell{$i}"] = rand(0,1000);
            }
        }

//        var_dump($data);exit;
        \Export::exportExcel("a","a",$header,$data);
    }
}