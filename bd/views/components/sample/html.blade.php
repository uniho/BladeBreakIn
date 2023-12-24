
@props([
  'title' => 'NO TITLE',
  'header' => '',
])

<?php
  // Dynamic SCSS
  $style = $__env->yieldPushContent('style');
  if ($style) {
    $cahce_key = '__::scss_inline_cache::__'.$style;
    if (Cache::store('file')->has($cahce_key)) {
      $style = Cache::store('file')->get($cahce_key);
    } else {
      $style = Compilers::scss()->inline($style);
      $style = new Illuminate\View\ComponentSlot('<style>' . $style . '</style>');
      Cache::store('file')->put($cahce_key, $style, now()->addDays(14));
    }
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1, width=device-width" />
    <title>{{ $title }}</title>
    <link rel="stylesheet" href="{{request()->root()}}/fd/css/normalize.css">
    <link rel="stylesheet" href="{{request()->root()}}/fd/css/preflight.css">
    <link rel="stylesheet" href="{{request()->root()}}/fd/css/style.css">
    {{ $header }}
    {{ $style }}
  </head>
  <body>
    {{ $slot }}
  </body>
</html>
