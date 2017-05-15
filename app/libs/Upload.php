<?php

/**
 * Created by PhpStorm
 * FileName: Upload.php
 * ProjectName:flight
 * User: jianjia.zhou@longmaster.com.cn
 * DateTime: 2017/4/12 16:05
 */
class UpLoad
{

    private $allowTypes = array('gif', 'jpg', 'png', 'bmp', 'xlsx');//允许上传类型
    private $uploadPath = "./uploads/"; //上传路径
    private $maxSize = 1000000; //允许上传文件大小
    private $msgCode = null;    //错误状态码
    private $isRandName = true; //是否生成随机文件名

    private $newFileName;   //新文件名
    private $newFilePath;   //新文件路径

    private $fileName;      //上传文件名
    private $fileSize;      //上传文件大小
    private $fileType;      //上传文件类型
    private $fileTmpName;   //上传文件临时文件名
    private $fileError;     //上传文件错误

    /**
     * 初始化类 传参
     * UpLoadFile constructor.
     * @param array $options
     */
    public function __construct($options = array())
    {
        //取类内的所有变量
        $vars = get_class_vars(get_class($this));
        //设置类内变量
        foreach ($options as $key => $value) {
            if (array_key_exists($key, $vars)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * 文件上传
     * @param $myFile
     * @return bool
     */
    public function fileUpload($myFile)
    {

        $this->fileName = $myFile['name'];
        $this->fileTmpName = $myFile['tmp_name'];
        $this->fileError = $myFile['error'];
        $this->fileSize = $myFile['size'];


        //检查上传文件的大小 and 类型 or 上传的目录
        if ($this->fileError > 0) {
            $this->msgCode = $this->fileError;
            return false;
        } elseif (!$this->checkType($this->fileName)) {
            return false;
        } elseif (!$this->checkSize($this->fileSize)) {
            return false;
        } elseif (!$this->checkUploadFolder()) {
            return false;
        } elseif (!$this->checkTodayFolder()) {
            return false;
        }

        $newFolder = $this->uploadPath . '/' . date('Ymd');//上传到当天目录下
        $newFile = $newFolder . '/' . $this->checkRandName($this->fileName);

        $this->newFileName = $this->checkRandName($this->fileName);
        $this->newFilePath = $newFolder . '/' . $this->newFileName;


        //复制文件到上传目录
        if (!is_uploaded_file($this->fileTmpName)) {
            $this->msgCode = -1;
            return false;
        } elseif (@move_uploaded_file($this->fileTmpName, $newFile)) {
            $this->msgCode = 0;
            return true;
        } else {
            $this->msgCode = -3;
            return false;
        }
    }

    /**
     * 检查上传文件大小
     * @param $size
     * @return bool
     */
    private function checkSize($size)
    {
        if ($size > $this->maxSize) {
            $this->msgCode = -2;
            return false;
        } else {
            return true;
        }
    }

    /**
     * 检查文件类型
     * @param $fileName
     * @return bool
     */
    private function checkType($fileName)
    {
        $arr = explode(".", $fileName);
        $type = end($arr);
        $this->fileType = $type;
        if (in_array(strtolower($type), $this->allowTypes)) {
            return true;
        } else {
            $this->msgCode = -1;
            return false;
        }
    }

    /**
     * 检查上传目录 是否存在 不存在创建
     * @return bool
     */
    private function checkUploadFolder()
    {
        if (null === $this->uploadPath) {
            $this->msgCode = -5;
            return false;
        }

        $this->uploadPath = rtrim($this->uploadPath, '/');
        $this->uploadPath = rtrim($this->uploadPath, '\\');

        if (!file_exists($this->uploadPath)) {
            if (@mkdir($this->uploadPath, 0755)) {
                return true;
            } else {
                $this->msgCode = -4;
                return false;
            }
        } elseif (!is_writable($this->uploadPath)) {
            $this->msgCode = -777;
            return false;
        } else {
            return true;
        }
    }

    /**
     * 创建当天文件目录
     * @return bool
     */
    private function checkTodayFolder()
    {
        $todayFolder = $this->uploadPath . '/' . date('Ymd');
        if (!file_exists($todayFolder)) {
            if (@mkdir($todayFolder, 0755)) {
                return true;
            } else {
                $this->msgCode = -6;
                return false;
            }
        } elseif (!is_writable($todayFolder)) {
            $this->msgCode = -777;
            return false;
        } else {
            return true;
        }
    }

    /**
     * 检查是否需要生成随机文件名
     * @param $fileName
     * @return string
     */
    private function checkRandName($fileName)
    {
        if ($this->isRandName) {
            return $this->randFileName($fileName);
        } else {
            return $fileName;
        }
    }

    /**
     * 生成随机文件名
     * @param $fileName
     * @return string
     */
    private function randFileName($fileName)
    {

        list($name, $type) = explode(".", $fileName);

        $newFile = md5(uniqid());

        return $newFile . '.' . $type;
    }

    /**
     * 错误状态码
     * @return array
     */
    public function getStatus()
    {
        $messages = array(
            4 => "没有文件被上传",
            3 => "文件只被部分上传",
            2 => "上传文件超过了HTML表单中MAX_FILE_SIZE选项指定的值",
            1 => "上传文件超过了php.ini 中upload_max_filesize选项的值",
            0 => "上传成功",
            -1 => "末充许的类型",
            -2 => "文件过大，上传文件不能超过{$this->maxSize}个字节",
            -3 => "上传失败",
            -4 => "建立存放上传文件目录失败，请重新指定上传目录",
            -5 => "必须指定上传文件的路径",
            -6 => "创建当天文件目录出错",
            -777 => "上传目录没有写入权限"
        );

        return array('error' => $this->msgCode, 'message' => $messages[$this->msgCode]);
    }

    /**
     * 上传文件信息
     * @param $status
     * @return string
     */
    public function getNewFileNature($status)
    {
        if ($status == 0) {
            $str = "上传文件名：" . $this->fileName;
            $str .= "<br/>";
            $this->fileSize = $this->fileSize / 1024;
            $str .= "上传文件大小：" . $this->fileSize . 'kb';
            $str .= "<br/>";
            $str .= "上传文件类型：" . $this->fileType;
            $str .= "<br/>";
            $str .= "新文件名：" . $this->newFileName;
            $str .= "<br/>";
            $str .= "新文件路径:" . $this->newFilePath;
            return $str;
        } else {
            $str = '上传出错，请仔细查看错误信息。';
            return $str;
        }
    }
}
