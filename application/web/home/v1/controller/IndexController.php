<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/9/30
 * Time: 09:31
 * describe: Fill in the description of the document here
 */

namespace application\web\home\v1\controller;

use application\web\home\v1\HomeController;
use quickphp\lib\Request;

class IndexController extends HomeController
{

    public function index()
    {
        dump($_SERVER);
        if (Request::isAjax()) {
            $mobile = Request::request('get', 'mobile');
        }
        return $this->display('home/v1/:/Index/login.php');
    }

}