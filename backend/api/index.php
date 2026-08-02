<?php

$test = phpversion();
header('Content-Type: application/json');
echo json_encode(['php' => $test, 'status' => 'ok']);
