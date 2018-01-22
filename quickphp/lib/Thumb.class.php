<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/5/25
 * Time: 23:14
 */

/**
 * 缩略图类
 */

namespace quickphp\lib;
class Thumb {
	private $thumbWidth; //缩略图的宽
	private $thumbHeight; //缩略图的高
	private $thumbPath; //缩略图保存的路径
	
	private $sourcePath; //原图的路径
	private $sourceWidth; //原图的宽度
	private $sourceHeight; //原图的高度
	private $sourceType; //原图的图片类型
	
	/**
	 * 构造函数
	 * @param string  $sourcePath 原图的绝对路径
	 * @param integer $thumbWidth 缩略图的宽
	 * @param integer $thumbHeight 缩略图的高
	 */
	public function __construct( $sourcePath, $thumbWidth = 200, $thumbHeight = 200 )
	{
		// 获取原图的绝对路径
		$this->sourcePath = $sourcePath;
		// 获取缩略图的大小
		$this->thumbWidth = $thumbWidth;
		$this->thumbHeight = $thumbHeight;
		$this->thumbPath = $this->getThumbPath();
		// 计算大图的大小
		list( $this->sourceWidth, $this->sourceHeight, $this->sourceType ) = getimagesize( $this->sourcePath );
	}
	
	/**
	 * 确定缩略图保存的路径
	 * @return string
	 */
	private function getThumbPath()
	{
		$ext = $this->getExt();
		$filename = basename( $this->sourcePath, '.' . $ext ) . '_thumb' . '.' . $ext;
		return $thumbPath = __DIR__ . '/' . $filename;
	}
	
	/**
	 * 获取原图的扩展名
	 * @return string 扩展名
	 */
	private function getExt()
	{
		return pathinfo( $this->sourcePath, PATHINFO_EXTENSION );
	}
	
	/**
	 * 检测原图的扩展名是否合法，并返回相应类型
	 * @return bool/str 原图的类型
	 */
	public function getType()
	{
		$typeArr = array( 1 => 'gif', 2 => 'jpeg', 3 => 'png', 15 => 'wbmp' );
		if ( ! in_array( $this->sourceType, array_keys( $typeArr ) ) ) {
			return false;
		}
		return $typeArr[ $this->sourceType ];
	}
	
	/**
	 * 按照缩略图大小，计算大图的缩放比例
	 * @return float 缩放比例
	 */
	public function calculateRate()
	{
		return min( $this->thumbWidth / $this->sourceWidth, $this->thumbHeight / $this->sourceHeight );
	}
	
	/**
	 * 计算大图按照缩放比例后，最终的图像大小
	 * @param float $rate 缩放比例
	 * @return array 缩放后的图片大小
	 */
	public function getImageSizeRate( $rate )
	{
		$width = $this->sourceWidth * $rate;
		$height = $this->sourceHeight * $rate;
		return array( 'w' => $width, 'h' => $height );
	}
	
	/**
	 * 保存成文件
	 * @param $image
	 */
	public function saveFile( $image )
	{
		$method = "image" . $this->getType();
		$method( $image, $this->thumbPath );
	}
	
	/**
	 * 进行绘画操作
	 */
	public function draw()
	{
		if ( ! ( $type = $this->getType() ) ) {
			echo "文件类型不支持";
			return;
		}
		// 创建大图和小图的画布
		$method = "imagecreatefrom" . $type;
		$bigCanvas = $method( $this->sourcePath );
		$smallCanvas = imagecreatetruecolor( $this->thumbWidth, $this->thumbHeight );
		// 创建白色画笔，并给小图画布填充背景
		$white = imagecolorallocate( $smallCanvas, 255, 255, 255 );
		imagefill( $smallCanvas, 0, 0, $white );
		// 计算大图的缩放比例
		$rate = $this->calculateRate();
		// 计算大图缩放后的大小信息
		$info = $this->getImageSizeRate( $rate );
		// 进行缩放
		imagecopyresampled( $smallCanvas, $bigCanvas, ( $this->thumbWidth - $info['w'] ) / 2, ( $this->thumbHeight - $info['h'] ) / 2, 0, 0, $info['w'], $info['h'], $this->sourceWidth, $this->sourceHeight );
		// 保存成文件
		$this->saveFile( $smallCanvas );
		// 销毁画布
		imagedestroy( $bigCanvas );
		imagedestroy( $smallCanvas );
	}
}
