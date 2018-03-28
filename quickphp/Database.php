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
    var $table;
    var $pkey;
    private $auto_commit = true;
    private static $_link;
    private static $_config;

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
                Response::api_response(0, $e->getMessage());
            }
        }
    }

    /**
     * 获取查询列表(此方法不进行分页)
     * @param array $data
     * @param string $cols
     * @param null $order
     * @return array|null
     */
    public function get_list($data = array(), $cols = '*', $order = null)
    {
        if (!empty($data)) {
            $where = array();
            foreach ($data as $key => $item) {
                $sym = isset($item[2]) ? $item[2] : "=";
                //in关键字不添加单引号
                $where[] = $sym === 'in' ? "`" . $item[0] . "` {$sym} {$item[1]}" : "`" . $item[0] . "` {$sym} '{$item[1]}'";
            }
            $where = implode(' and ', $where);
            $where = " where $where";
        } else {
            $where = null;
        }
        if ($order != null) {
            $order = ' order by ' . $order;
        }
        $sql = "select $cols from `" . $this->table . "` $where $order";
        $ret = mysqli_query(self::$_link, $sql);
        $this->get_debug();
        $row = $this->get_fetch($ret);
        return $row;
    }

    /**
     * 根据主键ID获取单条数据
     * @param int $pkey 主键ID
     * @param string $col 需要获取的列
     * @param bool $for 是否需要添加行锁
     * @return mixed
     */
    public function get_one_date_by_pkey($pkey, $col = '*', $for = false)
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
     * @param array $data
     * @param string $col
     * @param string|null $order
     * @return mixed
     */
    public function get_one_data_by_array($data, $col = '*', $order = null)
    {
        if (!empty($data)) {
            $where = array();
            foreach ($data as $key => $item) {
                $sym = isset($item[2]) ? $item[2] : "=";
                $where[] = "`" . $item[0] . "` {$sym}'{$item[1]}'";
            }
            $where = implode(' and ', $where);
            $where = " where $where";
        } else {
            $where = null;
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
            $order = " order by {$order}";
        }
        $sql = "select $col from `" . $this->table . "` $where $order limit 1";
        $ret = mysqli_query(self::$_link, $sql);
        $this->get_debug();
        $row = $this->get_fetch($ret);
        return $row[0];
    }

    /**
     * 获取多条数据
     * @param array $data
     * @param int $count
     * @param string $col
     * @param string|null $order
     * @param int $idx
     * @param int $limit
     * @return array|null
     */
    public function get_data_by_array($data, &$count = 0, $col = '*', $order = null, $idx = 1, $limit = 10)
    {
        if (!empty($data)) {
            $where = array();
            foreach ($data as $key => $item) {
                $sym = isset($item[2]) ? $item[2] : "=";
                //in关键字不添加单引号
                $where[] = $sym === 'in' ? "`" . $item[0] . "` {$sym} {$item[1]}" : "`" . $item[0] . "` {$sym} '{$item[1]}'";
            }
            $where = implode(' and ', $where);
            $where = " where $where";
        } else {
            $where = null;
        }
        if ($order != null) {
            $order = " order by {$order}";
        }
        if ($col != '*') {
            $cols = explode(',', $col);
            $ncols = array();
            foreach ($cols as $key => $items) {
                $ncols[] = "`{$items}`";
            }
            $col = implode(',', $ncols);
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
     * @param array $post 条件关联数组
     * @return bool
     */
    public function update_by_key($pkey, $post)
    {
        $data = null;
        foreach ($post as $key => $item) {
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
     * @param int $pkey 主键id
     * @param string $where 如: `ltime` = XXX,`lip`= XXX;
     * @return bool
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
     * 增加一条数据
     * @param array $data
     * @return int|string
     */
    public function insert(array $data)
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
     * @param         $ret
     * @param  Int $resulttype 产生哪种类型的数组 （MYSQLI_ASSOC | MYSQLI_NUM | MYSQLI_BOTH）
     * @return array|null
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
     */
    private function get_debug()
    {
        //查看是否开启调试模式并且存在错误码
        if (DEBUG && mysqli_errno(self::$_link)) {
            return Response::api_response(mysqli_errno(self::$_link), mysqli_error(self::$_link));
        }
        return true;
    }

    /**
     * 执行原生sql语句
     * @param $sql
     * @param $model
     * @return array|bool|int|null|string
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
                return Response::api_response(0, '未知操作类型');
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
     * 获取受影响记录数
     * @return int
     */
    public function get_affected_rows()
    {
        $rows = mysqli_affected_rows(self::$_link);
        return $rows;
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