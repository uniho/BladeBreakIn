<?php

spl_autoload_register(function($class_name) {
  $s = str_replace('\\', '/', $class_name);
  $file_name = __DIR__ . "/{$s}.php";
  if (is_file($file_name)) {
    require_once $file_name;
  }
});
