<?php

namespace Rest\v1\anon;

class login
{ 
  //
  public static function get($request)
  {
    $user = \Auth::user();
    if ($user) {
      $user->isAdmin = $user->isAdmin();
      $user->token = $request->session()->token();
    } else {
      $user = [];
    }
    
    $maintenance = \HQ::getMaintenanceMode();
    if ($maintenance) {
      $user['maintenance'] = $maintenance;
    }

    $user['debug'] = \HQ::getDebugMode();

    return [
      'data' => $user,
    ];
  }

  //
  public static function post($request)
  {
    $validated = $request->validate([
      'mail' => \HQ::getenv('CCC::VALIDATE_EMAIL'),
      'pass' => \HQ::getenv('CCC::VALIDATE_PASSWORD'),
    ]);

    $mail = $validated['mail'];
    $pass = $validated['pass'];

    $ipAddr = $_SERVER["REMOTE_ADDR"];

    // Flood Controls

    if (!\Unsta\FloodControl::isAllowed('login', 10, 60, "$mail")) {
      // ERROR: 10回/1分、ログイン失敗した(Mail毎)
      throw new \Exception("per 1 min");
    }

    if (!\Unsta\FloodControl::isAllowed('login', 10, 60, "$ipAddr")) {
      // ERROR: 10回/1分、ログイン失敗した(IP毎)
      throw new \Exception("per 1 min");
    }

    if (!\Unsta\FloodControl::isAllowed('login', 10, 60*60, "$mail-$ipAddr")) {
      // ERROR: 10回/1時間、ログイン失敗した(Mail+IP毎)
      throw new \Exception("per 1 hour");
    }

    if (!\Unsta\FloodControl::isAllowed('login', 20, 60*60*24, "$mail-$ipAddr")) {
      // ERROR: 20回/1日、ログイン失敗した(Mail+IP毎)
      throw new \Exception("per 1 day");
    }

    if (!\Unsta\FloodControl::isAllowed('login', 30, 60*60*24*7, "$mail-$ipAddr")) {
      // ERROR: 30回/1週、ログイン失敗した(Mail+IP毎)
      throw new \Exception("per 1 week");
    }

    // Login 処理
    if (\Auth::attempt(['init' => $mail, 'password' => $pass,], true)
      || \Auth::attempt(['email' => $mail, 'password' => $pass,], true)) {

      if (\HQ::getMaintenanceMode() && !\Auth::user()->isAdmin()) {
        throw new \Exception('page under maintenance');
      }

      $request->session()->regenerate();

      \Unsta\FloodControl::clear('login', "$mail-$ipAddr");

      $user = \Auth::user();
      return ['data' => $user];
    }

    // ログイン失敗
    \Unsta\FloodControl::register('login', 60, "$mail");
    \Unsta\FloodControl::register('login', 60, "$ipAddr");
    \Unsta\FloodControl::register('login', 60*60*24*7, "$mail-$ipAddr");

    throw new \Exception('Sorry, unrecognized credentials.');
  }
}
