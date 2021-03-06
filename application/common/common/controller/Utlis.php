<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/9/26
 * Time: 21:05
 * describe: Fill in the description of the document here
 */

namespace application\common\common\v1\controller;

use application\Controller;
use model\sys\Region;
use quickphp\lib\Cookie;
use quickphp\lib\Request;
use quickphp\lib\Response;
use quickphp\lib\Upload;

class Utlis extends Controller
{
    /**
     * 上传图片
     * @throws \Exception
     */
    public function upload()
    {
        //获取文本域名
        $name = array_keys($_FILES)[0];

        //外部参数
        $id = Request::request('post', 'id');

        //上传图片
        $ret = Upload::getInstance($name)->uploadFile();
        if ($this->is_error($ret)) {
            $this->check_error($ret);
        }

        //封装数据
        $data = array('id' => $id, 'src' => $ret);

        //返回
        Response::api_response(SUCCESS, '上传成功', $data);
    }

    /**
     * kindeditor上传图片
     */
    public function kindeditorupload()
    {
        //上传图片
        $ret = Upload::getInstance('imgFile')->uploadFile();
        if ($this->is_error($ret)) {
            $this->check_error($ret);
        }

        if ($ret['error'] == -1) {
            //失败返回
            exit(json_encode(array('error' => 1, 'message' => $ret['etext'])));
        }

        //成功返回
        exit(json_encode(array('error' => 0, 'url' => $ret)));
    }

    /**
     * 获取城市列表
     * @throws \Exception
     */
    public function getList()
    {
        //检查是否为post方式提交
        if (Request::isPost()) {
            //获取prt对应的列表
            $id = Request::request('post', 'id');

            $list = Region::getInstance()->get_list_by_prt($id);
            Response::api_response(SUCCESS, '请求成功', array('data' => $list));
        }
        Response::api_response(ERR_REQUEST_METHOD);
    }

    /**
     * 设置Cookie
     * @throws \Exception
     */
    public function setCookie()
    {
        //检查是否为post方式提交
        if (Request::isPost()) {
            //外部参数
            $name = Request::request('post', 'name');
            $value = Request::request('post', 'value');
            $expire = Request::request('post', 'expire', 0);
            $path = Request::request('post', 'path', '/');
            $domain = Request::request('post', 'domain');
            $secure = Request::request('post', 'secure', false);
            $httponly = Request::request('post', 'httponly', false);
            $urlencode = Request::request('post', 'urlencode', false);

            //封装参数
            $option = array(
                'expire' => $expire,
                'path' => $path,
                'domain' => $domain,
                'secure' => $secure,
                'httponly' => $httponly,
                'urlencode' => $urlencode
            );

            //设置cookie
            Cookie::getInstance()->setcookie($name, $value, $option);

            //返回
            Response::api_response(SUCCESS);
        }
        Response::api_response(ERR_REQUEST_METHOD);
    }
}