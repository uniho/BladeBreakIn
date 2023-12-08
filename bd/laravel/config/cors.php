<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    /*
     * CORSヘッダーを出力するパスのパターン、任意でワイルドカード(*)が利用できる。
     * 一切のルートでCORS使わない場合(=違うオリジンからのアクセスは遮断される): []
     * 全てのルートを対象にする場合(=許可するオリジンを指定するなどしたい場合): ['*']
     */
    'paths' => [], // ※ デフォルトは ['api/*', 'sanctum/csrf-cookie']

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
