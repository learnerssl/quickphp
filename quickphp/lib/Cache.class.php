<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/5/23
 * Time: 13:06
 */

/**
 * 生成静态缓存类
 */

namespace quickphp\lib;
class Cache
{
    const EXT = '.txt';
    const DIR = '/cache/';
    private static $_instance;
    private static $_dir;
    private static $_path;

    private function __construct($key)
    {
        self::$_dir = ROOT . self::DIR;
        self::$_path = self::$_dir . $key . self::EXT;
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public static function getInstance($key)
    {
        if (!self::$_instance) {
            self::$_instance = new self($key);
        }
        return self::$_instance;
    }

    /**
     * 设置静态缓存文件
     * @param string $value 内容
     * @param int $expire 过期时间  0:永久缓存
     * @return array|bool|int
     */
    public function setCache($value, $expire = 0)
    {
        //判断缓存文件夹是否存在，不存在则创建
        if (!file_exists(self::$_dir)) {
            if (!mkdir(self::$_dir, 0777, true)) {
                return array('error' => -1, 'etext' => '创建目录失败:' . self::$_dir);
            }
        }
        chmod(self::$_path, 0777);
        return file_put_contents(self::$_path, json_encode(array(
            'data' => $value,
            'expire' => $expire
        )), JSON_UNESCAPED_UNICODE);
    }

    /**
     * 获取静态缓存内容
     * @return array|bool|mixed
     */
    public function getCache()
    {
        if (!file_exists(self::$_path)) {
            return array('error' => -1, 'etext' => '缓存文件不存在:' . self::$_path);
        }
        $data = json_decode(file_get_contents(self::$_path), true);
        $expire = end($data);
        if ($expire !== 0 && ($expire + filemtime(self::$_path) < time())) {
            return unlink(self::$_path);
        }
        return $data;
    }

    /**
     * 删除静态缓存内容
     * @return bool
     */
    public function delCache()
    {
        return unlink(self::$_path);
    }
}

