<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/5/17
 * Time: 11:03
 */

/**
 * 百度地图类
 */

namespace quickphp\lib;
class Map {
	private static $ak;
	private static $_instance;
	
	private function __construct()
	{
		self::$ak = \config::$map_ak;
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
	 * 获取当前ip经纬度
	 * @param string $type 坐标类型(bd09ll|bd09mc)，默认为bd09ll
	 * @param string $output 输出类型(xml|json) 默认为json
	 * @return mixed|\SimpleXMLElement
	 */
	public function getAddress( $output = 'json', $type = 'bd09ll' )
	{
		$url = 'https://api.map.baidu.com/location/ip?ak=' . self::$ak . '&coor=' . $type . '&ip=' . \common::get_ip();
		$result = Http::http_curl( $url );
		return $this->output( $result, $output );
	}
	
	/**
	 * 逆地理编码服务
	 * @param string $position 根据经纬度坐标获取地址
	 * @param string $output 输出格式 默认为json
	 * @param int    $pois 是否显示指定位置周边的poi，0为不显示，1为显示。当值为1时，默认显示周边1000米内的poi。
	 * @param int    $radius poi召回半径，允许设置区间为0-1000米，超过1000米按1000米召回。
	 * @param string $coordtype 坐标的类型，目前支持的坐标类型包括：bd09ll（百度经纬度坐标）、bd09mc（百度米制坐标）、gcj02ll（国测局经纬度坐标）、wgs84ll（ GPS经纬度）
	 * @return mixed|\SimpleXMLElement
	 */
	public function getPosition( $position = '', $output = 'json', $pois = 0, $radius = 1000, $coordtype = 'bd09ll' )
	{
		if ( empty( $position ) ) {
			$res = $this->getAddress();
			$y = $res['content']['point']['y'];
			$x = $res['content']['point']['x'];
			$position = $y . ',' . $x;
		}
		$url = 'http://api.map.baidu.com/geocoder/v2/?callback=renderReverse&coordtype=' . $coordtype . '&radius=' . $radius . '&location=' . $position . '&output=' . $output . '&pois=' . $pois . '&ak=' . self::$ak;
		$result = Http::http_curl( $url );
		return $this->output( $result, $output );
	}
	
	/**
	 * 地址解析
	 * @param string $address 详细地址
	 * @param string $city 地址所在城市
	 * @param string $output 输出格式
	 * @return mixed|\SimpleXMLElement
	 */
	public function getLngLat( $address, $city, $output = 'json' )
	{
		$url = "http://api.map.baidu.com/geocoder/v2/?address=" . $address . "&city=" . $city . "&output=" . $output . "&ak=" . self::$ak;
		$result = Http::http_curl( $url );
		return $this->output( $result, $output );
	}
	
	/**
	 * 输出结果
	 * @param string $result
	 * @param string $output
	 * @return mixed|\SimpleXMLElement
	 */
	public function output( $result, $output = 'json' )
	{
		if ( $output == 'xml' ) {
			return simplexml_load_string( $result );
		}
		return json_decode( $result, true );
	}
}