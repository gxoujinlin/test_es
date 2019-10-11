<?php
/**
 * Created by PhpStorm.
 * User: oujinlin
 * Date: 2019/9/11
 * Time: 14:30
 */

namespace App\Task;

use App\Loader;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\Component\Singleton;
use EasySwoole\Component\Timer;

class Task
{
    use Singleton;

    protected $timer_task_used;
    public function timerTask()
    {
        $config = Loader::getInstance()->config() ;
        if(!empty($config['timerTask'])){
            $timer_task = $config['timerTask'];
            $timer_task_used = [];
            foreach ($timer_task as $key=>$value){
                $model_name = $value['model_name']??'';
                $method_name = $value['method_name']??'';
                $interval_time = $value['interval_time']??1;
                $max_exec = $value['max_exec']??-1;
                if (!array_key_exists('start_time', $value)) {
                    $start_time = time();
                } else {
                    $start_time = strtotime(date($value['start_time']));
                }
                if (!array_key_exists('end_time', $value)) {
                    $end_time = -1;
                } else {
                    $end_time = strtotime(date($value['end_time']));
                }
                if (!array_key_exists('delay', $value)) {
                    $delay = false;
                } else {
                    $delay = $value['delay'];
                }
                if(empty($model_name) || empty($method_name)){
                    Logger::getInstance()->console("定时任务 $key 配置错误，缺少task_name或者model_name.");
                    continue;
                }
                $this->timer_task_used[] = [
                    'model_name' => $model_name,
                    'method_name' => $method_name,
                    'start_time' => $start_time,
                    'next_time' => $start_time,
                    'end_time' => $end_time,
                    'interval_time' => $interval_time,
                    'max_exec' => $max_exec,
                    'now_exec' => 0,
                    'delay' => $delay,
                ];
            }
var_dump($timer_task_used);
            Timer::getInstance()->loop(1 * 1000, function (){
                $this->executeTimerTask();
            });

        }
    }

    public function executeTimerTask()
    {
        $time = time();
        if(!empty($this->timer_task_used)){
            foreach ($this->timer_task_used as &$timer_task){

                $model_name = $timer_task['model_name']??'';
                $method_name = $timer_task['method_name']??'';

                if ($timer_task['next_time'] < $time) {
                    $count = round(($time - $timer_task['start_time']) / $timer_task['interval_time']);
                    $timer_task['next_time'] = $timer_task['start_time'] + $count * $timer_task['interval_time'];
                }
                if ($timer_task['end_time'] != -1 && $time > $timer_task['end_time']) {//说明执行完了一轮，开始下一轮的初始化
                    $timer_task['start_time'] = strtotime(date("Y-m-d H:i:s", $timer_task['start_time']));
                    $timer_task['end_time'] = strtotime(date("Y-m-d H:i:s", $timer_task['end_time']));
                    $timer_task['next_time'] = $timer_task['start_time'];
                    $timer_task['now_exec'] = 0;
                }
                //var_dump($timer_task);
                if (($time == $timer_task['next_time']) &&
                    ($time < $timer_task['end_time'] || $timer_task['end_time'] == -1) &&
                    ($timer_task['now_exec'] < $timer_task['max_exec'] || $timer_task['max_exec'] == -1)
                ) {
                    if ($timer_task['delay']) {
                        $timer_task['next_time'] += $timer_task['interval_time'];
                        $timer_task['delay'] = false;
                        continue;
                    }
                    $timer_task['now_exec']++;
                    $timer_task['next_time'] += $timer_task['interval_time'];
                    $model = Loader::getInstance()->model($model_name);
                    $model->$method_name();
                }
            }
        }
    }

}