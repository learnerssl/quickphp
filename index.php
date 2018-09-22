<?php
//------------------------
// QuickPHP 框架入口文件
//-------------------------
require_once __DIR__ . '/quickphp/base.php';
try {
    \quickphp\Loader::Run($argv = [], false);
} catch (\Exception $e) {
    echo $e->getMessage();
}



