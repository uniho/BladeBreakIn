<?php

/*
* Rename this file to `on.php` if you want to use this file. 
*/

namespace HQ;

use Illuminate\Http\Request;

class On
{
  // Called from index.php
  public static function onStart()
  {
    \HQ::setDebugMode(true);
    \HQ::setDebugShowSource(false); // <====== For security, the default value is false!
    \HQ::setDebugbarShowAlways(false); // <====== For security, the default value is false!
  } 
  
  // Called from App\Providers\AppServiceProvider::boot()
  public static function onBoot()
  {
    \HQ::setenv('CCC::APP_NAME', 'Test App!');
    \Log::debug(\HQ::getenv('CCC::APP_NAME') . ' boot!');

    \HQ::setDebugbarPageSecret(false);

    \HQ::setMaintenanceMode(0);
    // \HQ::setMaintenanceMode(5, [
    //   'secret' => 'your secret key',
    //   'message' => 'Sorry for the inconvenience but we’re performing some maintenance at the moment.',
    // ]);

    // ここではもう request() が使えます。
    \Log::debug(request()->query("test"));
  } 
  
  // Called from routes/console.php
  // Define the application's command schedule and application's command schedule.
  // See
  //   https://laravel.com/docs/11.x/scheduling
  //   https://laravel.com/docs/11.x/artisan#closure-commands 
  public static function onConsole()
  {
    \Log::debug('onConsole!');

    // 例： inspire2 という Artisan Command を追加する
    \Artisan::command('inspire2', function () {
        $this->comment(\Inspiring::quote());
    })->purpose('Display an inspiring quote v2');
  } 

  // Called from laravel/routes/web.php
  public static function onWeb($router)
  {
    \Log::debug('onWeb!');

    // You can use $router->get() instead of \Router::get()
    \Route::get('test', function (Request $request) {
      debugbar()->debug('test web route!');
      return $request->path();
    });
  }
}
