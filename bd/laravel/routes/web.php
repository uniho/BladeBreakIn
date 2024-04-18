<?php

use Illuminate\Http\Request;

\Route::match(['get', 'post'], '/', function (Request $request) {
  return \HQ::webOrigin($request);
});

\HQ::onWeb($router);
