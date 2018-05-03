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
    private static $verison;
    private static $controller;
    private static $method;

    //路由解析
    public static function geRoute($argv)
    {
        //获取crontab定时脚本参数
        if (CRONTAB && is_array($argv) && !empty($argv) && count($argv) <= 6) {
            self::$direction = $argv[1];
            self::$module = $argv[2];
            self::$verison = $argv[3];
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
                self::$direction = isset($pathPrev[1]) ? $pathPrev[1] : \config::$default_route['direction'];
                self::$module = isset($pathPrev[2]) ? $pathPrev[2] : \config::$default_route['module'];
                self::$verison = isset($pathPrev[3]) ? $pathPrev[3] : \config::$default_route['version'];
                self::$controller = isset($pathPrev[4]) ? $pathPrev[4] : \config::$default_route['controller'];
                self::$method = isset($pathPrev[5]) ? $pathPrev[5] : \config::$default_route['method'];
            } else {
                self::$direction = \config::$default_route['direction'];
                self::$module = \config::$default_route['module'];
                self::$verison = \config::$default_route['version'];
                self::$controller = \config::$default_route['controller'];
                self::$method = \config::$default_route['method'];
            }
        }

        //定义当前方向、模块、控制器、方法
        define('CURRENT_DIRECTION', self::$direction);
        define('CURRENT_MODULE', self::$module);
        define('CURRENT_VERISON', self::$verison);
        define('CURRENT_CONTROLLER', self::$controller);
        define('CURRENT_METHOD', self::$method);

        //返回
        return array(self::$direction, self::$module, self::$verison, self::$controller, self::$method);
    }
}