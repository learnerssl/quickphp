<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/14
 * Time: 10:06
 */
class Http
{
    CONST HOST = '0.0.0.0';
    CONST PORT = 9503;

    private $http = null;

    public function __construct()
    {
        $this->http = new swoole_http_server(self::HOST, self::PORT);

        $this->http->set([
            'enable_static_handler' => true,
            'document_root' => '/home/Quickphp/public/live',
            'worker_num' => 5,
            'task_worker_num' => 4
        ]);
        $this->http->on('workerstart', [$this, 'onWorkerStart']);
        $this->http->on('request', [$this, 'onRequest']);
        $this->http->on('task', [$this, 'onTask']);
        $this->http->on('finish', [$this, 'onFinish']);
        $this->http->on('close', [$this, 'onClose']);

        $this->http->start();
    }

    /**
     * Worker进程/Task进程启动时发生
     * @param $server
     * @param $worker_id
     */
    public function onWorkerStart($server, $worker_id)
    {
        require __DIR__ . '/../quickphp/base.php';
    }

    /**
     * 监听request请求事件
     * @param $request
     * @param $response
     * @throws Exception
     */
    public function onRequest($request, $response)
    {
        $request_type = array('server', 'header', 'get', 'post', 'cookie', 'files');
        foreach ($request_type as $type) {
            if (isset($request->$type)) {
                foreach ($request->$type as $key => $val) {
                    switch ($type) {
                        case 'get':
                            $_GET[$key] = $val;
                            break;
                        case 'post':
                            $_POST[$key] = $val;
                            break;
                        case 'cookie':
                            $_COOKIE[$key] = $val;
                            break;
                        case 'files':
                            $_FILES[$key] = $val;
                            break;
                        default:
                            $_SERVER[strtoupper($key)] = $val;
                            break;
                    }
                }
            }
            $_SERVER['http'] = $this->http;
        }
        ob_start();
        \quickphp\Loader::Run($argv = [], true);
        $content = ob_get_contents();
        ob_end_clean();
        $response->end($content);
    }

    /**
     * 监听task投递任务事件
     * @param $serv
     * @param $task_id
     * @param $src_worker_id
     * @param $data
     * @return bool
     */
    public function onTask($serv, $task_id, $src_worker_id, $data)
    {
        //获取投递任务类型
        $type = $data['type'];
        unset($data['type']);

        try {
            //投递任务
            swoole\server\task::getInstance()->$type($data);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        return true;
    }

    /**
     * 监听task投递完成事件
     * @param $serv
     * @param $task_id
     * @param $data
     */
    public function onFinish($serv, $task_id, $data)
    {

    }

    /**
     * 监听ws关闭事件
     * @param $ser
     * @param $fd
     */
    public function onClose($ser, $fd)
    {
        echo "client {$fd} close\n";
    }
}

$Ws = new Http();