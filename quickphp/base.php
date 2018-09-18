<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/14
 * Time: 11:32
 */

header("Content-type: text/html; charset=utf-8");
ini_set('date.timezone', 'PRC');

define('ROOT', __DIR__ . '/..'); //定义当前框架所在的根目录
define('QUICKPHP', ROOT . '/quickphp');//定义框架的核心文件所处的目录
define('APPLICATION', ROOT . '/application');//定义项目根目录
define('PUB', '/public');//定义项目资源目录
define('DEBUG', true);//调试模式
define('CRONTAB', true); //定时任务

require ROOT . "/vendor/autoload.php";//自动加载composer下载下来的类库文件
require QUICKPHP . '/Loader.php';//加载框架引导文件
require ROOT . '/error.php';//加载框架错误常量文件
if (DEBUG) {
    //如果要使用调试功能，请先使用composer install安装相关依赖
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
    ini_set('display_errors', 'On'); //开启错误信息
    ini_set('error_reporting', E_ALL & ~E_STRICT & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);
} else {
    ini_set('display_errors', 'Off');//关闭错误信息
}

Seaslog::setBasePath(ROOT . '/log/');

//使用示例：
//SeasLog::log(SEASLOG_ERROR,'this is a error test by ::log');
//SeasLog::debug('this is a {userName} debug',array('{userName}' => 'neeke'));
//SeasLog::info('this is a info log');
//SeasLog::notice('this is a notice log');
//SeasLog::warning('your {website} was down,please {action} it ASAP!',array('{website}' => 'github.com','{action}' => 'rboot'));
//SeasLog::error('a error log');
//SeasLog::critical('some thing was critical');
//SeasLog::alert('yes this is a {messageName}',array('{messageName}' => 'alertMSG'));
//SeasLog::emergency('Just now, the house next door was completely burnt out! {note}',array('{note}' => 'it`s a joke'));
//$countResult_1 = SeasLog::analyzerCount();
//$countResult_2 = SeasLog::analyzerCount(SEASLOG_WARNING);
//$countResult_3 = SeasLog::analyzerCount(SEASLOG_ERROR,date('Ymd',time()));
//var_dump($countResult_1,$countResult_2,$countResult_3);

if (version_compare(PHP_VERSION, '7.0', '<')) {
    throw new \RuntimeException('require PHP > 7.0 !');
}

spl_autoload_register('\\quickphp\\Loader::autoload');