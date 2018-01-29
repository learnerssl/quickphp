<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/9/21
 * Time: 10:16
 * describe: Fill in the description of the document here
 */

namespace application\admin\controller;

use application\admin\AdminController;

class Login extends AdminController
{
    /**
     * 后台登录操作
     * @return bool
     */
    public function login()
    {
        return $this->display('admin:Login/index.php');
    }

    /**
     * 注册操作
     * @return bool
     */
    public function register()
    {
        return $this->display('admin:Login/register.php');
    }
}