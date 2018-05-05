<?php

namespace application\api;

use application\Controller;
use model\user\Ticket;
use quickphp\lib\Redis;
use quickphp\lib\Request;
use quickphp\lib\Response;

class ApiController extends Controller
{
    public $user_info = null;

    public function __construct()
    {
        parent::__construct();

        //请求方式
        $req_method = strtoupper($_SERVER['REQUEST_METHOD']);

        //获取登录票据
        $ticket = Request::request('GET', 'ticket');

        //处理请求数据、检查签名
        if ($req_method == 'OPTIONS') {
            Response::api_response(0);
        } elseif ($req_method == 'GET') {
            //检查签名
            $this->check_sign($_GET);
        } elseif (empty($_FILES)) {
            //如果提交的是json数据，替换到$_POST
            $input = file_get_contents('php://input');
            $input = \common::json_to_array($input);
            if ($input != false) {
                $_POST = $input;
            }

            //检查签名
            $this->check_sign($_POST);
        }

        //获取用户数据
        $user_info = null;
        if (!empty($ticket)) {
            //取出缓存的登录信息
            $redis_key = 'api_user_info' . md5($ticket);
            $user_info = Redis::getInstance()->get($redis_key);

            //检查缓存中是否有数据
            if (empty($user_info)) {
                //获取票据信息
                $ticket_info = Ticket::getInstance()->get_info_by_ticket($ticket);
                $this->check_error($ticket_info);

                //缓存用户信息（缓存12小时）
                Redis::getInstance()->set($redis_key, $user_info, 43200);
            }
        }

        //检查是否需要登录
        if (!empty($ticket) && empty($user_info)) {
            Response::api_response(ERR_LOGIN);
        }

        //记录信息
        $this->user_info = $user_info;
        $this->uid = isset($user_info['uid']) ? $user_info['uid'] : 0;
    }

    /**
     * 检查数据签名
     * @param array $data 数据（包含sign参数，否则签名不通过）
     */
    private function check_sign($data)
    {
        //数据检查
        if ($data == false || !is_array($data)) {
            Response::api_response(ERR_SIGN_EXCEPTION);
        }

        //提取参数、检查随机数和签名字段
        $random = \common::array_get($data, 'random', null);
        $sign = \common::array_get($data, 'sign', null);
        if (empty($random) || empty($sign)) {
            Response::api_response(ERR_SIGN_EXCEPTION);
        }

        //剔除sign
        unset($data['sign']);

        //生成签名
        $_sign = $this->get_data_sign($data, \config::$skey);

        //检查签名（md5）
        if ($_sign != $sign) {
            Response::api_response(ERR_SIGN_EXCEPTION);
        }
    }

    /**
     * 生成数据签名
     * @param array $data 数据
     * @param string $key 加密key
     * @return string 数据的签名
     */
    private static function get_data_sign($data, $key)
    {
        //字典序排列数组
        ksort($data);

        //以&符号拼接非空数据，并补上api_key
        $arr = array();
        foreach ($data as $_key => $_item) {
            //如果参数值为空，此处不能使用empty，因为参数值为0也需要参与签名
            if ($_item === null || $_item === '') {
                continue;
            }
            $arr[] = "$_key=$_item";
        }
        $str = implode('&', $arr) . $key;
        $sign = strtolower(md5($str));

        //返回
        return $sign;
    }
}