<?php

/**
 * Created by PhpStorm.
 * User: jianjia.zhou@longmaster.com.cn
 * Date: 2017-6-7
 * Time: 16:05:28
 */
use Medoo\Medoo;
class Model
{
    // 链接数据库标识
    protected $_db;
    // 待链接的数据库
    protected $_connection;
    // 默认使用的表
    protected $_table;
    // 主键
    protected $_primary_key;

    /**
     * 构造方法 使用medoo数据库框架链接数据库
     *
     * Model constructor.
     */
    public function __construct()
    {
        if (empty($this->_connection)) {
            return false;
        }
        $config = $this->getConfig($this->_connection);
        $this->_db = new Medoo($config);
    }

    /**
     * 获取待链接的数据库配置项
     *
     * @version             V1.0
     * @author              JianJia.Zhou<jianjia.zhou@longmaster.com.cn>
     * @changeTime          2017-6-7 16:33:53
     * @param string $connection 待链接的数据库名
     * @return array
     */
    private function getConfig($connection = '')
    {
        if (!empty($connection)) {
            $dbCfg = Flight::get($connection);

            $db_host = $dbCfg['host'];
            $db_port = $dbCfg['port'];
            $db_user = $dbCfg['username'];
            $db_pass = $dbCfg['password'];
            $db_name = $dbCfg['database'];
            $db_charset = $dbCfg['charset'];
            $db_prefix = $dbCfg['prefix'];

            if (is_null($db_host)) {
                $db_host = "localhost";
            }

            if (is_null($db_port)) {
                $db_port = 3306;
            }

            if (is_null($db_user)) {
                $db_user = "";
            }

            if (is_null($db_pass)) {
                $db_pass = "";
            }

            if (is_null($db_name)) {
                $db_name = "";
            }

            if (is_null($db_charset)) {
                $db_charset = "utf8";
            }

            $config = array(
                "database_type" => "mysql",
                "database_name" => $db_name,
                "server" => $db_host,
                "port" => $db_port,
                "username" => $db_user,
                "password" => $db_pass,
                "charset" => $db_charset
            );

            if (!empty($db_prefix)) {
                $config['prefix'] = $db_prefix;
            }

            return $config;
        }
    }

    /**
     * 根据条件获取一条数据
     * @version             V1.0
     * @author              JianJia.Zhou<jianjia.zhou@longmaster.com.cn>
     * @changeTime          2017年6月15日16:40:12
     * @param $where
     * @return bool
     */
    public function get($where = [])
    {
        return $this->_db->get($this->_table, '*', $where);
    }

    /**
     * 根据条件获取一列数据
     * @version             V1.0
     * @author              JianJia.Zhou<jianjia.zhou@longmaster.com.cn>
     * @changeTime          2017-6-15 16:40:42
     * @param $where
     * @return array|bool
     */
    public function select($where = [])
    {
        return $this->_db->select($this->_table, '*', $where);
    }

    /**
     * 根据条件获取一列数据
     * @version             V1.0
     * @author              JianJia.Zhou<jianjia.zhou@longmaster.com.cn>
     * @changeTime          2017-6-15 16:40:42
     * @param $where
     * @return array|bool
     */
    public function count($where = [])
    {
        return $this->_db->count($this->_table, '*', $where);
    }

    /**
     * 根据设定的主键名更新数据
     * @version             V1.0
     * @author              JianJia.Zhou<jianjia.zhou@longmaster.com.cn>
     * @changeTime          2017-6-15 16:40:42
     * @param $data
     * @param $id
     * @return array|bool
     */
    public function update($data,$id)
    {
        $where[$this->_primary_key] = $id;
        return $this->_db->update($this->_table, $data, $where);
    }
}

