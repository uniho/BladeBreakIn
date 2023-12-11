<?php

return [

  'app' => [
    
    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',
  ],

  'mail' => [
    'mailers' => [
      'smtp' => [
        'host' =>  'your smtp domain',
        'port' => 587,
        'encryption' => 'tls',
        'username' => 'your smtp user',
        'password' => 'your smtp pass',
      ],
    ],
  ],

];
