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


class IndexController extends HomeController
{
    public function index()
    {
        return $this->display('home/v1/:/Index/index.php',['name' => 'simon','age' => 23]);
    }

}