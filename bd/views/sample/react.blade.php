@props([
  'page' => request()->query('page') ?? 1,
])

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1, width=device-width" />
    <title>React & MUI on Blade Sample</title>
    <link rel="stylesheet" href="{{request()->root()}}/fd/css/normalize.css">
    <link rel="stylesheet" href="{{request()->root()}}/fd/css/preflight.css">
    <link rel="stylesheet" href="{{request()->root()}}/fd/css/style.css">
  </head>
  <body>
    <div id="app"></div>
  </body>

  <noscript>
    <div style="height:100vh; display:flex; align-items:center; justify-content:center;">You need to enable JavaScript to run this app.</div>
  </noscript>
  
  <script crossorigin src="https://unpkg.com/react@18/umd/react.development.js"></script>
  <script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
  <script crossorigin src="https://unpkg.com/htm"></script>
  <script crossorigin src="https://unpkg.com/@mui/material@5/umd/material-ui.production.min.js"></script>
  <script crossorigin src="https://unpkg.com/clsx/dist/clsx.min.js"></script>

  <script async src="https://unpkg.com/es-module-shims/dist/es-module-shims.js"></script>
  <script type="importmap">
    {
      "imports": {
        "modules/": "./modules/",
        "pages/": "./pages/",
        "fd/": "./fd/",
        "~/": "./"
      }
    }
  </script>
  <script type="module">
    import {main} from "fd/sample/main.js";
    main({{ Js::from(['csrf_token' => request()->session()->token(), 'page' => $page]) }});
  </script>

</html>
