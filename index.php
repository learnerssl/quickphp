<?php
//------------------------
// QuickPHP 框架入口文件
//-------------------------

header("Content-type: text/html; charset=utf-8");
ini_set('date.timezone', 'PRC');

/**
 * 第一步：定义常量
 */
define('ROOT', dirname(__FILE__)); //定义当前框架所在的根目录
define('QUICKPHP', ROOT . '/quickphp');//定义框架的核心文件所处的目录
define('APPLICATION', ROOT . '/application');//定义项目根目录
define('PUB', '/public');//定义项目资源目录
define('DEBUG', true);//调试模式
define('CRONTAB', true); //定时任务

/**
 * 第二步：加载函数库
 */
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

Seaslog::setBasePath(ROOT.'/log/');

/**
 * 第三步：判断PHP版本必须在7.0以上
 */
if (version_compare(PHP_VERSION, '7.0', '<')) {
    throw new \RuntimeException('require PHP > 7.0 !');
}

/**
 * 第四步：将该函数注册成为自动加载函数
 */
spl_autoload_register('\\quickphp\\Loader::autoload');

/**
 * 第五步：启动框架
 */
\quickphp\Loader::Run($argv);



