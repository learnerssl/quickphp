<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/5/22
 * Time: 20:16
 */

/**
 * 通用接口通信类
 */

namespace quickphp\lib;
class Response
{
    //允许跨域请求域名
    public static $allow_origins = [
    ];

    /**
     * 通信接口
     * @param int $error
     * @param string $etext
     * @param array $data
     * @throws \Exception
     */
    public static function api_response($error = 0, $etext = '', $data = [])
    {
        //允许跨域请求
        $origin = isset($_SERVER['HTTP_ORIGIN']) ?? '';
        if (in_array($origin, self::$allow_origins)) {
            header('Access-Control-Allow-Origin:' . $origin);
        }

        //不正确的错误码或错误信息唯为空
        if ((int)$error <= -1 || empty($etext)) {
            list($error, $etext) = \common::get_text_by_error(empty($etext) ? $error : ERR_ERROR_CODE);
        }
        self::jsonEncode($error, $etext, $data);
    }

    /**
     * JSON格式
     * @param $status
     * @param $msg
     * @param $data
     * @throws \Exception
     */
    private static function jsonEncode($status, $msg, $data)
    {
        $json_data = array('error' => $status, "etext" => $msg, "data" => $data);
        $msg = json_encode($json_data, JSON_UNESCAPED_UNICODE);
        \common::common_exit($msg);
    }
}