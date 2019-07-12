<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/6/21
 * Time: 14:51
 */

/*
 * Http请求类
 */

namespace quickphp\lib;
class Http {
	private static $_instance;
	
	private function __construct()
	{
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
     * 封装通用curl操作
     * @param string $url 请求的接口地址
     * @param string $type 请求方式(get|post) 默认为get
     * @param string $output 输出格式 (json|xml|array) 默认为json
     * @param mixed  $data post方式传递的参数，默认为空数组
     * @return bool|mixed|\SimpleXMLElement|string|void|null
     * @throws \Exception
     */
	public static function http_curl( $url, $type = 'get', $output = 'array', $data = array() )
	{
		$ret = null;
		$curl = curl_init(); //初始化curl
		curl_setopt( $curl, CURLOPT_URL, $url ); //抓取指定网页
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true ); //要求结果为字符串且输出到屏幕上
		curl_setopt( $curl, CURLOPT_HEADER, false ); //不返回头部信息
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );//跳过证书检查
		curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, false );//从证书中检查ssl加密算法是否存在
		if ( $type == 'post' ) {
			curl_setopt( $curl, CURLOPT_POST, true );//post提交方式
			curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );//post方式提交的数据
		}
		$ret = curl_exec( $curl );
		if ( curl_errno( $curl ) ) {
			return Response::api_response( curl_errno( $curl ), curl_error( $curl ) );
		}
		if ( $output == 'json' ) {
		} elseif ( $output == 'xml' ) {
			$ret = simplexml_load_string( $ret );
		} else {
			$ret = json_decode( $ret, true );
		}
		curl_close( $curl );//关闭一个CURL会话会释放所有资源，CURL句柄$ch也会被释放
		return $ret;
	}
}