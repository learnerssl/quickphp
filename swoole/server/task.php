<?php

namespace swoole\server;

use quickphp\lib\Redis;

class  task
{
    private static $_instance;

    private function __construct()
    {
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 发送短信
     * @param $data
     */
    public function sms($data)
    {
        $tmp = $data['data']['tpl'];
        unset($data['data']['tpl']);

        //根据短信模版，投递短信
        self::$tmp($data['data']);
    }

    private static function tpl_101($data)
    {
        try {
            \quickphp\lib\Message::getInstance()->send(__FUNCTION__, $data['mobile'], array($data['code']));

            Redis::getInstance()->set("mobile_" . $data['mobile'], $data['code'], 120);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        return true;
    }
}