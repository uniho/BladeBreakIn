<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1, width=device-width" />
    <title>{{ HQ::getenv('CCC::APP_NAME') }}</title>
    <link rel="stylesheet" href="fd/css/normalize.css">
    <link rel="stylesheet" href="fd/css/preflight.css">
    <link rel="stylesheet" href="fd/css/style.css">
    <link rel="stylesheet" href="?css_route=sample.style">
  </head>
  <body>
    <div class="wrapper">
      <div class="title">
        {{ HQ::getenv('CCC::APP_NAME') }} 
      </div>  
      <div>
        Your Query => <br/>
        {{ json_encode(request()->query(), JSON_UNESCAPED_UNICODE) }}
      </div>
      <div>
        Your Content => <br/>
        {{ request()->getContent() }}
      </div>
    </div>
  </body>
</html>
