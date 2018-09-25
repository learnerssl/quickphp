<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/6/4
 * Time: 22:21
 */
//------------------------
// QuickPHP 基类模型类
//-------------------------
namespace model;

use quickphp\Database;

class Model extends Database
{
    public function __construct($table, $key)
    {
        parent::__construct($table, $key);
    }

    /**s
     * 提取数组的值(map数组)
     * @param array $array 数组
     * @param string $key 键
     * @param null $dft 默认值
     * @return null
     */
    public static function get($array, $key, $dft = null)
    {
        return \common::get_array_value_by_key($array, $key, $dft);
    }
}