<?php
/**
 * Created by PhpStorm.
 * User: Olly
 * Date: 2016/9/2
 * Time: 19:51
 */

namespace App;
class AppError extends \Exception
{

    public $others;
    public function __construct($message = "", $code = 0, $others = null)
    {
        parent::__construct($message, $code);
        $this->others = $others;
    }
}