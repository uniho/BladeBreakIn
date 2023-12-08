#!/usr/bin/env php
<?php

require_once __DIR__.'/../../modules/autoload.php';
require_once __DIR__.'/../../laravel/vendor/autoload.php';

$app = require_once __DIR__.'/../../laravel/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$status = $kernel->call(
  'inspire2', [],
  new Symfony\Component\Console\Output\ConsoleOutput
);

$kernel->terminate(new Symfony\Component\Console\Input\ArgvInput, $status);

exit($status);
