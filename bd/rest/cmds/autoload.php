<?php

spl_autoload_register(function($class_name) {
  if (substr($class_name, 0, 5) != 'Rest\\') return;
  $s = substr($class_name, 5);
  $s = str_replace('\\', '/', $s);
  $file_name = __DIR__ . "/{$s}.php";
  if (is_file($file_name)) {
    require_once $file_name;
  }
});
