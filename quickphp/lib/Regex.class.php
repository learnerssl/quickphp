<?php
/**
 * 正则表达式类
 */

namespace quickphp\lib;
class Regex
{
    private $validate = array(
        'require' => '/.+/',
        //必填
        'email' => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
        //检查是否为邮箱格式
        'url' => '/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/',
        //检查是否为url格式
        'number' => '/^[-]?\d+(\.\d+)?$/',
        //检查是否为数字
        'english' => '/^[A-Za-z]+$/',
        //检查是否为全字符格式
        'qq' => '/^\d{5,11}$/',
        //测是否为QQ号格式
        'mobile' => '/^1(3|4|5|7|8)\d{9}$/',
        //检测是否为手机格式
        'date' => '/^\d{4}-\d{2}-\d{2}$/',
        //检查是否为日期类型(yyyy-mm-dd)
        'illegal' => '/[,/\\=\?\'%\*&#%\(\)\[\]!@\$\^\-\+]',
        //是否包含非法字符
        'password' => '/^[a-zA-Z0-9]{6,16}$/',
        //检查是否符合密码要求(6-16位)
        'username' => '/^[a-zA-Z0-9]{5,}$/',
        //检查输入字符串是否为string类型(5位以上)
        'text' => '/^[a-zA-Z0-9_\-\.\/@,%]+$/',
        //检查字符串是否为KEY类型
        'que' => '/?/'
        //检查是否含有?
    );
    private $returnMatchResult = false;  //是否返回匹配结果集
    private $fixMode = null;    //修饰符  U(懒惰模式,匹配结果存在歧义的取其短)，i(忽略英文字母大小写),x(忽略空白,包括空格,tab键输出的制表符),s(让元字符'.'匹配包括换行符\n在内的所有字符)
    private $matches = array(); //匹配结果集
    private $isMatch = false;   //是否成功匹配结果
    private static $_instance;

    private function __construct($returnMatchResult, $fixMode)
    {
        $this->returnMatchResult = $returnMatchResult;
        $this->fixMode = $fixMode;
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self($returnMatchResult = false, $fixMode = null);
        }
        return self::$_instance;
    }

    /**
     * 正则匹配
     * @param $pattern
     * @param $subject
     * @return array|bool
     */
    public function regex($pattern, $subject)
    {
        $pattern = strtolower($pattern);
        if (array_key_exists($pattern, $this->validate)) {
            $pattern = $this->validate[$pattern] . $this->fixMode;
        }
        $this->returnMatchResult ? preg_match_all($pattern, $subject, $this->matches) : $this->isMatch = preg_match($pattern, $subject) === 1;
        return $this->getRegexResult();
    }

    /**
     * 输出匹配结果
     * @return array|bool
     */
    private function getRegexResult()
    {
        if ($this->returnMatchResult) {
            return $this->matches;
        } else {
            return $this->isMatch;
        }
    }

    /**
     * 切换输出匹配结果
     * @param null $bool
     */
    public function toggleReturnType($bool = null)
    {
        if (empty($bool)) {
            $this->returnMatchResult = !$this->returnMatchResult;
        } else {
            $this->returnMatchResult = is_bool($bool) ? $bool : (bool)$bool;
        }
    }

    /**
     * 设置修饰符
     * @param $fixMode
     */
    public function setFixMode($fixMode)
    {
        $this->fixMode = $fixMode;
    }

    public function isRequire($str)
    {
        return $this->regex('require', $str);
    }

    public function isEmail($email)
    {
        return $this->regex('email', $email);
    }

    public function isUrl($url)
    {
        return $this->regex('url', $url);
    }

    public function isNumber($number)
    {
        return $this->regex('number', $number);
    }

    public function isEnglish($english)
    {
        return $this->regex('english', $english);
    }

    public function isQQ($qq)
    {
        return $this->regex('qq', $qq);
    }

    public function isMobile($mobile)
    {
        return $this->regex('mobile', $mobile);
    }

    public function isDate($date)
    {
        return $this->regex('date', $date);
    }

    public function isIllegal($char)
    {
        return $this->regex('illegal', $char);
    }

    public function isPassword($password)
    {
        return $this->regex('password', $password);
    }

    public function isUsername($username)
    {
        return $this->regex('text', $username);
    }

    public function isText($text)
    {
        return $this->regex('text', $text);
    }

    public function isQue($que)
    {
        return $this->regex('que', $que);
    }

    public function check($pattern, $subject)
    {
        return $this->regex($pattern, $subject);
    }
}