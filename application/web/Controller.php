<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/6/21
 * Time: 11:39
 */

//------------------------
// QuickPHP 基类控制器
//-------------------------
namespace application\web;

use quickphp\lib\Response;

class Controller
{
    protected $uid;
    protected $mobile;
    protected $param;
    private $assign = array();

    public function __construct()
    {
        //防止浏览器中的反射性xss
        \common::header('X-XSS-Protection', '1');
    }


    /**
     * 检查错误
     * @param             $info
     * @param bool $error 是否跳转页面 默认为false
     * @param string|null $message 错误提示信息
     * @param string|null $url 跳转URL地址
     * @return mixed
     */
    public function check_error($info, $error = false, $message = '', $url = '/')
    {
        //检查是否存在错误信息
        if ($info['error'] || $info['etext']) {
            return $error === true ? $this->error($message, $url) : Response::api_response($info['error'], $info['etext']);
        }
        return true;
    }

    /**
     * 错误信息展示页面
     * @param string $message 错误信息
     * @param string $url 跳转路径
     * @return mixed
     */
    public function error($message = '', $url = '/')
    {
        $default_message = empty($message) ? '抱歉，你输入的网址可能不正确，或者该网页不存在。' : $message;
        $this->display('common:common/error.php', array('message' => $default_message, 'url' => $url));
        exit;
    }

    /**
     * @desc 检查是否存在错误
     * @param  $info
     * @return bool
     */
    public function is_error($info)
    {
        if (($info === false) || (empty($info)) || (is_array($info) && isset($info['error']) && !empty($info['etext']))) {
            return true;
        }
        return false;
    }

    /**
     * @desc 渲染模版操作
     * @param String $file 模版名
     * @param array $array 赋值参数，默认为array()
     * @return bool
     */
    public function display($file, $array = array())
    {
        $path = explode(':', $file);
        $file = APPLICATION . '/' . $path[0] . '/view/' . $path[1];
        try {
            if (is_file($file)) {
                foreach ($array as $key => $val) {
                    $this->assign($key, $val);
                }
                $this->assign('PUBLICCSS', PUBLICCSS);
                $this->assign('PUBLICJS', PUBLICJS);
                $this->assign('PUBLICIMG', PUBLICIMG);
                extract($this->assign);
                include_once $file;
            } else {
                throw  new \Exception("模版不存在:" . $file);
            }
        } catch (\Exception $exception) {
            $error = array(
                '错误码' => $exception->getCode(),
                '错误信息' => $exception->getMessage(),
                '错误地址' => $exception->getFile() . ' ' . $exception->getLine() . '行',
            );
            return \common::output($error);
        }
        return true;
    }

    /**
     * @desc 模版赋值操作
     * @param string $key 变量名
     * @param string || array $value 变量值
     */
    public function assign($key, $value)
    {
        $this->assign[$key] = $value;
    }
}