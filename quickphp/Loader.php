<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/4/29
 * Time: 23:31
 * Des: 框架运行核心文件
 */

namespace quickphp;

use quickphp\lib\Response;

class Loader
{
    /**
     * 启动框架
     * @param array $argv 递给脚本的参数数组,包含当运行于命令行下时传递给当前脚本的参数的数组。
     * @param bool $swoole 是否启用swoole服务
     * @return bool
     * @throws \Exception
     */
    public static function Run(array $argv, bool $swoole = false)
    {
        //定义当前运行环境
        define('ENV', $swoole === true ? 'swoole' : 'php');

        list($direction, $module, $version, $controller, $method) = Route::geRoute($argv);

        try {
            $controller_class = '\application\\' . $direction . '\\' . $module . '\\' . $version . '\controller\\' . ucfirst($controller) . 'Controller';

            //判断api地址是否正确
            if (!method_exists($controller_class, $method) || !class_exists($controller_class)) {
                Response::api_response(ERR_PATH);
            }
            $init = new $controller_class();
            return $init->$method();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        return true;
    }

    /**
     * 自动加载类
     * @des 正常实例化类：new quickphp\Route();
     *      那么由于未引入文件，将会自动加载类，其中$class：quickphp\Route()；
     *      替换成为文件路径 ROOT.'/quickphp/Route.php;
     * @param string $class
     * @return bool|mixed
     */
    public static function autoload(string $class)
    {
        //判断是否是类文件;如果是类文件,文件后缀添加.class.
        if (preg_match('/\\\lib\\\/', $class)) {
            return \common::common_include_file(ROOT . '/' . str_replace('\\', '/', $class) . '.class.php');
        } else {
            return \common::common_include_file(ROOT . '/' . str_replace('\\', '/', $class) . '.php');
        }
    }
}