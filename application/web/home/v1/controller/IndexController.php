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
use quickphp\lib\Redis;
use quickphp\lib\Request;
use quickphp\lib\Response;

class IndexController extends HomeController
{


    public function login()
    {
        if (Request::isAjax()) {
            $mobile = Request::request('get', 'mobile');

            //生成四位数随机验证码
            $code = \common::random(4, 1);

            try {
                Message::getInstance()->send('tpl_101', $mobile, array($code));

                $redis = new \swoole\Coroutine\redis();
                $redis->connect(\config::$redis['host'], \config::$redis['port']);
                $redis->set("mobile_$mobile", $code, 120);

                Response::api_response(SUCCESS);
            } catch (\Exception $e) {
                echo $e->getMessage();
                return true;
            }
            return true;
        }
        return $this->display('home/v1/:/Index/login.php');
    }

    public function doLogin()
    {
        if (Request::isAjax()) {
            $mobile = Request::request('get', 'phone_num');
            $code = Request::request('get', 'code');

            try {
                $mobile_code = Redis::getInstance()->get("mobile_$mobile");
                if ($code == $mobile_code) {
                    Redis::getInstance()->del("mobile_$mobile");
                    Response::api_response(SUCCESS);
                } else {
                    Response::api_response(FAIL);
                }
            } catch (\Exception $e) {
                echo $e->getMessage();
                return true;
            }
        }
    }

    public function index()
    {
        return $this->display('home/v1/:/Index/index.php');
    }
}