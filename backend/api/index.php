<?php

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '0');

putenv('CACHE_STORE=array');
putenv('SESSION_DRIVER=array');
putenv('QUEUE_CONNECTION=sync');
putenv('LOG_CHANNEL=stderr');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

$paths = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($paths as $path) {
    if (!is_dir($path)) {
        @mkdir($path, 0755, true);
    }
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
