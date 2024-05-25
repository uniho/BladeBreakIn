<?php

namespace Utils;

final class AsyncCLI
{
  public static function runQueueWorker()
  {
    $php = \HQ::getenv('CCC::PHP_CLI');
    $cmd = __DIR__ . '/../../async/cli/queue_work.php';
    exec("$php $cmd > /dev/null &");
  }

  public static function pruneBatches()
  {
    $php = \HQ::getenv('CCC::PHP_CLI');
    $cmd = __DIR__ . '/../../async/cli/prune_batches.php';
    exec("$php $cmd > /dev/null &");
  }
}
