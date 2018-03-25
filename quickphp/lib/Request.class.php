<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/6/22
 * Time: 16:42
 */

namespace quickphp\lib;
class Request
{
    /**
     * 是否是AJAx提交的
     * @return bool
     */
    public static function isAjax()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        }
        return false;
    }

    /**
     * 是否是GET提交的
     * @return bool
     */
    public static function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;
    }

    /**
     * 是否是POST提交
     * @return bool
     */
    public static function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST' ? true : false;
    }

    /**
     * 获取POST,GET参数
     * @param  string $method POST | GET  http请求方式，默认为GET
     * @param  string $param 指定参数
     * @param  string $default 默认值
     * @return array | string
     */
    public static function request($method = 'GET', $param = '', $default = null)
    {
        $param = trim($param);
        if (empty($param)) {
            $data = strtoupper($method) === 'GET' ? $_GET : $_POST;
            return self::html_xss_by_array($data, $default);
        } else {
            $data = strtoupper($method) === 'GET' ? $_GET[$param] : $_POST[$param];
            if (is_array($data)) {
                return self::html_xss_by_array($data, $default);
            } else {
                return (!empty($data) && isset($data)) ? \common::html_xss($data) : $default;
            }
        }
    }

    /**
     * 数组元素依次进行xss防御操作
     * @param array $data
     * @param       $default
     * @return array
     */
    private static function html_xss_by_array($data = array(), $default)
    {
        unset($data['s']);
        foreach ($data as $key => $item) {
            if (!is_array($item)) {
                $data[$key] = (!empty($item) && isset($item)) ? \common::html_xss($item) : $default;
            } else {
                //多维数组进行递归操作
                self::html_xss_by_array($item, $default);
            }
        }
        return $data;
    }

    /**
     * 随机生成csrfToken令牌
     * @return string
     */
    public static function setToken()
    {
        $csrf_token = \config::$skey;
        $csrf = sha1(md5(time() . $csrf_token) . $csrf_token);
        Redis::getInstance()->set('csrf', base64_encode($csrf), 1800);
        return $csrf;
    }

    /**
     * 验证csrfToken令牌
     * @param $csrf
     * @return array|bool
     */
    public static function checkToken($csrf)
    {
        return base64_encode($csrf) == Redis::getInstance()->get('csrf') ? true : \common::output_error(ERR_FORM_AUTHFAILED);
    }

    /**
     * 删除csrfToken令牌
     * @param $post array 表单信息
     * @return bool
     */
    public static function delToken(&$post = array())
    {
        unset($post['token']);
        return Redis::getInstance()->del('csrf');
    }
}