<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '0');

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

require __DIR__ . '/../vendor/autoload.php';

if ($isVercel && !empty($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] === '/api/index.php') {
    header('Content-Type: application/json');
    echo json_encode([
        'request_uri' => $_SERVER['REQUEST_URI'] ?? 'N/A',
        'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'N/A',
        'query_string' => $_SERVER['QUERY_STRING'] ?? 'N/A',
        'http_host' => $_SERVER['HTTP_HOST'] ?? 'N/A',
    ]);
    exit;
}

$app = require __DIR__ . '/../bootstrap/app.php';

if ($isVercel) {
    $app->useBootstrapPath('/tmp/bootstrap');
    $app->useStoragePath('/tmp/storage');
}

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
