<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-01-01
 * Time: 20:06
 */

return [
    'SERVER_NAME'    => "EasySwoole",
    'MAIN_SERVER'    => [
        'LISTEN_ADDRESS' => '0.0.0.0',
        'PORT'           => 9501,
        'SERVER_TYPE'    => EASYSWOOLE_WEB_SOCKET_SERVER, //可选为 EASYSWOOLE_SERVER  EASYSWOOLE_WEB_SERVER EASYSWOOLE_WEB_SOCKET_SERVER,EASYSWOOLE_REDIS_SERVER
        'SOCK_TYPE'      => SWOOLE_TCP,
        'RUN_MODEL'      => SWOOLE_PROCESS,
        'SETTING'        => [
            'worker_num'            => 8,
            'reload_async'          => true,
            'max_wait_time'         => 5,
            'document_root'         => EASYSWOOLE_ROOT . '/Static',
            'enable_static_handler' => true,
        ],
        'TASK'           => [
            'workerNum' => 4, 'maxRunningNum' => 128, 'timeout' => 15
        ]
    ],
    'TEMP_DIR'       => null,
    'LOG_DIR'        => null,
    'CONSOLE'        => [
        'ENABLE'         => true,
        'LISTEN_ADDRESS' => '127.0.0.1',
        'HOST'           => '127.0.0.1',
        'PORT'           => 9500,
        'USER'           => 'root',
        'PASSWORD'       => '123456'
    ],
    'DISPLAY_ERROR'  => true,
    'PHAR'           => [
        'EXCLUDE' => ['.idea', 'Log', 'Temp', 'easyswoole', 'easyswoole.install']
    ],

    // 当前的域名
    'HOST'           => 'http://192.168.8.199:9501',
    'WEBSOCKET_HOST' => 'ws://192.168.8.199:9501',


    'CHECK_EMAIL'   => true,
    'EMAIL_SETTING' => [
        'PORT'     => 465,
        'FORM'     => 'EASY-DEMO <mipone@foxmail.com>',
        'SERVER'   => 'smtp.qq.com',
        'SECURE'   => true,
        'USERNAME' => 'mipone@foxmail.com',
        'PASSWORD' => 'abltlnhpmdyfbcga',
    ],
    /*################ MYSQL CONFIG ##################*/
    'MYSQL'         => [
    'host'          => '192.168.8.50',//
    'port'          => '3306',
    'user'          => 'root',
    'timeout'       => '5',
    'charset'       => 'utf8mb4',
    'password'      => '123456',
    'database'      => 'im',
    'POOL_MAX_NUM'  => '10',
    'POOL_TIME_OUT' => '0.1'
    ],
    /*################ REDIS CONFIG ##################*/
    'REDIS' => [
        'host'          => '192.168.8.200',
        'port'          => '6379',
        'auth'          => '123456',
        'db'            => 0,//选择数据库,默认为0
        'intervalCheckTime'    => 30 * 1000,//定时验证对象是否可用以及保持最小连接的间隔时间
        'maxIdleTime'          => 15,//最大存活时间,超出则会每$intervalCheckTime/1000秒被释放
        'maxObjectNum'         => 20,//最大创建数量
        'minObjectNum'         => 5,//最小创建数量 最小创建数量不能大于等于最大创建
    ],
    /*################ 定时任务 ##################*/
    'timerTask'         => [
        [
            'model_name' => 'MFriend',
            'method_name' => 'tickAddFriend',
            'interval_time' => '4',
            'delay' => true
        ]
    ],
];
