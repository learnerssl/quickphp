<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/9/15
 * Time: 10:52
 * describe: Fill in the description of the document here
 */

//错误码
define('ERR_WRONG_ARG', 1);//对不起，参数错误。
define('ERR_NO_DATA', 2);//对不起，此数据已无效，请查证。
define('ERR_NO_RIGHT', 3);//对不起，您无权处理此记录。
define('ERR_OUT_OF_TIME', 4);//对不起，已经超时，不能再操作。
define('ERR_OUT_OF_EDIT', 5); //对不起，此数据不能被修改。
define('ERR_OUT_OF_DELETE', 6); //对不起，此数据不能被删除。
define('ERR_OUT_OF_OPERATE', 7); //对不起，此数据不能执行此操作。
define('ERR_OUT_OF_USING', 8); //对不起，此数据不能被使用。
define('ERR_SYSTEM_ERROR', 9);  //抱歉，系统出现异常，请刷新重试。
define('ERR_FORM_AUTHFAILED', 10);  //对不起，表单验证失败。
define('ERR_DATA_EXIST', 11);  //对不起，数据已存在，请选用其它的。
define('ERR_DATA_MISS', 12);  //对不起，数据缺少，请确认是否已完全输入。
define('ERR_DATA_DONE', 13);  //对不起，数据已经处理完成，请选择其它的。
define('ERR_SIGN_EXCEPTION', 14);  //对不起，数据签名异常。
define('ERR_LOGINID_ILEGAL', 15);  //'对不起，登录帐号不符合要求，请使用字母、数字等。
define('ERR_LOGINID_USED', 16);  //对不起，此帐号已经被他人使用，请使用其它帐号。
define('ERR_TICKET_DISABLED', 17);  //对不起，您的登录状态已失效。
define('ERR_REFRESH_TICKET_DISABLED', 18);  //对不起，用户的刷新票据失效。
define('ERR_REQUEST_METHOD', 19);  //对不起，错误的http请求方式。
define('ERR_VERITY', 20);  //对不起，验证码错误。
define('ERR_LOGIN', 21);  //对不起，此操作需要登录。
define('ERR_UNKNOWN', 22);  //对不起，未知操作类型