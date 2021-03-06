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
use quickphp\lib\Redis;
use quickphp\lib\Request;
use quickphp\lib\Response;

class IndexController extends HomeController
{

    /**
     * @return bool
     * @throws \Exception
     */
    public function login()
    {
        if (Request::isAjax()) {
            $mobile = Request::request('get', 'mobile');

            //生成四位数随机验证码
            $code = \common::random(4, 1);

            try {
                //投递task任务
                $taskData = [
                    'type' => 'sms',
                    'data' => [
                        'tpl' => 'tpl_101',
                        'mobile' => $mobile,
                        'code' => $code
                    ]
                ];
                $_SERVER['http']->task($taskData);

                Response::api_response(SUCCESS);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
            return true;
        }
        return $this->display('home/v1/:/Index/login.php');
    }

    /**
     * @return bool
     * @throws \Exception
     */
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
            }
            return true;
        }
        Response::api_response(ERR_REQUEST_METHOD);
    }

    public function index()
    {
        return $this->display('home/v1/:/Index/index.php');
    }

    public function sphinx()
    {
        require QUICKPHP . '/lib/Sphinx.class.php';
        $cl = new \Sphinx();
        $cl->SetServer("localhost", 9312);
        $cl->SetMatchMode(SPH_MATCH_EXTENDED);
        $cl->SetArrayResult(true);
        $result = $cl->Query('电视台', 'test1');
        dump($result);
        exit;
    }
}