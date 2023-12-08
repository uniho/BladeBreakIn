<?php

final class CCC
{
  const APP_NAME = 'BladeBreakIn';

  const DIR_LARAVEL = './bd/laravel';

  const FILE_CFG_APP = '/storage/framework/config-app.php';
  const FILE_DEBUG = '/storage/framework/debug.json';
  const STORAGE_FILE_CFG_APP = self::DIR_LARAVEL . self::FILE_CFG_APP;
  const STORAGE_FILE_DEBUG = self::DIR_LARAVEL . self::FILE_DEBUG;

  const STORAGE_LOCAL_PRIVATE_FILES_PATH = self::DIR_LARAVEL . '/storage/app';
  const STORAGE_LOCAL_PUBLIC_FILES_PATH = './storage';

  const PHP_CLI = 'php';

  // パスワードリセットなどのチャレンジタイム(秒)
  const RESETPASS_CHALLENGE_TIME = 60 * 10;

  const REGEX_PASSWORD = '/^[a-zA-Z0-9!"#$%&\'()\\-=^~@\\[;:\\],.\\/\\|`{+*}<>?_]{6,1024}$/';
  const VALIDATE_PASSWORD = ['required', 'min:6', 'max:1024', 'regex:'.self::REGEX_PASSWORD];
  const VALIDATE_EMAIL = ['required', 'email', 'max:100'];
}
