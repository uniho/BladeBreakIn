<?php

define('LARAVEL_START', microtime(true));

require_once 'bd/modules/autoload.php';
require_once 'bd/laravel/vendor/autoload.php';

\HQ::onStart();
\HQ::setenv('debug', true);

$app = require_once 'bd/laravel/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$request = \Illuminate\Http\Request::capture();
$response = $kernel->handle($request)->send();
$kernel->terminate($request, $response);
