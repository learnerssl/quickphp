<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/5/25
 * Time: 23:22
 */

/*
 * cookie类
 * 1.cookie不要存储敏感数据，cookie不是很安全，劫取cookie之后可以用来cookie欺骗
 * 2.不要把cookie当作客户端存储器来使用，首先每个域名允许cookie是有限的，根据不同的浏览器这个限制也不同。cookie中保存数据的最大字节数是4k
 * 3.cookie设置之后每次都会附着在http头中一起发送,浪费带宽
 */

namespace quickphp\lib;
class Cookie
{
    private static $_instance;
    private $urlencode = true;
    private $expire = 0;
    private $path = '/';
    private $domain = '';
    private $secure = false;
    private $httponly = true;

    private function __construct()
    {
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * 设置参数
     * @param array $options
     * @desc int    $expire 设置Cookie的过期时间,默认值为0,单位是秒,如果未设置过期时间，则为内存Cookie,浏览器关闭则数据消失
     * @desc string $path 设置Cookie的有效路径，默认是当前目录及其子目录有效，也可以指定成整个根目录/，在整个根目录有效
     * @desc string $domain 设置Cookie的作用域,默认在本域下,如果想域名的二级域名,三级域名同样使用该Cookie，则设置为.+域名的形式, 如:'.ssl.woaicc.com'
     * @desc bool   $secure 设置Cookie是否只能通过Https传输,默认值是false
     * @desc bool   $httponly 设置是否只使用http访问Cookie，默认值是false,如果设置成true,那么客户端的js就无法操作这个Cookie，使用这个参数可以减少XSS攻击
     * @desc bool   $urlencode 设置是否进行url编码
     * @return $this
     */
    public function setOption($options = array())
    {
        if (isset($options['expire'])) {
            $this->expire = $options['expire'] === 0 ? 0 : (int)$options['expire'] + time();
        }
        if (isset($options['path'])) {
            $this->path = (string)$options['path'];
        }
        if (isset($options['domain'])) {
            $this->domain = (string)$options['domain'];
        }
        if (isset($options['secure'])) {
            $this->secure = (bool)$options['secure'];
        }
        if (isset($options['httponly'])) {
            $this->httponly = (bool)$options['httponly'];
        }
        if (isset($options['urlencode'])) {
            $this->urlencode = (bool)$options['urlencode'];
        }
        return $this;
    }

    /**
     * 单例模式实例化cookie类
     * @return Cookie
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 设置/更新指定cookie  更新时候需要保证$path,$domain和之前的保持一致，否则它会认为是两个cookie,从而无法更新
     * @param string $name
     * @param string $value
     * @param array $options
     */
    public function setcookie($name, $value = '', $options = array())
    {
        if (is_array($options) && count($options) > 0) {
            $this->setOption($options);
        }
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value, JSON_FORCE_OBJECT);
        }
        $fnc = $this->urlencode ? 'setcookie' : 'setrawcookie';
        $fnc($name, $value, $this->expire, $this->path, $this->domain, $this->secure, $this->httponly);
    }

    /**
     * 获取指定cookie
     * @param string $key
     * @param array $options
     * @return mixed|null
     */
    public function getcookie($key, $options = array())
    {
        if (is_array($options) && count($options) > 0) {
            $this->setOption($options);
        }
        if (isset($_COOKIE[$key])) {
            return substr($_COOKIE[$key], 0, 1) == "{" ? json_decode($_COOKIE[$key], true) : $_COOKIE[$key];
        }
        return null;
    }

    /**
     * 删除指定cookie,删除的时候需要保证$path,$domain和之前的保持一致，否则它会认为是两个cookie,从而无法删除
     * @param string $key
     * @param array $options
     */
    public function delcookie($key, $options = array())
    {
        if (is_array($options) && count($options) > 0) {
            $this->setOption($options);
        }
        setcookie($key, '', time() - 1, $this->path, $this->domain, $this->secure, $this->httponly);
    }

    /**
     * 删除指定cookie
     * @param array $array 默认为空，如果为空，则删除所有cookie
     * @param array $options
     */
    public function del_all_cookie($array = array(), $options = array())
    {
        $data = empty($array) ? array_keys($_COOKIE) : $array;
        foreach ($data as $key => $item) {
            $this->delcookie($item, $options);
        }
    }
}