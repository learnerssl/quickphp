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
class Cache {
	const EXT = '.txt';
	private static $_instance;
	private static $_dir;
	
	private function __construct()
	{
		self::$_dir = ROOT . '/cache/';
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
	 * 设置静态缓存文件
	 * @param string $key 文件名
	 * @param string $value 内容
	 * @param int    $expire 过期时间  0:永久缓存
	 * @return array|bool|int
	 */
	public function setCache( $key, $value, $expire = 0 )
	{
		$path = self::$_dir . $key . self::EXT;
		//判断缓存文件夹是否存在，不存在则创建
		if ( ! file_exists( self::$_dir ) ) {
			if ( ! mkdir( self::$_dir, 0777, true ) ) {
				return array( 'error' => - 1, 'etext' => '创建目录失败:' . self::$_dir );
			}
		}
		chmod( $path, 0777 );
		return file_put_contents( $path, json_encode( array(
			'data'   => $value,
			'expire' => $expire
		) ), JSON_UNESCAPED_UNICODE );
	}
	
	/**
	 * 获取静态缓存内容
	 * @param string $key 文件名
	 * @return array|bool|mixed
	 */
	public function getCache( $key )
	{
		$path = self::$_dir . $key . self::EXT;
		if ( ! file_exists( $path ) ) {
			return array( 'error' => - 1, 'etext' => '缓存文件不存在:' . $path );
		}
		$data = json_decode( file_get_contents( $path ), true );
		$expire = end( $data );
		if ( $expire !== 0 && ( $expire + filemtime( $path ) < time() ) ) {
			return unlink( $path );
		}
		return $data;
	}
	
	/**
	 * 删除静态缓存内容
	 * @param string $key 文件名
	 * @return bool
	 */
	public function delCache( $key )
	{
		return unlink( $path = self::$_dir . $key . self::EXT );
	}
}

