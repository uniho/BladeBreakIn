<?php

namespace RestApi;

final class Procedures 
{
  const PREG_QUERY = '{^/v(?P<_ver>[\d]+)/(?P<_role>[\w\d\-]+)/(?P<_cmd>[\w\d]+)/(?P<_arg>[\w\d\-]+)(?:/|&|$)}';

  //
  public static function handle($request)
  {
    try {
      if (preg_match(self::PREG_QUERY, $request->query('rest_route'), $match)) {
        foreach ($match as $key => $value) {
          if (!is_int($key)) $request->query->add([$key => $value]);
        }  
        $request->query->remove('rest_route');

        $role = $request['_role'];
        if ($role != 'anon') {
          if (!self::hasRole($role)) {
            throw new \Exception('You are not authorized to access this page.');
          }
        }

        if (!in_array($request['_cmd'], ['login', 'logout', 'token']) && \HQ::getMaintenanceMode()) {
          if (!self::isAdmin()) {
            throw new \Exception('page under maintenance');
          }
        }

        $cmd = $request['_cmd'];
        $file = __DIR__."/cmds/v{$request['_ver']}/$role/$cmd.php";
        $class = "\\Rest\\v{$request['_ver']}\\$role\\$cmd";

        switch ($request->method()) {
          case 'GET':
            if ($cmd == 'token') {
              return ['data' => $request->session()->token()];
            }
            if (!is_file($file)) throw new \Exception('bad cmd');
            return $class::get($request);

          case 'POST':
            if (!is_file($file)) throw new \Exception('bad cmd');
            return $class::post($request);

        }
        throw new \Exception('bad method');
      }
      throw new \Exception('bad route');
    } catch (\Exception $e) {
      return ['error' => ['message' => $e->getMessage()]];
    }
  }

  //
  public static function check($request)
  {
    if (preg_match(self::PREG_QUERY, $request->query('rest_route'), $match)) {
      $result = [];
      foreach ($match as $key => $value) {
        if (!is_int($key)) $result[$key] = $value;
      }  
      return $result;
    }
    return false;
  }

  //
  public static function isAdmin()
  {
    $user = \Auth::user();
    return $user && class_exists('\Models\UserEx') && \Models\UserEx::find($user->id)->isAdmin();
  }

  //
  public static function hasRole($role)
  {
    $user = \Auth::user();
    return $user && class_exists('\Models\UserEx') && \Models\UserEx::find($user->id)->hasRole($role);
  }
}
