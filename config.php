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
    public static $Domain = 'www.cqtw777.com';

    //定义csrf令牌常量
    public static $CSRF = 'ca969a1bc97732d97b1e88ce8396c216';

    //自动登录Oauth令牌
    public static $Oauth = '1c1147d6dfd6dd9cda5ab2f44b0186e5';

    //MySQL服务器配置
    public static $database_conf = array(
        "dbms" => "mysql",
        "host" => "localhost",
        "dbname" => "cqtw777",
        "username" => "root",
        "password" => "s15213430760",
        "charset" => "utf8",
        "port" => 3306,
    );

    //微信配置
    public static $wx_conf = array(
        "AppID" => "wx40a1191e6ea697d6",
        "AppSecret" => "e5e294d953104d8d3899d8fbb872f88a",
    );

    //默认路由
    public static $default_route = array(
        "model" => "home",
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

    //redis配置信息
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

    //短信配置
    public static $message = array(
        'uid' => 'CQLKJ0005302',
        'passwd' => 'twsw@6688',
    );

    //行业短信接口、模板配置
    public static $sms_conf = array(
        'tpl_101' => '您的验证码是：[%v]。请不要把验证码泄露给其他人。',
    );
}

