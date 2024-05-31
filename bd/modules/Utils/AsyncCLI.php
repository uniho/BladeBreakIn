<?php

namespace Utils;

final class AsyncCLI
{
  public static function runArtisan($params = '')
  {
    $php = \HQ::getenv('CCC::PHP_CLI');
    $cmd = __DIR__ . '/../../async/cli/artisan.php ' . $params;
    exec("$php $cmd > /dev/null &");
  }
}
