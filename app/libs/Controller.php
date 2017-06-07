<?php

class Controller
{

    protected static $_dbInstances = array();
    protected static $_cacheInstances = array();
    protected static $_logInstances = array();
    protected static $_controllerInstances = array();
    protected static $_modelInstances = array();

    /**
     * 初始化 框架
     * 定义对应方法
     */
    public static function init()
    {
        date_default_timezone_set("Asia/Shanghai");
        if (get_magic_quotes_gpc()) {
            $_GET = self::stripSlashesDeep($_GET);
            $_POST = self::stripSlashesDeep($_POST);
            $_COOKIE = self::stripSlashesDeep($_COOKIE);
        }
        $_REQUEST = array_merge($_GET, $_POST, $_COOKIE);

        Flight::map("db", array(__CLASS__, "db"));
        Flight::map("cache", array(__CLASS__, "cache"));
        Flight::map("log", array(__CLASS__, "log"));
        Flight::map("curl", array(__CLASS__, "curl"));
        Flight::map("halt", array(__CLASS__, "halt"));
        Flight::map("getRunTime", array(__CLASS__, "getRunTime"));
        Flight::map("returnJson", array(__CLASS__, "returnJson"));
        Flight::map("controller", array(__CLASS__, "getController"));
        Flight::map("model", array(__CLASS__, "getModel"));
    }

    /**
     * 过滤用户提交数据防止sql注入
     * @param $data
     * @return array|string
     */
    public static function stripSlashesDeep($data)
    {
        if (is_array($data)) return array_map(array(__CLASS__, __FUNCTION__), $data);
        else return stripslashes($data);
    }
    
    /**
     * 定义请求地址http响应
     * @param string $msg
     * @param int $code
     */
    public static function halt($code = 200, $msg = "")
    {
        Flight::response(false)
            ->status($code)
            ->header("Content-Type", "text/html; charset=utf8")
            ->write($msg)
            ->send();
    }

    /**
     * 获取程序运行时间
     * @return string
     */
    public static function getRunTime()
    {
        if (!defined("START_TIME")) {
            return "";
        }

        $start_time = explode(" ", START_TIME);
        $end_time = explode(" ", microtime());
        return sprintf("%0.4f", round($end_time[0] + $end_time[1] - $start_time[0] - $start_time[1], 4));
    }

    /**
     * 返回Json数据
     * @param int $status       状态码
     * @param string $msg       提示信息
     * @param string $data      数据
     * @return array|string
     */
    public static function returnJson($status, $msg, $data = NULL)
    {
        $res = array(
            "status" => $status,
            "msg" => $msg,
            "data" => $data
        );
        Flight::json($res);
    }

    /**
     * 实例化控制器
     * @param $name
     * @return mixed
     */
    public static function getController($name)
    {
        $class = "\\" . trim(str_replace("/", "\\", $name), "\\") . "Controller";
        if (!isset(self::$_controllerInstances[$class])) {
            $instance = new $class();
            self::$_controllerInstances[$class] = $instance;
        }
        return self::$_controllerInstances[$class];
    }

    /**
     * 实例化 Model
     * @param $name
     * @param bool $initDb
     * @return mixed
     */
    public static function getModel($name/*, $initDb = TRUE*/)
    {
        $class = "\\" . trim(str_replace("/", "\\", $name), "\\") . "Model";
        if (!isset(self::$_modelInstances[$class])) {
            $instance = new $class();
            // if($initDb) {
            //     $instance->setDb(self::db());
            // }
            self::$_modelInstances[$class] = $instance;
        }

        return self::$_modelInstances[$class];
    }

    /**
     * 获取客户端IP
     * @return int
     */
    public static function curIp()
    {
        $ip = '';
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } else if (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = '127.0.0.1';
        }
        return ip2long($ip);
    }

    public static function __callStatic($name, $arguments)
    {
        foreach ($arguments as $k => $v) {
            Flight::request()->query[$k] = $v;
        }
        //获取调用的类名
        $class = get_called_class();
        //方法名
        $name = substr($name, 1);
        $controller = new $class($name);
        $controller->$name();
    }

    /**
     * ajax分页处理
     * @param int $rows 需要传递的总条数
     * @param int $limit 每页显示条数
     * @return array
     */
    public function ajaxPage($rows, $limit)
    {
        $p = new \Pagination;
        $page_num = (isset($_REQUEST["page"]) && $_REQUEST["page"] != '') ? $_REQUEST["page"] : 1;
        $p->items($rows);//总条数
        $p->limit($limit);//页大小
        //$currentPage = $p->currentPage($page_num);//当前页
        $p->nextLabel('');//移除“next”文本
        $p->prevLabel('');//移除“previous”文本
        $pageInfo = $p->show();
        $limit_arr = $p->getLimitArr();
        return array('pageInfo' => $pageInfo, 'limit' => $limit_arr);
    }

}

