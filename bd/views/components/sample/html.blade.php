
@props([
  'title' => 'NO TITLE',
  'header' => '',
])

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
  </head>
  <body>
    {{ $slot }}
  </body>
</html>
