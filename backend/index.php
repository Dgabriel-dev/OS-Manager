<?php

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

if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/vendor/autoload.php';

if (isset($_GET['_debug'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'N/A',
        'script_filename' => $_SERVER['SCRIPT_FILENAME'] ?? 'N/A',
        'request_uri' => $_SERVER['REQUEST_URI'] ?? 'N/A',
        'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'N/A',
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
        'is_vercel' => $isVercel,
        'doc_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
    ]);
    exit;
}

$app = require_once __DIR__.'/bootstrap/app.php';

if ($isVercel) {
    $app->useBootstrapPath('/tmp/bootstrap');
    $app->useStoragePath('/tmp/storage');
}

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
