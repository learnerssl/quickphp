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

class Index extends AdminController
{
    /**
     * 后台主页
     * @return bool
     */
    public function index()
    {
        return $this->display('admin:Index/index.php');
    }

    /**
     * 后台首页
     * @return bool
     */
    public function home()
    {
        return $this->display('admin:Index/home.php');
    }
}