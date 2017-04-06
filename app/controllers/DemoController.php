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
        $head = ['one','two'];
        $data = [
            0=>['one0','two0'],
            1=>['one1','two1']
        ];
        $excel = new OutputExcel();
        $excel->outputExcelSlice($head,$data,'test',1);

    }
}