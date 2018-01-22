<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/5/17
 * Time: 11:03
 */

/**
 * 百度地图类
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
     * @param int $tpl 短信模版id
     * @param $mobile
     * @param array $params
     */
    public function send($tpl, $mobile, $params = array())
    {
        //验证非空
        if (!Regex::getInstance()->isRequire($mobile)) {
            Response::api_response(0, '手机号不能为空');
        }

        //验证手机格式
        if (!Regex::getInstance()->isMobile($mobile)) {
            Response::api_response(0, '请输入有效手机号');
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
            Response::api_response(1, '发送成功!');
        } else {
            Response::api_response(0, '发送失败!');
        }
    }
}