<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/5/22
 * Time: 20:16
 */

/**
 * 通用接口通信类
 */

namespace quickphp\lib;
class Response {
	const FORMAT = 'json';
	
	/**
	 * @desc 按综合方式输出通信数据
	 * @param  int    $error 状态码  1:请求成功,其余都为请求失败,默认为请求成功
	 * @param  string $etext 提示信息
	 * @param  mixed  $data 数据
	 * @param  string $format 输出格式
	 * @return bool
	 */
	public static function api_response( $error = 1, $etext = "", $data = array(), $format = self::FORMAT )
	{
		if ( ! is_numeric( $error ) ) {
			$error = - 1;
			$etext = "无效的状态码";
		}
		$format = isset( $_GET['format'] ) ? $_GET['format'] : $format;
		if ( $format === 'json' ) {
			self::jsonEncode( $error, $etext, $data );
		} elseif ( $format === 'xml' ) {
			self::xmlEncode( $error, $etext, $data );
		}
		return true;
	}
	
	/**
	 * @desc 按json方式输出通信数据
	 * @param  int    $status 状态码  1:请求成功 0:请求失败
	 * @param  string $msg 提示信息
	 * @param  array  $data 数据
	 * @return string
	 */
	private static function jsonEncode( $status, $msg, $data )
	{
		$json_data = array( 'error' => $status, "etext" => $msg, "data" => $data );
		exit( json_encode( $json_data, JSON_UNESCAPED_UNICODE ) );
	}
	
	/**
	 * @desc 按xml方式输出通信数据
	 * @param  int    $status 状态码  1:请求成功 0:请求失败
	 * @param  string $msg 提示信息
	 * @param  array  $data 数据
	 * @return string
	 */
	private static function xmlEncode( $status, $msg, $data )
	{
		$xml_data = array( 'error' => $status, "etext" => $msg, "data" => $data );
		header( "Content-Type:text/xml" );
		$xml = "<?xml version='1.0' encoding='UTF-8'?>";
		$xml .= "<root>";
		$xml .= self::xmlToEncode( $xml_data );
		$xml .= "</root>";
		return $xml;
	}
	
	/**
	 * 将数据转化为xml格式
	 * @param $data
	 * @return string
	 */
	private static function xmlToEncode( $data )
	{
		$xml = $attr = "";
		foreach ( $data as $key => $value ) {
			//xml格式节点不能为数字
			if ( is_numeric( $key ) ) {
				$attr = " id='{$key}'";
				$key = "item";
			}
			$xml .= "<{$key}{$attr}>";
			$xml .= is_array( $value ) ? self::xmlToEncode( $value ) : $value;
			$xml .= "</{$key}>";
		}
		return $xml;
	}
}