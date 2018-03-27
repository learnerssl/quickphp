<?php

namespace application\api;

class api_base
{
    public $allow_headers = [
        'Origin',
        'No-Cache',
        'X-Requested-With',
        'If-Modified-Since',
        'Pragma',
        'Last-Modified',
        'Cache-Control',
        'Expires',
        'Content-Type',
        'X-E4M-With',
        'Api-Token',
    ];

    public $allow_origins = [
        '',
    ];

    function __construct()
    {
    }


    /**
     * 输出
     * @param array|string $data 数据
     * @param int|string $error 错误代码|错误描述
     */
    function output($data, $error = null)
    {
        //检查错误
        $ecode = 0;
        $etext = null;
        foreach (\data::$error_codes as $key => $item) {
            if ($item['id'] == $error) {
                $ecode = $item['id'];
                $etext = $item['text'];
                break;
            }
        }
        if ($ecode == 0 && $error !== null) {
            $ecode = -1;
            $etext = $error;
        }

        //组装数据
        $result = [
            'data' => $data,
            'error' => $ecode,
            'etext' => $etext
        ];
        $result = json_encode($result);

        //通行header
        header('Access-Control-Allow-Headers: ' . join(',', $this->allow_headers));

        //允许跨域请求
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        if (in_array($origin, $this->allow_origins)) {
            header('Access-Control-Max-Age:' . 300);
            header('Access-Control-Allow-Origin:' . $origin);
        }

        //输出
        die($result);
    }
}