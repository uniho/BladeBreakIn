<?php

namespace Unsta;

final class FloodControl
{
  const PREFIX = 'unsta-flood';
  const CACHE_STORE = 'file';

  public static function register($name, int $window = 3600, $identifier = NULL)
  {
    // We can't use REQUEST_TIME here, because that would not guarantee
    // uniqueness.
    $time = time();
    $events = \Cache::store(self::CACHE_STORE)->get(self::getKey($name, $identifier));
    if (!$events) $events = [];
    $events = array_filter($events, function ($entry) use ($time) {
      return $entry['expire'] > $time;
    });
    $events[] = ['expire' => $time + $window, 'time' => $time];
    \Cache::store(self::CACHE_STORE)->put(self::getKey($name, $identifier), $events, $window);
  }

  public static function clear($name, $identifier = NULL)
  {
    \Cache::store(self::CACHE_STORE)->forget(self::getKey($name, $identifier));
  }

  public static function isAllowed($name, int $threshold, int $window = 3600, $identifier = NULL)
  {
    $events = \Cache::store(self::CACHE_STORE)->get(self::getKey($name, $identifier));
    if (!$events) {
      return $threshold > 0;
    }
    $time = time();
    $limit = $time - $window;
    // $number = count(array_filter($events, function ($entry) use ($limit) {
    //   return $entry['time'] > $limit;
    // }));
    $number = 0;
    foreach ($events as $e) {
      if ((int)$e['expire'] > $time && (int)$e['time'] > $limit) $number++;
    }
    return ($number < $threshold);
  }

  private static function getKey($name, $identifier)
  {
    return self::PREFIX . "_" . $name . "_" . (isset($identifier) ? $identifier : $_SERVER["REMOTE_ADDR"]);
  }
}
