<?php

/**
 * Created by PhpStorm.
 * User: daxin.yang@longmaster.com.cn<daxin.yang@longmaster.com.cn>
 * Date: 2015/8/26
 * Time: 10:28
 * 功能: 输出信息模型
 */

class OutPutInfo
{
    private static $instance;
    private $outInfo = array(); //输出信息

    private function __construct()
    {

    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 添加输出信息,同一个参数后面传入的会覆盖前面的
     * 如code第一次为0,第二次调用为-1，则最终输出code为-1
     * @param mixed $key string or array
     * eg:$parms = array("code"=>0)
     * @param string $val [optional] if $key is array then $val is unncessary
     */
    public function AddOutInfo($key, $val = "")
    {
        $args = func_num_args();
        if (is_array($key)) {
            $key = $this->formateArr($key);
            $this->outInfo = array_merge($this->outInfo, $key);
        } elseif ($args == 2 && is_string($key)) {
            $arr = $this->formateArr(array($key => $val));
            $this->outInfo = array_merge($this->outInfo, $arr);
        }
    }

    /**
     * 数组形式输出信息
     * param array $parms
     * eg:$parms = array("code"=>0)
     */
    public function getOutInfo()
    {
        return $this->outInfo;
    }

    /**
     * 用header形式输出
     */
    public function outPutHeader()
    {
        foreach ($this->outInfo as $key => $val) {
            header("$key:$val");
        }
    }

    /**
     * 以json形式返回
     */
    public function outPutJson()
    {
        if ($this->outInfo["code"] == 0) { //如果最终结果成功，则不显示notice信息
            unset($this->outInfo["notice"]);
        }
        $this->outInfo = $this->changeColorValue($this->outInfo);

        $json_str = json_encode($this->outInfo, JSON_UNESCAPED_UNICODE);
        return $json_str;
    }

    public function changeColorValue($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                if (is_string($key) && $key == "color_value" && ($val === 0 || $val === "0")) {
                    $data["color_value"] = "-1";
                }
                if (is_array($val)) {
                    $data[$key] = $this->changeColorValue($val);
                }
            }
        }
        return $data;


    }

    /**
     * 遍历多维数组，如果有null值则替换为空字符串
     * 数据强制转为字符串
     * @param $arr
     * @return array
     */
    public function formateArr($arr)
    {
        if (!is_array($arr)) {
            return $arr;
        }

        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                $arr[$key] = $this->formateArr($val);
            } elseif (is_object($val)) {
                $arr[$key] = (array)$arr[$key];
            } else {
                $arr[$key] = (string)$arr[$key];
            }
        }
        return $arr;
    }


}

