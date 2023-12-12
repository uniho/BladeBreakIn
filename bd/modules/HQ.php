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

  public static function onSchedule($schedule)
  {
    if (self::on_exists() && method_exists(\HQ\On::class, 'onSchedule')) {
      \HQ\On::onSchedule($schedule);
    }
  }

  public static function onCommands()
  {
    if (self::on_exists() && method_exists(\HQ\On::class, 'onCommands')) {
      \HQ\On::onCommands();
    }
  }

  public static function onWeb($router)
  {
    if (self::on_exists() && method_exists(\HQ\On::class, 'onWeb')) {
      \HQ\On::onWeb($router);
    }
  }

  public static function onWebNoCsrf($router)
  {
    if (self::on_exists() && method_exists(\HQ\On::class, 'onWebNoCsrf')) {
      \HQ\On::onWebNoCsrf($router);
    }
  }

  public static function onApi($router)
  {
    if (self::on_exists() && method_exists(\HQ\On::class, 'onApi')) {
      \HQ\On::onApi($router);
    }
  }

  public static function webOrigin($request)
  {
    if ($request->query('rest_route')) {
      require_once __DIR__.'/../rest/cmds/autoload.php';
      require_once __DIR__.'/../rest/RestApi.php';
      $request->headers->set('Accept', 'application/json');
      return \RestApi\Procedures::handle($request);
    }

    if ($name = $request->query('view_route')) {
      abort_unless(view()->exists($name), 404, "View [{$name}] not found.");
      return view($name);
    }

    if ($request->method() == 'GET') {

      if ($name = $request->query('css_route')) {
        abort_unless(\Compilers::scss()->exists($name), 404, "CSS [{$name}] not found.");
        $css = \Compilers::scss($request->query('css_route'), [],
          ['force_compile' => $request->query('force_compile')]);
        $response = Response::make($css, 200);
        return $response->header('Content-Type', 'text/css; charset=utf-8');
      }

      if (basename(url()->current()) == 'debugbar.php') {
        $user = \Auth::user();
        if ($user && method_exists($user, 'isAdmin') && $user->isAdmin()) {
          if ($request->query('phpinfo')) {
            phpinfo();
            exit();
          }
          return view('welcome');
        }

        // Rate Limit
        if (!\Unsta\FloodControl::isAllowed('browse debugbar.php', 20, 60)) {
          \Debugbar::disable();
          return App::abort(429);
        }
        \Unsta\FloodControl::register('browse debugbar.php', 60);

        $secret = self::getDebugbarPageSecret();
        if ($secret && $request->query('secret') === $secret) {
          if ($request->query('phpinfo')) {
            phpinfo();
            exit();
          }
          return view('welcome');
        }

        \Debugbar::disable();
        return App::abort(403);
      }

      if (basename(url()->current()) == 'adminer.php') {
        require 'bd/vendor/adminer/adminer-4.8.1-en.php';
        exit();
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

    if ($request->method() == 'POST') {
      return App::abort(403);
    }
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

    return json_decode(\CachedConfig::get('$$__maintenance'), true)['level'];
  }

  public static function getMaintenanceData()
  {
    if (app()->isDownForMaintenance()) {
      return app()->maintenanceMode()->data();
    }

    if (!\CachedConfig::exists('$$__maintenance')) {
      return false;
    } 

    return json_decode(\CachedConfig::get('$$__maintenance'), true);
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
      \CachedConfig::set('$$__maintenance', json_encode($data));
    } else {
      \CachedConfig::delete('$$__maintenance');
    }
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

  public static function basePath($path = '')
  {
    return __BASE_DIR__.'/'.rtrim($path, '\/');
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
