<?php

namespace Rest\v1\auth;

class logout
{ 
  //
  public static function get($request)
  {
    \Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return ['data' => !\Auth::check()];
  }

  //
  public static function post($request)
  {
    return ['data' => 0];
  }
}