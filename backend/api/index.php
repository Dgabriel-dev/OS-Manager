<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

$paths = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($paths as $path) {
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

try {
    require __DIR__ . '/../vendor/autoload.php';

    $app = require_once __DIR__ . '/../bootstrap/app.php';

    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );

    $response->send();

    $kernel->terminate($request, $response);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ]);
}
