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

$http->on('request', function ($request, $response) {
//    ob_start();
    $_SERVER['REQUEST_URI'] = $request->server['request_uri'];
    $content = \quickphp\Loader::Run(true);
//    ob_end_clean();
    $response->end($content);
});

$http->start();