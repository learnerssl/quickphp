<?php

/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/8/26
 * Time: 09:01
 * Des:系统配置文件
 */
class config
{
    //系统加密key
    public static $skey = 'GcLX2pahtqMIfnOxThV5US5hg3OCf7JO';

    //域名
    public static $domain = 'http://quickphp.romantic.ren:9503';

    //MySQL服务器配置
    public static $database_conf = array(
        "dbms" => "mysql",
        "host" => "localhost",
        "dbname" => "",
        "username" => "",
        "password" => "",
        "charset" => "utf8",
        "port" => 3306,
    );

    //Sphinx配置
    public static $sphinx_conf = array(
        'host' => 'localhost',
        'port' => 9312,
        'timeout' => 0
    );

    //微信公众号配置
    public static $wx_conf = array(
        "AppID" => "",
        "AppSecret" => "",
    );

    //默认路由
    public static $default_route = array(
        "direction" => "web",
        "module" => "home",
        "version" => "v1",
        "controller" => "Index",
        "method" => "index"
    );

    //百度地图ak
    public static $map_ak = "wi0TE5A42Te9HD5why9NzEAP";

    //快递100
    public static $express = array(
        'EBusinessID' => 1282121,
        'AppKey' => 'f6c95f4c-0d9a-4652-a666-c585244db577',
        'ReqURL' => 'http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx',
    );

    //Redis配置信息
    public static $redis = array(
        'host' => '127.0.0.1',
        'port' => 6379,
        'password' => '',
        'select' => 0,
        'timeout' => 0,
        'expire' => 0,
        'persistent' => false,
        'prefix' => '',
    );

    //短信配置(凌凯短信)
    public static $message = array(
        'uid' => 'CQLKJ0005302',
        'passwd' => 'twsw@6688',
    );

    //行业短信接口、模板配置
    public static $sms_conf = array(
        'tpl_101' => '您的验证码是：[%v]。请不要把验证码泄露给其他人。',
    );
}

