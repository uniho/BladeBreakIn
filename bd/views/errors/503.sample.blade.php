<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1, width=device-width" />
    <title>503 Error</title>
    <link rel="stylesheet" href="fd/css/normalize.css">
    <link rel="stylesheet" href="fd/css/preflight.css">
    <link rel="stylesheet" href="fd/css/style.css">
  </head>
  <body>
    <div style="
      display: flex;
      flex-direction: column;
      width: 100%;
      height: 100vh; height: 100dvh;
      align-items: center;
      justify-content: center;
    ">
      <div style="
        font-family: Roboto;
        font-size: 30px;
      ">
        Page Under Maintenance (503)      
      </div>
      <p style="
        margin: 2rem;
        max-width: 800px;
      ">
        {{ app()->maintenanceMode()->data()['message'] }}
      </p>
    </div>
  </body>
</html>
