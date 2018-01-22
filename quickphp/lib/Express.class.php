<?php
/**
 * 快递鸟即时查询API
 */

namespace quickphp\lib;
class Express {
	public $EBusinessID;
	public $AppKey;
	public $ReqURL;
	public $company;//快递公司编码
	public $order; //运单号
	private static $_instance;
	
	private function __construct( $company, $order ) {
		$express = \config::$express;
		$this->EBusinessID = $express['EBusinessID'];
		$this->AppKey = $express['AppKey'];
		$this->ReqURL = $express['ReqURL'];
		$this->company = $company;
		$this->order = $order;
	}
	
	
	private function __clone() {
		// TODO: Implement __clone() method.
	}
	
	public static function getInstance( $company, $order ) {
		if ( ! self::$_instance ) {
			self::$_instance = new self( $company, $order );
		}
		return self::$_instance;
	}
	
	/**
	 *  即时查询订单物流信息
	 */
	public function getOrderTracesByJson() {
		$requestData = "{'OrderCode':'','ShipperCode':'" . $this->company . "','LogisticCode':'" . $this->order . "'}";
		$datas = array( 'EBusinessID' => $this->EBusinessID, 'RequestType' => '1002',
		                'RequestData' => urlencode( $requestData ), 'DataType' => '2', );
		$datas['DataSign'] = $this->encrypt( $requestData, $this->AppKey );
		$result = $this->sendPost( $this->ReqURL, $datas );
		
		//根据公司业务处理返回的信息......
		
		return $result;
	}
	
	/**
	 *  post提交数据
	 * @param  string $url 请求Url
	 * @param  array  $datas 提交的数据
	 * @return string url响应返回的html
	 */
	private function sendPost( $url, $datas ) {
		$temps = array();
		foreach ( $datas as $key => $value ) {
			$temps[] = sprintf( '%s=%s', $key, $value );
		}
		$post_data = implode( '&', $temps );
		$url_info = parse_url( $url );
		if ( empty( $url_info['port'] ) ) {
			$url_info['port'] = 80;
		}
		$httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
		$httpheader .= "Host:" . $url_info['host'] . "\r\n";
		$httpheader .= "Content-Type:application/x-www-form-urlencoded\r\n";
		$httpheader .= "Content-Length:" . strlen( $post_data ) . "\r\n";
		$httpheader .= "Connection:close\r\n\r\n";
		$httpheader .= $post_data;
		$fd = fsockopen( $url_info['host'], $url_info['port'] );
		fwrite( $fd, $httpheader );
		$gets = "";
		while ( ! feof( $fd ) ) {
			if ( ( $header = @fgets( $fd ) ) && ( $header == "\r\n" || $header == "\n" ) ) {
				break;
			}
		}
		while ( ! feof( $fd ) ) {
			$gets .= fread( $fd, 128 );
		}
		fclose( $fd );
		
		return $gets;
	}
	
	/**
	 * 电商Sign签名生成
	 * @param string $data 内容
	 * @param string $appkey Appkey
	 * @return string DataSign签名
	 */
	private function encrypt( $data, $appkey ) {
		return urlencode( base64_encode( md5( $data . $appkey ) ) );
	}
}
