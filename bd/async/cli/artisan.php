#!/usr/bin/env php
<?php

define('LARAVEL_START', microtime(true));

// Register the Composer autoloader...
require __DIR__.'/../../modules/autoload.php';
require __DIR__.'/../../laravel/vendor/autoload.php';

// Bootstrap Laravel and handle the command...
$status = (require_once __DIR__.'/../../laravel/bootstrap/app.php')
  ->handleCommand(new \Symfony\Component\Console\Input\ArgvInput);

exit($status);
