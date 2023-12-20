
@props([
  'title' => 'NO TITLE',
  'header' => '',
])

<?php
  if (!is_null($style)) {
    // Dynamic SCSS
    $css = Compilers::scss()->inline(e($style));
    $style = new Illuminate\View\ComponentSlot('<style>' . $css . '</style>');
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1, width=device-width" />
    <title>{{ $title }}</title>
    <link rel="stylesheet" href="fd/css/normalize.css">
    <link rel="stylesheet" href="fd/css/preflight.css">
    <link rel="stylesheet" href="fd/css/style.css">
    {{ $header }}
    {{ $style }}
  </head>
  <body>
    {{ $slot }}
  </body>
</html>
