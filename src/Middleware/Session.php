<?php

namespace App\Middleware;

use GingerTek\Routy;
use App\Services\Tokens;
use App\Services\Users;

class Session
{
  private static string $secret = 'potatodemon';

  public static function init(Routy $app)
  {
    if ($token = $_COOKIE['token'] ?? null) {
      if ($parsed = Tokens::decode($token))
        $app->setCtx('session', $parsed);
    }
  }

  public static function id(Routy $app)
  {
    self::init($app);
    if (!($sess = $app->getCtx('session')))
      return $app->redirect('/login');
    if (!($user = (new Users)->get($sess->uid)))
      return $app->end(401);
    $app->setCtx('user', $user);
  }
}
