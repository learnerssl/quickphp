<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/9/21
 * Time: 10:16
 * describe: Fill in the description of the document here
 */

namespace application\web\admin\BasicController;

use application\web\admin\AdminController;
use quickphp\lib\Request;
use quickphp\lib\Response;

class Basic extends AdminController
{
    /**
     * 网站基本信息
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

            //清空token
            Request::delToken($post);

            //编辑系统设置信息
            $ret = \model\sys\Basic::save($post);
            $this->check_error($ret);

            //输出
            Response::api_response(1, '操作成功', array('url' => '/index.php/admin/Basic/index'));
        }

        //生成令牌
        $this->param['token'] = Request::setToken();
        $this->param['basic'] = \model\sys\Basic::given();
        return $this->display('admin:Basic/index.php', $this->param);
    }

    /**
     * 数据库备份
     */
    public function mysqldump()
    {
        $shell = "php mysqldump -u" . \config::$database_conf['username'] . " -p" . \config::$database_conf['password'] . " " . \config::$database_conf['dbname'] . " > " . ROOT . '/sql/' . date('Ymd') . ".sql";
        exec($shell);
    }
}