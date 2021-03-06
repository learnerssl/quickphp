<?php
/**
 * Created by PhpStorm.
 * User: ssl
 * Date: 2017/4/30
 * Time: 13:07
 */

namespace quickphp;

use quickphp\lib\Response;

class Database
{
    public $table;
    public $pkey;
    private $auto_commit = true;
    private static $_link;
    private static $_config;

    /**
     * Database constructor.
     * @param $table
     * @param $key
     * @throws \Exception
     */
    public function __construct($table, $key)
    {
        $this->table = $table;
        $this->pkey = $key;
        if (!self::$_link) {
            self::$_config = \config::$database_conf;
            self::$_link = mysqli_connect(self::$_config['host'], self::$_config['username'], self::$_config['password'], self::$_config['dbname'], self::$_config['port']);
            try {
                if (mysqli_connect_errno()) {
                    throw new \Exception(mysqli_connect_error(), mysqli_connect_errno());
                }
                mysqli_set_charset(self::$_link, self::$_config['charset']);
            } catch (\Exception $e) {
                Response::api_response($e->getCode(), $e->getMessage());
            }
        }
    }

    /**
     * 获取查询列表(此方法不进行分页)
     * @param array $data 如:$data[] = array('username', $username,'=');
     * @param string $col
     * @param null $order
     * @return array
     * @throws \Exception
     */
    public function get_list($data = array(), $col = '*', $order = null)
    {
        $where = null;
        if (!empty($data)) {
            $where = array();
            foreach ($data as $key => $item) {
                $sym = isset($item[2]) ? $item[2] : "=";
                //in关键字不添加单引号
                $where[] = $sym === 'in' ? "`" . $item[0] . "` {$sym} ({$item[1]})" : "`" . $item[0] . "` {$sym} '{$item[1]}'";
            }
            $where = implode(' and ', $where);
            $where = " where $where";
        }
        if ($col != '*') {
            $cols = explode(',', $col);
            $ncols = array();
            foreach ($cols as $key => $items) {
                $ncols[] = "`{$items}`";
            }
            $col = implode(',', $ncols);
        }
        if ($order != null) {
            $order = ' order by ' . $order;
        }
        $sql = "select $col from `" . $this->table . "` $where $order";
        $ret = mysqli_query(self::$_link, $sql);
        $this->get_debug();
        $row = $this->get_fetch($ret);
        return $row;
    }

    /**
     * 根据主键ID获取单条数据
     * @param $pkey
     * @param string $col 需要获取的列
     * @param bool $for 是否需要添加行锁
     * @return mixed
     * @throws \Exception
     */
    public function get_one_data_by_pkey($pkey, $col = '*', $for = false)
    {
        if ($col != '*') {
            $cols = explode(',', $col);
            $ncols = array();
            foreach ($cols as $key => $items) {
                $ncols[] = "`{$items}`";
            }
            $col = implode(',', $ncols);
        }
        if ($for) {
            $for = " for update";
        }
        $sql = "select $col from `" . $this->table . "` where  `" . $this->pkey . "` = '{$pkey}' $for";
        $ret = mysqli_query(self::$_link, $sql);
        $this->get_debug();
        $row = $this->get_fetch($ret);
        return $row[0];
    }

    /**
     * 获取单条数据
     * @param array $data 如:$data[] = array('username', $username,'=');
     * @param string $col
     * @param null $order
     * @return mixed
     * @throws \Exception
     */
    public function get_one_data_by_array($data, $col = '*', $order = null)
    {
        $where = null;
        if (!empty($data)) {
            $where = array();
            foreach ($data as $key => $item) {
                $sym = isset($item[2]) ? $item[2] : "=";
                //in关键字不添加单引号
                $where[] = $sym === 'in' ? "`" . $item[0] . "` {$sym} ({$item[1]})" : "`" . $item[0] . "` {$sym} '{$item[1]}'";
            }
            $where = implode(' and ', $where);
            $where = " where $where";
        }
        if ($col != '*') {
            $cols = explode(',', $col);
            $ncols = array();
            foreach ($cols as $key => $items) {
                $ncols[] = "`{$items}`";
            }
            $col = implode(',', $ncols);
        }
        if ($order != null) {
            $order = ' order by ' . $order;
        }
        $sql = "select $col from `" . $this->table . "` $where $order limit 1";
        $ret = mysqli_query(self::$_link, $sql);
        $this->get_debug();
        $row = $this->get_fetch($ret);
        return $row[0];
    }

    /**
     * 获取多条数据
     * @param $data
     * @param int $count
     * @param string $col
     * @param null $order
     * @param int $idx
     * @param int $limit
     * @return array
     * @throws \Exception
     */
    public function get_data_by_array($data, &$count = 0, $col = '*', $order = null, $idx = 1, $limit = 15)
    {
        $where = null;
        if (!empty($data)) {
            $where = array();
            foreach ($data as $key => $item) {
                $sym = isset($item[2]) ? $item[2] : "=";
                //in关键字不添加单引号
                $where[] = $sym === 'in' ? "`" . $item[0] . "` {$sym} ({$item[1]})" : "`" . $item[0] . "` {$sym} '{$item[1]}'";
            }
            $where = implode(' and ', $where);
            $where = " where $where";
        }
        if ($col != '*') {
            $cols = explode(',', $col);
            $ncols = array();
            foreach ($cols as $key => $items) {
                $ncols[] = "`{$items}`";
            }
            $col = implode(',', $ncols);
        }
        if ($order != null) {
            $order = ' order by ' . $order;
        }
        $limit = $limit > 0 ? " limit " . ($idx - 1) * $limit . "," . $limit : null;
        $sql = "select $col from `" . $this->table . "`  $where $order $limit";
        $ret = mysqli_query(self::$_link, $sql);
        $this->get_debug();
        $row = $this->get_fetch($ret);
        $sql = "select count(*)  as count from `" . $this->table . "`  $where";
        $ret = mysqli_query(self::$_link, $sql);
        $count = $this->get_fetch($ret)[0]['count'];
        return $row;
    }

    /**
     * 根据主键ID修改单条数据
     * @param int $pkey 主键ID
     * @param array $datas 如:$data = array('uname' => XXX,'mobile' => XXX,);
     * @return bool
     * @throws \Exception
     */
    public function update_by_key($pkey, $datas)
    {
        $data = null;
        foreach ($datas as $key => $item) {
            $data .= "`{$key}` = '{$item}',";
        }
        $data = substr($data, 0, strlen($data) - 1);
        $sql = "update`" . $this->table . "` set $data  where `" . $this->pkey . "` = '{$pkey}'";
        mysqli_query(self::$_link, $sql);
        $this->get_debug();
        if ($this->get_affected_rows() >= 0) {
            return true;
        }
        return false;
    }

    /**
     * 根据主键ID修改数据
     * @param string $pkey 主键id
     * @param string $where 如: `ltime` = XXX,`lip`= XXX;
     * @return bool
     * @throws \Exception
     */
    public function update_with_set_by_key($pkey, $where)
    {
        $sql = "update`" . $this->table . "` set $where where `" . $this->pkey . "` = '{$pkey}'";
        mysqli_query(self::$_link, $sql);
        $this->get_debug();
        if ($this->get_affected_rows() >= 0) {
            return true;
        }
        return false;
    }

    /**
     * 插入一条数据
     * @param array $data 如:$data = array('uname' => XXX,'mobile' => XXX);
     * @return int|string
     * @throws \Exception
     */
    public function insert($data)
    {
        $cols = null;
        $value = null;
        foreach ($data as $key => $item) {
            $cols .= "`" . $key . "`,";
            $value .= "'" . $item . "',";
        }
        $cols = substr($cols, 0, strlen($cols) - 1);
        $value = substr($value, 0, strlen($value) - 1);
        $sql = "insert into `" . $this->table . "` ($cols) values ($value)";
        mysqli_query(self::$_link, $sql);
        $this->get_debug();
        $insert_id = mysqli_insert_id(self::$_link);
        return $insert_id > 0 ? $insert_id : 0;
    }

    /**
     * 获取结果集数据
     * @param $ret
     * @param int $resulttype 产生哪种类型的数组 （MYSQLI_ASSOC | MYSQLI_NUM | MYSQLI_BOTH）
     * @return array
     */
    private function get_fetch($ret, $resulttype = MYSQLI_ASSOC)
    {
        $posts = array();
        while ($row = mysqli_fetch_array($ret, $resulttype)) {
            $posts[] = $row;
        }
        mysqli_free_result($ret);
        return $posts;
    }

    /**
     * debug调试
     * @return bool
     * @throws \Exception
     */
    private function get_debug()
    {
        //查看是否开启调试模式并且存在错误码
        if (DEBUG && mysqli_errno(self::$_link)) {
            Response::api_response(mysqli_errno(self::$_link), mysqli_error(self::$_link));
        }
        return true;
    }

    /**
     * 获取受影响记录数
     * @return int
     */
    private function get_affected_rows()
    {
        $rows = mysqli_affected_rows(self::$_link);
        return $rows;
    }

    /**
     * 执行原生sql语句
     * @param $sql
     * @param $model
     * @return array|bool|int|null|string
     * @throws \Exception
     */
    public function sql($sql, $model)
    {
        $ret = mysqli_query(self::$_link, $sql);
        $this->get_debug();
        $data = null;
        switch ($model) {
            case "insert":
                $insert_id = mysqli_insert_id(self::$_link);
                $data = $insert_id > 0 ? $insert_id : 0;
                break;
            case 'delete':
                break;
            case "update":
                $data = $this->get_affected_rows() >= 0 ? true : false;
                break;
            case "select":
                $data = $this->get_fetch($ret);
                break;
            default:
                Response::api_response(ERR_UNKNOWN);
        }
        return $data;
    }

    /**
     * 执行预处理语句
     * @param string $sql 预处理语句
     * @param string $types 字符串类型  i:整形 s:字符型 f:浮点型
     * @param array $data 索引数组  如:$data = array('admin','123456');
     * @param string $type 操作类型  insert|update|detele|select
     * @return bool|int
     * @throws \Exception
     */
    public function sql_prepare($sql, $types, $data, $type)
    {
        $mysqli_stmt = mysqli_prepare(self::$_link, $sql);
        $length = count($data);
        for ($i = 0; $i < $length; $i++) {
            $mysqli_stmt->bind_param($types{$i}, $data[$i]);
        }
        $ret = $mysqli_stmt->execute();
        if ($ret) {
            switch ($type) {
                case 'insert':
                    return $mysqli_stmt->insert_id;
                    break;
                case 'update':
                    break;
                case 'select':
                    break;
                case 'delete':
                    break;
                default:
            }
        } else {
            if (DEBUG && $mysqli_stmt->errno) {
                Response::api_response($mysqli_stmt->errno, $mysqli_stmt->errno, '');
            }
        }
        $mysqli_stmt->free_result();
        return $mysqli_stmt->close();
    }

    /**
     * 设置是否自动提交
     * @param bool $auto_commit 是否自动提交
     * @return bool
     */
    public function set_auto_commit($auto_commit = true)
    {
        $this->auto_commit = ($auto_commit == true) ? true : false;
        return mysqli_autocommit(self::$_link, $this->auto_commit);
    }

    /**
     * 开启事物
     * @return bool
     */
    public function begin()
    {
        mysqli_commit(self::$_link);
        return $this->set_auto_commit(false);
    }

    /**
     * 事务提交
     * @return bool
     */
    public function commit()
    {
        mysqli_commit(self::$_link);
        return $this->set_auto_commit(true);
    }

    /**
     * 事务回滚
     * @return bool
     */
    public function rollback()
    {
        mysqli_rollback(self::$_link);
        return $this->set_auto_commit(true);
    }

    /**
     * 关闭数据库连接
     */
    public function close()
    {
        mysqli_close(self::$_link);
    }
}