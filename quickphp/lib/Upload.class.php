<?php

/**
 * 文件上传类
 */

namespace quickphp\lib;
class Upload
{
    private static $_instance;
    protected $fileName;
    protected $maxSize;  //限制文件上传大小（字节）
    protected $allowMime = array('image/jpeg', 'image/png', 'image/gif', 'image/jpg');//允许的mime类型
    protected $allowExt; //允许的文件后缀名
    protected $uploadPath;//上传路径
    protected $imgFlag; //是否进行真实图片检验
    protected $fileInfo;//文件信息
    protected $error;
    protected $ext;
    protected $uniName;
    protected $destination;

    /**
     * @param string $fileName
     * @param string $uploadPath
     * @param bool $imgFlag
     * @param int $maxSize
     * @param array $allowExt
     */
    private function __construct($fileName, $uploadPath, $imgFlag, $maxSize, $allowExt)
    {
        $this->fileName = $fileName;
        $this->maxSize = $maxSize;
//        $this->allowMime = $allowMime;
        $this->allowExt = $allowExt;
        $this->uploadPath = $uploadPath;
        $this->imgFlag = $imgFlag;
        $this->fileInfo = $_FILES[$this->fileName];
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public static function getInstance($fileName = 'img', $uploadPath = '/upload', $imgFlag = true, $maxSize = 5242880, $allowExt = array('jpeg', 'jpg', 'png', 'gif'))
    {
        if (!self::$_instance) {
            self::$_instance = new self($fileName, $uploadPath, $imgFlag, $maxSize, $allowExt);
        }
        return self::$_instance;
    }

    /**
     * 检测上传文件是否出错
     * @return boolean
     */
    protected function checkError()
    {
        if (!is_null($this->fileInfo)) {
            if ($this->fileInfo['error'] > 0) {
                switch ($this->fileInfo['error']) {
                    case 1:
                        $this->error = '超过了PHP配置文件中upload_max_filesize选项的值';
                        break;
                    case 2:
                        $this->error = '超过了表单中MAX_FILE_SIZE设置的值';
                        break;
                    case 3:
                        $this->error = '文件部分被上传';
                        break;
                    case 4:
                        $this->error = '没有选择上传文件';
                        break;
                    case 6:
                        $this->error = '没有找到临时目录';
                        break;
                    case 7:
                        $this->error = '文件不可写';
                        break;
                    case 8:
                        $this->error = '由于PHP的扩展程序中断文件上传';
                        break;

                }
                return false;
            } else {
                return true;
            }
        } else {
            $this->error = '未获取有效参数';
            return false;
        }
    }

    /**
     * 检测上传文件的大小
     * @return boolean
     */
    protected function checkSize()
    {
        if ($this->fileInfo['size'] > $this->maxSize) {
            $this->error = '上传文件过大';
            return false;
        }
        return true;
    }

    /**
     * 检测扩展名
     * @return boolean
     */
    protected function checkExt()
    {
        $this->ext = strtolower(pathinfo($this->fileInfo['name'], PATHINFO_EXTENSION));
        if (!in_array($this->ext, $this->allowExt)) {
            $this->error = '不允许的后缀名';
            return false;
        }
        return true;
    }

    /**
     * 检测文件的类型
     * @return boolean
     */
    //	protected function checkMime()
    //	{
    //		if ( ! in_array( $this->fileInfo['type'], $this->allowMime ) ) {
    //			$this->error = '不允许的文件类型';
    //			return false;
    //		}
    //		return true;
    //	}

    /**
     * 检测是否是真实图片
     * @return boolean
     */
    protected function checkTrueImg()
    {
        if ($this->imgFlag) {
            if (!@getimagesize($this->fileInfo['tmp_name'])) {
                $this->error = '不是真实图片';
                return false;
            }
        }
        return true;
    }

    /**
     * 检测是否通过HTTP POST方式上传上来的
     * @return boolean
     */
    protected function checkHTTPPost()
    {
        if (!is_uploaded_file($this->fileInfo['tmp_name'])) {
            $this->error = '文件不是通过HTTP POST方式上传上来的';
            return false;
        }
        return true;
    }


    /**
     * 检测目录不存在则创建
     */
    protected function checkUploadPath()
    {
        if (!file_exists(ROOT . $this->uploadPath)) {
            $ret = mkdir(ROOT . $this->uploadPath, 0777, true);
            if ($ret) {
                return true;
            } else {
                $this->error = '创建目录失败';
                return false;
            }
        }
        return true;
    }

    /**
     * 产生唯一字符串
     * @return string
     */
    protected function getUniName()
    {
        return md5(uniqid(microtime(true), true));
    }


    /**
     * 设置默认参数
     * @param string $key 参数名
     * @param mixed $val 参数值
     */
    public function set($key, $val)
    {
        $key = strtolower($key);
        if (array_key_exists($key, get_class_vars(get_class($this)))) {
            $this->$key = $val;
        }
    }

    /**
     * 上传文件
     * @return array|string
     */
    public function uploadFile()
    {
        if ($this->checkError() && $this->checkSize() && $this->checkExt() && $this->checkTrueImg() && $this->checkHTTPPost() && $this->checkUploadPath()) {
            $this->uniName = $this->getUniName();
            $src = $this->uploadPath . '/' . $this->uniName . '.' . $this->ext;
            $this->destination = ROOT . $src;
            if (move_uploaded_file($this->fileInfo['tmp_name'], $this->destination)) {
                return $src;
            } else {
                $this->error = '文件移动失败';
                return array('error' => -1, 'etext' => $this->error);
            }
        } else {
            return array('error' => -1, 'etext' => $this->error);
        }
    }
}



