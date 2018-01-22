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
    private static $model;
    private static $controller;
    private static $method;

    //路由解析
    public static function geRoute($argv)
    {
        //获取crontab定时脚本参数
        if (is_array($argv) && !empty($argv) && count($argv) < 4) {
            self::$model = $argv[1];
            self::$controller = $argv[2];
            self::$method = $argv[3];
        } else {
            //外部参数
            $REQUEST_URI = $_SERVER['REQUEST_URI'];
            if (isset($REQUEST_URI) && $REQUEST_URI != '/') {
                //参数解析
                $pathUri = explode('?', ltrim($REQUEST_URI, '/'));
                $pathPrev = explode('/', $pathUri[0]);

                //解析参数
                self::$model = isset($pathPrev[1]) ? $pathPrev[1] : \config::$default_route['model'];
                self::$controller = isset($pathPrev[2]) ? $pathPrev[2] : \config::$default_route['controller'];
                self::$method = isset($pathPrev[3]) ? $pathPrev[3] : \config::$default_route['method'];
            } else {
                self::$model = \config::$default_route['model'];
                self::$controller = \config::$default_route['controller'];
                self::$method = \config::$default_route['method'];
            }
        }

        //定义当前模块、控制器、方法
        define('CURRENT_MODULE', self::$model);
        define('CURRENT_CONTROLLER', self::$controller);
        define('CURRENT_METHOD', self::$method);

        //返回
        return array(self::$model, self::$controller, self::$method);
    }
}