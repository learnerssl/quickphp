<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/5/28
 * Time: 16:28
 */

/**
 * 微信公共类
 */

namespace application\common\controller;

use application\Controller;
use quickphp\lib\Http;
use quickphp\lib\Response;

class Wx extends Controller
{
    private static $appId;
    private static $appSecret;

    public function __construct()
    {
        parent::__construct();
        self::$appId = \config::$wx_conf['AppID'];
        self::$appSecret = \config::$wx_conf['AppSecret'];
    }

    /**
     * 验证微信服务器配置信息
     */
    public function init()
    {
        //获得参数
        $nonce = $_GET['nonce'];
        $token = 'root';
        $timestamp = $_GET['timestamp'];
        $echostr = $_GET['echostr'];
        $signature = $_GET['signature'];
        //形成数组，然后按字典序排序
        $array = array($nonce, $timestamp, $token);
        sort($array);
        //拼接成字符串,sha1加密 ，然后与signature进行校验
        $str = sha1(implode($array));
        if ($str == $signature && $echostr) {
            echo $echostr;
            exit;
        } else {
            //获取到微信推送过来post数据（xml格式）
            $postArr = file_get_contents('php://input');

            //将xml格式的数据转化为object类型
            $postObj = simplexml_load_string($postArr);

            //创建自定义菜单
            $this->createCustomMenu();

            //接收事件推送
            $msgtype = strtolower($postObj->MsgType);
            $event = strtolower($postObj->Event);
            $eventKey = strtolower($postObj->EventKey);
            switch ($msgtype) {
                //事件推送
                case 'event':
                    switch ($event) {
                        //关注事件
                        case 'subscribe';
                            //不带参数二维码事件
                            if (empty($eventKey)) {
                                return $this->reposnseText($postObj, '1');
                            } else {
                                $param = explode('_', $eventKey);
                                $key = $param[1];//二维码参数
                                //TODO 这儿写获取带参数二维码后的处理
                            }
                            break;
                        //取消关注事件
                        case 'unsubscribe';
                            break;
                        //用户已关注时的事件推送
                        case 'scan';
                            return $this->reposnseText($postObj, 1);
                            break;
                        default:
                            break;
                    }
                    break;
                //文本消息
                case 'text';
                    return $this->reposnseText($postObj, $postObj->Content);
                    break;
                //图片消息
                case 'image';
                    break;
                //语音消息
                case 'voice';
                    break;
                //视频消息
                case 'video';
                    break;
                //小视频消息
                case 'shortvideo';
                    break;
                //地理位置消息
                case 'location';
                    break;
                //链接消息
                case 'link';
                    break;
                default;
                    break;
            }
            return true;
        }
    }

    /**
     * 创建自定义菜单
     */
    public function createCustomMenu()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $this->getGlobalAccessToken();
        $data = array(
            'button' => array(
                array(
                    'name' => '明日头条',
                    'type' => 'view',
                    'url' => 'http://' . \config::$Domain
                ),
                array(
                    'name' => '关注公众号',
                    'type' => 'view',
                    'url' => 'http://' . \config::$Domain . '/index.php/home/Index/subscribe'
                )
            )
        );
        $post = json_encode($data, JSON_UNESCAPED_UNICODE);
        $ret = Http::http_curl($url, 'post', 'json', $post);
        echo $ret;
    }

    /**
     * 回复文本消息
     * @param        $postObj
     * @param string $content
     */
    public function reposnseText($postObj, $content = '')
    {
        switch ($content) {
            case 1:
                $test = '欢迎关注我们的微信公众号';
                break;
            default :
                $test = "暂未查询到该关键字";
                break;
        }
        $template = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                     </xml>";
        $fromUser = $postObj->ToUserName;
        $toUser = $postObj->FromUserName;
        $time = time();
        $msgType = 'text';
        echo sprintf($template, $toUser, $fromUser, $time, $msgType, $test);
    }

    /**
     * 回复图文消息
     * @param       $postObj
     * @param array $arr 图文内容
     */
    public function reponseNews($postObj, $arr)
    {
        $toUser = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        $template = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <ArticleCount>" . count($arr) . "</ArticleCount>
					    <Articles>";
        foreach ($arr as $k => $v) {
            $template .= "<item>
                                <Title><![CDATA[" . $v['title'] . "]]></Title>
                                <Description><![CDATA[" . $v['description'] . "]]></Description>
                                <PicUrl><![CDATA[" . $v['picUrl'] . "]]></PicUrl>
                                <Url><![CDATA[" . $v['url'] . "]]></Url>
                            </item>";
        }
        $template .= "   </Articles>
					</xml> ";
        echo sprintf($template, $toUser, $fromUser, time(), 'news');
    }

    /**
     * 获取全局access_token
     */
    public function getGlobalAccessToken()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . self::$appId . "&secret=" . self::$appSecret;
        $data = Http::http_curl($url);
        $file = ROOT . '/access_token.php'; //存放全局access_token的文件
        if (!file_exists($file)) {
            @chmod(ROOT, 0777);
            if (is_writable($file)) {
                if (file_put_contents($file, json_encode($data))) {
                    return $data['access_token'];
                }
            }
        }
        $file_create_time = fileatime($file);//获取文件的创建时间
        $fileContent = json_decode(file_get_contents($file), true);
        //acces_token未过期，直接返回access_token;
        if (($file_create_time + $fileContent['expires_in']) >= time()) {
            return $fileContent['access_token'];
        }
        //acces_token已过期，删除旧文件并生成新文件
        if (unlink($file)) {
            if (file_put_contents($file, json_encode($data))) {
                return $data['access_token'];
            }
        }
        return false;
    }

    /**
     * 生成带参数的二维码
     * @param string $action_name 二维码类型，QR_SCENE为临时的整型参数值，QR_STR_SCENE为临时的字符串参数值，QR_LIMIT_SCENE为永久的整型参数值，QR_LIMIT_STR_SCENE为永久的字符串参数值
     * @param int $scene_id 场景值(场景值ID，临时二维码时为32位非0整型，永久二维码时最大值为100000)
     * @param string $scene_str 场景值(字符串类型，长度限制为1到64，仅永久二维码支持此字段)
     * @return string
     */
    public function createQrCode($action_name = 'QR_SCENE', $scene_id = 0, $scene_str = '')
    {
        //创建二维码ticket
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=" . $this->getGlobalAccessToken();
        if ($action_name === 'QR_SCENE' || $action_name === 'QR_STR_SCENE') {
            //临时二维码
            $array = $action_name === 'QR_SCENE' ? array('scene_id' => $scene_id) : array('scene_str' => $scene_str);
            $data = array(
                'expire_seconds' => 2592000,//设置临时二维码过期时间
                'action_name' => $action_name,
                'action_info' => array('scene' => $array)
            );
        } elseif ($action_name === 'QR_LIMIT_SCENE' || $action_name === 'QR_LIMIT_STR_SCENE') {
            //永久二维码
            $array = $action_name === 'QR_LIMIT_SCENE' ? array('scene_id' => $scene_id) : array('scene_str' => $scene_str);
            $data = array('action_name' => $action_name, 'action_info' => array('scene' => $array));
        } else {
            return Response::api_response(-1, '无效的二维码类型');
        }
        //通过ticket换取二维码
        $post = json_encode($data);
        $ret = Http::http_curl($url, 'post', 'array', $post);
        return "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . urlencode($ret['ticket']);//返回二维码链接地址
    }

}