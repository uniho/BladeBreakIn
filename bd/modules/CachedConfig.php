<?php

final class CachedConfig
{
  protected static $KEY; 

  public static function __init()
  {
    // Cache の保存先が file の場合は DOCUMENT_ROOT などでユニークにする必要なし
    // self::$KEY = md5($_SERVER['DOCUMENT_ROOT']) . '_CACHED_CONFIG'; 
    self::$KEY = 'CACHED_CONFIG'; 
  }

  public static function get($key)
  {
    $config = \Cache::store('file')->get(self::$KEY);
    if (!isset($config[$key])) return null;
    return $config[$key];
  }

  public static function set($key, $val)
  {
    $config = \Cache::store('file')->get(self::$KEY);
    if (!$config) $config = [];
    $config[$key] = $val;
    return \Cache::store('file')->put(self::$KEY, $config);
  }

  public static function exists($key)
  {
    $config = \Cache::store('file')->get(self::$KEY);
    return isset($config[$key]);
  }

  public static function delete($key)
  {
    $config = \Cache::store('file')->get(self::$KEY);
    if (!isset($config[$key])) return true;
    unset($config[$key]);
    return \Cache::store('file')->put(self::$KEY, $config);
  }
}

CachedConfig::__init();
