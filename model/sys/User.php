<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/5/4
 * Time: 22:42
 */

namespace model\sys;

use model\Model;
use model\user\Ticket;

class User extends Model
{
    private static $_instance;
    var $table = 'sys_user';
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
     * 添加(编辑)用户
     * @param $data
     * @return array|int|string
     */
    public static function save($data)
    {
        //外部参数
        $time = time();
        $openid = parent::get($data, 'openid');
        $uname = parent::get($data, 'uname');
        $avatar = parent::get($data, 'avatar');
        $gender = parent::get($data, 'gender', 0);
        $country = parent::get($data, 'country');
        $province = parent::get($data, 'province');
        $city = parent::get($data, 'city');

        //检查参数
        if (empty($openid) || empty($uname) || empty($avatar) || $gender <= 0 || empty($country) || empty($province) || empty($city)) {
            return \common::output_error(ERR_DATA_MISS);
        }

        //查询用户是否注册
        //封装数据
        $search[] = array('openid', $openid);

        //根据用户名获取用户信息
        $info = User::getInstance()->get_one_data_by_array($search, 'uid');
        $uid = $info['uid'];
        if ($uid > 0) {
            //编辑用户信息
            //封装参数
            $data = array(
                'uname' => $uname,
                'avatar' => $avatar,
                'gender' => $gender,
                'country' => $country,
                'province' => $province,
                'city' => $city,
                'mtime' => $time
            );

            $ret = User::getInstance()->update_by_key($uid, $data);
            if ($ret == false) {
                return \common::output_error(ERR_SYSTEM_ERROR);
            }
        } else {
            //新增用户信息
            //封装参数
            $data = array(
                'tid' => 200,
                'openid' => $openid,
                'uname' => $uname,
                'avatar' => $avatar,
                'gender' => $gender,
                'country' => $country,
                'province' => $province,
                'city' => $city,
                'atime' => $time,
                'mtime' => $time,
                'ltime' => 0,
                'stat' => 1
            );

            $uid = User::getInstance()->insert($data);
            if ($uid <= 0) {
                return \common::output_error(ERR_SYSTEM_ERROR);
            }

            //建立用户票据
            Ticket::save(array('uid' => $uid));
        }

        return $uid;
    }


    /**
     * 创建ticket（每次更新票据都需要经过此函数）
     * @param int $uid 用户id
     * @return mixed
     */
    public static function update_kets($uid)
    {
        //外部参数
        $time = time();

        //参数检查
        if ($uid <= 0) {
            return \common::output_error(ERR_DATA_MISS);
        }

        //生成票据和刷新票据
        $ket = null;
        $rket = null;
        self::build($uid, $time, $ket, $rket);

        //组装数据
        $data = array(
            'ket' => $ket,
            'rket' => $rket,
            'mtime' => $time
        );

        //更新数据
        $ret = Ticket::getInstance()->update_by_key($uid, $data);
        if ($ret == false) {
            return \common::output_error(ERR_SYSTEM_ERROR);
        }

        //组装返回数据
        $result = array(
            'uid' => $uid,
            'ket' => $ket,
            'rket' => $rket,
            'expire' => $time + 432000
        );

        //返回
        return $result;
    }

    /**
     * 生成票据
     * @param int $uid 用户id
     * @param int $mtime 生成时间（此字段与ticket表中的要一致）
     * @param string $ket 票据
     * @param string $rket 刷新票据
     */
    private static function build($uid, $mtime, &$ket, &$rket)
    {
        //票据（字母序顺序）
        $ket = \common::get_md5_crypt("mtime=$mtime&uid=$uid", \config::$skey);

        //刷新票据（字母序倒序）
        $rket = \common::get_md5_crypt("uid=$uid&mtime=$mtime", \config::$skey);
    }

    /**
     * 用户登录操作
     * @param $data
     * @return array|mixed
     */
    public static function login($data)
    {
        //外部参数
        $time = time();
        $loginid = parent::get($data, 'loginid');
        $pwd = parent::get($data, 'pwd');

        //参数检查
        if (empty($loginid) || empty($pwd)) {
            return \common::output_error(ERR_DATA_MISS);
        }

        //封装参数
        $data = array();
        $data[] = array('loginid', $loginid);

        //根据用户名获取用户信息
        $info = User::getInstance()->get_one_data_by_array($data);
        if ($info == false) {
            return \common::output_error('用户名不存在!');
        }

        $pwd = \common::get_md5_crypt($pwd, \config::$skey);
        if ($pwd != $info['pwd']) {
            return \common::output_error('密码错误!');
        }

        //组装数据
        $data = array(
            'ltime' => $time
        );

        //更新用户数据
        $ret = User::getInstance()->update_by_key($info['uid'], $data);
        if ($ret == false) {
            return \common::output_error(ERR_NO_DATA);
        }

        //更新用户票据
        $ticket = User::update_kets($info['uid']);

        //封装参数
        $info['uid'] = $ticket['uid'];
        $info['ket'] = $ticket['ket'];
        $info['rket'] = $ticket['rket'];
        $info['expire'] = $ticket['expire'];

        //返回
        return $info;
    }

    /**
     * 获取用户信息
     * @param $uid
     * @return array|mixed
     */
    public static function get_info_by_uid($uid)
    {
        //参数检查
        if ($uid <= 0) {
            return \common::output_error(ERR_DATA_MISS);
        }

        //获取用户信息
        $result = User::getInstance()->get_one_data_by_pkey($uid);
        if ($result == false) {
            return \common::output_error(ERR_NO_DATA);
        }

        //返回
        return $result;
    }
}