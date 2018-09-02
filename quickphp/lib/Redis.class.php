<?php

/**
 * redis缓存类
 */

namespace quickphp\lib;
class Redis
{
    /**
     * @var object 操作句柄
     */
    protected static $handler;
    /**
     * @var array Redis连接项
     */
    protected static $options;

    private static $redis;

    private function __construct()
    {
        self::$options = \config::$redis;
        //检查一个扩展是否已经加载
        try {
            if (!extension_loaded('redis')) {
                throw new \Exception('不支持: Redis');
            }
        } catch (\Exception $e) {
            Response::api_response($e->getCode(), $e->getMessage());
        }


        if (!empty($options)) {
            //合并数组
            self::$options = array_merge(self::$options, $options);
        }

        $func = self::$options['persistent'] ? 'pconnect' : 'connect';

        //实例化Redis数据库对象
        self::$redis = new \Redis;
        self::$redis->$func(self::$options['host'], self::$options['port'], self::$options['timeout']);

        if ('' != self::$options['password']) {
            self::$redis->auth(self::$options['password']);
        }

        if (0 != self::$options['select']) {
            self::$redis->select(self::$options['select']);
        }
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public static function getInstance()
    {
        if (!self::$handler) {
            self::$handler = new self();
        }
        return self::$handler;
    }

    /**
     * 判断缓存
     * @param string $name 缓存变量名
     * @return bool
     * 示例: $x->has('sss');
     */
    public function has($name)
    {
        return self::$redis->get(self::getCacheKey($name)) ? true : false;
    }

    /**
     * 读取缓存
     * @param string $name 缓存变量名
     * @param mixed $default 默认值
     * @return mixed
     * 示例: 无默认值$x->get('sss'), 带默认值$x->get('sss', 0);
     */
    public function get($name, $default = false)
    {
        $value = self::$redis->get(self::getCacheKey($name));

        if (is_null($value)) {
            return $default;
        }

        $jsonData = json_decode($value, true);

        // 检测是否为JSON数据 true 返回JSON解析数组, false返回源数据
        return (null === $jsonData) ? $value : $jsonData;
    }

    /**
     * 写入缓存
     * @param string $name 缓存变量名
     * @param mixed $value 存储数据
     * @param integer $expire 有效时间（秒）
     * @return boolean
     * 示例: 无有效时间$x->set('sss', 'xxx'), 带有效时间 $x->set('sss', 'xxx', 60);
     */
    public function set($name, $value, $expire = 0)
    {
        if (is_null($expire)) {
            $expire = self::$options['expire'];
        }

        $key = self::getCacheKey($name);
        //对数组/对象数据进行缓存处理，保证数据完整性
        $value = (is_object($value) || is_array($value)) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;

        if (is_int($expire) && $expire) {
            $result = self::$redis->setex($key, $expire, $value);
        } else {
            $result = self::$redis->set($key, $value);
        }

        return $result;
    }

    /**
     * 删除缓存
     * @param string $name 缓存变量名
     * @return int
     * 示例: $x->del('sss');
     */
    public function del($name)
    {
        return self::$redis->delete(self::getCacheKey($name));
    }

    /**
     * 获取实际的缓存标识
     * @param string $name 缓存名
     * @return string
     */
    private static function getCacheKey($name)
    {
        return self::$options['prefix'] . $name;
    }
}