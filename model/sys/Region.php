<?php

namespace model\sys;

use model\Model;
use quickphp\lib\Redis;

class Region extends Model
{
    private static $_instance;
    var $table = 'sys_region';
    var $pkey = 'rid';

    public function __construct()
    {
        parent::__construct($this->table, $this->pkey);
    }

    /**
     * 单例模型
     * @return Region
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 通过prt地区获取列表
     * @param int $prt 父级id
     * @param string $cols
     * @return mixed
     */
    public function get_list_by_prt($prt, $cols = '*')
    {
        //数据条件
        $where = "where prt = $prt";

        //获取列表
        $list = parent::get_list($where, $cols);

        //返回
        return $list;
    }

    /**
     * 获取全地区名字（默认从缓存中提取）
     * @param int $rid 地区id
     * @param string $sep 分隔符
     * @param int $min 最小级
     * @return string
     */
    public function get_fullname($rid, $sep = '', $min = 1)
    {
        if ($rid == 0) {
            return null;
        }

        $redis_key = 'sys_region_fullname_' . $rid;
        $val = Redis::getInstance()->get($redis_key);
        if ($val == false) {
            $tmp = array();
            while (true) {
                $val = Region::getInstance()->get_one_date_by_pkey($rid);
                if ($val == false) {
                    break;
                }
                $tmp[] = $val['rname'];
                $rid = $val['prt'];
                if ($val['prt'] == $min) {
                    break;
                }
            }
            $tmp = array_reverse($tmp);
            $val = join($sep, $tmp);
            Redis::getInstance()->set($redis_key, $val);
        }
        return $val;
    }

    /**
     * 获取地区名字（默认从缓存中提取）
     * @param $rid int 地区id
     * @return string
     */
    public function get_name($rid)
    {
        if ($rid == 0) {
            return null;
        }

        $redis_key = 'sys_region_name_' . $rid;
        $val = Redis::getInstance()->get($redis_key);
        if ($val == false) {
            $val = Region::getInstance()->get_one_date_by_pkey($rid, 'rname');
            if ($val != false) {
                Redis::getInstance()->set($redis_key, $val);
            }
        }
        return $val['rname'];
    }

    /**
     * 地区父级别集合
     * @param $rid
     * @param int $min
     * @return array
     */
    public function get_prt_list($rid, $min = 1)
    {
        $rids[0] = $rid;
        while (true) {
            $val = Region::getInstance()->get_one_date_by_pkey($rid);
            if ($val == false) {
                break;
            }
            $rid = $val['prt'];
            if ($val['prt'] == $min) {
                break;
            }
            $rids[] = $val['prt'];
        }
        return $rids;
    }
}