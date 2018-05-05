<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/9/18
 * Time: 11:40
 * describe: 系统设置model
 */

namespace model\user;

use model\Model;

class Ticket extends Model
{
    private static $_instance;
    var $table = 'user_ticket';
    var $pkey = 'uid';

    public function __construct()
    {
        parent::__construct($this->table, $this->pkey);
    }

    /**
     * 单例模型
     * @return Ticket
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 建立用户票据
     * @param $data
     * @return array|bool|int
     */
    public static function save($data)
    {
        //参数提取
        $uid = parent::get($data, 'uid', 0);
        $time = time();

        //参数检查
        if ($uid <= 0) {
            return \common::output_error(ERR_DATA_MISS);
        }

        //新增，主键uid与用户uid保持一致
        $data['uid'] = $uid;
        $data['atime'] = $time;
        $data['mtime'] = $time;
        $uid = Ticket::getInstance()->insert($data);

        //返回
        return $uid;
    }

    /**
     * 通过ticket获取用户票据信息
     * @param string $ticket 用户票据
     * @return array|mixed
     */
    public static function get_info_by_ticket($ticket)
    {
        //外部参数
        $time = time();

        //参数检查
        if (empty($ticket)) {
            return \common::output_error(ERR_DATA_MISS);
        }

        //数据条件
        $data[] = array('ket', $ticket);

        //获取信息
        $ticket = Ticket::getInstance()->get_one_data_by_array($data);
        if ($ticket == false) {
            return \common::output_error(ERR_TICKET_DISABLED);
        }

        //检查ket有效期
        if ($time - $ticket['mtime'] > 432000) {
            return \common::output_error(ERR_TICKET_DISABLED);
        }

        //获取数据并返回
        return $ticket;
    }
}