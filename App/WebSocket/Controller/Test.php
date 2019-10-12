<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-12-02
 * Time: 01:19
 */

namespace App\WebSocket\Controller;

use App\Storage\OnlineUser;
use App\WebSocket\Actions\User\UserInfo;
use App\WebSocket\Actions\User\UserOnline;
use Exception;

class Test extends Base
{
    public function who(){
        //$this->response()->setMessage('your fd is '. $this->caller()->getClient()->getFd());
        $data = [
          'aaa'=>11111,
          'fd'=>$this->caller()->getClient()->getFd(),
        ];

        $this->response()->setMessage(json_encode($data));
    }
}