<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/9/13
 * Time: 09:00
 * describe: 用户日志model
 */

namespace model\crm;

use model\Model;

class Log extends Model
{
    private static $_instance;
    var $table = 'crm_log';
    var $pkey = 'lid';

    public function __construct()
    {
        parent::__construct($this->table, $this->pkey);
    }

    /**
     * 单例模型
     * @return Log
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * 新增用户登录日志
     * @param $data
     * @return array|int|string
     */
    public function save($data)
    {
        //外部参数
        $uid = parent::get($data, 'uid', 0);
        $ip = parent::get($data, 'ip');
        $atime = parent::get($data, 'atime', 0);

        //参数检查
        if ($uid <= 0 || $atime <= 0 || empty($ip)) {
            return \common::output_error(ERR_DATA_MISS);
        }

        //新增日志记录
        $lid = parent::insert($data);
        if ($lid <= 0) {
            return \common::output_error(ERR_SYSTEM_ERROR);
        }

        return $lid;
    }

}