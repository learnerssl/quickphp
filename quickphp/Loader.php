<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/4/29
 * Time: 23:31
 * Des: 框架运行核心文件
 */

namespace quickphp;

class Loader
{
    /**
     * 启动框架
     * @param array $argv 传递给脚本的参数数组,包含当运行于命令行下时传递给当前脚本的参数的数组。
     * @return bool
     */
    public static function Run($argv)
    {
        list($model, $controller, $method) = Route::geRoute($argv);
        $model_dir = APPLICATION . '/' . $model;
        $controller_file = APPLICATION . '/' . $model . '/controller/' . $controller . '.php';
        $controller_Class = '\application\\' . $model . '\controller\\' . $controller;
        try {
            //模块检查
            if (!is_dir($model_dir)) {
                throw  new \Exception($model_dir . '模块不存在');
            }
            if (!file_exists($controller_file)) {
                throw  new \Exception($controller_file . '文件不存在');
            }
            $ins = new $controller_Class();
            if (!method_exists($ins, $method)) {
                throw  new \Exception($controller_Class . '\\' . $method . '方法不存在');
            }

            return $ins->$method();
        } catch (\Exception $exception) {
            $error = array(
                '错误码' => $exception->getCode(),
                '错误信息' => $exception->getMessage(),
                '错误地址' => $exception->getFile() . ' ' . $exception->getLine() . '行',
            );
            return \common::output($error);
        }
    }


    /**
     * 自动加载类
     * @param        $class string 待加载类命名空间路径
     * @des 正常实例化类：new quickphp\Route();
     *      那么由于未引入文件，将会自动加载类，其中$class：quickphp\Route()；
     *      替换成为文件路径 ROOT.'/quickphp/Route.php;
     */
    public static function autoload($class)
    {
        //判断是否是类文件;如果是类文件,文件后缀添加.class.
        if (preg_match('/\\\lib\\\/', $class)) {
            require_once ROOT . '/' . str_replace('\\', '/', $class) . '.class.php';
        } else {
            require_once ROOT . '/' . str_replace('\\', '/', $class) . '.php';
        }
    }
}