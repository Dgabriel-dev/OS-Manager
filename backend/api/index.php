<?php

error_reporting(E_ALL);
ini_set('display_errors', '0');

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

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

if (isset($_GET['_debug_uri'])) {
    $response = $kernel->handle($request = Illuminate\Http\Request::capture());
    
    $router = $app->make('router');
    $routes = $router->getRoutes();
    
    $routeList = [];
    foreach ($routes as $route) {
        foreach ($route->methods() as $method) {
            $routeList[] = $method . ' ' . $route->uri();
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'status' => $response->getStatusCode(),
        'request_uri' => $request->getRequestUri(),
        'path' => $request->getPathInfo(),
        'method' => $request->getMethod(),
        'route_count' => count($routeList),
        'api_routes' => array_values(array_filter($routeList, fn($r) => str_starts_with($r, 'POST api/') || str_starts_with($r, 'GET api/'))),
    ]);
    $kernel->terminate($request, $response);
    exit;
}

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
