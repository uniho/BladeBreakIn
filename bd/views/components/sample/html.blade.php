
@props([
  'title' => 'NO TITLE',
  'header' => '',
])

<?php
  // Dynamic SCSS
  $style = $__env->yieldPushContent('style');
  if ($style) {
    $style = Compilers::scss()->inline($style);
    $style = new Illuminate\View\ComponentSlot('<style>' . $style . '</style>');
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
