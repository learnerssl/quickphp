<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/6/1
 * Time: 22:48
 * Des:微信支付公共控制器
 */

/**
 * 微信支付类
 */

namespace application\common\WxPayController;

use application\web\Controller;
use wxpay\database\WxPayUnifiedOrder;
use wxpay\NativePay;
use wxpay\WxPayConfig;

class WxPay extends Controller
{

    /**
     * 微信扫码支付回调地址
     */
    public function notify()
    {
        $weixinData = file_get_contents("php://input");
    }

    /**
     * 生成扫码支付二维码
     */
    public function weixinQrcode()
    {
        $notify = new NativePay();
        $input = new WxPayUnifiedOrder();
        $input->setBody("支付 0.01 元");
        $input->setAttach("支付 0.01 元");
        $input->setOutTradeNo(WxPayConfig::MCHID . date("YmdHis"));
        $input->setTotalFee("1");
        $input->setTimeStart(date("YmdHis"));
        $input->setTimeExpire(date("YmdHis", time() + 600));
        $input->setGoodsTag("QRCode");
        $input->setNotifyUrl("http://" . \config::$domain . "/index.php/common/weixinpay/notify");//回调地址
        $input->setTradeType("NATIVE");
        $input->setProductId(1);
        $result = $notify->getPayUrl($input);
        $url2 = urlencode($result["code_url"]);//生成二维码
        return $this->display('home:index/index.php', array('url' => $url2));
    }
}