<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/9/28
 * Time: 22:26
 * describe: Fill in the description of the document here
 */

namespace application\web\home\v1;

use application\Controller;

class HomeController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->_twig = false; //是否使用twig模版引擎
        $this->assign('PUBLICCSS', PUB . '/css');//定义项目css目录
        $this->assign('PUBLICJS', PUB . '/js');//定义项目js目录
        $this->assign('PUBLICIMG', PUB . '/img');//定义项目img目录
    }
}