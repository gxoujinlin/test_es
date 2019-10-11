<?php

namespace App\HttpController;

use App\Utility\ReverseProxyTools;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\AbstractInterface\Controller;
use App\Model\Member\Member2Model;
use App\Model\Member\MemberModel;
use EasySwoole\MysqliPool\Mysql;
use EasySwoole\Component\Pool\PoolManager;
use App\Utility\Pool\MysqlPool;
use EasySwoole\Mysqli\Mysqli;
use App\Model\ConditionBean;
use App\Utility\Pool\MysqlObject;
use EasySwoole\Spl\SplBean;
use App\Loader;
use App\AppError;

/**
 * Class Index
 * @package App\HttpController
 */
class Test extends Base
{

    /**
     * @var \App\Model\Member\MemberModel;
     */
    public $MemberModel;
    public function __construct()
    {
        parent::__construct();
    }
    function index()
    {
        //$this->redis()->set('name','222');
        //var_dump($this->redis()->get('name'));
        //throw new AppError('失败', 0);
        $this->writeJson(200,['xsk'=>'仙士可'],'success');

    }
    function test()
    {
        //for ($i=0;$i<10000;$i++){
            $this->MemberModel = $this->model("Member\\MemberModel");
            $data = $this->MemberModel->test();
        //$data = Loader::getInstance()->config() ;
            $this->writeJson(200,$data,'success');
        //}

    }
    function testRedis()
    {
        $this->redis()->set('name','222');
        $name = $this->redis()->get('name');
        var_dump($name);
        $this->writeJson(200,['name'=>$name],'success');
    }
    public function testThrow()
    {
        throw new AppError('失败', 0);
        var_dump(1111);
        $this->writeJson(200,['xsk'=>'仙士可'],'success');
    }
    public function testHttpClient()
    {
        $url = 'http://tingapi.ting.baidu.com/v1/restserver/ting';
        $test = new \EasySwoole\HttpClient\HttpClient($url);
//$test->post();

        $test->addCookie('c1','c1')->addCookie('c2','c2');

        $test->setHeader('myHeader','myHeader');
        $page_start = 0 ;
        $data = [
            'from'=>'qianqian',
            'version'=>'2.1.0',
            'method'=>'baidu.ting.billboard.billList',
            'format'=>'json',
            'size'=>20,
            'type'=>1,
            'offset'=>$page_start,
        ];

        //$ret = $test->postJSON(json_encode(['json'=>1]));
        $ret = $test->post($data);

        var_dump($ret->getBody());
    }
    //协程
    public function testCoHttpClient()
    {
        //\EasySwoole\EasySwoole\Core::getInstance()->initialize();
        go(function () {
            //实例化
            $url = 'http://tingapi.ting.baidu.com/v1/restserver/ting';
            $client = new \EasySwoole\HttpClient\HttpClient();
            $client->setUrl($url);
            $client->setHeader('myHeader','myHeader');
            $page_start = 0 ;
            $data = [
                'from'=>'qianqian',
                'version'=>'2.1.0',
                'method'=>'baidu.ting.billboard.billList',
                'format'=>'json',
                'size'=>20,
                'type'=>1,
                'offset'=>$page_start,
            ];
            $ret = $client->post($data);
            var_dump($ret->getBody());
        });

    }
}
