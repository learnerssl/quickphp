<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/5/17
 * Time: 11:03
 */

/**
 * 发送短信类
 */

namespace quickphp\lib;
class Message
{
    private static $uid;
    private static $passwd;
    private static $message;
    private static $_instance;

    private function __construct()
    {
        self::$message = \config::$message;
        self:: $uid = self::$message['uid'];
        self::$passwd = self::$message['passwd'];
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
     * 发送短信操作
     * @param $tpl
     * @param $mobile
     * @param array $params
     * @return bool
     * @throws \Exception
     */
    public function send($tpl, $mobile, $params = array())
    {
        //验证非空
        if (!Regex::getInstance()->isRequire($mobile)) {
            return Response::api_response(FAIL, '手机号不能为空');
        }

        //验证手机格式
        if (!Regex::getInstance()->isMobile($mobile)) {
            return Response::api_response(FAIL, '请输入有效手机号');
        }

        //获取短信模版
        $sms_conf = \config::$sms_conf[$tpl];

        //替换模板变量
        $content = $sms_conf;
        foreach ($params as $key => $item) {
            $content = preg_replace('/\[\%v\]/', $item, $content, 1);
        }
        $content = urlencode($content);

        header("Content-type: text/html; charset=utf-8");
        date_default_timezone_set('PRC'); //设置默认时区为北京时间
        $content = rawurlencode(mb_convert_encoding($content, "gb2312", "utf-8"));
        $gateway = "http://mb345.com:999/WS/BatchSend.aspx?CorpID=" . self::$uid . "&Pwd=" . self::$passwd . "&Mobile={$mobile}&Content={$content}&Cell=&SendTime=";
        $result = file_get_contents($gateway);
        if ($result) {
            return true;
        } else {
            return Response::api_response(FAIL, '发送失败!');
        }
    }
}