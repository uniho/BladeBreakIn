<?php

define('LARAVEL_START', microtime(true));

// Check If The Application Is Under Maintenance
if (is_file(__DIR__.'/bd/laravel/storage/framework/down')) {
  $query = [];
  parse_str($_SERVER['QUERY_STRING'], $query);
  if (isset($query['secret4down'])) {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $_SERVER['REQUEST_URI'] = $path . $query['secret4down'];
  }
}

// bd/modules ディレクトリ内のモジュールをオートロードする
require_once 'bd/modules/autoload.php';

// bd/laravel/vendor ディレクトリ内のモジュールをオートロードする
require_once 'bd/laravel/vendor/autoload.php';

\HQ::onStart();

$app = require_once 'bd/laravel/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$request = \Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

$configFile = app()->getCachedConfigPath();
$configFileCustom = \HQ::getConfigFile();
if (!is_file($configFile) || filemtime($configFile) !== filemtime($configFileCustom)) {
  
  require($configFileCustom); // for Error Check

  if (!is_file($cfgFile = \HQ::getenv('CCC::STORAGE_FILE_CFG_APP'))) {
    $key = base64_encode(Illuminate\Support\Str::random(32));
    $cfgBody = "<?php return 'base64:$key';";
    @file_put_contents($cfgFile, $cfgBody);
  }

  \Artisan::call('config:cache', []);
  \File::move($configFile, \File::dirname($configFile).'/config0.php');
  \File::put($configFile, "<?php return array_replace_recursive(require(__DIR__.'/config0.php'), require(__DIR__.'/config1.php'), [
    'app' => [
      'debug' => is_file(\HQ::getenv('CCC::STORAGE_FILE_DEBUG')) || \HQ::getenv('debug'),
      'key' => require(\HQ::getenv('CCC::STORAGE_FILE_CFG_APP')),
    ],
    'debugbar' => [
      'inject' => \HQ::getDebugbarShowAlways() || \HQ::getenv('debug'),
    ],  
  ]);");
  \touch($configFile, filemtime($configFileCustom));          
  \File::copy($configFileCustom, \File::dirname($configFile).'/config1.php');

  header("Location: {$request->fullUrl()}");
  exit(0);
}

$response = $response->send();
$kernel->terminate($request, $response);
