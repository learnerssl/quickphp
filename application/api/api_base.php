<?php

namespace application\api;

use quickphp\lib\Response;

class api_base
{
    function __construct()
    {
    }

    /**
     * 检查并输出错误信息
     * @param $info
     */
    function check_error($info)
    {
        if (self::is_error($info)) {
            Response::api_response($info['error'], $info['etext']);
        }
    }

    /**
     * 检查是否存在错误
     * @param mixed $info
     * @return bool
     */
    public static function is_error($info)
    {
        if (is_array($info) && !empty($info['etext'])) {
            return true;
        }
        return false;
    }
}