<?php

namespace Utils;

final class AsyncCLI
{
  const PLUNE_BATCHES_ID = 'Plune Batches on Utils/Job';
  const PLUNE_BATCHES_INTERVAL = 60 * 60 * 24;

  public static function runQueueWorker()
  {
    $php = \HQ::getenv('CCC::PHP_CLI');
    $cmd = __DIR__ . '/../../async/cli/queue_work.php';
    exec("$php $cmd > /dev/null &");
  }

  public static function pluneBatches()
  {
    $php = \HQ::getenv('CCC::PHP_CLI');
    $cmd = __DIR__ . '/../../async/cli/plune_batches.php';
    exec("$php $cmd > /dev/null &");
  }
}
