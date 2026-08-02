<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '0');

if (isset($_GET['_debug'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'request_uri' => $_SERVER['REQUEST_URI'] ?? 'N/A',
        'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'N/A',
        'query_string' => $_SERVER['QUERY_STRING'] ?? 'N/A',
        'http_host' => $_SERVER['HTTP_HOST'] ?? 'N/A',
        'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'N/A',
        'is_vercel' => getenv('VERCEL') ?: 'no',
    ]);
    exit;
}

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

$app = require __DIR__ . '/../bootstrap/app.php';

if ($isVercel) {
    $app->useBootstrapPath('/tmp/bootstrap');
    $app->useStoragePath('/tmp/storage');
}

if (isset($_GET['_debug_routes'])) {
    try {
        $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
        $routes = array_map(fn($r) => $r->methods()[0] . ' ' . $r->uri(), app('router')->getRoutes()->getRoutes());
        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'count' => count($routes), 'first_5' => array_slice($routes, 0, 5)]);
    } catch (\Throwable $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => get_class($e), 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    }
    exit;
}

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
