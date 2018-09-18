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
use quickphp\lib\Message;
use quickphp\lib\Request;

class IndexController extends HomeController
{

    public function index()
    {
        if (Request::isAjax()) {
            $mobile = Request::request('get', 'mobile');

            //生成四位数随机验证码
            $code = \common::random(4, 1);

            try {
                Message::getInstance()->send('tpl_101', $mobile, array($code));
            } catch (\Exception $e) {
                echo $e->getMessage();
                return true;
            }
            return true;
        }
        return $this->display('home/v1/:/Index/login.php');
    }

}