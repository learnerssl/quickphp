<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/4/30
 * Time: 10:12
 */

namespace quickphp;

class Route
{
    private static $direction;
    private static $module;
    private static $version;
    private static $controller;
    private static $method;

    //路由解析
    public static function geRoute($argv)
    {
        //获取crontab定时脚本参数
        if (CRONTAB && is_array($argv) && !empty($argv) && count($argv) <= 6) {
            self::$direction = $argv[1];
            self::$module = $argv[2];
            self::$version = $argv[3];
            self::$controller = $argv[4];
            self::$method = $argv[5];
        } else {
            //外部参数
            $REQUEST_URI = $_SERVER['REQUEST_URI'];
            if (isset($REQUEST_URI) && $REQUEST_URI != '/') {
                //参数解析
                $pathUri = explode('?', ltrim($REQUEST_URI, '/'));
                $pathPrev = explode('/', $pathUri[0]);

                //解析参数
                self::$direction = \common::get_default_value($pathPrev[1],\config::$default_route['direction']);
                self::$module = \common::get_default_value($pathPrev[2],\config::$default_route['module']);
                self::$version = \common::get_default_value($pathPrev[3],\config::$default_route['version']);
                self::$controller = \common::get_default_value($pathPrev[4],\config::$default_route['controller']);
                self::$method = \common::get_default_value($pathPrev[5],\config::$default_route['method']);
            } else {
                self::$direction = \config::$default_route['direction'];
                self::$module = \config::$default_route['module'];
                self::$version = \config::$default_route['version'];
                self::$controller = \config::$default_route['controller'];
                self::$method = \config::$default_route['method'];
            }
        }

        //定义当前方向、模块、版本、控制器、方法
        define('CURRENT_DIRECTION', self::$direction);
        define('CURRENT_MODULE', self::$module);
        define('CURRENT_VERISON', self::$version);
        define('CURRENT_CONTROLLER', self::$controller);
        define('CURRENT_METHOD', self::$method);

        //返回
        return array(self::$direction, self::$module, self::$version, self::$controller, self::$method);
    }
}