<?php

/**
 * Created by PhpStorm
 * FileName: DemoController.php
 * ProjectName:flight
 * User: jianjia.zhou@longmaster.com.cn
 * DateTime: 2017/4/6 14:50
 */
class DemoController extends Controller
{
    public function index()
    {
        //Flight::halt(200, 'Be right back...');
        self::returnJson('200','哈哈','312');
    }



    function arrayToXml($arr, $dom = 0, $item = 0)
    {
        if (!$dom) {
            $dom = new DOMDocument("1.0");
        }
        if (!$item) {
            $item = $dom->createElement("xml");
            $dom->appendChild($item);
        }
        foreach ($arr as $key => $val) {
            $itemXml = $dom->createElement(is_string($key) ? $key : "item");
            $item->appendChild($itemXml);
            if (!is_array($val)) {
                $text = $dom->createTextNode($val);
                $itemXml->appendChild($text);
            } else {
                self::arrayToXml($val, $dom, $itemXml);
            }
        }
        return $dom->saveXML();
    }

    function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $xmlString = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $array = json_decode(json_encode($xmlString), true);
        return $array;
    }
}