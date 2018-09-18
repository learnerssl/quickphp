<?php

/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/8/26
 * Time: 09:01
 * Des:硬编码文件
 */
class data
{
    /**
     * 状态码以及对应的描述
     * @var array
     */
    public static $error_codes = array(
        array('id' => SUCCESS, 'text' => '对不起，错误的状态码。'),
        array('id' => FAIL, 'text' => '对不起，错误的状态码。'),
        array('id' => ERR_WRONG_ARG, 'text' => '对不起，参数错误。'),
        array('id' => ERR_NO_DATA, 'text' => '对不起，此数据已无效，请查证。'),
        array('id' => ERR_NO_RIGHT, 'text' => '对不起，您无权处理此记录。'),
        array('id' => ERR_OUT_OF_TIME, 'text' => '对不起，已经超时，不能再操作。'),
        array('id' => ERR_OUT_OF_EDIT, 'text' => '对不起，此数据不能被修改。'),
        array('id' => ERR_OUT_OF_DELETE, 'text' => '对不起，此数据不能被删除。'),
        array('id' => ERR_OUT_OF_OPERATE, 'text' => '对不起，此数据不能执行此操作。'),
        array('id' => ERR_OUT_OF_USING, 'text' => '对不起，此数据不能被使用。'),
        array('id' => ERR_SYSTEM_ERROR, 'text' => '抱歉，系统出现异常，请刷新重试。'),
        array('id' => ERR_FORM_AUTHFAILED, 'text' => '对不起，表单验证失败。'),
        array('id' => ERR_DATA_EXIST, 'text' => '对不起，数据已存在，请选用其它的。'),
        array('id' => ERR_DATA_MISS, 'text' => '对不起，数据缺少，请确认是否已完全输入。'),
        array('id' => ERR_DATA_DONE, 'text' => '对不起，数据已经处理完成，请选择其它的。'),
        array('id' => ERR_SIGN_EXCEPTION, 'text' => '对不起，数据签名异常。'),
        array('id' => ERR_LOGINID_ILEGAL, 'text' => '对不起，登录帐号不符合要求，请使用字母、数字等。'),
        array('id' => ERR_LOGINID_USED, 'text' => '对不起，此帐号已经被他人使用，请使用其它帐号。'),
        array('id' => ERR_TICKET_DISABLED, 'text' => '对不起，您的登录状态已失效。'),
        array('id' => ERR_REFRESH_TICKET_DISABLED, 'text' => '对不起，用户的刷新票据失效。'),
        array('id' => ERR_REQUEST_METHOD, 'text' => '对不起，错误的http请求方式。'),
        array('id' => ERR_VERITY, 'text' => '对不起，验证码错误。'),
        array('id' => ERR_LOGIN, 'text' => '对不起，此操作需要登录。'),
        array('id' => ERR_UNKNOWN, 'text' => '对不起，未知操作类型。'),
        array('id' => ERR_ERROR_CODE, 'text' => '对不起，错误的状态码。'),

    );
}
