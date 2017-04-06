<?php

class Controller
{

    protected static $_dbInstances = array();
    protected static $_cacheInstances = array();
    protected static $_logInstances = array();
    protected static $_controllerInstances = array();
    protected static $_modelInstances = array();

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

    public static function stripSlashesDeep($data)
    {
        if (is_array($data)) return array_map(array(__CLASS__, __FUNCTION__), $data);
        else return stripslashes($data);
    }


    public static function cache($path = "data")
    {
        $path = Flight::get("cache.path") . "/$path";
        if (!is_dir($path)) {
            mkdir($path, 0777, TRUE);
        }
        if (!isset(self::$_cacheInstances[$path])) {
            $cache = new \Doctrine\Common\Cache\FilesystemCache($path, ".cache");
            self::$_cacheInstances[$path] = $cache;
        }

        return self::$_cacheInstances[$path];
    }

    public static function log($name)
    {
        if (!isset(self::$_logInstances[$name])) {
            $logger = new \Apix\Log\Logger\File(Flight::get("log.path") . "/{$name}.log");
            self::$_logInstances[$name] = $logger;
        }

        return self::$_logInstances[$name];
    }

    public static function curl($url, $data, $timeout = 0)
    {
        if (!$url || !$data) {
            return false;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        if ($timeout) {
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
        }
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public static function xmlCurl($url, $data)
    {
        $header = array(
            'Content-type: text/xml',
            'User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.57 Safari/537.36'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15); //15秒超时时间
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //返回内容不是输出
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public static function halt($msg = "", $code = 200)
    {
        Flight::response(false)
            ->status($code)
            ->header("Content-Type", "text/html; charset=utf8")
            ->write($msg)
            ->send();
    }

    public static function getRunTime()
    {
        if (!defined("START_TIME")) {
            return "";
        }

        $stime = explode(" ", START_TIME);
        $etime = explode(" ", microtime());
        return sprintf("%0.4f", round($etime[0] + $etime[1] - $stime[0] - $stime[1], 4));
    }

    public static function returnJson($status, $msg, $data = NULL, $is_return = false)
    {
        $res = array(
            "status" => $status,
            "msg" => $msg,
            "data" => $data
        );
        if ($is_return) {
            return $res;
        } else {
            Flight::json($res);
        }
    }

    public static function getController($name)
    {
        $class = "\\" . trim(str_replace("/", "\\", $name), "\\") . "Controller";
        if (!isset(self::$_controllerInstances[$class])) {
            $instance = new $class();
            self::$_controllerInstances[$class] = $instance;
        }
        return self::$_controllerInstances[$class];
    }

    public static function getModel($name, $initDb = TRUE)
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

    public static function curIp()
    {
        $ip = '';
        if (Getenv('HTTP_CLIENT_IP') && StrCaseCmp(Getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = Getenv('HTTP_CLIENT_IP');
        } else if (Getenv('HTTP_X_FORWARDED_FOR') && StrCaseCmp(Getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = Getenv('HTTP_X_FORWARDED_FOR');
        } else if (Getenv('REMOTE_ADDR') && StrCaseCmp(Getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = Getenv('REMOTE_ADDR');
        } else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && StrCaseCmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
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

        $class = get_called_class();
        $name = substr($name, 1);
        $controller = new $class($name);
        $controller->$name();
    }

    /**
     * ajax分页处理
     * @param $rows 需要传递的总条数
     * @param $limit 每页显示条数
     * @return array
     */
    public function ajaxpage($rows, $limit)
    {
        $p = new \Pagination;
        $pagenum = (isset($_REQUEST["page"]) && $_REQUEST["page"] != '') ? $_REQUEST["page"] : 1;
        $p->items($rows);//总条数
        $p->limit($limit);//页大小
        $currentPage = $p->currentPage($pagenum);//当前页
        $p->nextLabel('');//移除“next”文本
        $p->prevLabel('');//移除“previous”文本
        $pageInfo = $p->show();
        $limit_arr = $p->getLimitArr();
        return array('pageInfo' => $pageInfo, 'limit' => $limit_arr);
    }

}

