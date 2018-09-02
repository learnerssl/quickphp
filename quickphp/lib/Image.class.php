<?php

/**
 * 图片处理类
 */

namespace quickphp\lib;
class Image
{
    public $info;
    private $image;
    private static $_instance;

    //构造函数，打开文档
    public function __construct($src)
    {
        $info = getimagesize($src);
        $this->info = array(
            'width' => $info[0],
            'height' => $info[1],
            'type' => image_type_to_extension($info[2], false),
            'mime' => $info['mime']
        );
        $fun = "imagecreatefrom{$this->info['type']}";
        $this->image = $fun($src);
    }

    public static function getInstance($src)
    {
        if (!self::$_instance) {
            self::$_instance = new self($src);
        }
        return self::$_instance;
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    //压缩图片
    public function thumb($width, $height)
    {
        $image_thumb = imagecreatetruecolor($width, $height);
        imagecopyresampled($image_thumb, $this->image, 0, 0, 0, 0, $width, $height, $this->info['width'], $this->info['height']);
        imagedestroy($this->image);
        $this->image = $image_thumb;
    }

    //显示图片
    public function show()
    {
        header("Content-type:" . $this->info['mime']);
        $funs = "image{$this->info['type']}";
        $funs($this->image);
    }

    //保存图片
    public function save($path)
    {
        $funs = "image{$this->info['type']}";
        $funs($this->image, $path . "." . $this->info['type']);
    }

    //文字水印
    public function fontMark($content, $url, $size, $color, $local, $angle)
    {
        $color = imagecolorallocatealpha($this->image, $color[0], $color[1], $color[2], $color[3]);
        imagettftext($this->image, $size, $angle, $local['x'], $local['y'], $color, $url, $content);
    }

    //图片水印
    public function imageMark($source, $local, $alpha)
    {
        $info2 = getimagesize($source);
        $type2 = image_type_to_extension($info2[2], false);
        $func2 = "imagecreatefrom{$type2}";
        $water = $func2($source);
        imagecopymerge($this->image, $water, $local['x'], $local['y'], 0, 0, $info2[0], $info2[1], $alpha);
        imagedestroy($water);
    }

    //销毁图片
    public function __destruct()
    {
        imagedestroy($this->image);
    }

}
