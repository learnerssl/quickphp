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

class Index extends AdminController
{
    /**
     * 后台主页
     * @return bool
     */
    public function index()
    {
        //检查是否登录
        if ($this->uid) {
            return $this->display('web:admin:v1:index/index.php', $this->param);
        }
    }

    /**
     * 后台首页
     * @return bool
     */
    public function home()
    {
        return $this->display('web:admin:v1:index/home.php');
    }
}