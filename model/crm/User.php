<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/5/4
 * Time: 22:42
 */

namespace model\crm;

use model\Model;
use quickphp\lib\Regex;

class User extends Model
{
    private static $_instance;
    var $table = 'crm_user';
    var $pkey = 'uid';

    public function __construct()
    {
        parent::__construct($this->table, $this->pkey);
    }

    /**
     * 单例模型
     * @return User
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 执行登录操作
     * @param $info
     * @param $pwd
     * @return array|bool
     */
    public function doLogin($info, $pwd)
    {
        //开启事务，关闭自动提交操作
        $this->begin();

        //验证登录
        $User = $this->login($info, $pwd);

        //封装数据
        $data = array('uid' => $info['uid'], 'ip' => \common::get_ip(), 'atime' => time());

        //增加用户登录日志信息
        $Log = Log::getInstance()->save($data);

        //检查返回
        if (!$User['error'] && !$Log['error']) {
            $this->commit();
        } else {
            $this->rollback();
            $etext = !empty($User['etext']) ? $User['etext'] : $Log['etext'];
            return \common::output_error($etext);
        }
        return true;
    }

    /**
     * 验证登录
     * @param $info
     * @param $pwd
     * @return array|bool
     */
    public function login($info, $pwd)
    {
        //外部参数
        $time = time();
        $uid = $info['uid'];

        //验证密码格式
        if (!Regex::getInstance()->isPassword($pwd)) {
            return \common::output_error('请输入有效的密码');
        }

        //验证密码
        if ($pwd != null && \common::get_md5_crypt($pwd, $info['username']) != $info['password']) {
            return \common::output_error('对不起，密码错误！');
        }

        //检查帐号状态
        if ($info['status'] != 1) {
            return \common::output_error('对不起，当前账号已被禁用！');
        }

        //封装数据
        $where = "`lip` = '" . \common::get_ip() . "',`mtime` = " . $time . ",`ltime` = '" . $time . "',`logins`=`logins`+1";

        //更新数据
        $ret = parent::update_with_set_by_key($uid, $where);
        if ($ret == false) {
            return \common::output_error('更新用户登录信息失败！');
        }

        //返回
        return $ret;
    }

    /**
     * 根据用户名获取用户信息
     * @param $username
     * @return array|mixed
     */
    public function get_user_by_username($username)
    {
        //检测参数
        if (!Regex::getInstance()->isUsername($username)) {
            return \common::output_error('请输入正确的账号格式');
        }
        //封装数据
        $data[] = array('username', $username);

        //根据用户名获取用户信息
        $info = parent::get_one_data_by_array($data);
        if ($info == false) {
            return \common::output_error('用户名不存在！');
        }

        //返回
        return $info;
    }
}