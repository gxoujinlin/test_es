<?php

namespace App\HttpController;

use App\Utility\PlatesRender;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Template\Render;
use App\Loader;
use App\AppError;
use EasySwoole\Component\Di;
use EasySwoole\RedisPool\Redis;
/**
 * 基础控制器
 * Class Base
 * @package App\HttpController
 */
class Base extends Controller
{
    public $loader;
    public $redis;
    public function __construct()
    {
        parent::__construct();
       //$this->loader = new Loader();
       //$this->loader = Di::getInstance()->get('Loader');;
        //$this->redis = Redis::defer('redis');
    }
    function index()
    {
        $this->actionNotFound('index');
    }

    /**
     * 分离式渲染
     * @param $template
     * @param $vars
     */
    function render($template, array $vars = [])
    {
        $engine = new PlatesRender(EASYSWOOLE_ROOT . '/App/Views');
        $render = Render::getInstance();
        $render->getConfig()->setRender($engine);
        $content = $engine->render($template, $vars);
        $this->response()->write($content);
    }

    /**
     * 获取配置值
     * @param $name
     * @param null $default
     * @return array|mixed|null
     */
    function cfgValue($name, $default = null)
    {
        $value = Config::getInstance()->getConf($name);
        return is_null($value) ? $default : $value;
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
    protected function onException(\Throwable $throwable): void
    {
        if($throwable instanceof AppError){
            $this->writeJson($throwable->getCode(),null,$throwable->getMessage());
            return;
        }else{
            throw $throwable;
        }
    }
}