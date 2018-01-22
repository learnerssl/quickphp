<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/4/29
 * Time: 23:30
 */

//------------------------
// QuickPHP 项目自定义函数
//-------------------------
class common
{
    /**
     * 打印信息
     * @param mixed $var
     * @return bool
     */
    public static function output($var)
    {
        if (gettype($var) == 'string') {
            echo $var;
        } elseif (gettype($var) == 'boolean') {
            var_dump($var);
        } else {
            echo "<pre style='position: relative;z-index: 1000;padding: 10px;border-radius: 5px;background: #F5F5F5;border: 1px solid #aaa;font-size: 14px;line-height: 18px;opacity: 0.9'>" . print_r($var, true) . "</pre>";
        }
        echo "<br/>";
        return true;
    }

    /**
     * 输出错误信息
     * @param int || string $error 错误代码|错误描述
     * @return array
     */
    public static function output_error($error)
    {
        //检查错误
        list($ecode, $etext) = self::get_text_by_error($error);
        if ($ecode === -1 && $error !== null) {
            $ecode = -1;
            $etext = $error;
        }

        //封装数据
        $data = array('error' => $ecode, 'etext' => $etext, 'data' => array());

        //输出
        return $data;
    }

    /**
     * 根据错误码获取错误信息
     * @param $error
     * @return array
     */
    public static function get_text_by_error($error)
    {
        //想要的值
        $ecode = -1;
        $etext = null;

        //参数处理
        $etext = self::get_array_value(\data::$error_codes, $error);
        if (!empty($etext)) {
            $ecode = $error;
        }

        //返回
        return array($ecode, $etext);
    }

    /**
     * 手动抛出错日信息
     * @param string $message 错误信息
     * @param string $line 错误文件
     * @param int $file 错误行号
     * @return bool
     */
    public static function log($message, $line, $file)
    {
        $PHP_VERSION = PHP_VERSION;
        $log = <<<EOF
			发生错误:{$message}
	        错误文件：{$file}
	        错误行:{$line}
	        PHP版本：{$PHP_VERSION}
EOF;
        return error_log($log);
    }

    /**
     * 获取处理后的数字
     * 如:
     * 3 => 3,
     * 3.00 => 3,
     * 3.10 => 3.10
     * @param $number
     * @return int
     */
    public static function get_number($number)
    {
        if (is_int($number)) {
            $rtext = $number;
        } else {
            $numberArr = explode('.', $number);
            $rtext = end($numberArr) > 1 ? $number : $numberArr[0];
        }
        return $rtext;
    }

    /**
     * 过滤防范
     * @param string $str 需要过滤的文本
     * @return string
     */
    public static function html_filter($str)
    {
        $search = array(
            '/<script[\s\S]*?<\/script>/i',
            '/<iframe[^>]*?>.*?<\/iframe>/',
            '/<style[\s\S]*?<\/style>/i',
            /*            '/<img.*?>/i',*/
            '/<a[\s\S]*?<\/a>/i'
        );
        return preg_replace($search, '', $str);
    }

    /**
     * HTML编码
     * @param string $html
     * @param int $ops 1 清除换行，2 HTML编码
     * @return string
     */
    public static function html_encode($html, $ops)
    {
        $html = htmlspecialchars($html);
        $html = str_replace("\r", '', $html);

        if ($ops === 1) {
            //过滤换行符
            $html = str_replace("\n", ' ', $html);
        } elseif ($ops === 2) {
            //换行符变成<br/>
            $html = str_replace("\n", '<br/>', $html);
        }

        //返回
        return $html;
    }

    /**
     * xss防御
     * @param string $html
     * @param int $opt 1 清除换行，2 HTML编码
     * @return string
     */
    public static function html_xss($html, $opt = 1)
    {
        $html = self::html_filter($html);
        $html = self::html_encode($html, $opt);
        return $html;
    }

    /**
     * 添加引号
     * @param string $string 需要分割的字符串 如:1,2,3 => '1','2','3'
     * @param string $delimiter 分割字符串
     * @return string  处理后的字符串
     */
    public function add_quotes($string, $delimiter = ',')
    {
        $str = explode($delimiter, $string);
        $string = array();
        foreach ($str as $key => $val) {
            $string[$key] = "'" . $val . "'";
        }
        $data = implode(',', $string);
        return $data;
    }


    /**
     * 返回二维数组中某个单一列的值。
     * 如:
     * $a = array(
     * array(
     * 'id' => 5698,
     * 'first_name' => 'Bill',
     * 'last_name' => 'Gates',
     * ),
     * array(
     * 'id' => 4767,
     * 'first_name' => 'Steve',
     * 'last_name' => 'Jobs',
     * ),
     * array(
     * 'id' => 3809,
     * 'first_name' => 'Mark',
     * 'last_name' => 'Zuckerberg',
     * )
     * );
     * $last_names = array_column($a, 'last_name');
     * print_r($last_names);
     * Array
     * (
     * [0] => Gates
     * [1] => Jobs
     * [2] => Zuckerberg
     * )
     * @param array $array
     * @param string $param key
     * @return array
     */
    public static function get_array_column($array, $param)
    {
        return array_column($array, $param);
    }

    /**
     * 一维数组转化为二维数组
     * @param array $array 一维数组
     * @return array 二维数组
     */
    public static function get_array_by_array($array)
    {
        $Array = array();
        foreach ($array as $key => $val) {
            $Array[0][$key] = $val;
        }
        return $Array;
    }

    /**
     * 页面重定向
     * @param string $url 跳转页面
     */
    public static function redirect($url)
    {
        header('Location:' . $url);
        exit;
    }

    /**
     * 添加header头信息
     * @param $key
     * @param $val
     */
    public static function header($key, $val)
    {
        header($key . ':' . $val);
    }

    /**
     * 获取真实ip地址
     */
    public static function get_ip()
    {
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } elseif (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } elseif (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) {
            $ip = getenv("REMOTE_ADDR");
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = "unknown";
        }
        return $ip;
    }

    /**
     * 获取浏览器ua
     * @return string
     */
    public static function get_browser()
    {
        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            return 'robot！';
        }
        if ((false == strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) && (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false)) {
            return 'Internet Explorer 11.0';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 10.0')) {
            return 'Internet Explorer 10.0';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 9.0')) {
            return 'Internet Explorer 9.0';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 8.0')) {
            return 'Internet Explorer 8.0';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 7.0')) {
            return 'Internet Explorer 7.0';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0')) {
            return 'Internet Explorer 6.0';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Edge')) {
            return 'Edge';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox')) {
            return 'Firefox';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome')) {
            return 'Chrome';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Safari')) {
            return 'Safari';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Opera')) {
            return 'Opera';
        }
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], '360SE')) {
            return '360SE';
        }
        //微信浏览器
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            return 'MicroMessenger';
        }
        return 'unknown';
    }

    /**
     * 获取当前微秒数(字符串类型)
     * @return string
     */
    public static function get_milli_time()
    {
        list($t1, $t2) = explode(' ', microtime());
        $mt = sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
        return $mt;
    }

    /**
     * 获取数组中某个键名的所有值
     * @param      $array
     * @param      $key
     * @param null $dft
     * @return null
     */
    public static function get_array_values_by_key($array, $key, $dft = null)
    {
        return (isset($array[$key]) && !empty($array[$key])) ? $array[$key] : $dft;
    }

    /**
     * 获取硬编码text
     * @param array $array
     * @param int $id
     * @param string $key 硬编码字典id
     * @param string $val 硬编码字典name
     * @return string
     */
    public static function get_array_value($array, $id, $key = 'id', $val = 'text')
    {
        foreach ($array as $value) {
            if ($value[$key] == $id) {
                return isset($value[$val]) ? $value[$val] : $value['text'];
            }
        }
        return null;
    }

    /**
     * 格式化时间
     * @param string $time 时间戳
     * @param string $format 格式化时间格式
     * @return false|string
     */
    public static function format_date($time = 'default', $format = 'Y-m-d H:i:s')
    {
        return $time == 'default' ? date($format, time()) : date($format, $time);
    }

    /**
     * 数组转化为拼接后的字符串 如：array(1,2,3) => '1','2','3'
     * @param array $data 数组
     * @param string $delimiter 连接字符串
     * @return bool|string
     */
    public static function array_to_str($data, $delimiter = ',')
    {
        $ids = '';
        foreach ($data as $key => $val) {
            $ids .= "'" . $val . "'" . "$delimiter";
        }
        $ids = substr($ids, 0, strlen($ids) - 1);
        return $ids;
    }

    /**
     * 把array转化为json字符串格式
     * @param $json
     * @return string
     */
    public static function array_to_json($json)
    {
        if (empty($json)) {
            return '{}';
        }
        return json_encode($json, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 把json字符串格式转化为array
     * @param string $array
     * @return array|mixed
     */
    public static function json_to_array($array)
    {
        if (empty($array)) {
            return array();
        }
        return json_decode($array, true);
    }

    /**
     * 合算数组里面的值
     * @param      $list array 数据源
     * @param      $key string 某个键名
     * @param null $zero string 如果是0时输出什么样的代替符
     * @return int|null
     */
    public static function array_sum($list, $key, $zero = null)
    {
        $sum = 0;
        foreach ($list as $value) {
            if (isset($value[$key])) {
                $sum += $value[$key];
            }
        }
        if ($zero != null && $sum == 0) {
            $sum = $zero;
        }
        return $sum;
    }

    /**
     * @param      $sep string 分隔符
     * @param      $list array 数据源
     * @param null $default 如果合并后为空，则输出本值
     * @return string
     */
    public static function join($sep, $list, $default = null)
    {
        $tmp = join($sep, $list);
        if ($tmp == '' && $default !== null) {
            return $default;
        }
        return $tmp;
    }

    /**
     * 检查一维的数组里面是否含有指定的值
     * @param $val string 待检查的值
     * @param $array array 检查对象
     * @param $key string 键名
     * @return bool
     */
    public static function in_1d_array($val, $array, $key)
    {
        foreach ($array as $value) {
            if ($value[$key] == $val) {
                return true;
            }
        }
        return false;
    }

    /**
     * 从URL地址是获取字符参数
     * @param string $key
     * @param string $dft
     * @return null
     */
    public static function get_url_str($key, $dft = null)
    {
        if (isset($_GET[$key])) {
            return trim($_GET[$key]);
        }
        return $dft;
    }

    /**
     * 从URL地址是获取字符参数并做url编码处理
     * @param string $key
     * @param string $dft
     * @return null
     */
    public static function get_url_urlencode($key, $dft = '')
    {
        if (isset($_GET[$key])) {
            $val = trim($_GET[$key]);
            return urlencode($val);
        }
        return $dft;
    }

    /**
     * 从URL地址是获取字符KEY参数（限定字母数字中线下线范围）
     * @param string $key
     * @param string $dft 默认值
     * @return null
     */
    public static function get_url_key($key, $dft = null)
    {
        if (isset($_GET[$key])) {
            $val = trim($_GET[$key]);
            if (preg_match('/^[a-zA-Z0-9_\-,\.]+$/', $val)) {
                return $val;
            }
        }
        return $dft;
    }

    /**
     * 从URL地址是获取字符参数
     * @param string $key
     * @param int $dft 默认值
     * @return null
     */
    public static function get_url_int($key, $dft = 0)
    {
        if (isset($_GET[$key])) {
            $val = $_GET[$key];
            if (is_numeric($val)) {
                return intval($val);
            }
        }
        return $dft;
    }

    /**
     * 从URL地址是获取字符数字参数
     * @param string $key
     * @param double $dft 默认值
     * @return null
     */
    public static function get_url_double($key, $dft = 0.00)
    {
        if (isset($_GET[$key])) {
            $val = trim($_GET[$key]);
            if (is_numeric($val)) {
                return doubleval($val);
            }
        }
        return $dft;
    }

    /**
     * 从URL获取日期
     * @param string $key
     * @param int $dft 默认值
     * @return null
     */
    public static function get_url_time($key, $dft = 0)
    {
        if (isset($_GET[$key])) {
            return strtotime($_GET[$key]);
        }
        return $dft;
    }

    /**
     * 从表单中获取字符，根据安全要求过滤内容
     * @param string $key
     * @param int $ops 1 清除换行，2 HTML编码
     * @param string $dft 默认值
     * @return null
     */
    public static function get_form_str($key, $ops = 0, $dft = '')
    {
        if (isset($_POST[$key])) {
            $val = trim($_POST[$key]);
            if (strlen($val) == 0) {
                return '';
            }

            //html安全处理
            $val = static::html_filter($val);
            $val = static::html_encode($val, $ops);

            //返回
            return $val;
        }
        return $dft;
    }

    /**
     * 从表单中获取字符并做url编码处理
     * @param string $key
     * @param string $dft 默认值
     * @return null
     */
    public static function get_form_urlencode($key, $dft = '')
    {
        if (isset($_POST[$key])) {
            $val = trim($_POST[$key]);
            return urlencode($val);
        }
        return $dft;
    }

    /**
     * 从表单中JSON数据（需要依赖get_form_str和json_to_array）
     * @param string $key
     * @param mixed $ops false: 制作简单的处理，1 清除换行，2 HTML编码
     * @param array $dft 默认值
     * @return null
     */
    public static function get_form_json($key, $ops = false, $dft = array())
    {
        if (!isset($_POST[$key])) {
            return $dft;
        }
        $str = self::get_form_str($key, $ops);
        return self::json_to_array($str);
    }

    /**
     * 从表单中获取数字
     * @param string $key
     * @param int $dft 默认值
     * @return null
     */
    public static function get_form_int($key, $dft = 0)
    {
        if (isset($_POST[$key])) {
            $val = $_POST[$key];
            if (is_numeric($val)) {
                return intval($val);
            }
        }
        return $dft;
    }

    /**
     * 从表单中获取double
     * @param string $key
     * @param double $dft 默认值
     * @return null
     */
    public static function get_form_double($key, $dft = 0.00)
    {
        if (isset($_POST[$key])) {
            $val = $_POST[$key];
            if (is_numeric($val)) {
                return doubleval($val);
            }
        }
        return $dft;
    }

    /**
     * 从表单中获取checkbox
     * @param string $key
     * @param int $dft 默认值
     * @return null
     */
    public static function get_form_chk($key, $dft = 0)
    {
        if (isset($_POST[$key])) {
            if ($_POST[$key] == 'false') {
                return 0;
            }
            return 1;
        }
        return $dft;
    }

    /**
     * 从表单中获取check array
     * @param string $key
     * @param bool $json 是否输出JSON字符串，默认true
     * @param string $dft 默认值
     * @return null
     */
    public static function get_form_arr($key, $json = true, $dft = '')
    {
        if (isset($_POST[$key])) {
            if ($json == true) {
                return json_encode($_POST[$key], JSON_UNESCAPED_UNICODE);
            } elseif ($json == false) {
                return join('', $_POST[$key]);
            } else {
                return $_POST[$key];
            }
        }
        return $dft;
    }

    /**
     * 从表单中获取日期
     * @param string $key
     * @param int $dft
     * @return null
     */
    public static function get_form_time($key, $dft = 0)
    {
        if (isset($_POST[$key])) {
            return strtotime($_POST[$key]);
        }
        return $dft;
    }

    /**
     * 从表单中获取字符KEY参数（限定字母数字中线下线范围）
     * @param string $key
     * @param string $dft 默认值
     * @return null
     */
    public static function get_form_key($key, $dft = '')
    {
        if (isset($_POST[$key])) {
            $val = trim($_POST[$key]);
            if (\quickphp\lib\Regex::getInstance()->isText($val)) {
                return $val;
            }
        }
        return $dft;
    }

    /**
     * 通过指定的数据结构，返回内容
     * @param array $init
     * @param array $ensure 强制输入项 array( 'stat' => 1, 'money' => 0.1)
     * @return array
     */
    public static function get_form_data($init, $ensure = array())
    {
        //获取表单数据
        $out = array();
        foreach ($init as $key => $item) {
            if (isset($ensure[$key])) {
                $out[$key] = $ensure[$key];
                continue;
            }
            if (!isset($_POST[$key])) {
                continue;
            }
            $type = gettype($init[$key]);
            switch ($type) {
                case 'int':
                case 'integer':
                    $out[$key] = self::get_form_int($key);
                    break;
                case 'float':
                case 'double':
                    $out[$key] = self::get_form_double($key);
                    break;
                default:
                    $out[$key] = self::get_form_str($key);
                    break;
            }
        }

        //返回
        return $out;
    }

    /**
     * 转换成INT类型
     * @param $val string
     * @return int
     */
    public static function to_int($val)
    {
        if (is_numeric($val)) {
            return intval($val);
        }
        return 0;
    }

    /**
     * 转换成Double类型
     * @param $val string
     * @return float|int
     */
    public static function to_double($val)
    {
        if (is_numeric($val)) {
            return doubleval($val);
        }
        return 0;
    }

    /**
     * 获取URL上带有url返回的地址
     * @param $url string
     * @return mixed
     */
    public static function get_backurl($url = null)
    {
        if (isset($_GET['url'])) {
            return $_GET['url'];
        } else {
            return $url;
        }
    }

    /**
     * 获取当前请求URL
     * @param $encoding bool 是否URL编码
     * @return mixed
     */
    public static function get_current_url($encoding = true)
    {
        $url = $_SERVER['REQUEST_URI'];
        if ($encoding) {
            return urlencode($url);
        }
        return $url;
    }

    /**
     * 使用固定算法得到MD5后的值
     * @param $txt
     * @param $key
     * @return string
     */
    public static function get_md5_crypt($txt, $key)
    {
        $v1 = md5($txt);
        $v2 = substr($v1, 0, 10) . strtolower($key) . substr($v1, 10);
        return md5($v2);
    }

    /**
     * 生成数据签名
     * @param array $data 数据
     * @param string $key 加密key
     * @return string 数据的签名
     */
    public static function get_data_sign($data, $key)
    {
        //字典序排列数组
        ksort($data);

        //以&符号拼接非空数据，并补上api_key
        $arr = array();
        foreach ($data as $_key => $_item) {
            //如果参数值为空，此处不能使用empty，因为参数值为0也需要参与签名
            if ($_item === null || $_item === '') {
                continue;
            }
            $arr[] = "$_key=$_item";
        }
        $str = implode('&', $arr) . $key;
        $sign = strtolower(md5($str));

        //返回
        return $sign;
    }

    /**
     * 获取随机码
     * @param int $len
     * @param int $type 0:数字字母组合, 1:纯数字，2：纯字母
     * @return string
     */
    public static function random($len, $type = 0)
    {
        $nums = '0123456789';
        $ltrs = 'abcdefghijklmnopqrstuvwxyz';
        $chars = null;
        switch ($type) {
            case 0:
                $chars = $nums . $ltrs;
                break;
            case 1:
                $chars = $nums;
                break;
            case 2:
                $chars = $ltrs;
                break;
        }
        $size = strlen($chars);
        $outs = array();
        for ($i = 0; $i < $len; $i++) {
            $outs[] = $chars[mt_rand(0, $size - 1)];
        }
        return join('', $outs);
    }

    /**
     * 根据需要的列返回新的数组
     * @param array $needs 需要的列 [xxx,xxx,xxx]
     * @param array $array 原数组
     * @return array 新的数组
     */
    public static function array_needs($needs, $array)
    {
        $_array = array();
        if ($needs == false) {
            return $_array;
        }
        foreach ($needs as $key => $item) {
            $_array[$item] = self::get_array_values_by_key($array, $item, null);
        }
        return $_array;
    }

    /**
     * 用星星替换部分字符串
     * @param string $str 字符串
     * @param int $start 开始索引（包含）
     * @param int $length 替换长度
     * @param int $count 星星数量，默认和lenght数量一致
     * @return string
     */
    public static function replace_star($str, $start, $length, $count = null)
    {
        //首尾字符串截取
        $s1 = self::substr($str, 0, $start);
        $s2 = self::substr($str, $start + $length);

        //星星数量
        $star = '';
        if ($count === null) {
            $count = $length;
        }
        for ($idx = 0; $idx < $count; $idx++) {
            $star .= '*';
        }

        //组装并返回
        return $s1 . $star . $s2;
    }

    /**
     * 截取字符串（需开启mbstring支持）
     * @param string $str 原字符串
     * @param int $start
     * @param null $length
     * @return string
     */
    public static function substr($str, $start = 0, $length = null)
    {
        $encoding = 'UTF-8';
        return mb_substr($str, $start, $length, $encoding);
    }

    /**
     * 组装scheme信息
     * @param string $protocol scheme协议
     * @param string $path scheme路径
     * @param array $query scheme参数
     * @return string
     */
    public static function scheme_encode($protocol, $path, $query = array())
    {
        //参数检查
        if (empty($protocol) || empty($path)) {
            return '';
        }

        //组装参数
        $query_str = null;
        $query_arr = array();
        foreach ($query as $key => $item) {
            $query_arr[] = "$key=$item";
        }
        $query_str = implode('&', $query_arr);

        //组装scheme
        $scheme = "{$protocol}://router{$path}?{$query_str}";

        //返回
        return $scheme;
    }
}