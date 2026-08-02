<?php

if (isset($_SERVER['HTTP_X_DEBUG']) || isset($_GET['_test123'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'file' => __FILE__,
        'query' => $_GET,
        'server' => $_SERVER['QUERY_STRING'] ?? 'N/A',
        'uri' => $_SERVER['REQUEST_URI'] ?? 'N/A',
    ]);
    exit;
}

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$isVercel = getenv('VERCEL') || getenv('VERCEL_ENV');

if ($isVercel) {
    putenv('LOG_CHANNEL=stderr');
    putenv('CACHE_STORE=array');
    putenv('SESSION_DRIVER=array');
    putenv('QUEUE_CONNECTION=sync');
    putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

    @mkdir('/tmp/bootstrap/cache', 0755, true);
    @mkdir('/tmp/storage/framework/views', 0755, true);
    @mkdir('/tmp/storage/framework/cache/data', 0755, true);
    @mkdir('/tmp/storage/framework/sessions', 0755, true);
    @mkdir('/tmp/storage/logs', 0755, true);
}

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

if ($isVercel) {
    $app->useBootstrapPath('/tmp/bootstrap');
    $app->useStoragePath('/tmp/storage');
}

if (isset($_GET['_debug'])) {
    $req = Request::capture();
    header('Content-Type: application/json');
    echo json_encode([
        'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'N/A',
        'request_uri' => $_SERVER['REQUEST_URI'] ?? 'N/A',
        'path_info' => $req->getPathInfo(),
        'base_url' => $req->getBaseUrl(),
        'base_path' => $req->getBasePath(),
        'method' => $req->getMethod(),
        'is_vercel' => $isVercel,
    ]);
    exit;
}

$app->handleRequest(Request::capture());
