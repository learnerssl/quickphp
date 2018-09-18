<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/14
 * Time: 10:06
 */

$http = new swoole_http_server("0.0.0.0", 9503);

$http->set([
    'enable_static_handler' => true,
    'document_root' => '/home/Quickphp/public/live',
    'worker_num' => 5,
]);

$http->on('WorkerStart', function ($server, $worker_id) {
    require __DIR__ . '/../quickphp/base.php';
});

$http->on('request', function ($request, $response) use ($http) {
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
    }
    ob_start();
    \quickphp\Loader::Run(true);
    $content = ob_get_contents();
    ob_end_clean();
    $response->end($content);
});

$http->start();