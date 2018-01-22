<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/5/25
 * Time: 22:30
 */

/**
 * session类 (session会话就是服务器和浏览器保有共同小秘密的这段时间)
 * 1.准备建立会话的时候，PHP会先查看请求中是否包含，如果没有服务器会在自己的内存里创建一个新的变量，这个变量就是session_id，假如这个变量就是session_1234
 * 2.服务器会把这个session_id发送到浏览器保存,一般浏览器会把这个id保存在cookie中
 * 3.之后每次我的浏览器再去访问服务器的时候，都会携带cookie中存储的这个session_1234，这样服务器就认识这个浏览器了
 * 4.服务器端的session_1234变量就可以存放任意的会话数据，这些数据是经过序列化之后存放进去的
 * 5.每次浏览器访问服务器都可以凭借自己的session_id到服务器中认领自己的信息
 * 6.如果想销毁会话，可以删除掉会话中的数据，也可以销毁会话文件
 */

namespace quickphp\lib;
class Session {
	private static $_instance;
	
	private function __construct()
	{
		session_start();
	}
	
	private function __clone()
	{
		// TODO: Implement __clone() method.
	}
	
	public static function getInstance()
	{
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * 设置seesion
	 * @param string $key
	 * @param mixed  $value
	 * @param bool   $alive 是否设置长久session
	 * @param int    $expire 有效期，单位秒
	 */
	public function setsession( $key, $value, $alive = false, $expire = 0 )
	{
		if ( $alive ) {
			Cookie::getInstance()->setcookie( session_name(), session_id(), array( 'expire' => time() + $expire ) );
		}
		$_SESSION[ $key ] = $value;
	}
	
	/**
	 * 获取session
	 * @param string $key
	 * @return null
	 */
	public function getsession( $key = '' )
	{
		if ( empty( $key ) ) {
			return $_SESSION;
		} else {
			return isset( $_SESSION[ $key ] ) ? $_SESSION[ $key ] : null;
		}
	}
	
	/**
	 * 删除session
	 * @param string $key
	 */
	public function delsession( $key = '' )
	{
		if ( empty( $key ) ) {
			// 如果要清理的更彻底，那么同时删除会话 cookie
			// 注意：这样不但销毁了会话中的数据，还同时销毁了会话本身
			if ( ini_get( "session.use_cookies" ) ) {
				$params = session_get_cookie_params();
				setcookie( session_name(), '', time() - 1, $params["path"], $params["domain"], $params["secure"], $params["httponly"] );
			}
			// 最后，销毁会话
			session_destroy();
		} else {
			unset( $_SESSION[ $key ] );
		}
	}
	
	/**
	 * session 验证
	 * @param string $val
	 * @param string $key
	 * @return bool
	 */
	public function checksession( $val, $key )
	{
		return $val == $this->getsession( $key ) ? true : false;
	}
}