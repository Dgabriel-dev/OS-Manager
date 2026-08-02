<?php

header('Content-Type: application/json');
echo json_encode([
    'status' => 'booted',
    'file' => __FILE__,
    'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'N/A',
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'N/A',
    'query_string' => $_SERVER['QUERY_STRING'] ?? 'N/A',
    'is_vercel' => getenv('VERCEL') || getenv('VERCEL_ENV'),
    'time' => time(),
]);
exit;
