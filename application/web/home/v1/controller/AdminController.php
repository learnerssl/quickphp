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
use quickphp\lib\Response;
use quickphp\lib\Upload;

class AdminController extends HomeController
{
    public function index()
    {
        return $this->display('home/v1/:/Admin/live.php');
    }


    /**
     * @throws \Exception
     */
    public function upload()
    {
        $ret = Upload::getInstance('file')->uploadFile();
        if ($this->is_error($ret)) {
            $this->check_error($ret);
        }

        //封装数据
        $data = array('src' => $ret);

        //返回
        Response::api_response(SUCCESS, '上传成功', $data);
    }
}