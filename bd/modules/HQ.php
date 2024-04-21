<?php

final class HQ
{
  private static $env = []; 

  public static function onStart()
  {
    if (self::on_exists() && method_exists(\HQ\On::class, 'onStart')) {
      \HQ\On::onStart();
    }
  }

  public static function onBoot()
  {
    if (self::on_exists() && method_exists(\HQ\On::class, 'onBoot')) {
      \HQ\On::onBoot();
    }
  }

  public static function onConsole()
  {
    if (self::on_exists() && method_exists(\HQ\On::class, 'onConsole')) {
      \HQ\On::onConsole();
    }
  }

  public static function onWeb($router)
  {
    if (self::on_exists() && method_exists(\HQ\On::class, 'onWeb')) {
      \HQ\On::onWeb($router);
    }
  }

  public static function webOrigin($request)
  {
    if ($request->has('rest_route')) {
      require_once __DIR__.'/../rest/cmds/autoload.php';
      require_once __DIR__.'/../rest/RestApi.php';
      $request->headers->set('Accept', 'application/json');
      return \RestApi\Procedures::handle($request);
    }

    if ($request->has('view_route')) {
      $name = $request->query('view_route');
      abort_unless(view()->exists($name), 404, "View [{$name}] not found.");
      return view($name); // $data dosen't need.
    }

    if ($request->method() == 'GET') {

      if ($request->has('css_route')) {
        $name = $request->query('css_route');
        abort_unless(\Compilers::scss()->exists($name), 404, "CSS [{$name}] not found.");
        $contents = \Compilers::scss($name, [], ['force_compile' => $request->has('force_compile')]);
        $response = Response::make($contents, 200);
        return $response->header('Content-Type', 'text/css; charset=utf-8');
      }

      if ($request->has('js_route')) {
        $name = $request->query('js_route');
        abort_unless(\Compilers::js()->exists($name), 404, "JS [{$name}] not found.");
        $contents = \Compilers::js($name, [], ['force_compile' => $request->has('force_compile')]);
        $response = Response::make($contents, 200);
        return $response->header('Content-Type', 'application/javascript; charset=utf-8');
      }

      if (basename(url()->current()) == 'debugbar.php') {
        if (self::allowDebugbar()) {
          if ($request->has('phpinfo')) {
            phpinfo();
            exit();
          }
          return view('welcome');
        }
        debugbar()->disable();
        return App::abort(403);
      }

    }

    if (view()->exists('index')) {
      return view('index');
    }

    if (is_file('./fd/index.html')) {
      $path = 'fd/';
      if ($request->query()) {
        $path .= '?'.Arr::query($request->query());
      }
      header("Location: $path");
      exit();
    }

    if (view()->exists('sample.index')) {
      return view('sample.index');
    }

    return \App::abort(404);
  }

  public static function getenv($name)
  {
    if (isset(self::$env[$name])) {
      return self::$env[$name];
    }

    if (substr($name, 0, 5) === 'CCC::') {
      $refClass = new ReflectionClass(\CCC::class);
      $consts = $refClass->getConstants();
      return $consts[substr($name, 5)] ?? null;
    }
  }

  public static function setenv($name, $val)
  {
    self::$env[$name] = $val;
  }

  public static function getDebugMode(): bool
  {
    return is_file(self::getenv('CCC::STORAGE_FILE_DEBUG'));
  }

  public static function setDebugMode(bool $mode)
  {
    if ($mode) {
      if (self::getDebugMode()) return;
      file_put_contents(self::getenv('CCC::STORAGE_FILE_DEBUG'), '1');
    } else {
      if (!self::getDebugMode()) return;
      @unlink(self::getenv('CCC::STORAGE_FILE_DEBUG'));
    }
  }

  public static function getMaintenanceMode()
  {
    if (app()->isDownForMaintenance()) {
      return 5;
    }

    if (!\CachedConfig::exists('$$__maintenance')) {
      return 0;
    } 

    return \CachedConfig::get('$$__maintenance')['level'];
  }

  public static function getMaintenanceData()
  {
    if (app()->isDownForMaintenance()) {
      return app()->maintenanceMode()->data();
    }

    if (!\CachedConfig::exists('$$__maintenance')) {
      return false;
    } 

    return \CachedConfig::get('$$__maintenance');
  }

  public static function setMaintenanceMode($level, $data = [])
  {
    if (is_int($level) && $level >= 5) {
      app()->maintenanceMode()->activate($data);

      // It doesn't matter, maybe.
      // file_put_contents(
      //   storage_path('framework/maintenance.php'),
      //   file_get_contents(__DIR__.'/../laravel/vendor/laravel/framework/src/Illuminate/Foundation/Console/stubs/maintenance-mode.stub')
      // );

      return;
    }

    if (app()->isDownForMaintenance()) {
      app()->maintenanceMode()->deactivate();
      @unlink(storage_path('framework/maintenance.php')); // just to make sure
    }

    if ($level) {
      $data['level'] = $level;
      \CachedConfig::set('$$__maintenance', $data);
    } else {
      \CachedConfig::delete('$$__maintenance');
    }
  }

  public static function getDebugShowSource()
  {
    return isset(self::$env['debugShowSource']);
  }

  public static function setDebugShowSource($mode)
  {
    if (!$mode) {
      unset(self::$env['debugShowSource']);
      return;
    }
    self::$env['debugShowSource'] = true;
  }

  public static function getDebugbarShowAlways()
  {
    return isset(self::$env['debugbarShowAlways']);
  }

  public static function setDebugbarShowAlways($mode)
  {
    if (!$mode) {
      unset(self::$env['debugbarShowAlways']);
      return;
    }
    self::$env['debugbarShowAlways'] = true;
  }

  public static function getDebugbarPageSecret()
  {
    return \CachedConfig::get('$$__DEBUGBAR_PAGE_SECRET');
  }

  public static function setDebugbarPageSecret($secret)
  {
    if (!$secret) {
      \CachedConfig::delete('$$__DEBUGBAR_PAGE_SECRET');
      return;
    }
    \CachedConfig::set('$$__DEBUGBAR_PAGE_SECRET', $secret);
  }

  public static function allowDebugbar() {
    if (basename(url()->current()) == 'debugbar.php') {
      $user = \Auth::user();
      if ($user && class_exists('\Models\UserEx') && \Models\UserEx::find($user->id)->isAdmin()) {
        return true;
      }

      // Rate Limit
      if (!\Unsta\FloodControl::isAllowed('browse debugbar.php', 20, 60)) {
        return false;
      }
      \Unsta\FloodControl::register('browse debugbar.php', 60);

      $secret = self::getDebugbarPageSecret();
      if ($secret && request()->query('secret') === $secret) {
        return true;
      }

      return false;
    }
  }

  public static function getConfigFile(): string
  {
    $file = __DIR__.'/../config.php';
    if (is_file($file)) {
      return $file;
    } 
    return __DIR__.'/../config.sample.php';
  }

  private static function on_exists(): bool
  {
    if (!class_exists('\HQ\On::class', false)) {
      $file = __DIR__.'/../on.php';
      if (!is_file($file)) return false;
      include_once($file);
    }
    return true;
  }
}
