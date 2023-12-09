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
  } 
  
  // Called from App\Providers\AppServiceProvider::boot()
  public static function onBoot()
  {
    \HQ::setenv('CCC::APP_NAME', 'Test App!');
    \Log::debug(\HQ::getenv('CCC::APP_NAME') . ' boot!');

    // \HQ::setDebugMode(true);
    \HQ::setDebugbarPageSecret(false);

    \HQ::setMaintenanceMode(0);
    // \HQ::setMaintenanceMode(5, [
    //   'secret' => 'your secret key',
    //   'message' => 'Sorry for the inconvenience but we’re performing some maintenance at the moment.',
    // ]);

    // ここではもう request() が使えます。
    \Log::debug(request()->query("test"));

    // メール送信の設定例
    \HQ::setenv('SSS::MAIL_FROM_ADDRESS', 'you@your-domain');
    \HQ::setenv('SSS::MAIL_SMTP_HOST', 'your smtp domain');
    \HQ::setenv('SSS::MAIL_SMTP_PORT', 587);
    \HQ::setenv('SSS::MAIL_SMTP_ENCRYPTION', 'tls');
    \HQ::setenv('SSS::MAIL_SMTP_USERNAME', 'your smtp user');
    \HQ::setenv('SSS::MAIL_SMTP_PASSWORD', 'your smtp pass');
  } 
  
  // Called from App\Console\Kernel::schedule()
  public static function onSchedule($schedule)
  {
    \Log::debug('schedule!');
  }

  // Called from App\Console\Kernel::commands()
  public static function onCommands()
  {
    \Log::debug('commands!');

    // 例： inspire2 という Artisan Command を追加する
    \Artisan::command('inspire2', function () {
        $this->comment(\Inspiring::quote());
    })->purpose('Display an inspiring quote v2');
    
  } 

  // Called from laravel/routes/web.php
  public static function onWeb($router)
  {
    \Log::debug('web!');

    // You can use $router->get() instead of \Router::get()
    \Route::get('test', function (Request $request) {
      return $request->path();
    });
  }
  
  // Called from laravel/routes/api.php
  public static function onApi()
  {
    \Log::debug('api!');
  }
}