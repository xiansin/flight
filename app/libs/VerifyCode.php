<?php

/**
 * Created by PhpStorm
 * FileName: VerifyCode.php
 * ProjectName:flight
 * User: jianjia.zhou@longmaster.com.cn
 * DateTime: 2017/4/12 11:52
 *
 * //生成验证码
 * Header("Content-type: image/gif");
 * $verify_code = new \VerifyCode(160, 50);
 * $verify_code->imageOut();
 * //验证
 * $verify_code->verifyCode($code);
 */
class VerifyCode
{
    //验证码宽度
    private $width;
    //验证码高度
    private $height;
    //验证码长度
    private $counts;
    //干扰码&验证码库
    private $disturb_code;
    //字体路径
    private $font_url;
    //验证码保存session
    private $session;
    //是否需要干扰码
    private $is_disturb;

    /**
     * 初始化
     * VerifyCode constructor.
     * @param int $width
     * @param int $height
     * @param int $counts
     * @param bool $is_disturb
     * @param string $disturb_code
     * @param string $font_url
     */
    public function __construct($width = 280, $height = 100, $counts = 4, $is_disturb = true, $disturb_code = "1235467890qwertyuiopasdfghjklzxcvbnm", $font_url = "app/libs/font/TektonPro-BoldCond.otf")
    {
        $this->width = $width;
        $this->height = $height;
        $this->counts = $counts;
        $this->disturb_code = $disturb_code;
        $this->font_url = $font_url;
        $this->is_disturb = $is_disturb;
        $this->session = $this->sessionCode();
        $_SESSION['verify_code'] = $this->session;
    }

    /**
     * 输出验证码
     */
    public function imageOut()
    {
        //创建图像
        $im = $this->createImageSource();
        //设置背景
        $this->setBackgroundColor($im);
        //填充验证码
        $this->setCode($im);
        //是否填充干扰码
        if ($this->is_disturb) {
            $this->setDisturbCode($im);
        }
        //输出图像
        imagegif($im);
        //释放创建图像的内存
        imagedestroy($im);
    }

    /**
     * 创建图片
     * @return resource
     */
    private function createImageSource()
    {
        return imagecreate($this->width, $this->height);
    }

    /**
     * 给图片添加颜色
     * @param $im
     */
    private function setBackgroundColor($im)
    {
        $bg_color = imagecolorallocate($im, rand(245, 255), rand(220, 255), rand(220, 255));//¡À3?¡ã??¨¦?
        imagefill($im, 0, 0, $bg_color);
    }

    /**
     * 添加干扰码
     * @param $im
     */
    private function setDisturbCode($im)
    {
        $count_h = $this->height;
        //生成干扰码总数
        $cou = floor($count_h * 2);
        for ($i = 0; $i < $cou; $i++) {
            $x = rand(0, $this->width);
            $y = rand(0, $this->height);
            //角度
            $angle = rand(0, 360);
            //字体大小
            $font_size = rand(8, 13);
            $font_url = $this->font_url;
            $original_code = $this->disturb_code;
            $count_disturb = strlen($original_code);
            //随机生成干扰码
            $d_session_code = $original_code[rand(0, $count_disturb - 1)];
            //干扰码颜色
            $color = imagecolorallocate($im, rand(40, 140), rand(40, 140), rand(40, 140));
            //给图片添加干扰码
            imagettftext($im, $font_size, $angle, $x, $y, $color, $font_url, $d_session_code);
        }
    }

    /**
     * 把生成的验证码填充到图片
     * @param $im
     */
    private function setCode($im)
    {
        $width = $this->width;
        $counts = $this->counts;
        $height = $this->height;
        $session_code = $this->session;
        //验证码Y轴位置
        $y = floor($height / 2) + floor($height / 4);
        $font_size = rand($this->height - 15, $this->height - 10);
        $font_url = $this->font_url;

        for ($i = 0; $i < $counts; $i++) {
            $char = $session_code[$i];
            //验证码Y轴位置
            $x = floor($width / $counts) * $i + 8;
            $angle = rand(-20, 30);
            //验证码颜色
            $color = imagecolorallocate($im, rand(0, 50), rand(50, 100), rand(100, 140));
            imagettftext($im, $font_size, $angle, $x, $y, $color, $font_url, $char);
        }
    }

    /**
     * 生成验证码
     * @return string
     */
    private function sessionCode()
    {
        $original_code = $this->disturb_code;
        $count_disturb = strlen($original_code);
        $_ds_code = "";
        $counts = $this->counts;
        for ($j = 0; $j < $counts; $j++) {
            $ds_code = $original_code[rand(0, $count_disturb - 1)];
            $_ds_code .= $ds_code;
        }
        return $_ds_code;
    }

    public static function verifyCode($code){
        if(empty($code)){
            return false;
        }
        if($_SESSION["verify_code"] === $code){
            return true;
        }else{
            return false;
        }
    }
}

