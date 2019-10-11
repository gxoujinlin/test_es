<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/11/26
 * Time: 12:31 PM
 */

namespace App\Model;


use App\Utility\Pool\MysqlObject;
use App\Loader;
use EasySwoole\Mysqli\Mysqli;
use EasySwoole\RedisPool\Redis;
/**
 * model写法1
 * 通过传入mysql连接去进行处理
 * Class BaseModel
 * @package App\Model
 */
class BaseModel
{
    public $db;
    public $loader;
    public $redis;
    /*function __construct(MysqlObject $dbObject)
    {
        $this->db = $dbObject;
    }*/
    function __construct(Mysqli $dbObject)
    {
        $this->db = $dbObject;
    }

    /*protected function getDb():MysqlObject
    {
        return $this->db;
    }*/
    protected function getDb():Mysqli
    {
        return $this->db;
    }

    /*function getDbConnection():MysqlObject
    {
        return $this->db;
    }*/
    function getDbConnection():Mysqli
    {
        return $this->db;
    }
    /**
     * 获取一个model
     * @param $model
     * @return mixed
     */
    function model($model){
        /*$Loader = new Loader();
        $this->loader = $Loader->model($model);*/
        $this->loader = Loader::getInstance()->model($model);
        return $this->loader;
    }

    /**
     * 获取一个redis
     * @return \EasySwoole\RedisPool\Connection
     */
    function redis(){
        $this->redis = Redis::defer('redis');
        return $this->redis;
    }

}