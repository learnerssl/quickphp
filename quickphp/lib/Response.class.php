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
    const FORMAT = 'json';

    //允许的hender头信息
    public static $allow_headers = [
    ];

    //允许跨域请求域名
    public static $allow_origins = [
    ];

    /**
     * 按综合方式输出通信数据
     * @param int $error
     * @param string $etext
     * @param array $data
     * @param string $format
     * @return bool
     * @throws \Exception
     */
    public static function api_response($error = 0, $etext = '', $data = [], $format = self::FORMAT)
    {
        //header头信息
        header('Access-Control-Allow-Headers: ' . join(',', self::$allow_headers));

        //允许跨域请求
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        if (in_array($origin, self::$allow_origins)) {
            header('Access-Control-Max-Age:' . 300);
            header('Access-Control-Allow-Origin:' . $origin);
        }

//        //不正确的错误码
//        if ((int)$error <= 0) {
//            list($error, $etext) = \common::get_text_by_error(ERR_ERROR_CODE);
//        }

        $format = \common::get_default_value($_GET['format'], $format);
        if ($format === 'json') {
            self::jsonEncode($error, $etext, $data);
        } elseif ($format === 'xml') {
            self::xmlEncode($error, $etext, $data);
        }
        return true;
    }

    /**
     * 按json方式输出通信数据
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

    /**
     * @desc 按xml方式输出通信数据
     * @param  int $status 状态码  1:请求成功 0:请求失败
     * @param  string $msg 提示信息
     * @param  array $data 数据
     * @return string
     */
    private static function xmlEncode($status, $msg, $data)
    {
        $xml_data = array('error' => $status, "etext" => $msg, "data" => $data);
        header("Content-Type:text/xml");
        $xml = "<?xml version='1.0' encoding='UTF-8'?>";
        $xml .= "<root>";
        $xml .= self::xmlToEncode($xml_data);
        $xml .= "</root>";
        return $xml;
    }

    /**
     * 将数据转化为xml格式
     * @param $data
     * @return string
     */
    private static function xmlToEncode($data)
    {
        $xml = $attr = "";
        foreach ($data as $key => $value) {
            //xml格式节点不能为数字
            if (is_numeric($key)) {
                $attr = " id='{$key}'";
                $key = "item";
            }
            $xml .= "<{$key}{$attr}>";
            $xml .= is_array($value) ? self::xmlToEncode($value) : $value;
            $xml .= "</{$key}>";
        }
        return $xml;
    }
}