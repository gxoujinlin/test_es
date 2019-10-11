<?php

/**
 * Created by PhpStorm.
 * User: oujinlin
 * Date: 2019/9/6
 * Time: 11:08
 */

namespace App;

use App\Utility\Pool\MysqlPool;
use EasySwoole\Component\Singleton;
use EasySwoole\MysqliPool\Mysql;
class Loader
{
    public $config = [];
    use Singleton;
    /**
     * 获取一个model
     * @param $model
     * @return mixed
     */
    public function model($model)
    {
        if (empty($model)) {
            return null;
        }
        $class_name = "App\\Model\\$model";
        //$db = MysqlPool::defer();
        $db = Mysql::defer('mysql');
        $ModelObject = new $class_name($db);
        return $ModelObject;
    }

    /**
     * 获取配置文件
     * @return array
     */
    public function config()
    {
        $path = __DIR__.'/Config';
        if(is_dir($path)){
            $paths = glob($path . '/*.*');
            if(!empty($paths)){
                foreach ($paths as $path){
                    include_once "$path";
                }
            }
        }
        return $this->config;
    }

}