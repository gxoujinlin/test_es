<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;

use App\Storage\ChatMessage;
use App\HttpController\Base;
use App\Storage\OnlineUser;
use App\Task\Task;
use App\WebSocket\Actions\User\UserInfo;
use App\WebSocket\WebSocketEvents;
use App\WebSocket\WebSocketParser;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\FastCache\Cache;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Socket\Dispatcher;
use swoole_server;
use swoole_websocket_frame;
use \Exception;
use EasySwoole\Component\Pool\PoolManager;
use App\Utility\Pool\MysqlPool;
use App\Utility\Pool\RedisPool;
use EasySwoole\Component\Di;
use App\Loader;
use EasySwoole\EasySwoole\Config as GConfig;
use EasySwoole\MysqliPool\Mysql;
use EasySwoole\Mysqli\Config;
use EasySwoole\RedisPool\Config as RConfig;
use EasySwoole\RedisPool\Redis;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
        /*$mysqlConf = PoolManager::getInstance()->register(MysqlPool::class, Config::getInstance()->getConf('MYSQL.POOL_MAX_NUM'));
        if ($mysqlConf === null) {
            //当返回null时,代表注册失败,无法进行再次的配置修改
            //注册失败不一定要抛出异常,因为内部实现了自动注册,不需要注册也能使用
            throw new \Exception('mysql注册失败!');
        }
        //设置其他参数
        $mysqlConf->setMaxObjectNum(20)->setMinObjectNum(5);
        $redisConf = PoolManager::getInstance()->register(RedisPool::class);
        if ($redisConf === null) {
            //当返回null时,代表注册失败,无法进行再次的配置修改
            //注册失败不一定要抛出异常,因为内部实现了自动注册,不需要注册也能使用
            throw new \Exception('redis注册失败!');
        }*/
        $configData = GConfig::getInstance()->getConf('MYSQL');
        $config = new Config($configData);
        /**
        这里注册的名字叫mysql，你可以注册多个，比如mysql2,mysql3
         */
        $poolConf = Mysql::getInstance()->register('mysql',$config);

        $redisConfigData = GConfig::getInstance()->getConf('REDIS');
        $redis_config = new RConfig($redisConfigData);
        // $config->setOptions(['serialize'=>true]);
        /**
        这里注册的名字叫redis，你可以注册多个，比如redis2,redis3
         */
        $redis_poolConf = Redis::getInstance()->register('redis',$redis_config);
        $redis_poolConf->setMaxObjectNum($redisConfigData['maxObjectNum']);
        $redis_poolConf->setMinObjectNum($redisConfigData['minObjectNum']);
    }

    /**
     * 服务启动前
     * @param EventRegister $register
     * @throws Exception
     */
    public static function mainServerCreate(EventRegister $register)
    {
        $server = ServerManager::getInstance()->getSwooleServer();

        OnlineUser::getInstance();
        ChatMessage::getInstance();
        Loader::getInstance();//实例化一个类
        Task::getInstance();//实例化一个类
        Cache::getInstance()->setTempDir(EASYSWOOLE_ROOT . '/Temp')->attachToServer($server);

        // 注册服务事件
        $register->add(EventRegister::onOpen, [WebSocketEvents::class, 'onOpen']);
        $register->add(EventRegister::onClose, [WebSocketEvents::class, 'onClose']);

        // 收到用户消息时处理
        $conf = new \EasySwoole\Socket\Config;
        $conf->setType($conf::WEB_SOCKET);
        $conf->setParser(new WebSocketParser);
        $dispatch = new Dispatcher($conf);
        $register->set(EventRegister::onMessage, function (swoole_server $server, swoole_websocket_frame $frame) use ($dispatch) {
            $dispatch->dispatch($server, $frame->data, $frame);
        });

        $subPort = ServerManager::getInstance()->getSwooleServer()->addListener('0.0.0.0',9503,SWOOLE_TCP);
        $subPort->on('receive',function (\swoole_server $server, int $fd, int $reactor_id, string $data){
            //var_dump($data);
        });
        $register->add($register::onWorkerStart, function (\swoole_server $server, int $workerId) {
            if ($server->taskworker == false) {
                //每个worker进程都预创建连接
                PoolManager::getInstance()->getPool(MysqlPool::class)->preLoad(5);//最小创建数量
            }
        });

        //Di::getInstance()->set('Loader', Loader::class);
        $register->add(EventRegister::onWorkerStart, function (\swoole_server $server, $workerId) {
            //如何避免定时器因为进程重启而丢失
            //例如在第一个进程 添加一个10秒的定时器
            if ($workerId == 0) {
                /*\EasySwoole\Component\Timer::getInstance()->loop(10 * 1000, function () {
                    var_dump(time());
                    // 从数据库，或者是redis中，去获取下个就近10秒内需要执行的任务
                    // 例如:2秒后一个任务，3秒后一个任务 代码如下
                    \EasySwoole\Component\Timer::getInstance()->after(2 * 1000, function () {
                        //为了防止因为任务阻塞，引起定时器不准确，把任务给异步进程处理
                        //Logger::getInstance()->console("time 2", false);
                    });
                    \EasySwoole\Component\Timer::getInstance()->after(3 * 1000, function () {
                        //为了防止因为任务阻塞，引起定时器不准确，把任务给异步进程处理
                        //Logger::getInstance()->console("time 3", false);
                    });
                });*/
                Task::getInstance()->timerTask();

// 定时器得不到执行 不输出：timeout
            }
        });

    }

    public static function onRequest(Request $request, Response $response): bool
    {
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {

    }
}