<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/9/21
 * Time: 10:16
 * describe: Fill in the description of the document here
 */

namespace application\web\admin\v1\controller;

use application\web\admin\v1\AdminController;
use model\crm\User;
use quickphp\lib\Redis;
use quickphp\lib\Request;
use quickphp\lib\Response;

class Login extends AdminController
{
    /**
     * 后台登录操作
     * @return bool
     */
    public function index()
    {
        //检查是否为post方式提交
        if (Request::isPost()) {
            //外部参数
            $post = Request::request('post');

            //验证表单提交token
            $ret = Request::checkToken($post['token']);
            $this->check_error($ret);

            //根据用户名获取用户信息
            $info = User::getInstance()->get_user_by_username($post['username']);
            $this->check_error($info);

            //执行登录操作
            $ret = User::getInstance()->doLogin($info, $post['password']);
            $this->check_error($ret);

            //设置用户登录redis
            self::set_user_redis($info);

            //清空token
            Request::delToken($post);

            //输出
            Response::api_response(1, '登录成功', array('url' => '/index.php/web/admin/v1/Index/index'));
        }

        //生成表单验证token
        $this->param['token'] = Request::setToken();
        return $this->display('web:admin:v1:Login/index.php', $this->param);
    }

    /**
     * 退出操作
     */
    public function loginout()
    {
        //清空redis
        Redis::getInstance()->del('user');

        //输出
        \common::redirect('/index.php/web/admin/v1/Login/index');
    }

    /**
     * 设置用户登录redis
     * @param $info
     */
    private static function set_user_redis($info)
    {
        //释放数据
        unset($info['password'], $info['lip'], $info['ltime'], $info['stat'], $info['logins']);

        //存储用户信息
        Redis::getInstance()->set('user', base64_encode(json_encode($info)) . ':' . $info['uid'], array('expire' => 0));
    }

    /**
     * 注册操作
     * @return bool
     */
    public function register()
    {
        return $this->display('web:admin:v1:Login/register.php');
    }
}