<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/6/1
 * Time: 22:48
 * Des:微信支付公共控制器
 */

/**
 * 微信网页授权类
 */

namespace application\web\common\WxAuthController;

use application\web\Controller;
use quickphp\lib\Http;

class WxAuth extends Controller
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
     * 微信网页授权
     * 第一步：用户同意授权，获取code
     * 第二步：通过code换取网页授权access_token
     * 第三步：刷新access_token（如果需要）
     * 第四步：拉取用户信息(需scope为 snsapi_userinfo)
     */

    /**
     * 第一步：用户同意授权，获取code
     * @param string $scope 授权类型  snsapi_base(静默授权)|snsapi_userinfo(认证授权)
     * @param string $state 自定义参数,按照需求传递
     */
    public function Auth2($scope = 'snsapi_userinfo', $state = '123')
    {
        $redirect_uri = "http://" . \config::$domain . "/index.php/common/WxAuth/getAccessToken";
        $data = array(
            'appid' => self::$appId,
            'redirect_uri' => $redirect_uri,
            'response_type' => 'code',
            'scope' => $scope,
            'state' => $state
        );
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?" . http_build_query($data) . "#wechat_redirect";
        header("Location:" . $url);
    }

    /**
     * 第二步：通过code换取网页授权access_token
     */
    public function getAccessToken()
    {
        $code = $_GET['code'];
        $state = $_GET['state']; //用户自定义参数，按照需求使用
        $data = array(
            'appid' => self::$appId,
            'secret' => self::$appSecret,
            'code' => $code,
            'grant_type' => 'authorization_code'
        );
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?" . http_build_query($data);
        $ret = Http::http_curl($url);
        $access_token = $ret['access_token'];
        $openid = $ret['openid'];
        $refresh_token = $ret['refresh_token'];
        $scope = $ret['scope'];
        if ($scope == 'snsapi_base') {
            return $openid;
        } else {
            return $this->getUserInfo($access_token, $openid, $refresh_token);
        }
    }

    /**
     * 第三步：刷新access_token（如果需要）
     * @param $refresh_token
     * @return array
     */
    public function getRefreshToken($refresh_token)
    {
        $data = array('appid' => self::$appId, 'grant_type' => 'refresh_token', 'refresh_token' => $refresh_token);
        $url = "https://api.weixin.qq.com/sns/oauth2/refresh_token?" . http_build_query($data);
        $ret = Http::http_curl($url);
        $data = array(
            'access_token' => $ret['access_token'],
            'openid' => $ret['openid'],
            'refresh_token' => $ret['refresh_token'],
            'scope' => $ret['scope']
        );

        return $data;
    }

    /**
     * 第四步：拉取用户信息(需scope为 snsapi_userinfo)
     * @param string $access_token 口令
     * @param string $openid openid
     * @param string $refresh_token
     * @param string $lang 语言包
     * @return mixed
     */
    public function getUserInfo($access_token, $openid, $refresh_token, $lang = 'zh_CN')
    {
        /**
         * 检验授权凭证（access_token）是否有效
         */
        $res = $this->checkAccessToken($access_token, $openid);
        if ($res['errcode'] == '0' && $res['errmsg'] == 'ok') {
            //access_token有效
            $data = array('access_token' => $access_token, 'openid' => $openid, 'lang' => $lang);
            $url = "https://api.weixin.qq.com/sns/userinfo?" . http_build_query($data);
            $userInfo = Http::http_curl($url);
            //Todo $userInfo即微信服务器返回的用户信息
        }
        //access_token无效,重新获取access_token
        $data = $this->getRefreshToken($refresh_token);
        return $this->getUserInfo($data['access_token'], $data['openid'], $data['refresh_token']);
    }

    /**
     * 检验授权凭证（access_token）是否有效
     * @param string $access_token 口令
     * @param string $openid openid
     * @return mixed
     */
    public function checkAccessToken($access_token, $openid)
    {
        $data = array('access_token' => $access_token, 'openid' => $openid);
        $url = "https://api.weixin.qq.com/sns/auth?" . http_build_query($data);
        $ret = Http::http_curl($url);
        return $ret;
    }
}