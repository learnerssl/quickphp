<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/6/21
 * Time: 11:39
 */

//------------------------
// QuickPHP 基类控制器
//-------------------------
namespace application;

use quickphp\lib\Response;

class Controller
{
    private $_tmp = 'view';
    protected $_twig  = false;
    private $assign = array();

    public function __construct()
    {
        //防止浏览器中的反射性xss
        \common::header('X-XSS-Protection', '1');
    }

    /**
     * 检查并输出错误信息
     * @param $info
     * @throws \Exception
     */
    public function check_error($info)
    {
        if (self::is_error($info)) {
            Response::api_response($info['error'], $info['etext']);
        }
    }

    /**
     * 检查是否存在错误
     * @param mixed $info
     * @return bool
     */
    public static function is_error($info)
    {
        if (is_array($info) && !empty($info['etext'])) {
            return true;
        }
        return false;
    }

    /**
     * @desc 渲染模版操作
     * @param String $file 模版名
     * @param array $array 赋值参数，默认为array()
     * @return bool
     */
    public function display($file, $array = array())
    {
        $path = explode(':', $file);
        $file = implode('/', [APPLICATION, CURRENT_DIRECTION, $path[0] . $this->_tmp . $path[1]]);

        try {
            //处理模版参数
            foreach ($array as $key => $val) {
                $this->assign($key, $val);
            }

            if (is_file($file)) {
                if ($this->_twig) {
                    $loader = new \Twig_Loader_Filesystem(implode('/', [APPLICATION, CURRENT_DIRECTION, $path[0] . $this->_tmp]));
                    $twig = new \Twig_Environment($loader, array(
                        'cache' => ROOT . '/tmp',
                        'debug' => DEBUG
                    ));
                    $template = $twig->load($path[1]);
                    echo $template->render(\common::get_default_value($this->assign,null));
                } else {
                    extract($this->assign);
                    include_once $file;
                }
            } else {
                throw  new \Exception("模版不存在:" . $file);
            }
        } catch (\Exception $exception) {
            $error = array(
                '错误码' => $exception->getCode(),
                '错误信息' => $exception->getMessage(),
                '错误地址' => $exception->getFile() . ' ' . $exception->getLine() . '行',
            );
            return \common::output($error);
        }
        return true;
    }

    /**
     * @desc 模版赋值操作
     * @param string $key 变量名
     * @param string || array $value 变量值
     */
    protected function assign($key, $value)
    {
        $this->assign[$key] = $value;
    }
}